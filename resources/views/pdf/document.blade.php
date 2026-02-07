<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
    body {
        font-size: 14px;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    .document_div_totals {
        width: 100%;
    }

    .table-totals {
        width: 100%;
    }

    .div-table {
        width: 100%;
    }

    .table-bordered {
        border: 1px solid #000;
    }

    .table-data thead tr th {
        font-size: 12px;
    }

    .table-data tbody tr td {
        font-size: 11px;
    }

    .table-totals thead tr {
        /*background-color: #ced423;*/
        background-color: #e7e7e7;
    }

    .table-totals thead tr th {
        border-bottom: 1px solid #000;
        padding-top: 3px;
        padding-bottom: 3px;
        text-align: center;
        font-size: 11px;
    }

    .table-totals tbody tr td {
        padding-top: 3px;
        padding-bottom: 3px;
        padding-right: 5px;
        text-align: right;
    }

    .table-totals tfoot tr {
        /* background-color: #ced423; */
        background-color: #aaa7a7ff;
    }
    .table-totals tfoot tr th {
        padding-top: 3px;
        padding-bottom: 3px;
        padding-right: 5px;
        text-align: right;
        font-size: 11px;
    }

    .company-information {
        width: 100%;
        margin-top: 20px;
        font-size: 10px;
        text-align: left;
    }

    .address_company_logo {
        font-size: 9px;
        margin-bottom: 0px;
        padding-bottom: 0px;
        margin-top: 0px;
        padding-top: 0px;
    }

    .text-small {
        font-size: 10px;
    }
    .text-medium {
        font-size: 12px;
    }

    .col-print-1 {
        width: 8%;
        float: left;
    }

    .col-print-2 {
        width: 16%;
        float: left;
    }

    .col-print-3 {
        width: 25%;
        float: left;
    }

    .col-print-4 {
        width: 33%;
        float: left;
    }

    .col-print-5 {
        width: 42%;
        float: left;
    }

    .col-print-6 {
        width: 50%;
        float: left;
    }

    .col-print-7 {
        width: 58%;
        float: left;
    }

    .col-print-8 {
        width: 66%;
        float: left;
    }

    .col-print-9 {
        width: 75%;
        float: left;
    }

    .col-print-10 {
        width: 83%;
        float: left;
    }

    .col-print-11 {
        width: 92%;
        float: left;
    }

    .col-print-12 {
        width: 100%;
        float: left;
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

<body>
    <div class="container" style="padding-left: 10px;padding-right: 0">
        <htmlpageheader name="myHeader">
            <!-- <table style="width:100%; border-bottom: 1px solid #000; padding-bottom:10px; margin-bottom:10px"> -->
            <table style="width:100%; padding-bottom:10px; margin-bottom:10px">
                <tr>
                    <td style="text-align: left;font-size: 18px">
                        <strong>@yield('document_type'): @yield('document_number')</strong>
                    </td>
                    <td style="text-align: right;">
                        <img src="{{ storage_path('app/private/'.$data['company']->logo) }}" width="180"
                            alt="logo_company" />
                    </td>
                </tr>
            </table>
        </htmlpageheader>
        <sethtmlpageheader name="myHeader" value="on" show-this-page="1"/>

        <table style="width:100%; padding-bottom:10px; margin-bottom:10px">
            <tr>
                <td width="35%" style="text-align: left; padding-top:40px; vertical-align: top;">
                    @yield('document_data')
                    <br>
                    @yield('economics')
                </td>
                @if(!empty(trim($__env->yieldContent('verifactu_data'))))
                <td width="32%" style="text-align: center;">
                    @yield('verifactu_data')
                </td>
                @endif
                <td width="33%" style="text-align: left; padding-top:40px; vertical-align: top;">
                    @yield('thirdparty_data')
                </td>
            </tr>
        </table>

        <div class="row" style="padding-left: 0;margin-top: 25px">
            <div>
                <h5>@yield('detail_of_document')</h5>
                @yield('table_data')
            </div>
        </div>
        @yield('footer_data')
        <sethtmlpagefooter name="myFooter" value="on" />
    </div>
</body>

</html>
