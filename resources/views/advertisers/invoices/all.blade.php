@extends('advertiser_layouts.app')

@section('title')
    <title>Advertiser | Invoice</title>
@stop

@section('content')
<section class="content-header">
    <h1>
        All Invoices <small>All Invoices</small>
    </h1>

    <ol class="breadcrumb" style="font-size: 16px">
        <li><a href="#"><i class="fa fa-edit"></i> Invoices Management</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> View All Invoices</a> </li>
    </ol>
</section>

<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-xs-12">

            <div class="col-md-6" style="margin-bottom: 10px;"></div>

            <div class="col-md-12">

                <div class="box-body">

                    @if(count($all_invoices) === 0)

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
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($all_invoices as $invoice)
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
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    @endif

                </div>
            </div>
        </div>
    </div>

</section>

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