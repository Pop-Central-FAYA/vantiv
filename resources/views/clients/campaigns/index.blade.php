@extends('layouts.new_app')

@section('title')
    <title>Clients - Campaign-Lists</title>
@stop

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Campaign</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>All Campaign</a></li>
                        <li><a href="#">Campaign List</a></li>
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
                <div class="col-12 Campaign-List">
                    <table class="client_campaign_all table">
                        <thead class="thead-dark">
                        <th>No</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Product</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Campaign Details</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>


@stop
@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('agency_asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('agency_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('agency_asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('agency_asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('agency_asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="{{ asset('agency_asset/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('agency_asset/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('agency_asset/plugins/fastclick/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('agency_asset/dist/js/app.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>

    <script src="https://unpkg.com/flatpickr"></script>

    <script>
        $(document).ready(function () {

            flatpickr(".flatpickr", {
                altInput: true,
            });

            var Datefilter =  $('.client_campaign_all').DataTable({
                dom: 'Bfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    url: '/clients/campaigns-data',
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
                    {data: 'amount', name: 'amount'},
                    {data: 'status', name: 'status'},
                    {data: 'details', name: 'details'},
                ]
            });

            $('#button_ps').on('click', function() {
                Datefilter.draw();
            });
        })
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
@stop