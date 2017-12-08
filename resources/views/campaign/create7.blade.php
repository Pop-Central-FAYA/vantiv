@extends('layouts.app')

@section('content')

    <section class="content-header">
        <h1>
            Create Campaign
            <small><i class="fa fa-file-video-o"></i> Adslot </small>
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>
            <li><i class="fa fa-file-video-o"></i> Adslot </li>

        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row cart" id="cart">
            <div class="col-md-1 hidden-sm hidden-xs"></div>
            <div class="col-md-9 " style="padding:2%">
                <form class="campform">
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
                                    <p align="center"> Available</p>
                                    <p>{{ Session::get('broadcaster_brand') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="tv-time-box" style="border:1px solid #ccc" >
                        @foreach($adslot as $adslots)
                            <div class="row">
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                    <img src="{{ asset('asset/dist/img/nta-logo.jpg') }}" width="65%">
                                </div>
                                <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4">
                                    <h3 lign="center">{{ $adslots->hourly_range_id->time_range }}</h3>
                                </div>
                                @foreach($adslots->rate_card as $rating)
                                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-4
                                        @foreach($cart as $carts)
                                            @if($carts->rate_id === $rating[0]->id)
                                                choosen
                                            @endif
                                        @endforeach
                                        " id="rate_this">
                                        <p align="center">{{ $rating[0]->from_to_time }} Seconds</p> <br/>
                                        <span type="button"  data-toggle="modal" data-target=".bs-example-modal-lg{{ $rating[0]->id }}" class="avail-box
                                            @foreach($cart as $carts)
                                                @if($carts->rate_id === $rating[0]->id)
                                                    disabled
                                                @endif
                                            @endforeach
                                        " style="background:green; cursor: pointer; "></span>
                                        <div class="modal fade bs-example-modal-lg{{ $rating[0]->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content" style="padding: 5%">
                                                    <form id="form_cart" action="{{ route('store.cart') }}" method="POST">
                                                        {{ csrf_field() }}
                                                        <h2 align="center">{{ $rating[0]->from_to_time }} Seconds Available</h2>
                                                        <ul style="font-size: 21px; margin:0 auto; width: 80%">
                                                            <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                            <hr />
                                                            @foreach($data as $datas)
                                                                <div class="row">
                                                                    @foreach(((object) $rating) as $d)
                                                                        @if(((integer) $datas->time) === $d->time_in_seconds)
                                                                            <input type="hidden" class="hourly_break{{ $d->id }}" name="hourly_break" value="{{ $rating[0]->from_to_time }}">
                                                                            <div class="col-md-3"><i class="fa fa-video-camera"></i> <img src="{{ asset($datas->uploads) }}" alt="{{ $datas->uploads }}" style="height: 40px; width: 40px;" /> </div>
                                                                            <input type="hidden" name="file" class="file{{ $d->id }}" value="{{ $datas->uploads }}">
                                                                            <div class="col-md-3"><span style="margin-left:15%"></span>{{ $datas->time }} Seconds</div>
                                                                            <input type="hidden" name="time" class="time{{ $d->id }}" value="{{ $datas->time }}">
                                                                            <div class="col-md-3"><input name="hourly" class="hourly" value="{{ $d->id }}" type="radio"></div>
                                                                            <input type="hidden" name="rate_id" class="rating_id" value="{{ $d->id }}">
                                                                            <div class="col-md-3"><td>&#8358;{{ $d->price }}</td></div>
                                                                            <input type="hidden" name="price" class="price{{ $d->id }}" value="{{ $d->price }}">
                                                                            <input type="hidden" name="rate_id" class="rate_id{{ $d->id }}" id="rate_id" value="{{ $rating[0]->id }}">
                                                                        @endif
                                                                    @endforeach
                                                                </div>
                                                                <hr/>
                                                            @endforeach
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
                </form>
            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>

    <!-- /.content -->

@endsection

@section('scripts')

    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>

    <!-- date-range-picker -->
    <script src="{{ 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js' }}"></script>
    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <!-- bootstrap time picker -->
    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#step6').click(function(){
                window.location.href = "/campaign/create/1/step";
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
               if(pick){
                   $.ajax({
                       url: url1,
                       method: "POST",
                       data: {rate_id: rate_id, price: price, file: file, range: range, time: time, '_token':$('input[name=_token]').val()},
                       success: function(data){
//                           console.log(data);
                           if(data === "success"){
                               location.reload();
                               $("#rate_this").addClass('choosen');
                           }

                       }
                   })
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

@endsection
@section('styles')
    <style>
    .disabled{
    cursor: not-allowed;
    pointer-events: none;
    }
    </style>
@stop
