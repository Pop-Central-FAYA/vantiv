@extends('layouts.faya_app')
@section('extra-meta')
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
@stop
@section('title')
<title> FAYA | Invoice Details </title>
@stop

@section('content')
<!-- main container -->
<div class="main_contain">
    <!-- heaser -->

    <!-- subheader -->
    <div class="sub_header clearfix mb pt">
        <div class="column col_6">
            <h2 class="sub_header">INVOICE DETAILS</h2><br>
        </div>

    </div>

    <div class="the_frame clearfix mb">
        <div class="border_bottom clearfix client_name">
            <a href="" class=" block_disp left"></a>
            <div class="left">
                <h2 class='sub_header'>{{ $invoice_details['client_name'] }}</h2>
                <p class="small_faint">{{ $invoice_details['client_address'] }}</p>
            </div>

            <span class="client_ava right"><img src="{{ asset($invoice_details['client_logo']) }}" alt="coca cola"></span>
        </div>

        <div class="clearfix client_personal">
            <div class="column col_3">
                <span class="small_faint">Campaign Name</span>
                <p class='weight_medium'>{{ $invoice_details['campaign_name'] }}</p>
            </div>

            <div class="column col_3">
                <span class="small_faint">Duration</span>
                <p class='weight_medium'>{{ date('Y-m-d', strtotime($invoice_details['start_date'])) .' - '. date('Y-m-d', strtotime($invoice_details['end_date'])) }}</p>
            </div>

            <div class="column col_3">
                <span class="small_faint">Invoice Number</span>
                <p class='weight_medium'>{{ $invoice_details['invoice_number'] }}</p>
            </div>

            <div class="column col_3">
                <span class="small_faint">Date</span>
                <p class='weight_medium'>{{ $invoice_details['invoice_date'] }}</p>
            </div>
        </div>
    </div>

    <div class="the_frame client_dets mb4">

        <table class="invoice">
            <thead>
            <tr>
                <th>Publishers</th>
                <th>Details</th>
                <th>MPO</th>
                <th>INV</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Gross</th>
                <th>V.Disc %</th>
                <th>V.Disc Amnt</th>
                <th>AG. Comm %</th>
                <th>AG. Comm Amnt</th>
                <th>Others</th>
                <th>VAT</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($invoice_details['publishers_specific_details'] as $invoice_detail)
            <tr>
                <td>{{ $invoice_detail['publisher'] }}</td>
                <td>{{ $invoice_details['campaign_name'] }}</td>
                <td></td>
                <td></td>
                <td>{{ $invoice_detail['quantity'] }}</td>
                <td>{{ $invoice_detail['rate'] }}</td>
                <td>{{ $invoice_detail['gross'] }}</td>
                <td>{{ $invoice_detail['agency_percentage_discount'] }}</td>
                <td>{{ $invoice_detail['agency_discount_value'] }}</td>
                <td>{{ $invoice_detail['agency_commission_percentage'] }}</td>
                <td>{{ $invoice_detail['agency_commission_value'] }}</td>
                <td>{{ $invoice_detail['others'] }}</td>
                <td>{{ $invoice_detail['vat'] }}</td>
                <td>{{ $invoice_detail['total'] }}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>Sub Totals:</b></td>
                <td>{{ $invoice_details['total_gross'] }}</td>
                <td></td>
                <td>{{ $invoice_details['total_value_amount'] }}</td>
                <td></td>
                <td>{{ $invoice_details['agency_commission_value'] }}</td>
                <td></td>
                <td>{{ $invoice_details['total_vat'] }}</td>
                <td>{{ number_format($invoice_details['total_net'],2) }}</td>
            </tr>
            <tr>
                <td><b>NET</b></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><b>NGN</b></td>
                <td><b>{{ number_format($invoice_details['total_net'],2) }}</b></td>
            </tr>
            </tbody>
        </table>
        <div >
            <p>{{ ucfirst($invoice_details['total_net_word']).' Naira Only' }}</p>
        </div>
    </div>
    <!--  -->

</div>

@stop

@section('styles')
{{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>--}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
@stop

@section('scripts')
<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
{{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

@stop
