@extends('layouts.app')

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

@section('title', trans('app.view-all-invoices'))

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
                                        <td>{{ $invoice->invoice_number }}</td>
                                        <td>{{ $invoice->campaign_name }}</td>
                                        <td>{{ $invoice->brand }}</td>
                                        <td>&#8358;{{ $invoice->actual_amount_paid }}</td>
                                        <td>&#8358;{{ $invoice->refunded_amount }}</td>
                                        <td>
                                            <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $agency_discount->id }}" style="cursor: pointer;"> View</span></a>
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