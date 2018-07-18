@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Checkout</title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.agency.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create New Campaign</h2>
            </div>
        </div>


        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color">

            <div class="margin_center col_7 clearfix pt4 create_fields">

                <!-- progress bar -->
                <div class="create_gauge">
                    <div class=""></div>
                </div>


                <p class='weight_medium m-b'>Booked Ad Spots</p>
                <p class="small_faint col_9 mb4"></p>


                <table class="mb border_bottom">
                    <tr>
                        <th>Channel</th>
                        <th>Time Slots</th>
                        <th>Ad Length</th>
                        <th>Price</th>
                        <th>Surge</th>
                        <th>Position</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    @foreach($queries as $query)
                        <tr>
                            <td>{{ $query['broadcaster_brand'] }}</td>
                            <td>{{ $query['from_to_time'] }}</td>
                            <td>{{ $query['time'] }} seconds</td>
                            <td>&#8358; {{ number_format($query['price'], 2) }}</td>
                            <td>{{ $query['percentage'] }}%</td>
                            <td>{{ $query['position'] }}</td>
                            <td>&#8358; {{ number_format($query['total_price'], 2) }}</td>
                            <td><a href="#delete_cart{{ $query['id'] }}" class="color_red close_red modal_click"><span class="_icon"></span> Remove</a></td>
                        </tr>
                    @endforeach
                </table>


                <div class="clearfix weight_medium _total_amount">
                    <p class="left">Total:</p>
                    <p class="left">&#8358; {{ number_format($calc[0]->total_price, 2) }}</p>
                </div>

                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_6">
                        <a href="{{ route('agency_campaign.step4', ['id' => $id, 'broadcaster' => $broadcaster[0]->id]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="column col_6 align_right">
                        <a href="#payment" class="btn uppercased _proceed modal_click">Proceed <span class=""></span></a>
                    </div>
                </div>

            </div>
        </div>
        <!-- main frame end -->

    </div>

    <!-- are you sure modal -->
    @foreach($queries as $query)
        <div class="modal_contain" id="delete_cart{{ $query['id'] }}">
            <div class="wallet_placer margin_center mb3"></div>

                <p class="align_center margin_center col_10 mb4">Are you sure you want to delete this item ?</p>

                <div class="align_right">
                    <a href="{{ route('agency_cart.remove', ['id' => $query['id']]) }}" class="btn">Delete</a>
                </div>

        </div>
    @endforeach

    <!-- payment modal -->
    <div class="modal_contain payment_modal" id="payment">
        <h2 class="border_bottom align_center">Complete Purchase</h2>

        <div class="padd mb4 pt">
            <h3 class="weight_medium uppercased">TOTAL: &#8358; {{ number_format($calc[0]->total_price, 2) }}</h3>
            <p class="small_faint mb4"></p>

            <p class="mb">Choose Payment Option</p>
            <form method="POST" action="{{ route('agency_submit.campaign', ['id' => $id]) }}">
                {{ csrf_field() }}

                <input type="hidden" value="{{ $calc[0]->total_price }}" name="total"/>
                <div class="mb4 create_payment">
                    <li class="m-b">
                        <input type="radio" name="p" value="Cash" id="cash">
                        <label class="weight_medium" for="cash">Cash</label>
                    </li>

                    {{--<li class="m-b">--}}
                        {{--<input type="radio" name="p" value="Cart" id="card">--}}
                        {{--<label class="weight_medium" for="card">Card</label>--}}
                    {{--</li>--}}

                    <li class="">
                        <input type="radio" name="p" value="Transfer" id="trans">
                        <label class="weight_medium" for="trans">Transfer</label>
                    </li>
                    <br>
                    <div class="column align_right">
                        <button type="submit" class="btn uppercased _proceed">Proceed <span class=""></span></button>
                    </div>
                </div>
            </form>
        </div>

        <br>
        <br>
    </div>
    <!-- end -->
@stop



{{--@extends('layouts.new_app')--}}
{{--@section('title')--}}
    {{--<title>Agency | Create Campaigns</title>--}}
{{--@stop--}}
{{--@section('content')--}}

    {{--<div class="main-section">--}}
        {{--<div class="container">--}}
            {{--<div class="row">--}}
                {{--<div class="col-12 heading-main">--}}
                    {{--<h1>Create Campaigns</h1>--}}
                    {{--<ul>--}}
                        {{--<li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Agency</a></li>--}}
                        {{--<li><a href="{{ route('agency.campaign.all') }}">All Campaign</a></li>--}}
                    {{--</ul>--}}
                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="row">--}}
                {{--<div class="Create-campaign">--}}
                    {{--<div class="col-12 ">--}}
                        {{--<h2>Summary</h2>--}}
                        {{--<hr>--}}
                        {{--<p><br></p>--}}
                        {{--<div class="row">--}}
                            {{--<div class="col-md-12 ">--}}
                                {{--@if(count($query) === 0)--}}
                                    {{--<p>You have 0 item in your cart</p>--}}
                                {{--@else--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-md-12" style="padding:2%">--}}
                                            {{--<form class="campform">--}}
                                                {{--<div class="row">--}}
                                                    {{--<div class="col-md-12 ">--}}
                                                        {{--<hr style="border-bottom: 1px solid #333">--}}

                                                        {{--<div class="col-md-6">--}}
                                                            {{--<p><b>Campaign Name:</b> {{ $first_session->name }}  </p>--}}
                                                            {{--<p><b>Brand Name:</b> {{ $brand[0]->name }} </p>--}}
                                                            {{--<p><b>Product Name:</b> {{ $first_session->product }}  </p>--}}
                                                            {{--<p><b>Date:</b> {{ $first_session->start_date }}--}}
                                                                {{--to {{ $first_session->end_date }}  </p>--}}
                                                        {{--</div>--}}
                                                        {{--<div class="col-md-6">--}}
                                                            {{--<p><b><i class="fa fa-users"></i> Day Parts: </b>--}}
                                                                {{--@foreach($day_part as $daypart)--}}
                                                                    {{--{{ $daypart->day_parts }},--}}
                                                                {{--@endforeach--}}
                                                            {{--</p>--}}
                                                            {{--<p><b><i class="fa fa-user"></i> Viewers age:--}}
                                                                {{--</b>{{ $first_session->min_age }}--}}
                                                                {{--- {{ $first_session->max_age }} years</p>--}}

                                                            {{--<p><b><i class="fa fa-map-marker" aria-hidden="true"></i>--}}
                                                                    {{--Region: @foreach($region as $regions)</b>--}}
                                                                {{--{{ $regions->region }}--}}
                                                                {{--@endforeach</p>--}}
                                                        {{--</div>--}}
                                                    {{--</div>--}}
                                                {{--</div>--}}
                                                {{--<div class="row" style="margin-top: 20px;">--}}
                                                    {{--<div class="col-md-12">--}}
                                                        {{--<h2 style="margin-bottom: 20px;">Uploaded list</h2>--}}

                                                        {{--<table class="table table-hover" style="font-size:16px">--}}
                                                            {{--<tr>--}}
                                                                {{--<th>ID</th>--}}
                                                                {{--<th>Broadcaster</th>--}}
                                                                {{--<th>Time</th>--}}
                                                                {{--<th>Duration</th>--}}
                                                                {{--<th>Price</th>--}}
                                                                {{--<th>Position</th>--}}
                                                                {{--<th>Surge</th>--}}
                                                                {{--<th>Total Price</th>--}}
                                                                {{--<th>Action</th>--}}
                                                            {{--</tr>--}}
                                                            {{--@foreach($query as $queries)--}}
                                                                {{--<tr>--}}
                                                                    {{--<td>{{ $loop->iteration }}</td>--}}
                                                                    {{--<td><img style="width: 150px; height: 150px;" src="{{ $queries['broadcaster_logo'] ? asset(decrypt($queries['broadcaster_logo'])) : asset('asset/dist/img/nta-logo.jpg') }}" width="100%"></td>--}}
                                                                    {{--<td>{{ $queries['from_to_time'] }}</td>--}}
                                                                    {{--<td>{{ $queries['time'] }} seconds</td>--}}
                                                                    {{--<td>--}}
                                                                        {{--&#8358;{{ number_format($queries['price'], 2) }}</td>--}}
                                                                    {{--<td>{{ $queries['position'] }}</td>--}}
                                                                    {{--<td>{{ $queries['percentage'] }}%</td>--}}
                                                                    {{--<td>--}}
                                                                        {{--&#8358;{{ number_format($queries['total_price'], 2) }}</td>--}}
                                                                    {{--<td>--}}
                                                                        {{--<a href="{{ route('agency_cart.remove', ['id' => $queries['id']]) }}"--}}
                                                                           {{--style="font-size: 16px; text-decoration: none;">--}}
                                                                            {{--<span class="label label-danger">--}}
                                                                                {{--<i class="fa fa-trash-o"--}}
                                                                                   {{--aria-hidden="true"></i>--}}
                                                                                {{--Remove--}}
                                                                            {{--</span>--}}
                                                                        {{--</a>--}}
                                                                    {{--</td>--}}
                                                                {{--</tr>--}}
                                                            {{--@endforeach--}}
                                                        {{--</table>--}}
                                                    {{--</div>--}}

                                                    {{--<h3 style="padding: 0;">--}}
                                                        {{--&#8358;{{ number_format($calc[0]->total_price, 2) }}--}}
                                                    {{--</h3>--}}

                                                {{--</div>--}}
                                            {{--</form>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                            {{--</div>--}}
                            {{--<!-- /.col -->--}}
                        {{--</div>--}}
                        {{--<!-- /.row -->--}}

                        {{--<div class="container">--}}

                            {{--<p align="right">--}}
                                {{--<button id="step7" class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>--}}
                                {{--<button class="btn campaign-button btn-danger btn-lg" style="margin-right:15%"--}}
                                        {{--data-toggle="modal" data-target=".bs-example2-modal-lg">Create Campaign <i--}}
                                            {{--class="fa fa-play" aria-hidden="true"></i></button>--}}

                            {{--</p>--}}
                            {{--<div class="modal fade bs-example2-modal-lg" tabindex="-1" role="dialog"--}}
                                 {{--aria-labelledby="myLargeModalLabel">--}}
                                {{--<div class="modal-dialog modal-lg" role="document">--}}
                                    {{--<div class="modal-content" style="padding: 5%">--}}
                                        {{--<h3>Payment</h3>--}}
                                        {{--<hr style="border-bottom: 1px solid #eee">--}}

                                        {{--for the time-slot bought for your adverts, the price is: <br/>--}}
                                        {{--<h3> Total: &#8358;{{ number_format($calc[0]->total_price, 2) }} </h3>--}}

                                        {{--Choose payment plab:--}}
                                        {{--<form method="POST"--}}
                                              {{--action="{{ route('agency_submit.campaign', ['id' => $id]) }}">--}}
                                            {{--{{ csrf_field() }}--}}
                                            {{--<input type="radio" name="payment" value="Cash" checked> Cash<br>--}}
                                            {{--<input type="radio" name="payment" value="Payment"> Cash<br>--}}
                                            {{--<input type="radio" name="payment" value="other"> Transfer--}}
                                            {{--<input type="hidden" value="{{ $calc[0]->total_price }}" name="total"/>--}}

                                            {{--<p align="center">--}}
                                                {{--<button type="submit" class="btn btn-large"--}}
                                                        {{--style="background: #34495e; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">--}}
                                                    {{--Confirm payment--}}
                                                {{--</button>--}}
                                            {{--</p>--}}
                                        {{--</form>--}}
                                    {{--</div>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--@endif--}}
                    {{--</div>--}}
                {{--</div>--}}

            {{--</div>--}}
        {{--</div>--}}
    {{--</div>--}}


    {{--@foreach($query as $queries)--}}
        {{--<div class="modal fade {{ $queries['id'] }}delete" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">--}}
            {{--<div class="modal-dialog modal-lg" role="document">--}}
                {{--<div class="modal-content" style="padding: 5%">--}}
                    {{--<p>Are you sure you want to delete this campaign from checkout?</p>--}}
                    {{--<br>--}}
                    {{--<a class="btn btn-danger btn-xs" href="{{ route('agency_cart.remove', ['id' => $queries['id']]) }}">Yes</a> <button class="btn btn-primary btn-xs" data-dismiss="modal">Cancel</button>--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    {{--@endforeach--}}

{{--@stop--}}
{{--@section('scripts')--}}
    {{--<!-- Select2 -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/select2/select2.full.min.js') }}"></script>--}}
    {{--<!-- InputMask -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.js') }}"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>--}}
    {{--<!-- date-range-picker -->--}}
    {{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>--}}
    {{--<!-- bootstrap datepicker -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>--}}
    {{--<!-- bootstrap color picker -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>--}}
    {{--<!-- bootstrap time picker -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>--}}
    {{--<!-- SlimScroll 1.3.0 -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>--}}
    {{--<!-- iCheck 1.0.1 -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/iCheck/icheck.min.js') }}"></script>--}}
    {{--<!-- FastClick -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/fastclick/fastclick.js') }}"></script>--}}
    {{--<!-- AdminLTE App -->--}}
    {{--<script src="{{ asset('agency_asset/dist/js/app.min.js') }}"></script>--}}

    {{--<!-- DataTables -->--}}
    {{--<script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>--}}
    {{--<script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>--}}
    {{--<script>--}}
        {{--$(document).ready(function(){--}}
            {{--$("#txtFromDate").datepicker({--}}
                {{--numberOfMonths: 2,--}}
                {{--onSelect: function (selected) {--}}
                    {{--$("#txtToDate").datepicker("option", "minDate", selected)--}}
                {{--}--}}
            {{--});--}}


            {{--$("#txtToDate").datepicker({--}}
                {{--numberOfMonths: 2,--}}
                {{--onSelect: function(selected) {--}}
                    {{--$("#txtFromDate").datepicker("option","maxDate", selected)--}}
                {{--}--}}
            {{--});--}}

        {{--});--}}
    {{--</script>--}}
    {{--<script>--}}
        {{--$(function () {--}}
            {{--//Initialize Select2 Elements--}}
            {{--$(".select2").select2();--}}

            {{--//Datemask dd/mm/yyyy--}}
            {{--$("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});--}}
            {{--//Datemask2 mm/dd/yyyy--}}
            {{--$("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});--}}
            {{--//Money Euro--}}
            {{--$("[data-mask]").inputmask();--}}

            {{--//Date range picker--}}
            {{--$('#reservation').daterangepicker();--}}
            {{--//Date range picker with time picker--}}
            {{--$('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});--}}
            {{--//Date range as a button--}}
            {{--$('#daterange-btn').daterangepicker(--}}
                {{--{--}}
                    {{--ranges: {--}}
                        {{--'Today': [moment(), moment()],--}}
                        {{--'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],--}}
                        {{--'Last 7 Days': [moment().subtract(6, 'days'), moment()],--}}
                        {{--'Last 30 Days': [moment().subtract(29, 'days'), moment()],--}}
                        {{--'This Month': [moment().startOf('month'), moment().endOf('month')],--}}
                        {{--'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]--}}
                    {{--},--}}
                    {{--startDate: moment().subtract(29, 'days'),--}}
                    {{--endDate: moment()--}}
                {{--},--}}
                {{--function (start, end) {--}}
                    {{--$('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));--}}
                {{--}--}}
            {{--);--}}

            {{--//Date picker--}}
            {{--$('#datepicker').datepicker({--}}
                {{--autoclose: true--}}
            {{--});--}}

            {{--$('#datepickerend').datepicker({--}}
                {{--autoclose: true--}}
            {{--});--}}

            {{--//iCheck for checkbox and radio inputs--}}
            {{--$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({--}}
                {{--checkboxClass: 'icheckbox_minimal-blue',--}}
                {{--radioClass: 'iradio_minimal-blue'--}}
            {{--});--}}
            {{--//Red color scheme for iCheck--}}
            {{--$('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({--}}
                {{--checkboxClass: 'icheckbox_minimal-red',--}}
                {{--radioClass: 'iradio_minimal-red'--}}
            {{--});--}}
            {{--//Flat red color scheme for iCheck--}}
            {{--$('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({--}}
                {{--checkboxClass: 'icheckbox_flat-green',--}}
                {{--radioClass: 'iradio_flat-green'--}}
            {{--});--}}

            {{--//Colorpicker--}}
            {{--$(".my-colorpicker1").colorpicker();--}}
            {{--//color picker with addon--}}
            {{--$(".my-colorpicker2").colorpicker();--}}

            {{--//Timepicker--}}
            {{--$(".timepicker").timepicker({--}}
                {{--showInputs: false--}}
            {{--});--}}
        {{--});--}}
    {{--</script>--}}
{{--@stop--}}


