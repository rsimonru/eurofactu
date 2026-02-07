@extends('pdf.document')

@section('title')
    {{ $document->number }}
@endsection

@section('document_type'){{ trans_choice('sales.notes', 1) }}@endsection

@section('detail_of_document')
    {{ __('sales.detail_of_note') }}
@endsection

@section('document_number')
    {{ $document->number }}
@endsection

@section('document_data')
    @if (!empty($document->date))
        <div><strong>{{ __('general.date') }}:</strong> {{ $document->date->format('d-m-Y') }}</div>
    @endif

    @if (!empty($document->reference))
        <div class="text-medium"><strong>{{ __('sales.reference') }}:</strong> {{ $document->reference }}</div>
    @endif

    @if (!empty($document->observations))
        <div class="text-medium"><strong>{{ __('sales.observations') }}:</strong> {{ $document->observations }}</div>
    @endif
@endsection

@section('economics')
    <div>
        @if (!empty($document->expiration_date))
        <div class="text-medium"><strong>{{ __('sales.expiration_date') }}:</strong> {{ $document->expiration_date->format('d-m-Y') }}</div><br>
        @endif
        <div class="text-medium"><strong>{{ __('sales.payment_method') }}:</strong> Transferencia<br><br>{{ $data['company']->banks_account->iban }}</div>
    </div>
@endsection

@section('thirdparty_data')
    <div class="text-medium">
        <h2 style="font-size: 14px; padding-bottom:10px;">{{ __('sales.customer') }}</h2><br>
        <div><strong>{{ $document->thirdparty->legal_form }}</strong></div>
        <div>{{ $document->thirdparty->vat }}</div>
        <div>{{ $document->thirdparty->address }}</div>
        <div>{{ $document->thirdparty->zip }} - {{ $document->thirdparty->town }}</div>
        <div>{{ $document->thirdparty->province }}</div>
    </div>
@endsection

@section('table_data')
    <table style="width:100%" class="table-data table-striped">
        <thead>
            <tr style="background-color: #e7e7e7">
                <th style="height:20px;font-size: 11px;padding-left: 5px;text-align: center;width:14%;">{{ __('general.code') }}</th>
                <th style="width:39%;text-align: center;">{{ __('sales.description') }}</th>
                <th style="width:10%;text-align: center;">{{ __('sales.short_units') }}</th>
                <th style="width:10%;text-align: center;">{{ __('general.price') }}</th>
                <th style="width:10%;text-align: center;">{{ __('sales.discount') }}</th>
                <th style="width:10%;text-align: center;">{{ __('general.net') }}</th>
                <th style="width:10%;text-align: center;">{{ __('sales.total') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($document->products as $line)
                <tr>
                    <td style="height:20px;font-size: 11px;padding-left: 5px">
                        {{ $line->units != 0 ? $line->sales_orders_product->product_variant?->product?->reference : '***' }}
                    </td>
                    <td style="height:20px;font-size: 11px;padding-left: 5px" colspan="{{ $line->units == 0 ? '5' : '1' }}" >
                        {{ $line->description }}
                    </td>
                    @if($line->units != 0)
                        <td style="height:20px;font-size: 11px;padding-left: 5px;text-align: center">
                            {{ decimal2string($line->units,'number0') }}
                        </td>
                        <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                            {{ decimal2string($line->sales_orders_product->base_unit) }}
                        </td>
                        <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                            @if ($line->discount_type == 'P')
                                {{ decimal2string($line->sales_orders_product->discountp,'percent2') }}
                            @else
                                {{ decimal2string($line->sales_orders_product->discounti) }}
                            @endif
                        </td>
                        <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                            {{ decimal2string($line->sales_orders_product->base_result) }}
                        </td>
                    @endif
                    <td style="height:20px;font-size: 11px;padding-right: 5px;text-align: right">
                        {{ $line->units != 0 ? decimal2string($line->units * $line->sales_orders_product->base_result) : '***' }}
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
    @endphp
    <htmlpagefooter name="myFooter">
        <div class="document_div_totals">
            <div class="div-table">
                <table class="table-totals table-bordered" style="page-break-inside: avoid ">
                    <thead>
                        <tr class="table-bordered">
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

            <div class="company-information">
                <strong>{{ $data['company']->legal_form }}</strong>&nbsp;##&nbsp;{{ $data['company']->address }}&nbsp;##&nbsp;{{ $data['company']->zip }}&nbsp;&nbsp;{{ $data['company']->town }}&nbsp;##&nbsp;{{ $data['company']->province }} &nbsp;##&nbsp; {{ $data['company']->vat }}<br>
                {{ $data['company']->register }}
            </div>
            <div style="width: 100%;margin-top: 10px;font-size: 9px;" class="text-center vertical-text">

            </div>

        </div>
    </htmlpagefooter>
@endsection
