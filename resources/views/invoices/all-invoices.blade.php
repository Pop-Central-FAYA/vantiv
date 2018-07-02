@extends('layouts.faya_app')

@section('title')
    <title> FAYA | INVOICE </title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Invoices</h2>
            </div>
        </div>


        <div class="the_frame client_dets mb4">

            <table>
                <tr>
                    <th><input type="checkbox"></th>
                    <th>Invoice Number</th>
                    <th>Name</th>
                    <th>Client</th>
                    <th>Brand</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th></th>
                </tr>

                @foreach ($all_invoices as $invoice)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>{{ $invoice['invoice_number'] }}</td>
                        <td>{{ $invoice['campaign_name'] }}</td>
                        <td>{{ $invoice['name'] }}</td>
                        <td class="weight_medium">{{ ucfirst($invoice['campaign_brand']) }}</td>
                        <td>{{ $invoice['start_date'] }}</td>
                        <td>{{ $invoice['end_date'] }}</td>
                        <td>&#8358; {{ $invoice['actual_amount_paid'] }}</td>
                        @if($invoice['status'] === 1)
                            <td><span class="span_state status_success">Approved</span></td>
                        @else
                            <td><a href="#approve_invoice{{ $invoice['id'] }}" class="span_state status_pending modal_click">Pending</a></td>
                        @endif
                        <td><a href="">View</a></td>
                    </tr>
                @endforeach
            </table>
        </div>
        <!--  -->

    </div>

    @foreach($all_invoices as $invoice)
        <div class="modal_contain" id="approve_invoice{{ $invoice['id'] }}">
        <div class="wallet_placer margin_center mb3"></div>
        <form method="POST" class="selsec" action="{{ route('invoices.update', ['invoice_id' => $invoice['id']]) }}">
            {{ csrf_field() }}
            <p class="align_center margin_center col_10 mb4">By approving, you agree the sum of <span class='color_base weight_medium'>&#8358; {{ $invoice['actual_amount_paid'] }}</span> be deducted from your wallet </p>

            <div class="align_right">
                <span class="padd color_initial light_font" onclick="$.modal.close()">Cancel</span>
                <button type="submit" class="btn">Continue</button>
            </div>
        </form>
    </div>
    @endforeach
@stop


{{--@extends('layouts.new_app')--}}

{{--@section('title')--}}
    {{--<title>Agency - Invoice</title>--}}
{{--@stop--}}

{{--@section('styles')--}}

    {{--<link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />--}}
    {{--<link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">--}}

{{--@endsection--}}

{{--@section('content')--}}

    {{--<div class="main-section">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-12 heading-main">--}}
                    {{--<h1>All Invoice</h1>--}}
                    {{--<ul>--}}
                        {{--<li><a href="#"><i class="fa fa-edit"></i>Invoice Management</a></li>--}}
                        {{--<li><a href="#">View All Invoices</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}
                {{--<div class="col-12 invoice-Management">--}}
                    {{--@if (count($all_invoices) === 0)--}}

                        {{--<h4>You have no invoices at this moment</h4>--}}

                    {{--@else--}}
                        {{--<table class="table" id="example1">--}}
                            {{--<thead>--}}
                            {{--<tr>--}}
                                {{--<th>Invoice Number</th>--}}
                                {{--<th>Campaign Name</th>--}}
                                {{--<th>Client Name</th>--}}
                                {{--<th>Brand</th>--}}
                                {{--<th>Actual Amount Paid</th>--}}
                                {{--<th>Refunded Amount</th>--}}
                                {{--<th>Status</th>--}}
                            {{--</tr>--}}
                            {{--</thead>--}}
                            {{--<tbody>--}}
                                {{--@foreach ($all_invoices as $invoice)--}}
                                    {{--<tr>--}}
                                        {{--<td>{{ $invoice['invoice_number'] }}</td>--}}
                                        {{--<td>{{ $invoice['campaign_name'] }}</td>--}}
                                        {{--<td>{{ $invoice['name'] }}</td>--}}
                                        {{--<td>{{ $invoice['campaign_brand'] }}</td>--}}
                                        {{--<td>&#8358;{{ $invoice['actual_amount_paid'] }}</td>--}}
                                        {{--<td>&#8358;{{ $invoice['refunded_amount'] }}</td>--}}
                                        {{--<td>--}}
                                            {{--@if ($invoice['status'] == 1)--}}
                                                {{--<label style="font-size: 16px" class="label label-success">--}}
                                                    {{--Approved--}}
                                                {{--</label>--}}
                                            {{--@elseif ($invoice['status'] == 0)--}}
                                                {{--<label style="font-size: 16px" class="label label-warning">--}}
                                                    {{--Pending--}}
                                                {{--</label>--}}
                                            {{--@endif--}}
                                        {{--</td>--}}
                                    {{--</tr>--}}
                                {{--@endforeach--}}
                            {{--</tbody>--}}
                        {{--</table>--}}
                    {{--@endif--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}

{{--@stop--}}

{{--@section('scripts')--}}

    {{--{!! HTML::script('assets/js/moment.min.js') !!}--}}
    {{--{!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}--}}
    {{--{!! HTML::script('assets/js/as/profile.js') !!}--}}
    {{--<script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>--}}
    {{--<script src="{{ asset('asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
    {{--<script src="{{ asset('asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>--}}

    {{--<script>--}}
        {{--$(function () {--}}
            {{--$("#example1").DataTable();--}}
            {{--$('#example2').DataTable({--}}
                {{--"paging": true,--}}
                {{--"lengthChange": false,--}}
                {{--"searching": false,--}}
                {{--"ordering": true,--}}
                {{--"info": true,--}}
                {{--"autoWidth": false--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}


{{--@stop--}}