@extends('layouts.app')

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

@section('title', trans('app.view-pending-invoices'))

<section class="content-header">
    <h1>
        Pending Invoices <small>Pending Invoices</small>
    </h1>

    <ol class="breadcrumb" style="font-size: 16px">
        <li><a href="#"><i class="fa fa-edit"></i> Invoices Management</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> View Pending Invoices</a> </li>
    </ol>
</section>

<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-xs-12">

            <div class="col-md-6" style="margin-bottom: 10px;"></div>

            <div class="col-md-12">

                <div class="box-body">

                    @if(count($pending_invoices) === 0)

                        <h4>You have no Pending Invoices at this moment</h4>

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
                                <th>View/Approve</th>
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
                                    <td>
                                        <a href="#" style="font-size: 16px"><span class="label label-success" data-toggle="modal" data-target="#myModal{{ $invoice['invoice_number'] }}" style="cursor: pointer;"> View</span></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                    @endif

                </div>

                @foreach ($pending_invoices as $invoice)

                    <div class="modal fade" id="myModal{{ $invoice['invoice_number'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">

                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title" id="myModalLabel">
                                        View/Approve - Invoice <strong>{{ $invoice['invoice_number'] }}</strong> for <strong>{{ $invoice['campaign_name'] }}</strong> campaign
                                    </h4>
                                </div>


                                <form method="POST" class="selsec" action="{{ route('invoices.update', ['invoice_id' => $invoice['id']]) }}">
                                    {{ csrf_field() }}
                                    <div class="modal-body">
                                        <p class="text-center">
                                            By approving, you agree that the sum of
                                            <strong>{{ $invoice['actual_amount_paid'] }}</strong>
                                            be deducted from your wallet.
                                        </p>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                                        <input type="submit" value="Approve Invoice" class="btn btn-primary" />
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                @endforeach
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