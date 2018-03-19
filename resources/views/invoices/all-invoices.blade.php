@extends('layouts.new_app')

@section('title')
    <title>Agency - Invoice</title>
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
                    <h1>All Invoice</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Invoice Management</a></li>
                        <li><a href="#">View All Invoices</a></li>
                    </ul>
                </div>
                <div class="col-12 invoice-Management">
                    @if (count($all_invoices) === 0)

                        <h4>You have no invoices at this moment</h4>

                    @else
                        <table class="table" id="example1">
                            <thead>
                            <tr>
                                <th>Invoice Number</th>
                                <th>Campaign Name</th>
                                <th>Client Name</th>
                                <th>Brand</th>
                                <th>Actual Amount Paid</th>
                                <th>Refunded Amount</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($all_invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice['invoice_number'] }}</td>
                                        <td>{{ $invoice['campaign_name'] }}</td>
                                        <td>{{ $invoice['name'] }}</td>
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