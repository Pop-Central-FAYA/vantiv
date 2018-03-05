@extends('layouts.new_app')

@section('title')
    <title>Reports</title>
@endsection

@section('content')

<div class="main-section">
    <div class="container">
        <div class="row">
            <div class="col-12 heading-main">
                <h1>Broadcaster Report</h1>
                <ul>
                    <li><a href="#"><i class="fa fa-edit"></i>Broadcaster</a></li>
                    <li><a href="#">Reports</a></li>
                </ul>
            </div>

            <div class="row">
                <div class="row" style="padding: 5%">
                    <div class="col-md-12">

                        <div class="col-md-12">
                            <div class="nav-tabs-custom">
                                <ul class="nav nav-tabs" style="background:#eee">
                                    <li class="active"><a href="#periodic_sales_report" data-toggle="tab">Periodic Sales Report</a></li>
                                    <li><a href="#inventory_fill_rate" data-toggle="tab">Inventory Fill Rate</a></li>
                                    <li><a href="#high_performing_dayparts" data-toggle="tab">High Performing Dayparts</a></li>
                                    <li><a href="#high_performing_days" data-toggle="tab">High Performing Days</a></li>
                                    <li><a href="#high_value_customer" data-toggle="tab">High Value Customer</a></li>
                                    <li><a href="#make_good_report" data-toggle="tab">Make Good Report</a></li>
                                    <li><a href="#total_volume_of_campaigns" data-toggle="tab">Total Volume of Capaigns</a></li>
                                    <li><a href="#paid_invoices" data-toggle="tab">Paid Invoices</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div class="tab-pane active" id="periodic_sales_report">
                                        <div class="row">
                                            <form action="#" method="GET" class="form-inline" style="text-align: center;margin: 20px auto;">
                                                {{ csrf_field() }}
                                                <h4 style="margin-bottom: 10px; font-weight: bold">Search by date</h4>
                                                <div class="col-md-10" style="margin-top: 10px">
                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtFromDate_ps" placeholder="Start Date" class="form-control pull-right txtFromDate" id="txtFromDate_ps">
                                                    </div>

                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtToDate_ps" placeholder="End Date" class="form-control pull-right txtToDate" id="txtToDate_ps" >
                                                    </div>
                                                    <div class="input-group" style="">
                                                        <input type="button" class="btn btn-primary search-btn" id="button_ps" value="Apply" style="float:left" >
                                                    </div>
                                                </div>
                                            </form>
                                            <hr>
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-body">
                                                        <div class="table-responsive">
                                                            <table id="ps-table" class="table table-bordered ps-table">
                                                                <thead>
                                                                <th>Id</th>
                                                                <th>Buyer</th>
                                                                <th>Campaign Name</th>
                                                                <th>Brand</th>
                                                                <th>Number of Slots</th>
                                                                <th>Total Amount/Revenue (&#8358;)</th>
                                                                <th>Date</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="inventory_fill_rate">

                                    </div>

                                    <div class="tab-pane" id="high_performing_dayparts">
                                        <div class="row">
                                            <form action="" method="GET">
                                                {{ csrf_field() }}
                                                <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                                                <div class="col-md-10" style="margin-top: -2%">
                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtFromDate_hpd" placeholder="Start Date" class="form-control pull-right txtFromDate" id="txtFromDate_hpd">
                                                    </div>

                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtToDate_hpd" placeholder="End Date" class="form-control pull-right txtToDate" id="txtToDate_hpd" >
                                                    </div>
                                                    <div class="input-group" style="">
                                                        <input type="button" class="search-btn" id="button_hpd" value="Apply" style="float:left" >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box">
                                                    <div class="box-body">
                                                        <div class="table-responsive">
                                                            <table id="hpd-table" class="table table-bordered">
                                                                <thead>
                                                                <th>Id</th>
                                                                <th>Dayparts</th>
                                                                <th>Percentage Fill Rate</th>
                                                                <th>Date</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="high_performing_days">
                                        <div class="row">
                                            <form action="" method="GET">
                                                {{ csrf_field() }}
                                                <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                                                <div class="col-md-10" style="margin-top: -2%">
                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtFromDate_hpdays" placeholder="Start Date" class="form-control pull-right txtFromDate" id="txtFromDate_hpdays">
                                                    </div>

                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtToDate_hpdays" placeholder="End Date" class="form-control pull-right txtToDate" id="txtToDate_hpdays" >
                                                    </div>
                                                    <div class="input-group" style="">
                                                        <input type="button" class="search-btn" id="button_hpdays" value="Apply" style="float:left" >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box">
                                                    <div class="box-body">
                                                        <div class="table-responsive">
                                                            <table id="hpdays-table" class="table table-bordered">
                                                                <thead>
                                                                <th>Id</th>
                                                                <th>Days</th>
                                                                <th>Percentage Fill Rate</th>
                                                                <th>Week</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="high_value_customer">
                                        <div class="row">
                                            <div class="row">
                                                <form action="#" id="formHVC" method="GET">
                                                    {{ csrf_field() }}
                                                    <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                                                    <div class="col-md-10" style="margin-top: -2%">
                                                        <div class="input-group date styledate" style="width:30% !important">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" name="txtFromDate_hvc" placeholder="Start Date" class="form-control pull-right txtFromDate" id="txtFromDate_hvc">
                                                        </div>

                                                        <div class="input-group date styledate" style="width:30% !important">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" name="txtToDate_hvc" placeholder="End Date" class="form-control pull-right txtToDate" id="txtToDate_hvc" >
                                                        </div>
                                                        <div class="input-group" style="">
                                                            <input type="button" class="search-btn" id="button_hvc" value="Apply" style="float:left" >
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <hr>
                                            <div class="col-xs-12">
                                                <div class="box">
                                                    <div class="box-body">
                                                        <div class="table-responsive">
                                                            <table id="hvc-table" class="table table-bordered hvc-table">
                                                                <thead>
                                                                <th>Id</th>
                                                                <th>Customer Name</th>
                                                                <th>Number of Campaigns</th>
                                                                <th>Number of Adslots</th>
                                                                <th>Total Amount/Revenue (&#8358;)</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="make_good_report">

                                    </div>

                                    <div class="tab-pane" id="total_volume_of_campaigns">
                                        <div class="row">
                                            <div class="row">
                                                <form action="#" method="GET">
                                                    {{ csrf_field() }}
                                                    <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                                                    <div class="col-md-10" style="margin-top: -2%">
                                                        <div class="input-group date styledate" style="width:30% !important">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" name="txtFromDate_tvc" placeholder="Start Date" class="form-control pull-right txtFromDate" id="txtFromDate_tvc">
                                                        </div>

                                                        <div class="input-group date styledate" style="width:30% !important">
                                                            <div class="input-group-addon">
                                                                <i class="fa fa-calendar"></i>
                                                            </div>
                                                            <input type="text" name="txtToDate_tvc" placeholder="End Date" class="form-control pull-right txtToDate" id="txtToDate_tvc" >
                                                        </div>
                                                        <div class="input-group" style="">
                                                            <input type="button" class="search-btn" id="button_tvc" value="Apply" style="float:left" >
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <hr>
                                            <div class="col-md-12">
                                                <div class="box">
                                                    <div class="box-body">
                                                        <div class="table-responsive">
                                                            <table id="tvc-table" class="table table-bordered tvc-table">
                                                                <thead>
                                                                <th>Id</th>
                                                                <th>Volume of Campaigns</th>
                                                                <th>Date</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="paid_invoices">
                                        <div class="row">
                                            <form action="" method="GET">
                                                {{ csrf_field() }}
                                                <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                                                <div class="col-md-10" style="margin-top: -2%">
                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtFromDate_pi" placeholder="Start Date" class="form-control pull-right txtFromDate" id="txtFromDate_pi">
                                                    </div>

                                                    <div class="input-group date styledate" style="width:30% !important">
                                                        <div class="input-group-addon">
                                                            <i class="fa fa-calendar"></i>
                                                        </div>
                                                        <input type="text" name="txtToDate_pi" placeholder="End Date" class="form-control pull-right txtToDate" id="txtToDate_pi" >
                                                    </div>
                                                    <div class="input-group" style="">
                                                        <input type="button" class="search-btn" id="button_pi" value="Apply" style="float:left" >
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="box">
                                                    <div class="box-body">
                                                        <div class="table-responsive">
                                                            <table id="pi-table" class="table table-bordered">
                                                                <thead>
                                                                <th>Id</th>
                                                                <th>Campaign Name</th>
                                                                <th>Customer</th>
                                                                <th>Date</th>
                                                                <th>Due Date</th>
                                                                </thead>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@stop

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}

    <script>

        $(document).ready(function(){

             var Datefilter =  $('.hvc-table').DataTable({
                    paging: true,
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: '/reports/total-volume-campaigns/all-data/',
                        data: function (d) {
                            d.start_date = $('input[name=txtFromDate_hvc]').val();
                            d.stop_date = $('input[name=txtToDate_hvc]').val();
                        }
                    },
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'customer_name', name: 'customer_name'},
                        {data: 'number_of_campaign', name: 'number_of_campaign'},
                        {data: 'total_adslot', name: 'total_adslot'},
                        {data: 'payment', name: 'payment'},
                    ]
                });

             var DateFilterPI =  $('#pi-table').DataTable({
                    paging: true,
                    serverSide: true,
                    processing: true,
                    ajax: {
                        url: '/reports/paid-invoices/all-data',
                        data: function (d) {
                            d.start_date = $('input[name=txtFromDate_pi]').val();
                            d.stop_date = $('input[name=txtToDate_pi]').val();
                        }
                    },
                    columns: [
                        {data: 'id', name: 'id'},
                        {data: 'campaign_name', name: 'campaign_name'},
                        {data: 'customer', name: 'customer'},
                        {data: 'date', name: 'date'},
                        {data: 'date_due', name: 'date_due'},
                    ]
                });

             var DateFilterPS = $('#ps-table').DataTable({
                 paging: true,
                 serverSide: true,
                 processing: true,
                 ajax: {
                     url: '/reports/periodic-sales/all',
                     data: function (d) {
                         d.start_date = $('input[name=txtFromDate_ps]').val();
                         d.stop_date = $('input[name=txtToDate_ps]').val();
                     }
                 },
                 columns: [
                     {data: 'id', name: 'id'},
                     {data: 'buyer', name: 'buyer'},
                     {data: 'campaign_name', name: 'campaign_name'},
                     {data: 'brand', name: 'brand'},
                     {data: 'adslot', name: 'adslot'},
                     {data: 'total_amount', name: 'total_amount'},
                     {data: 'date', name: 'date'},
                 ]
             });

             var DateFilterTvc = $('#tvc-table').DataTable({
                 paging: true,
                 serverSide: true,
                 processing: true,
                 ajax: {
                     url: '/reports/total-volume-of-campaign/all',
                     data: function (d) {
                         d.start_date = $('input[name=txtFromDate_tvc]').val();
                         d.stop_date = $('input[name=txtToDate_tvc]').val();
                     }
                 },
                 columns: [
                     {data: 'id', name: 'id'},
                     {data: 'volume', name: 'volume'},
                     {data: 'date', name: 'date'},
                 ]
             });

            var DateFilterHpd = $('#hpd-table').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/reports/high-day-parts/all',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_hpd]').val();
                        d.stop_date = $('input[name=txtToDate_hpd]').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'daypart', name: 'daypart'},
                    {data: 'percentage', name: 'percentage'},
                    {data: 'date', name: 'date'},
                ]
            });

            var DateFilterHpdays = $('#hpdays-table').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/reports/high-days/all',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_hpdays]').val();
                        d.stop_date = $('input[name=txtToDate_hpdays]').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'day', name: 'day'},
                    {data: 'percentage', name: 'percentage'},
                    {data: 'week', name: 'week'},
                ]
            });

            $(".txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $(".txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $(".txtToDate").datepicker({
                numberOfMonths: 2,
                onSelect: function(selected) {
                    $(".txtFromDate").datepicker("option","maxDate", selected)
                }
            });


            $('#button_hvc').on('click', function() {
                Datefilter.draw();
            });

            $('#button_pi').on('click', function() {
                DateFilterPI.draw();
            });

            $('#button_ps').on('click', function() {
                DateFilterPS.draw();
            });

            $('#button_tvc').on('click', function() {
                DateFilterTvc.draw();
            });

            $('#button_hpd').on('click', function() {
                DateFilterHpd.draw();
            });

            $('#button_hpdays').on('click', function() {
                DateFilterHpdays.draw();
            });

        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop