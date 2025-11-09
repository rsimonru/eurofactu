<?php

namespace App\Classes;

use App\Models\Company;
use App\Models\SalesInvoice;
use App\Models\VerifactuLog;
use DateTimeImmutable;
use DOMDocument;
use Exception;
use josemmo\Verifactu\Models\ComputerSystem;
use josemmo\Verifactu\Models\Records\BreakdownDetails;
use josemmo\Verifactu\Models\Records\FiscalIdentifier;
use josemmo\Verifactu\Models\Records\ForeignFiscalIdentifier;
use josemmo\Verifactu\Models\Records\ForeignIdType;
use josemmo\Verifactu\Models\Records\InvoiceIdentifier;
use josemmo\Verifactu\Models\Records\InvoiceType;
use josemmo\Verifactu\Models\Records\OperationType;
use josemmo\Verifactu\Models\Records\RegimeType;
use josemmo\Verifactu\Models\Records\RegistrationRecord;
use josemmo\Verifactu\Models\Records\TaxType;
use josemmo\Verifactu\Services\AeatClient;

class Verifactu
{
    public static function registerInvoice(SalesInvoice $sales_invoice)
    {
        $previous_sales_invoice = SalesInvoice::where('company_id', $sales_invoice->company_id)
            ->where('id', '<', $sales_invoice->id)
            ->orderBy('id', 'desc')->take(1)->get()->first();

        $verifactu_data = empty($sales_invoice->verifactu_data) ? [] : $sales_invoice->verifactu_data;
        $breakdown = [];
        $total_tax = 0;
        $total_invoice = 0;

        $recipients = [];
        if ($sales_invoice->thirdparty->foreign) {
            $recipient = new ForeignFiscalIdentifier();
            $recipient->name = $sales_invoice->thirdparty->legal_form;
            $recipient->country = $sales_invoice->thirdparty->country_code;
            $recipient->type = ForeignIdType::VAT;
            $recipient->value = $sales_invoice->thirdparty->vat;

        } else {
            $recipient = new FiscalIdentifier($sales_invoice->thirdparty->legal_form, $sales_invoice->thirdparty->vat);
        }
        $recipients[] = $recipient;

        foreach ($sales_invoice->sales_invoices_products as $line) {
            $breakdown_item = new BreakdownDetails();
            $breakdown_item->taxType = TaxType::IVA;
            $breakdown_item->regimeType = RegimeType::C01;
            $breakdown_item->operationType = ($line->tax_type > 0) ? OperationType::Subject : OperationType::NonSubject;
            $breakdown_item->taxRate = number_format($line->tax_type * 100, 2, '.', '');
            $breakdown_item->baseAmount = number_format($line->base_line, 2, '.', '');
            $breakdown_item->taxAmount = number_format($line->tax_line, 2, '.', '');
            $breakdown[] = $breakdown_item;
            $total_tax += $line->tax_line;
            $total_invoice += $line->total_line;
        }

        $record = new RegistrationRecord();
        $record->invoiceId = new InvoiceIdentifier();
        $record->invoiceId->issuerId = $sales_invoice->company->vat;
        $record->invoiceId->invoiceNumber = $sales_invoice->number;
        $record->invoiceId->issueDate = new DateTimeImmutable($sales_invoice->invoice_date->format('Y-m-d'));
        $record->issuerName = $sales_invoice->company->legal_form;
        $record->invoiceType = InvoiceType::Factura;
        $record->description = "Factura " . $sales_invoice->number;
        $record->breakdown = $breakdown;
        $record->recipients = $recipients;
        $record->totalTaxAmount = number_format($total_tax, 2, '.', '');
        $record->totalAmount = number_format($total_invoice, 2, '.', '');
        if ($previous_sales_invoice
            && isset($previous_sales_invoice->verifactu_data['hash'])
            && !empty($previous_sales_invoice->verifactu_data['hash'])
        ) {
            $record->previousInvoiceId = new InvoiceIdentifier();
            $record->previousInvoiceId->issuerId = $previous_sales_invoice->company->vat;
            $record->previousInvoiceId->invoiceNumber = $previous_sales_invoice->number;
            $record->previousInvoiceId->issueDate = new DateTimeImmutable($previous_sales_invoice->invoice_date->format('Y-m-d'));
            $record->previousHash = ($previous_sales_invoice) ? ($previous_sales_invoice->verifactu_data['hash'] ?? null) : null;
        } else {
            $record->previousInvoiceId = null; // primera factura de la cadena
            $record->previousHash = null; // primera factura de la cadena
        }
        $record->hashedAt = new DateTimeImmutable();
        $hash = $record->calculateHash();
        $record->hash = $hash;
        // dd($record);
        $record->validate();

        $verifactu_data['hash'] = $hash;
        $verifactu_data['hash_date'] = $record->hashedAt->format('Y-m-d H:i:s');

        // Define los datos del SIF
        $system = new ComputerSystem();
        $system->vendorName = config('mediforum.verifactu.vendorName');
        $system->vendorNif = config('mediforum.verifactu.vendorVat');
        $system->name = config('mediforum.verifactu.systemName');
        $system->id = config('mediforum.verifactu.systemId');
        $system->version = config('mediforum.verifactu.version');
        $system->installationNumber = $sales_invoice->company->verifactu_data['installation_id'] ?? '';
        $system->onlySupportsVerifactu = config('mediforum.verifactu.onlySupportsVerifactu');
        $system->supportsMultipleTaxpayers = config('mediforum.verifactu.supportsMultipleTaxpayers');
        $system->hasMultipleTaxpayers = config('mediforum.verifactu.hasMultipleTaxpayers');
        $system->validate();

        $taxpayer = new FiscalIdentifier($sales_invoice->company->legal_form, $sales_invoice->company->vat);
        $client = new AeatClient(
            $system,
            $taxpayer,
            config('mediforum.verifactu.certificate_path'),
            config('mediforum.verifactu.certificate_password'),
        );
        $client->setProduction(config('mediforum.verifactu.production'));
        // dd($client, $record);
        $res = $client->send([$record])->wait();

        $verifactu_log = new VerifactuLog();
        $verifactu_log->company_id = $sales_invoice->company_id;
        $verifactu_log->records = [
            'system' => $system,
            'taxpayer' => $taxpayer,
            'records' => [$record],
        ];
        $verifactu_log->response = $res;
        $verifactu_log->save();

        $lines_response = collect($res->items)->keyBy('invoiceId.invoiceNumber');
        $sales_invoice->verifactu_data = $lines_response[$sales_invoice->number] ?? null;
        $sales_invoice->save();

        // Obtiene la respuesta
        return $verifactu_log->response;
    }
    public static function registerInvoices($company_id = null, $fiscal_year = null)
    {
        $company_id = empty($company_id) ? session('company')->id : $company_id;
        $fiscal_year = empty($fiscal_year) ? today()->format('Y') : $fiscal_year;
        $company = Company::emtGet($company_id);

        $last_verifactu = SalesInvoice::where('company_id', $company_id)
            ->whereNotNull('verifactu_data')->orderBy('id', 'desc')->first();
        $sales_invoices = SalesInvoice::where('company_id', $company_id)
            ->when($last_verifactu, function($query) use ($last_verifactu) {
                return $query->where('id', '>', $last_verifactu->id);
            })
            ->where('fiscal_year', $fiscal_year)
            ->whereNull('verifactu_data')
            ->with('thirdparty', 'sales_invoices_products')
            ->orderBy('id', 'asc')->get();

        // dd(length($sales_invoices));

        if (length($sales_invoices) > 0) {
            $previous_sales_invoice = $last_verifactu;
            $verifactu_data = [];
            $records = [];
            foreach ($sales_invoices as $sales_invoice) {
                $verifactu_data[$sales_invoice->id] = empty($sales_invoice->verifactu_data) ? [] : $sales_invoice->verifactu_data;
                $breakdown = [];
                $total_tax = 0;
                $total_invoice = 0;

                $recipients = [];
                if ($sales_invoice->thirdparty->foreign) {
                    $recipient = new ForeignFiscalIdentifier();
                    $recipient->name = $sales_invoice->thirdparty->legal_form;
                    $recipient->country = $sales_invoice->thirdparty->country_code;
                    $recipient->type = ForeignIdType::VAT;
                    $recipient->value = $sales_invoice->thirdparty->vat;

                } else {
                    $recipient = new FiscalIdentifier($sales_invoice->thirdparty->legal_form, $sales_invoice->thirdparty->vat);
                }
                $recipients[] = $recipient;

                foreach ($sales_invoice->sales_invoices_products as $line) {
                    $breakdown_item = new BreakdownDetails();
                    $breakdown_item->taxType = TaxType::IVA;
                    $breakdown_item->regimeType = RegimeType::C01;
                    $breakdown_item->operationType = ($line->tax_type > 0) ? OperationType::Subject : OperationType::NonSubject;
                    $breakdown_item->taxRate = number_format($line->tax_type * 100, 2, '.', '');
                    $breakdown_item->baseAmount = number_format($line->base_line, 2, '.', '');
                    $breakdown_item->taxAmount = number_format($line->tax_line, 2, '.', '');
                    $breakdown[] = $breakdown_item;
                    $total_tax += $line->tax_line;
                    $total_invoice += $line->total_line;
                }

                $record = new RegistrationRecord();
                $record->invoiceId = new InvoiceIdentifier();
                $record->invoiceId->issuerId = $company->vat;
                $record->invoiceId->invoiceNumber = $sales_invoice->number;
                $record->invoiceId->issueDate = new DateTimeImmutable($sales_invoice->invoice_date->format('Y-m-d'));
                $record->issuerName = $company->legal_form;
                $record->invoiceType = InvoiceType::Factura;
                $record->description = "Factura " . $sales_invoice->number;
                $record->breakdown = $breakdown;
                $record->recipients = $recipients;
                $record->totalTaxAmount = number_format($total_tax, 2, '.', '');
                $record->totalAmount = number_format($total_invoice, 2, '.', '');
                if ($previous_sales_invoice
                    && isset($previous_sales_invoice->verifactu_data['hash'])
                    && !empty($previous_sales_invoice->verifactu_data['hash'])
                ) {
                    $record->previousInvoiceId = new InvoiceIdentifier();
                    $record->previousInvoiceId->issuerId = $company->vat;
                    $record->previousInvoiceId->invoiceNumber = $previous_sales_invoice->number;
                    $record->previousInvoiceId->issueDate = new DateTimeImmutable($previous_sales_invoice->invoice_date->format('Y-m-d'));
                    $record->previousHash = ($previous_sales_invoice) ? ($previous_sales_invoice->verifactu_data['hash'] ?? null) : null;
                } else {
                    $record->previousInvoiceId = null; // primera factura de la cadena
                    $record->previousHash = null; // primera factura de la cadena
                }
                $record->hashedAt = new DateTimeImmutable();
                $hash = $record->calculateHash();
                $record->hash = $hash;
                // dd($record);
                $record->validate();

                $verifactu_data[$sales_invoice->id]['hash'] = $hash;
                $verifactu_data[$sales_invoice->id]['hash_date'] = $record->hashedAt->format('Y-m-d H:i:s');
                $sales_invoice->verifactu_data = $verifactu_data[$sales_invoice->id];

                $previous_sales_invoice = $sales_invoice;
                $records[] = $record;
            }

            // Define los datos del SIF
            $system = new ComputerSystem();
            $system->vendorName = config('mediforum.verifactu.vendorName');
            $system->vendorNif = config('mediforum.verifactu.vendorVat');
            $system->name = config('mediforum.verifactu.systemName');
            $system->id = config('mediforum.verifactu.systemId');
            $system->version = config('mediforum.verifactu.version');
            $system->installationNumber = $company->verifactu_data['installation_id'] ?? '';
            $system->onlySupportsVerifactu = config('mediforum.verifactu.onlySupportsVerifactu');
            $system->supportsMultipleTaxpayers = config('mediforum.verifactu.supportsMultipleTaxpayers');
            $system->hasMultipleTaxpayers = config('mediforum.verifactu.hasMultipleTaxpayers');
            $system->validate();

            $taxpayer = new FiscalIdentifier($company->legal_form, $company->vat);
            $client = new AeatClient(
                $system,
                $taxpayer,
                config('mediforum.verifactu.certificate_path'),
                config('mediforum.verifactu.certificate_password'),
            );
            $client->setProduction(config('mediforum.verifactu.production'));
            // dd($client, $records);
            $res = $client->send($records)->wait();

            $verifactu_log = new VerifactuLog();
            $verifactu_log->company_id = $company->id;
            $verifactu_log->records = [
                'system' => $system,
                'taxpayer' => $taxpayer,
                'records' => $records,
            ];
            $verifactu_log->response = $res;
            $verifactu_log->save();

            $lines_response = collect($res->items)->keyBy('invoiceId.invoiceNumber');
            foreach ($sales_invoices as $sales_invoice) {
                $verifactu_data[$sales_invoice->id]['result'] = $lines_response[$sales_invoice->number] ?? null;
                $sales_invoice->verifactu_data = $verifactu_data[$sales_invoice->id];
                $sales_invoice->save();
            }

            // Obtiene la respuesta
            return $verifactu_log->response;
        } else {
            return null;
        }
    }

    public static function removeXmlPrefix($xmlString, $prefix = 'tikR')
    {
        if (empty($xmlString)) {
            return $xmlString;
        }

        // Quitar prefijo de etiquetas de apertura y cierre usando regex
        $pattern = '/(<\/?)' . preg_quote($prefix, '/') . ':/';
        return preg_replace($pattern, '$1', $xmlString);
    }

    public static function xmlToJson($xmlString)
    {
        // Verificar entrada
        if (empty($xmlString)) {
            return [];
        }

        // Quitar prefijo tikR: de todos los nodos
        $cleanXml = self::removeXmlPrefix($xmlString, 'tikR');
        $cleanXml = self::removeXmlPrefix($cleanXml, 'env');
        $cleanXml = self::removeXmlPrefix($cleanXml, 'tik');

        $xml = simplexml_load_string($cleanXml, "SimpleXMLElement");

        if ($xml === false) {
            return [];
        }

        $json = json_encode($xml->xpath('//Body'));
        $data = json_decode($json, TRUE);

        return $data ?: [];
    }
}
