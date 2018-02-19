@extends('layouts.new_app')
@section('title')
    <title>Advertiser | Create Campaigns</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create Campaigns</h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Advertiser</a></li>
                        <li><a href="{{ route('advertiser.campaign.all') }}">All Campaign</a></li>
                    </ul>
                </div>
                <div class="Create-campaign">
                    <div class="col-12 ">
                        <h2>Choose your slots</h2>
                        <hr>
                        <p><br></p>
                        @if(count($cart) != 0)
                            <ol class="breadcrumb" style="font-size: 16px">
                                <li><a href="{{ route('advertiser_campaign.checkout', ['id' => $id, 'broadcaster' => $broadcaster]) }}"><i class="fa fa-shopping-cart"></i>({{ count($cart) }}) Cart</a> </li>
                            </ol>
                        @endif
                        <div class="row cart" id="cart">
                            <div class="col-md-1 hidden-sm hidden-xs"></div>
                            <div class="col-md-9 " style="padding:2%">
                                <div class="row">
                                    <div class="col-md-12 ">
                                        <p align="center">The history of advertising can be traced to ancient civilizations. It became a major force in capitalist economies in the mid-19th century, based primarily on newspapers and magazines. In the 20th century, advertising grew rapidly with new technologies such as direct mail, radio, television, the internet and mobile devices.</p>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 5%">
                                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 ">
                                        <div class="tvspace-box">
                                            <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="100%">
                                            <div class="tv-space">
                                                <p align="center">{{ $result }} Adslot(s)</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tv-time-box" style="border:1px solid #ccc" >
                                    @foreach($ratecards as $ratecard)
                                        <div class="row">
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                                <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="65%">
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                                <h3 lign="center">{{ $ratecard['hourly_range'] }}</h3>
                                            </div>
                                            @foreach($ratecard['adslot'] as $rating)
                                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4
                                                @foreach($cart as $carts)
                                                @if($carts->adslot_id === $rating->id)
                                                        choosen
                                                    @endif
                                                @endforeach
                                                        " id="rate_this">
                                                    <p align="center">{{ $rating->from_to_time }}
                                                        <br>
                                                        {{ $rating->time_difference - $rating->time_used }} Seconds Available

                                                        <?php
                                                        $percentage_used = (($rating->time_difference - $rating->time_used) / $rating->time_difference) * 100;
                                                        ?>
                                                    </p> <br/>

                                                    <div class="progress">
                                                        <div class="progress-bar bg-success
                                                        @foreach($cart as $carts)
                                                        @if($carts->adslot_id === $rating->id)
                                                                disabled
                                                            @endif
                                                        @endforeach
                                                                " data-toggle="modal" data-target=".bs-example-modal-lg{{ $rating->id }}" role="progressbar" style="width: {{ $percentage_used }}%" aria-valuenow="{{ $percentage_used }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                    </div>
                                                    {{--{{ dd(count($times)) }}--}}
                                                    <div class="modal fade bs-example-modal-lg{{ $rating->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content" style="padding: 5%">

                                                                <form id="form_cart" action="{{ route('store.cart') }}" method="GET">
                                                                    {{ csrf_field() }}
                                                                    <h2 align="center">{{ $rating->from_to_time }} | {{ $rating->time_difference }} Seconds Available</h2>
                                                                    <ul style="font-size: 21px; margin:0 auto; width: 80%">
                                                                        <hr>
                                                                        <p><br></p>
                                                                        <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                                        <hr />
                                                                        <div class="row">
                                                                            <?php
                                                                            $select_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$rating->id'");
                                                                            if(!$select_price){
                                                                                $select_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$rating->id'");
                                                                            }
                                                                            ?>


                                                                            <table class="table table-bordered table-striped">
                                                                                <thead>
                                                                                <tr>
                                                                                    <th>S/N</th>
                                                                                    <th>Name</th>
                                                                                    <th>duration</th>
                                                                                    <th>price</th>
                                                                                    <th>Select</th>
                                                                                </tr>
                                                                                </thead>
                                                                                {{--{{ dd($datas) }}--}}
                                                                                <tbody>
                                                                                <?php $j = 1; $i = 0; for($i = 0; $i < count($datas); $i++){ ?>

                                                                                @if(((integer) $datas[$i]->time) === $times[$i] && ($rating->time_difference - $rating->time_used) >= $times[$i])
                                                                                    <tr>
                                                                                        <td>{{ $j }}</td>
                                                                                        <td><div class="col-md-3"> <video width="150" controls><source src="{{ asset($datas[$i]->uploads) }}"></video> </div></td>
                                                                                        <input type="hidden" name="file" class="file{{ $rating->id.$datas[$i]->id }}" value="{{ $datas[$i]->uploads }}">
                                                                                        <td><div class="col-md-3"><span style="margin-left:15%"></span>{{ $datas[$i]->time }} Seconds</div></td>
                                                                                        <input type="hidden" name="time" class="time{{ $rating->id.$datas[$i]->id }}" value="{{ $datas[$i]->time }}">
                                                                                        <input type="hidden" name="from_to_time" class="from_to_time{{ $rating->id.$datas[$i]->id }}" value="{{ $rating->from_to_time }}">
                                                                                        <input type="hidden" name="adslot_id" class="adslot_id{{ $rating->id.$datas[$i]->id }}" value="{{ $rating->id }}">
                                                                                        <input type="hidden" name="walkins" class="walkins" value="{{ $id }}">
                                                                                        @if($datas[$i]->time === 15)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_15 }}</div></td>
                                                                                            <input type="hidden" name="price" class="price{{ $rating->id.$datas[$i]->id }}" value="{{ $select_price[0]->price_15 }}">
                                                                                        @elseif($datas[$i]->time === 30)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_30 }}</div></td>
                                                                                            <input type="hidden" name="price" class="price{{ $rating->id.$datas[$i]->id }}" value="{{ $select_price[0]->price_30 }}">
                                                                                        @elseif($datas[$i]->time === 45)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_45 }}</div></td>
                                                                                            <input type="hidden" name="price" class="price{{ $rating->id.$datas[$i]->id }}" value="{{ $select_price[0]->price_45 }}">
                                                                                        @elseif($datas[$i]->time === 60)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_60 }}</div></td>
                                                                                            <input type="hidden" name="price" class="price{{ $rating->id.$datas[$i]->id }}" value="{{ $select_price[0]->price_60 }}">
                                                                                        @endif
                                                                                        <td><div class="col-md-3"><input name="hourly" class="hourly" value="{{ $rating->id.$datas[$i]->id }}" type="radio"></div></td>
                                                                                    </tr>
                                                                                @endif
                                                                                <?php $j++; } ?>
                                                                                </tbody>
                                                                            </table>

                                                                        </div>

                                                                        <hr/>
                                                                    </ul>
                                                                    <button type="button" id="save_cart"  class="btn btn-large save_cart" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                                                </form>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-md-2 hidden-sm hidden-xs"></div>
                            <!-- /.col -->
                        </div>
                    </div>
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
    <script>
        $(document).ready(function(){
            $("#txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $("body").delegate(".save_cart", "click", function() {
                var url1 = $("#form_cart").attr("action");
                var pick = $("input[name='hourly']:checked").val();
                var price = $(".price"+pick+"").val();
                var file = $(".file"+pick+"").val();
                var time = $(".time"+pick+"").val();
                var range = $(".from_to_time"+pick+"").val();
                var adslot_id = $(".adslot_id"+pick+"").val();
                var walkins = $(".walkins").val();
//                console.log(url1,pick,price,file,time,range,adslot_id, walkins);
                if(pick){
                    $.ajax({
                        url: url1,
                        method: "GET",
                        data: {price: price, adslot_id: adslot_id, file: file, range: range, time: time, walkins: walkins, '_token':$('input[name=_token]').val()},
                        success: function(data){
                            if(data === "success"){
                                location.reload();
                                $("#rate_this").addClass('choosen');
                            }

                        }
                    })
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
    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Datemask dd/mm/yyyy
            $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
            //Datemask2 mm/dd/yyyy
            $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
            //Money Euro
            $("[data-mask]").inputmask();

            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
            //Date range as a button
            $('#daterange-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }
            );

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true
            });

            $('#datepickerend').datepicker({
                autoclose: true
            });

            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            //Red color scheme for iCheck
            $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
                checkboxClass: 'icheckbox_minimal-red',
                radioClass: 'iradio_minimal-red'
            });
            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            //Colorpicker
            $(".my-colorpicker1").colorpicker();
            //color picker with addon
            $(".my-colorpicker2").colorpicker();

            //Timepicker
            $(".timepicker").timepicker({
                showInputs: false
            });
        });
    </script>
@stop
@section('styles')
    <style>
        .choosen{
            border:2px solid #9f005d;
            background: white;
            border-radius: 10px
        }
        .disabled{
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
@stop



