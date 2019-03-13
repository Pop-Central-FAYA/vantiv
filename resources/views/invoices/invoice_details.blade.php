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
    <style>
        #DataTables_Table_0_wrapper .dt-buttons button {
            line-height: 2.5;
            color: #fff;
            cursor: pointer;
            background: #44C1C9;
            -webkit-appearance: none;
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            border: 0;
            padding: 3px 20px 0;
            font-size: 14px;

            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;

            -webkit-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
            -moz-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
            box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);

            position: relative;
            display: inline-block;
            text-transform: uppercase;
        !important;
        }
    </style>
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

    <script>
        //datatables
        /*$(document).ready(function( $ ) {

            $("body").delegate(".modal_invoice_click", "click", function() {
                var href = $(this).attr("href");
                $(href).modal();
                return false;
            });

            var Datefilter =  $('.invoice').DataTable({
                dom: 'Bfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                "searching": false,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    url: '/agency/invoices/data',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_ps]').val();
                        d.stop_date = $('input[name=txtToDate_ps]').val();
                    }
                },
                columns: [
                    {data: 'invoice_number', name: 'invoice_number'},
                    {data: 'campaign_name', name: 'campaign_name'},
                    {data: 'name', name: 'name'},
                    {data: 'date', name: 'date'},
                    {data: 'actual_amount_paid', name: 'actual_amount_paid'},
                    {data: 'status', name: 'status'}
                ],
                "createdRow": function( row, data, dataIndex ) {

                    // Add a class to the cell in the second column
                    $(row).children(':nth-child(4)').addClass('weight_medium');

                    // Add a class to the row
                    $(row).addClass('important');
                }
            });

            $('#button_ps').on('click', function() {
                Datefilter.draw();
            });
        } );*/
    </script>
@stop
