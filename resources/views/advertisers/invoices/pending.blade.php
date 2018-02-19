@extends('layouts.new_app')

@section('title')
    <title>Advertiser | Pending Invoice</title>
@stop

@section('styles')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Pending Invoice</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Invoice Management</a></li>
                        <li><a href="#">View All Pending Invoices</a></li>
                    </ul>
                </div>
                <div class="col-12 invoice-Management">
                    @if(count($pending_invoices) === 0)

                        <h4>You have no invoices at this moment</h4>

                    @else
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Campaign Name</th>
                                <th>Brand</th>
                                <th>Actual Amount Paid</th>
                                <th>Refunded Amount</th>
                                <th>Status</th>
                                {{--<th>View/Approve</th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($pending_invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice['invoice_number'] }}</td>
                                    <td>{{ $invoice['campaign_name'] }}</td>
                                    <td>{{ $invoice['campaign_brand'] }}</td>
                                    <td>&#8358;{{ $invoice['actual_amount_paid'] }}</td>
                                    <td>&#8358;{{ $invoice['refunded_amount'] }}</td>
                                    <td>
                                        @if ($invoice['status'] == 1)
                                            <label style="font-size: 16px" class="label label-success">
                                                Approved
                                            </label>
                                        @elseif ($invoice['status'] == 0)
                                            <label style="font-size: 16px" class="label label-warning">
                                                Pending
                                            </label>
                                        @endif
                                    </td>
                                    {{--<td>--}}
                                        {{--<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#myModal{{ $invoice['invoice_number'] }}" > View</button>--}}
                                    {{--</td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}
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
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script>
        $(function () {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });


    </script>


@stop

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection