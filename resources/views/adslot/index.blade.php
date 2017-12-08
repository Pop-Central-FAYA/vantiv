@extends('layouts.app')

@section('content')

@section('title', 'Faya | Dashboard')

<!-- Content Header (Page header) -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Ad Management <small>Adslot</small>

    </h1>
    <ol class="breadcrumb" style="font-size: 16px">

        <li><a href="#"><i class="fa fa-edit"></i> Ad Management</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> Adslot</a> </li>

    </ol>
</section>

<!-- Main content -->

<section class="content">
    <div class="row">
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <div class="col-md-8 Campaign" style="padding:2%">
            <div class="row">
                <h4 style="margin-left: 17px;font-weight: bold">Search</h4>
                <div class="col-md-12" style="margin-top: -2%">
                    <div class="input-group date styledate" style="width:30% !important">
                        <input type="text" placeholder="Search here..." class="form-control pull-right"  >
                    </div>
                    <div class="input-group" style="">
                        <input type="submit" class="search-btn" value="search" style="float:left" >
                    </div>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <!-- /.col -->
        {{--@if(count($ratecard) === 0)--}}
           {{--<p>o</p>--}}
        {{--@else--}}
        {{--@foreach($ratecard as $ratecards)--}}
        {{--@endforeach--}}
        {{--@endif--}}
        <div class="row" style="padding: 5%">
            <div class="col-xs-12">
                <div class="col-md-11">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" style="background:#eee">
                            <li class="active"><a href="{{ route('adslot.all') }}" >All ADslots</a></li>
                            @foreach($preload->regions as $region)
                                <li><a href="{{ route('adslot.region', ['region_id' => $region->id]) }}" >{{ $region->region }}</a></li>
                            @endforeach

                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">
                                <!-- Post -->
                                <!-- /.post -->
                                <div class="box-body">
                                    @if(count($ratecard) === 0)
                                        <p class="text-center">No Adslot created for this region</p>
                                    @else
                                    <table id="example1" class="table table-bordered table-striped">
                                        <thead>
                                        <tr>
                                            <th>Day</th>
                                            <th>Time Slot</th>
                                            <?php $s = []; ?>
                                            <?php
                                            $count_sec = count($seconds);
                                            for($i = 0; $i < $count_sec; $i++)
                                            {
                                                echo "<th>". $seconds[$i] ." Seconds </th>";
                                            }
                                            ?>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $j = 0; ?>
                                        @foreach($ratecard as $ratecards)
                                            @foreach($ratecards->adslots as $rating)
                                                <tr class="adslot_tr{{ $ratecards->id }}" tr_id="{{ $ratecards->id }}">
                                                    <td>{{ $ratecards->day->day }}</td>
                                                    <td>{{ $rating[0]->from_to_time }}</td>
                                                    <td>{{ $rating[0]->price }}</td>
                                                    <td>{{ $rating[1]->price }}</td>
                                                    <td>{{ $rating[2]->price }}</td>
                                                    <td>{{ $rating[3]->price }}</td>
                                                    <td class="adslot_td{{ $ratecards->id }}">
                                                        <a href="#" id="edit" edit_adslot_id = "{{ $ratecards->id.$rating[0]->id }}" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target=".bs-example1-modal-md{{ $ratecards->id.$rating[0]->id.$j }}" style="cursor: pointer;">Edit</span></a>
                                                    </td>
                                                </tr>
                                                <?php $j++; ?>
                                            @endforeach
                                        @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                    @endif
                                    {{--{!! with(new Vanguard\Pagination\HDPresenter($adslot))->render() !!}--}}
                                </div>
                            </div>
                            <!-- /.tab-pane -->
                            <div class="tab-pane" id="nc">

                            </div>
                            <!-- /.tab-pane -->

                            <div class="tab-pane" id="ne">

                            </div>

                            <div class="tab-pane" id="nw">

                            </div>

                            <div class="tab-pane" id="se">

                            </div>

                            <div class="tab-pane" id="ss">

                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /.col -->
            </div>
            <?php $i=0; ?>
            @foreach($ratecard as $ratecards)
                @foreach($ratecards->adslots as $rating)
                    <div class="modal fade bs-example1-modal-md{{ $ratecards->id.$rating[0]->id.$i }}" tabindex="-1" role="dialog"  aria-labelledby="myLargeModalLabel">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content" style="padding: 7%">
                                <h3 align="center">All / {{ $ratecards->day->day }}</h3>
                                <form method="POST" action="{{ route('adslot.update', ['ratecard_id' => $ratecards->id]) }}" class="selsec" style="margin-left:">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="discount">Percentage Premium %</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="premium_percent" class="form-control">
                                        </div>
                                    </div>
                                    @foreach($rating as $r)
                                        {{--{{ dd($r) }}--}}
                                        <p align="center">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <td>{{ $r->time_in_seconds }}</td> Seconds
                                                <input type="hidden" name="time[]" value="{{ $r->time_in_seconds }}">
                                            </div>
                                            <div class="col-md-5">
                                                <input type="hidden" name="hourly_range_id" value="{{ $ratecards->hourly_range_id->id }}">
                                                <input type="hidden" name="ratecard_id" value="{{ $ratecards->id }}">
                                                <input type="hidden" name="is_premium" value="{{ $r->is_premium }}">
                                                <input type="hidden" name="day" value="{{ $ratecards->day->id }}">
                                                <input type="hidden" name="region_id" value="{{ $r->region->id }}">
                                                <input type="hidden" name="target_audience_id" value="{{ $r->target_audience->id }}">
                                                <input type="hidden" name="day_part_id" value="{{ $r->day_parts->id }}">
                                                <input type="hidden" name="from_to_time" value="{{ $r->from_to_time }}">
                                                <input type="hidden" name="adslot_id[]" id="adslot_id" value="{{ $r->id }}">
                                                <input type="hidden" name="min_age" value="{{ $r->min_age }}">
                                                <input type="hidden" name="max_age" value="{{ $r->max_age }}">
                                                <input type="text" name="price[]" class="form-control" value="{{ $r->price }}" />
                                                <input type="hidden" name="from_to_time" value="{{ $r->from_to_time }}">
                                            </div>
                                        </div>
                                        </p>
                                    @endforeach
                                    <?php $i++; ?>
                                    <p align="center">
                                        <button  class="btn btn-large btn-danger" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Cancel</button>
                                        <button id="update_adslot" type="submit" class="btn btn-large btn-success" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Save</button>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach


            {{--</div>--}}
        <!-- /.content -->
            @stop

            @section('scripts')
                {!! HTML::script('assets/js/moment.min.js') !!}
                {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}

                <script>
                    $(document).ready(function(){
                        $("body").delegate(".by_region", "click", function(){
                            var id = $(".by_region").attr("region_id");
                            console.log(id);
                        });
                    });

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
                <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
                <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop