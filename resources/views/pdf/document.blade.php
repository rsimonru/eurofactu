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
        font-size: 12px;
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

    .table-data thead tr th {
        font-size: 9px;
    }

    .table-data tbody tr td {
        font-size: 9px;
    }

    .table-totals thead tr {
        /*background-color: #ced423;*/
        background-color: #e7e7e7;
    }

    .table-totals thead tr th {
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

</style>

<body>
    <div class="container" style="padding-left: 10px;padding-right: 0">
        <div class="row" style="padding-left: 0;margin-left: 0;margin-right:20px;">
            <div class="col-xs-12 text-right">
                <div style="text-align: right; border-bottom: 1px solid #1e0999ff;">
                    <img src="{{ storage_path('app/private/'.$data['company']->logo) }}" width="180"
                         alt="logo_company" />
                </div>
            </div>
        </div>
        <div class="row" style="padding-left: 0;margin-left: 0">
            <div class="col-xs-10" style="padding-left: 0;margin-left: 0">
                <h4 style="margin-bottom: 1px;font-size: 18px;">
                    <strong>@yield('document_type')</strong> -@yield('document_number')
                </h4>
                @yield('document_data')
                <br>
                <div style="background-color: #1e0999ff;height: 7px;width: 150px;  margin-bottom:15px"></div>
            </div>
        </div>
        <div class="row" style="padding-left: 0;margin-left: 0">
            <div class="col-print-6" style="padding-left: 0;margin-left: 0">
                @yield('economics')
            </div>
            <div class="col-print-3">
                @yield('thirdparty_data')
            </div>
        </div>
        <div class="row" style="padding-left: 0;margin-top: 5px">
            <div class="col-xs-12">
                <br>
                <br>
                <h5>@yield('detail_of_document')</h5>
                @yield('table_data')
            </div>
        </div>
        @yield('footer_data')
        <sethtmlpagefooter name="myFooter" value="on" />
    </div>
</body>

</html>
