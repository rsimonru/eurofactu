<!DOCTYPE html>
<html lang="es">
<header>
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: medium;
        }

        table {
            width: 100%;
        }

        td.right {
            text-align: right;
        }
        td.center {
            text-align: center;
        }

        table.tbl-border-black {
            border: 2px solid black;
            border-radius: 25px;
        }
        table.tbl-border-blue {
            border: 2px solid rgb(20, 83, 136);
            border-radius: 25px;
        }
        table.tbl-border-gray {
            border: 2px solid gray;
            border-radius: 25px;
        }

        hr {
            font-weight: bold;
        }
        @page {
            header: page-header;
            footer: page-footer;
        }

        .barcode {
            text-align: center;
        }

        /* Estilo para el texto del código QR */
        .qr-text {
            font-size: 10px;
            text-align: center;
            margin-top: 5px;
            font-weight: bold;
        }
    </style>
</header>
<body>
    <htmlpageheader name="page-header">
        <div class="mb-2">
            <div style="width: 35%; position:absolute; float: left;">
                <span class="text-5xl" style="color: rgb(20, 83, 136);">FACTURA</span>

            </div>
             <div style="width: 25%; position:absolute; float: left; text-align: center;">
                <div class="qr-text" style="text-align: center;">QR tributario:</div>
                <barcode code="{{config('mediforum.verifactu.qr_base_url')}}?nif={{$invoice->company->vat}}&numserie={{$invoice->number}}&fecha={{$invoice->invoice_date->format('d-m-Y')}}&importe={{number_format($invoice->total_line, 2, '.', '')}}"
                    type="QR" class="barcode" size="1.8" error="M"></barcode>
                <div class="qr-text" style="text-align: center;">Factura verificable en la sede electrónica de la AEAT</div>
            </div>
            <div style="width: 40%; position:absolute; float: left; text-align: right;">
                <img src="/storage/companies/{{ $company->id }}/logo_gran.png" style="width: 250px;">
            </div>
        </div>
    </htmlpageheader>
    <table width="100%" class="mb-4">
        <tr>
            <td width="50%">
                <table>
                    <tr>
                        <td ><span class="text-xl" style="color: rgb(20, 83, 136);">{{ strtoupper($company->legal_form) }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ $company->address }}</td>
                    </tr>
                    <tr>
                        <td>{{ $company->zip }} {{ $company->town }}</td>
                    </tr>
                    <tr>
                        <td>CIF: {{ $company->vat }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    <tr>
                        <td width="40%" class="right"><span class="mr-2 text-xl" style="color: rgb(20, 83, 136);">Nº DE FACTURA</span></td>
                        <td width="20%" class="right">{{ $invoice->number }}</td>
                    </tr>
                    <tr>
                        <td class="right"><span class="mr-2 text-xl" style="color: rgb(20, 83, 136);">FECHA</span></td>
                        <td class="right">{{ $invoice->invoice_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="right"><span class="mr-2 text-xl" style="color: rgb(20, 83, 136);">Nº DE PEDIDO</span></td>
                        <td class="right">{{ $invoice->reference }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <table width="100%" class="mb-4">
        <tr>
            <td width="40%"><span class="text-xl" style="color: rgb(20, 83, 136);">FACTURAR A</span></td>
        </tr>
        <tr>
            <td>{{ $invoice->legal_form }}</td>
        </tr>
        <tr>
            <td>{{ $invoice->address }}</td>
        </tr>
        <tr>
            <td>{{ $invoice->zip }} {{ $invoice->town }}</td>
        </tr>
        <tr>
            <td>CIF: {{ $invoice->vat }}</td>
        </tr>
    </table>

    <div class="w-full mb-4" >
        <span class="w-full text-xl" style="color: rgb(20, 83, 136);">CONCEPTO</span>
        <div class="mb-4" style="border: 2px solid rgb(20, 83, 136); border-radius: 5px;">
            <table width="100%" >
                <tr>
                    <td class="h-60 p-4" style="vertical-align: top;">{!! $invoice->sales_invoices_products->first()->description !!}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mb-4" style="border: 2px solid rgb(20, 83, 136); border-radius: 5px;">
        <table width="100%">
            <tr>
                <td width="55%"></td>
                <td width="25%" class="right"><span class="mr-2">Base imponible:</span></td>
                <td width="20%" class="right"><span>{{ number_format($invoice->base_line, 2, ',', '.') }}€</span></td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><span class="mr-2">IVA:</span></td>
                <td class="right"><span>{{ $invoice->sales_invoices_products->first()->tax_type*100 }}%</span></td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><span class="mr-2">Importe IVA:</span></td>
                <td class="right"><span>{{ number_format($invoice->tax_line, 2, ',', '.') }}€</span></td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><span class="mr-2 text-2xl text-primary-500" style="color: rgb(20, 83, 136);">Total Factura:</span></td>
                <td class="right"><span class="text-2xl" style="color: rgb(20, 83, 136);">{{ number_format($invoice->total_line, 2, ',', '.') }}€</span></td>
            </tr>
        </table>
    </div>


    <htmlpagefooter name="page-footer">
        <div class="w-full">
            <div style="width: 100%; position:absolute; float: left;">
                Forma de pago: Transferencia bancaria <br>
                {{ $company->legal_form }} {{ ($invoice->banks_account) ?
                    ($invoice->banks_account->bank.', '.$invoice->banks_account->address) :
                    ($company->banks_account->bank.', '.$company->banks_account->address) }}<br>
                IBAN: {{ ($invoice->banks_account) ? $invoice->banks_account->iban : $company->banks_account->iban }}<br>
            </div>
        </div>
    </htmlpagefooter>
</body>
