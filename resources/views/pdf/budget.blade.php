@extends('pdf.document')

@section('title')
    {{ $document->number }}
@endsection

@section('document_type')
    {{ trans_choice('sales.budgets', 1) }}
@endsection

@section('detail_of_document')
    {{ __('sales.detail_of_budget') }}
@endsection

@section('document_number')
    {{ $document->number }}
@endsection

@section('document_data')
    <div class="span4">Fecha: {{ $document->created_at->format('d-m-Y') }}</div>
    @if (!empty($document->valid_until))
        <div class="span4">{{ __('sales.valid_until') }}: {{ $document->valid_until->format('d-m-Y') }}</div>
    @endif

    @if (!empty($document->reference))
        <div class="span4">{{ __('sales.reference') }}: {{ $document->reference }}</div>
    @endif

    @if (!empty($document->observations))
        <div class="span4">{{ __('sales.observations') }}: {{ $document->observations }}</div>
    @endif
@endsection

@section('economics')
    <h4 style="font-size: 12px;font-weight: bold">Condiciones Ecónomicas</h4>
    <div class="span4" style="font-size: 11px"><strong>Forma de pago:</strong> Datos de forma de pago</div>
@endsection

@section('thirdparty_data')
    <h4 style="font-size: 12px;font-weight: bold">{{ __('sales.customer') }}</h4>
    <div class="span4" style="font-size: 11px"><strong>{{ $document->thirdparty->legal_form }}</strong></div>
    <div class="span4" style="font-size: 11px">{{ $document->thirdparty->vat }}</div>
    <div class="span4" style="font-size: 11px">{{ $document->thirdparty->address }}</div>
    <div class="span4" style="font-size: 11px">{{ $document->thirdparty->town }} - {{ $document->thirdparty->zip }}</div>
    <div class="span4" style="font-size: 11px">{{ $document->thirdparty->province }}</div>
@endsection

@section('table_data')
    <table style="width:100%" class="table-striped table-bordered">
        <thead>
            <tr style="background-color: #e7e7e7">
                <th style="height:20px;font-size: 11px;padding-left: 5px;text-align: center;width:14%;">Código</th>
                <th style="width:39%;text-align: center;">Descripción</th>
                <th style="width:10%;text-align: center;">Unid.</th>
                <th style="width:10%;text-align: center;">P/U</th>
                <th style="width:10%;text-align: center;">Desc.</th>
                <th style="width:10%;text-align: center;">Total/U</th>
                <th style="width:10%;text-align: center;">Total</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($document->products as $line)
                <tr>
                    <td style="height:20px;font-size: 11px;padding-left: 5px">
                        {{ $line->sales_budget_id }}
                    </td>
                    <td style="height:20px;font-size: 11px;padding-left: 5px">
                        {{ $line->description }}
                    </td>
                    <td style="height:20px;font-size: 11px;padding-left: 5px;text-align: center">
                        {{ decimal2string($line->units,'number0') }}
                    </td>
                    <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                        {{ decimal2string($line->base_unit) }}
                    </td>
                    <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                        @if ($line->discount_type == 'P')
                            {{ decimal2string(100 * $line->discountp,'percent2') }}
                        @else
                            {{ decimal2string($line->discounti) }}
                        @endif
                    </td>
                    <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                        {{ decimal2string($line->base_result) }}
                    </td>
                    <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                        {{ decimal2string($line->base_line) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection


@section('footer_data')
    @php
        $retention = $document->tax_retention * $data['products_summary']['base_line'];
        $has_es = $data['products_summary']['es_line'] != 0 ? true : false;
        $taxes = $document->products->groupBy('tax_type');
    @endphp
    <htmlpagefooter name="myFooter">
        <div class="document_div_totals">
            <div class="div-table">
                <table class="table-totals table-bordered" style="page-break-inside: avoid ">
                    <thead>
                        <tr>
                            <th>{{ __('general.base') }}</th>
                            @if ($document->tax_retention != 0)
                                <th>% {{ __('general.retention') }}</th>
                                <th>{{ __('general.retention') }}</th>
                            @endif

                            <th>% {{ __('general.vat') }}</th>
                            <th>{{ __('general.vat') }}</th>

                            @if ($has_es)
                                <th>% {{ __('general.re') }}</th>
                                <th>{{ __('general.re') }}</th>
                            @endif

                            <th>{{ __('general.total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data['products_summary']['tax_summary'] as $tax)
                            <tr>
                                <td>{{ decimal2string($tax['base_line'], 'euro2') }}</td>
                                @if ($document->tax_retention != 0)
                                    <td>{{ decimal2string($document->tax_retention,'percent2') }}</td>
                                    <td>{{ decimal2string($tax['base_line'] * $document->tax_retention, 'euro2') }}</td>
                                @endif

                                <td>{{ decimal2string($tax['tax_rate'] ?? 0, 'percent2') }}</td>
                                <td>{{ decimal2string($tax['tax_line'], 'euro2') }}</td>

                                @if ($has_es)
                                    <td>{{ decimal2string($tax['es_rate'] ?? 0, 'percent2') }}</td>
                                    <td>{{ decimal2string($tax['es_line'], 'euro2') }}</td>
                                @endif

                                <td>{{ decimal2string($tax['total_line'] - $tax['retention'], 'euro2') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        @if (length($data['products_summary']['tax_summary']) > 1 )
                            <tr>
                                <th>{{ decimal2string($data['products_summary']['base_line'], 'euro2') }}</th>
                                @if ($document->tax_retention != 0)
                                    <th>{{ decimal2string($document->tax_retention, 'percent2') }}</th>
                                    <th>{{ decimal2string($data['products_summary']['base_line'] * $document->tax_retention, 'euro2') }}</th>
                                @endif
                                <th> - </th>
                                <th>{{ decimal2string($data['products_summary']['tax_line'], 'euro2') }}</th>
                                @if ($has_es)
                                    <th> - </th>
                                    <th>{{ decimal2string($data['products_summary']['es_line'], 'euro2') }}</th>
                                @endif
                                <th>{{ decimal2string($data['products_summary']['total_line'] - $retention, 'euro2') }}</th>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            {{-- @section('footer_company_data') --}}
                <div class="company-information">
                    <strong>{{ $data['company']->legal_form }}</strong>&nbsp;&nbsp;{{ $data['company']->address }}&nbsp;/&nbsp;{{ $data['company']->zip }}&nbsp;&nbsp;{{ $data['company']->town }}&nbsp;-&nbsp;{{ $data['company']->province }}<br>
                    {{ $data['company']->vat }}<br>
                    {{ $data['company']->register }}
                </div>
                <div style="width: 100%;margin-top: 10px;font-size: 9px;" class="text-center vertical-text">
                    Info adicional.
                </div>
            {{-- @endsection --}}

        </div>
    </htmlpagefooter>
@endsection
