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
                <barcode code="{{config('euromatica.verifactu.qr_base_url')}}?nif={{$document->vat}}&numserie={{$document->number}}&fecha={{$document->invoice_date->format('d-m-Y')}}&importe={{number_format($document->sales_invoices_products->sum('total_line'), 2, '.', '')}}"
                    type="QR" class="barcode" size="1.8" error="M"></barcode>
                <div class="qr-text" style="text-align: center;">Factura verificable en la sede electrónica de la AEAT</div>
            </div>
            <div style="width: 40%; position:absolute; float: left; text-align: right;">
                <img src="/storage/companies/{{ $document->company->id }}/logo_gran.png" style="width: 250px;">
            </div>
        </div>
    </htmlpageheader>
    <table width="100%" class="mb-4">
        <tr>
            <td width="50%">
                <table>
                    <tr>
                        <td ><span class="text-xl" style="color: rgb(20, 83, 136);">{{ strtoupper($document->company->legal_form) }}</span></td>
                    </tr>
                    <tr>
                        <td>{{ $document->company->address }}</td>
                    </tr>
                    <tr>
                        <td>{{ $document->company->zip }} {{ $document->company->town }}</td>
                    </tr>
                    <tr>
                        <td>CIF: {{ $document->company->vat }}</td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table>
                    <tr>
                        <td width="40%" class="right"><span class="mr-2 text-xl" style="color: rgb(20, 83, 136);">Nº DE FACTURA</span></td>
                        <td width="20%" class="right">{{ $document->number }}</td>
                    </tr>
                    <tr>
                        <td class="right"><span class="mr-2 text-xl" style="color: rgb(20, 83, 136);">FECHA</span></td>
                        <td class="right">{{ $document->invoice_date->format('d/m/Y') }}</td>
                    </tr>
                    <tr>
                        <td class="right"><span class="mr-2 text-xl" style="color: rgb(20, 83, 136);">Nº DE PEDIDO</span></td>
                        <td class="right">{{ $document->reference }}</td>
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
            <td>{{ $document->legal_form }}</td>
        </tr>
        <tr>
            <td>{{ $document->address }}</td>
        </tr>
        <tr>
            <td>{{ $document->zip }} {{ $document->town }}</td>
        </tr>
        <tr>
            <td>CIF: {{ $document->vat }}</td>
        </tr>
    </table>

    <div class="w-full mb-4" >
        <span class="w-full text-xl" style="color: rgb(20, 83, 136);">CONCEPTO</span>
        <div class="mb-4" style="border: 2px solid rgb(20, 83, 136); border-radius: 5px;">
            <table width="100%" >
                <tr>
                    <td class="h-60 p-4" style="vertical-align: top;">{!! $document->sales_invoices_products->first()->description !!}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="mb-4" style="border: 2px solid rgb(20, 83, 136); border-radius: 5px;">
        <table width="100%">
            <tr>
                <td width="55%"></td>
                <td width="25%" class="right"><span class="mr-2">Base imponible:</span></td>
                <td width="20%" class="right"><span>{{ number_format($document->base_line, 2, ',', '.') }}€</span></td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><span class="mr-2">IVA:</span></td>
                <td class="right"><span>{{ $document->sales_invoices_products->first()->tax_type*100 }}%</span></td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><span class="mr-2">Importe IVA:</span></td>
                <td class="right"><span>{{ number_format($document->tax_line, 2, ',', '.') }}€</span></td>
            </tr>
            <tr>
                <td></td>
                <td class="right"><span class="mr-2 text-2xl text-primary-500" style="color: rgb(20, 83, 136);">Total Factura:</span></td>
                <td class="right"><span class="text-2xl" style="color: rgb(20, 83, 136);">{{ number_format($document->total_line, 2, ',', '.') }}€</span></td>
            </tr>
        </table>
    </div>


    <htmlpagefooter name="page-footer">
    @php
        $retention = $document->tax_retention * $data['products_summary']['base_line'];
        $has_es = $data['products_summary']['es_line'] != 0 ? true : false;
        $taxes = $document->sales_invoices_products->groupBy('tax_type');
    @endphp
            <div class="document_div_totals">
                <div class="div-table">
                    <table class="table-totals table-bordered" style="page-break-inside: avoid ">
                        <thead>
                            <tr>
                                <th>{{ __('general.base') }}</th>
                                <th>% {{ __('general.vat') }}</th>
                                <th>{{ __('general.vat') }}</th>

                                @if ($has_es)
                                    <th>% {{ __('general.re') }}</th>
                                    <th>{{ __('general.re') }}</th>
                                @endif

                                <th>{{ __('general.total') }}</th>

                                @if ($document->tax_retention != 0)
                                    <th>% {{ __('general.retention') }}</th>
                                    <th>{{ __('general.retention') }}</th>
                                    <th>{{ __('general.net_total') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['products_summary']['tax_summary'] as $tax)
                                <tr>
                                    <td>{{ decimal2string($tax['base_line'], 'euro2') }}</td>

                                    <td>{{ decimal2string($tax['tax_rate'] ?? 0, 'percent2') }}</td>
                                    <td>{{ decimal2string($tax['tax_line'], 'euro2') }}</td>

                                    @if ($has_es)
                                        <td>{{ decimal2string($tax['es_rate'] ?? 0, 'percent2') }}</td>
                                        <td>{{ decimal2string($tax['es_line'], 'euro2') }}</td>
                                    @endif

                                    <td>{{ decimal2string($tax['total_line'], 'euro2') }}</td>
                                    @if ($document->tax_retention != 0)
                                        <td>{{ decimal2string($document->tax_retention,'percent2') }}</td>
                                        <td>{{ decimal2string($tax['base_line'] * $document->tax_retention, 'euro2') }}</td>
                                        <td>{{ decimal2string($tax['total_line'] - $tax['retention'], 'euro2') }}</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            @if (length($data['products_summary']['tax_summary']) > 1 )
                                <tr>
                                    <th>{{ decimal2string($data['products_summary']['base_line'], 'euro2') }}</th>
                                    <th> - </th>
                                    <th>{{ decimal2string($data['products_summary']['tax_line'], 'euro2') }}</th>
                                    @if ($has_es)
                                        <th> - </th>
                                        <th>{{ decimal2string($data['products_summary']['es_line'], 'euro2') }}</th>
                                    @endif
                                    <th>{{ decimal2string($data['products_summary']['total_line'], 'euro2') }}</th>
                                    @if ($document->tax_retention != 0)
                                        <th>{{ decimal2string($document->tax_retention, 'percent2') }}</th>
                                        <th>{{ decimal2string($data['products_summary']['base_line'] * $document->tax_retention, 'euro2') }}</th>
                                        <th>{{ decimal2string($data['products_summary']['total_line'] - $retention, 'euro2') }}</th>
                                    @endif
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </htmlpagefooter>
</body>
