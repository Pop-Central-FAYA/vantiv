@extends('layouts.new_app')

@section('title')
    <title>Campaigns</title>
@endsection


@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Campaigns</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Broadcaster</a></li>
                        <li><a href="#">Campaigns</a></li>
                    </ul>
                </div>

                <div class="col-12">

                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table id="example1" class="table table-bordered table-striped all_campaign">
                                        <thead>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Product</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Adslots</th>
                                        <th>Compliance</th>
                                        <th>Status</th>
                                        <th>Campaign Details</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane" id="nc"></div>

                            <div class="tab-pane" id="ne"></div>

                            <div class="tab-pane" id="nw"></div>

                            <div class="tab-pane" id="se"></div>

                            <div class="tab-pane" id="ss"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}

    <script>

        $(document).ready(function(){

            var Datefilter =  $('.all_campaign').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/campaign/all-campaign/data',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_hvc]').val();
                        d.stop_date = $('input[name=txtToDate_hvc]').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'brand', name: 'brand'},
                    {data: 'product', name: 'product'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'adslots', name: 'adslots'},
                    {data: 'compliance', name: 'compliance'},
                    {data: 'status', name: 'status'},
                    {data: 'details', name: 'details'}
                ]
            });

            $("#txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $("#txtToDate").datepicker({
                numberOfMonths: 2,
                onSelect: function(selected) {
                    $("#txtFromDate").datepicker("option","maxDate", selected)
                }
            });

        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop