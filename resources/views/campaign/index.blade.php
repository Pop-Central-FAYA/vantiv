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
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form action="#" method="GET" class="form-inline" style="text-align: center;margin: 20px auto;">
                        {{ csrf_field() }}
                        {{--<h4 style="margin-bottom: 10px; font-weight: bold">Search by date</h4>--}}
                        <div class="col-md-10" style="margin-top: 10px">
                            <div class="input-group date styledate" style="width:30% !important">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="txtFromDate_ps" placeholder="Start Date" class="flatpickr form-control pull-right txtFromDate " id="txtFromDate_ps">
                            </div>

                            <div class="input-group date styledate" style="width:30% !important">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" name="txtToDate_ps" placeholder="End Date" class="flatpickr form-control pull-right txtToDate" id="txtToDate_ps" >
                            </div>
                            <div class="input-group" style="">
                                <button type="button" class="btn btn-primary search-btn" style="float: left" id="button_ps">Apply</button>
                                {{--<input type="button" class="btn btn-primary search-btn" id="button_ps" value="Apply" style="float:left" >--}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <p><br></p>
            <div class="row">
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
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

    <script src="https://unpkg.com/flatpickr"></script>

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}

    <script>

        $(document).ready(function(){

            flatpickr(".flatpickr", {
                altInput: true,
            });

            var Datefilter =  $('.all_campaign').DataTable({
                dom: 'Bfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    url: '/campaign/all-campaign/data',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_ps]').val();
                        d.stop_date = $('input[name=txtToDate_ps]').val();
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

            $('#button_ps').on('click', function() {
                Datefilter.draw();
            });

        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
@stop