@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Invoices </title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Invoices</h2>
            </div>
        </div>


        <div class="the_frame client_dets mb4">

            <table class="invoice">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Campaign Name</th>
                        <th>Client</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>View</th>
                    </tr>
                </thead>

            </table>
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
        $(document).ready(function( $ ) {

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
                    url: '/invoices/data',
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
                    {data: 'status', name: 'status'},
                    {data: 'view', name: 'view'}
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
        } );
    </script>
@stop
