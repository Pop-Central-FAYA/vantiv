@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            All Campaigns

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> All Campaigns</a> </li>

        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>

            <div class="col-md-2 hidden-sm hidden-xs"></div>

            <div class="row" style="padding: 5%">
                <div class="col-xs-12">
                    <div class="col-md-11">
                        <div class="nav-tabs-custom">
                            <div class="tab-content">
                                <div class="active tab-pane" id="all">
                                    <!-- Post -->
                                    <!-- /.post -->
                                    <div class="box-body">
                                        <table id="example1" class="table table-bordered table-striped all_campaign">
                                            <thead>
                                                <th>S/N</th>
                                                <th>Name</th>
                                                <th>Brand</th>
                                                <th>Product</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Adslots</th>
                                                <th>Compliance</th>
                                                <th>Status</th>
                                            </thead>
                                        </table>
                                        {{--{!! with(new Vanguard\Pagination\HDPresenter($adslot))->render() !!}--}}
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="nc">

                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="ne">

                                </div>

                                <div class="tab-pane" id="nw">

                                </div>

                                <div class="tab-pane" id="se">

                                </div>

                                <div class="tab-pane" id="ss">

                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div>
                        <!-- /.nav-tabs-custom -->
                    </div>
                    <!-- /.col -->
                </div>
        </div>
    </section>

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