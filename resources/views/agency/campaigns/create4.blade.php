@extends('agency_layouts.app')
@section('title')
    <title>Agency | Create Campaigns</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Create Campaigns
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">
            @if(count($cart) != 0)
            <li><a href="{{ route('agency_campaign.checkout', ['id' => $id, 'broadcaster' => $broadcaster]) }}"><i class="fa fa-shopping-cart"></i>({{ count($cart) }}) Cart</a> </li>
            @endif
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row cart" id="cart">
            <div class="col-md-1 hidden-sm hidden-xs"></div>
            <div class="col-md-9 " style="padding:2%">
                {{--<form class="campform">--}}
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-md-12 ">
                        <h2></h2>
                        <p align="center">The history of advertising can be traced to ancient civilizations. It became a major force in capitalist economies in the mid-19th century, based primarily on newspapers and magazines. In the 20th century, advertising grew rapidly with new technologies such as direct mail, radio, television, the internet and mobile devices.</p>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 5%">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 ">
                        <div class="tvspace-box">
                            <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="100%">
                            <div class="tv-space">
                                <p align="center">You have adslots form {{ (count($rate)) }} Time range</p>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach($rate as $rates)
                    {{--{{ dd($rates) }}--}}
                    <div id="tv-time-box" style="border:1px solid #ccc" >
                        <div class="row">
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="65%">
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                <h3 lign="center">{{ $rates['hourly_range']->time_range }}</h3>
                            </div>
                            @foreach($rates['adslots'] as $ads)
                                @if($ads->time_in_seconds === ((integer)$file_upload[0]->time))

                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4
                                @foreach($cart as $carts)
                                    @if($carts->adslot_id == $ads->id)
                                            choosen
                                    @endif
                                @endforeach
                                    " id="rate_this">
                                    <p align="center">{{ $ads->from_to_time }}
                                        <br>
                                        {{ $ads->time_in_seconds }} Seconds
                                    </p> <br/>
                                    <span type="button"  data-toggle="modal" data-target=".bs-example-modal-lg{{ $ads->id }}" class="avail-box
                                            @foreach($cart as $carts)
                                                @if($carts->adslot_id == $ads->id)
                                                   disabled
                                                @endif
                                            @endforeach
                                            " style="background:green; cursor: pointer; "></span>
                                    <div class="modal fade bs-example-modal-lg{{ $ads->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content" style="padding: 5%">

                                                <form id="form_cart" action="{{ route('agency_campaign.cart',['id' => $id, 'broadcaster' => $broadcaster]) }}" method="POST">
                                                    {{ csrf_field() }}
                                                    <h2 align="center"> Seconds Available</h2>
                                                    <ul style="font-size: 21px; margin:0 auto; width: 80%">
                                                        <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                        <hr />
                                                        <div class="row">
                                                            <input type="hidden" class="hourly_break{{ $ads->id }}" name="hourly_break" value="{{ $ads->from_to_time }}">
                                                            <div class="col-md-3"><i class="fa fa-video-camera"></i> <video width="200" controls><source src="{{ asset($file_upload[0]->uploads) }}"></video> </div>
                                                            <input type="hidden" name="file" class="file{{ $ads->id }}" value="{{ $file_upload[0]->uploads }}">
                                                            <div class="col-md-3"><span style="margin-left:15%"></span> {{ $ads->time_in_seconds }}Seconds</div>
                                                            <input type="hidden" name="time" class="time{{ $ads->id }}" value="{{ $file_upload[0]->time }}">
                                                            <div class="col-md-3"><input name="hourly" class="hourly" value="{{ $ads->id }}" type="radio"></div>
                                                            <input type="hidden" name="rate_id" class="rating_id" value="{{ $ads->id }}">
                                                            <div class="col-md-3"><td>&#8358;{{ number_format($ads->price, 2) }}</td></div>
                                                            <input type="hidden" name="price" class="price{{ $ads->id }}" value="{{ $ads->price }}">
                                                            <input type="hidden" name="rate_id" class="rate_id{{ $ads->id }}" id="rate_id" value="{{ $ads->rate_card }}">
                                                            <input type="hidden" name="adslot_id", class="adslot_id{{ $ads->id }}" value="{{ $ads->id }}">
                                                        </div>
                                                        <hr/>
                                                    </ul>
                                                    <button type="button" id="save_cart"  class="btn btn-large save_cart" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>
                                                </form>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
                {{--</form>--}}
            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>

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
                var id = $(".rating_id").val();
                var pick = $("input[name='hourly']:checked").val();
                var price = $(".price"+pick+"").val();
                var file = $(".file"+pick+"").val();
                var time = $(".time"+pick+"").val();
                var range = $(".hourly_break"+pick+"").val();
                var rate_id = $(".rate_id"+pick+"").val();
                var adslot_id = $(".adslot_id"+pick+"").val();
                console.log(url1);
                if(pick){
                    $.ajax({
                        url: url1,
                        method: "POST",
                        data: {rate_id: rate_id, price: price, adslot_id: adslot_id, file: file, range: range, time: time, '_token':$('input[name=_token]').val()},
                        success: function(data){
                            if(data === "success"){
                                location.reload();
                                $("#rate_this").addClass('choosen');
                            }

                        }
                    })
                }
                })

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
@section('style')
    <style>
        .disabled{
            cursor: not-allowed;
            pointer-events: none;
        }
    </style>
@stop


