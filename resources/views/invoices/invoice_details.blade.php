@extends('layouts.faya_app')

@section('title')
    <title> FAYA | Invoice Details </title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.agency.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">INVOICE DETAILS</h2><br>
                <a href="{{ route('invoice.export', ['id' => $invoice_details['invoice_id']]) }}" class="btn small_btn uppercased ">Export To PDF <span class=""></span></a>
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
                <p>{{ ucfirst($invoice_details['total_net_word']) }}</p>
            </div>
        </div>
        <!--  -->

    </div>

@stop


