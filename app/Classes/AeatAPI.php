<?php

namespace App\Classes;

use Illuminate\Support\Facades\Http;

class AeatAPI
{
    public static function checkVATInfo($vat, $name)
    {
        $input_data = <<<EOD
        <?xml version="1.0" encoding="UTF-8"?>
        <env:Envelope xmlns:env="http://schemas.xmlsoap.org/soap/envelope/" xmlns:vnif="http://www2.agenciatributaria.gob.es/static_files/common/internet/dep/aplicaciones/es/aeat/burt/jdit/ws/VNifV2Ent.xsd">
            <env:Header/>
            <env:Body>
                <vnif:VNifV2Ent>
                    <vnif:Contribuyente>
                        <vnif:Nif><![CDATA[$vat]]></vnif:Nif>
                        <vnif:Nombre><![CDATA[$name]]></vnif:Nombre>
                    </vnif:Contribuyente>
                </vnif:VNifV2Ent>
            </env:Body>
        </env:Envelope>
        EOD;

        $response = Http::withOptions([
            'ssl_key' => [config('mediforum.verifactu.certificate_key_path', ''), config('mediforum.verifactu.certificate_password', '')],
            'cert' => [config('mediforum.verifactu.certificate_path', ''), config('mediforum.verifactu.certificate_password', '')],
            'decode_content' => false
        ])
        ->withBody($input_data, "application/xml")->post("https://www1.agenciatributaria.gob.es/wlpl/BURT-JDIT/ws/VNifV2SOAP");
        $response = $response->body();

        // if (auth()->user()->id == 1) {
        //     dd($response);
        // }

        $response = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $response);
        $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        // dd($xml->xpath('//env:Body'));
        $json = json_encode($xml->xpath('//envBody'));
        $data = json_decode($json, TRUE);

        $result = [];
        if (isset($data[0]['VNifV2SalVNifV2Sal']['VNifV2SalContribuyente'])) {
            // dd($data[0]['VNifV2SalVNifV2Sal']['VNifV2SalContribuyente']);
            $result = [
                'vat' => trim($data[0]['VNifV2SalVNifV2Sal']['VNifV2SalContribuyente']['VNifV2SalNif'] ?? ''),
                'name' => empty($data[0]['VNifV2SalVNifV2Sal']['VNifV2SalContribuyente']['VNifV2SalNombre']) ? '' : trim($data[0]['VNifV2SalVNifV2Sal']['VNifV2SalContribuyente']['VNifV2SalNombre'] ?? ''),
                'result' => trim($data[0]['VNifV2SalVNifV2Sal']['VNifV2SalContribuyente']['VNifV2SalResultado'] ?? ''),
            ];
        }

        return $result;
    }
}
