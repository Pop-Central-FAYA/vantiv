@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            Create Campaign
            <small><i class="fa fa-users"></i> Audience</small>
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>
            <li><i class="fa fa-users"></i> Audience </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-1 hidden-sm hidden-xs"></div>
            <div class="col-md-9 " style="padding:2%">
                <form class="campform" method="POST" action="{{ route('campaign.store3', ['id' => 1]) }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <label>Target Audience</label>
                            </br>
                            <?php $arr = []; ?>
                            <?php $male = []; $female = []; $both = [];?>
                        @foreach($ratecard as $ratecards)
                            <?php $adslots = (array) $ratecards->adslots ?>
                                @foreach($ratecards->adslots as $rating)
                                    {{ dd($rating) }}
                                    @foreach($rating as $r)
                                        <?php if($r->target_audience->audience = 'Male'){
                                            $male[] = $r->target_audience->audience;
                                        }elseif($r->target_audience->audience = 'Female'){
                                            $female[] = $r->target_audience->audience;
                                        }else{
                                            $both[] = $r->target_audience->audience;
                                        }
                                        ?>
                                    @endforeach
                                @endforeach
                            @endforeach
                            <?php $no_male = count($male); $no_female = count($female); $no_both = count($both)?>
                            <?php $male = array_unique($male); $female = array_unique($female); $both = array_unique($both); ?>
                            <?php $male[1] = $no_male; $female[1] = $no_female; $both[1] = $no_both; ?>
                            <?php $final_array = [$male,$female,$both] ?>
                            @foreach($final_array as $final)
                                @if($final[1] !== 0)
                                    <ul class="nav nav-stacked">
                                        <li role="presentation"><a href="#">{{ $final[0] }}</a></li>
                                        <li role="presentation"><a href="#">{{ $final[1] }}</a></li>
                                    </ul>
                                @endif
                            @endforeach
                            {{--<select name="target_audience">--}}
                                {{--@foreach($target_audience as $target)--}}
                                    {{--<option value="{{ $target->id }}">{{ $target->audience }}</option>--}}
                                {{--@endforeach--}}
                            {{--</select>--}}
                        </div>
                        <div class="col-lg-6 col-md-6 hidden-sm hidden-xs"></div>
                    </div>



                        <p align="right">
                            <button type="button" id="step2" class="btn campaign-button" >Back <i class="fa fa-backward" aria-hidden="true"></i></button>

                            <button type="submit" class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>

                        </p>
                    </div>

                </form>

            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->




        </div>
        <!-- /.row -->


    </section>

@stop

@section('scripts')
    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script>
        $(document).ready(function() {
           $('#step2').click(function(){
               window.location.href = "/campaign/create/1/step2";
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

@section('stylesheets')
    <link rel="stylesheet" href="{{ asset('asset/plugins/iCheck/all.css') }}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet" href="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.css') }}">
    <!-- Bootstrap time Picker -->
    <link rel="stylesheet" href="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('asset/plugins/select2/select2.min.css') }}">
    @stop