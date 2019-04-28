@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain" id="load_this">
        <!-- header -->
        @if(Session::get('broadcaster_id'))
            @include('partials.new-frontend.broadcaster.header')
            @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        @else
            @include('partials.new-frontend.agency.header')
        @endif
        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Stations & Programs</h2>
            </div>
        </div>
    <div>

    <mediaplan-suggested-plans-component :plan-id="{{ json_encode($mediaPlanId) }}" :plan-status="{{ $mediaPlanStatus }}" :prev-route="{{ json_encode(url('/')) }}" :next-route="{{ json_encode(route('agency.media_plan.create',['id'=>$mediaPlanId])) }}" :selected-filters="{{ json_encode($selectedFilters) }}" :filter-values="{{ json_encode($filterValues) }}" :suggestions="{{ $fayaFound['stations'] }}" :selected-suggestions="{{ json_encode($fayaFound['selected']) }}" :graph-days="{{ json_encode($fayaFound['days']) }}" :graph-details="{{ $fayaFound['total_graph'] }}"></mediaplan-suggested-plans-component>

    <!-- <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-5">
                                <p class="uppercased weight_medium mt2">Available Stations and Times</p>
                            </div>
                            <div class="col-md-7">
                                <form method="POST" action="" id="filter-form">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="mediaPlanId" value="{{ $mediaPlanId }}">
                                    <div class="col_3 column">
                                        <label for="station_type">Station Type</label>
                                        <select name="station_type" id="station_type">
                                            @foreach($filterValues['station_type'] as $station_type)
                                                <option value="{{$station_type}}" @if(isset($selectedFilters['station_type']) && $selectedFilters['station_type'] === $station_type) selected="selected" @endif>{{$station_type}}</option>
                                            @endforeach
                                        </select>                    
                                    </div>
                                    <div class="col_2 column">
                                        <label for="days">Days</label>
                                        <select name="days" id="days">
                                            <option value="all" selected="true">All</option>
                                            @foreach ($filterValues['days'] as $day)
                                                <option value="{{$day}}" @if(isset($selectedFilters['days']) && $selectedFilters['days'] === $day) selected="selected" @endif>{{$day}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col_2 column">
                                        <label for="states">States</label>
                                        <select name="states" id="states">
                                            <option value="all" selected="true">All</option>
                                            @foreach ($filterValues['state_list'] as $state)
                                            <option value="{{$state}}" @if(isset($selectedFilters['states']) && $selectedFilters['states'] === $state) selected="selected" @endif>{{$state}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col_2 column">
                                        <label for="day_parts">Day Parts</label>
                                        <select name="day_parts" id="day_parts">
                                            <option value="all" selected="true">All</option>
                                            @foreach($filterValues['day_parts'] as $day_part)
                                                <option value="{{$day_part}}" @if(isset($selectedFilters['day_parts']) && $selectedFilters['day_parts'] === $day_part) selected="selected" @endif>{{$day_part}}</option>
                                            @endforeach
                                        </select>                    
                                    </div>
                                    <div class="col_3 column">
                                        <button type="submit" class="filter-btn" id="filter-btn"><i class="material-icons">search</i>FILTER</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>


                    <div class="card-body">

                    </div>
                    
                </div>
            </div>
        </div>
        <section>
        </section>
    </div> -->

    <div class="the_frame client_dets mb1">
    
    

<!-- end -->
    </div>

  </div><!-- be -->

  <div>


    

    </div> <!-- be -->

    </div>
@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>
        $('#view-graph').addClass('inactive-dashboard-toggle-btn');
        function goBack() {
            window.history.back();
        }
        function formatNumber(num) {
          return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1,')
        }
    </script>

     <script src="{{asset('new_frontend/js/aria-accordion.js')}}"></script>
    <script>
      $(document).ready(function () {
        'use strict';

             //var accc = document.getElementById('accordion');
            // accc.style.display = "";  

        $('#timebelts-graph').hide();
        $('#view-table').on('click', function() {
            $('#timebelts-graph').hide();
            $('#timebelts-table').show();
            $('#view-table').removeClass('inactive-dashboard-toggle-btn');
            $('#view-graph').addClass('inactive-dashboard-toggle-btn');
        });

        $('#view-graph').on('click', function() {
            $('#timebelts-table').hide();
            $('#timebelts-graph').show();
            $('#view-graph').removeClass('inactive-dashboard-toggle-btn');
            $('#view-table').addClass('inactive-dashboard-toggle-btn');
        });

        // The submission button for the filters


            $('#view-Mon').show();
             $('#view-Tue').hide();
        	$('#view-Wed').hide();
            $('#view-Thu').hide();
        	$('#view-Fri').hide();
            $('#view-Sat').hide();
            $('#view-Sun').hide();

            
             $('#day-Tue').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').addClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').addClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').addClass('inactive-dashboard-toggle-btn');
             $('#day-Mon').removeClass('inactive-dashboard-toggle-btn');

        $('#day-Mon').on('click', function() {

             $('#view-Tue').hide();
        	$('#view-Wed').hide();
            $('#view-Thu').hide();
        	$('#view-Fri').hide();
            $('#view-Sat').hide();
            $('#view-Sun').hide();
                $('#view-Mon').show();


             $('#day-Tue').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').addClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').addClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').addClass('inactive-dashboard-toggle-btn');
             $('#day-Mon').removeClass('inactive-dashboard-toggle-btn');


                });
        $('#day-Tue').on('click', function() {

             $('#view-Mon').hide();
        	$('#view-Wed').hide();
            $('#view-Thu').hide();
        	$('#view-Fri').hide();
            $('#view-Sat').hide();
            $('#view-Sun').hide();
                $('#view-Tue').show();



            $('#day-Mon').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').addClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').addClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').addClass('inactive-dashboard-toggle-btn');
             $('#day-Tue').removeClass('inactive-dashboard-toggle-btn');

                });
        $('#day-Wed').on('click', function() {

             $('#view-Mon').hide();
             $('#view-Tue').hide();
            $('#view-Thu').hide();
        	$('#view-Fri').hide();
            $('#view-Sat').hide();
            $('#view-Sun').hide();
            $('#view-Wed').show();


             $('#day-Mon').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').removeClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').addClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').addClass('inactive-dashboard-toggle-btn');
             $('#day-Tue').addClass('inactive-dashboard-toggle-btn');
           
        });
        $('#day-Thu').on('click', function() {
            $('#view-Mon').hide();
             $('#view-Tue').hide();
        	$('#view-Wed').hide();
        	$('#view-Fri').hide();
            $('#view-Sat').hide();
            $('#view-Sun').hide();
            $('#view-Thu').show();



             $('#day-Mon').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').addClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').removeClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').addClass('inactive-dashboard-toggle-btn');
             $('#day-Tue').addClass('inactive-dashboard-toggle-btn');
        });

        $('#day-Fri').on('click', function() {
            $('#view-Mon').hide();
             $('#view-Tue').hide();
        	$('#view-Wed').hide();
            $('#view-Thu').hide();
            $('#view-Sat').hide();
            $('#view-Sun').hide();
            $('#view-Fri').show();

               $('#day-Mon').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').addClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').addClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').removeClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').addClass('inactive-dashboard-toggle-btn');
             $('#day-Tue').addClass('inactive-dashboard-toggle-btn');
           
           
        });
        $('#day-Sat').on('click', function() {
            $('#view-Mon').hide();
             $('#view-Tue').hide();
        	$('#view-Wed').hide();
            $('#view-Thu').hide();
        	$('#view-Fri').hide();
            $('#view-Sun').hide();
            $('#view-Sat').show();

             $('#day-Mon').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').addClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').addClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').removeClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').addClass('inactive-dashboard-toggle-btn');
             $('#day-Tue').addClass('inactive-dashboard-toggle-btn');

        });

        $('#day-Sun').on('click', function() {
            $('#view-Mon').hide();
             $('#view-Tue').hide();
        	$('#view-Wed').hide();
            $('#view-Thu').hide();
        	$('#view-Fri').hide();
            $('#view-Sat').hide();
            $('#view-Sun').show();

             $('#day-Mon').addClass('inactive-dashboard-toggle-btn');
             $('#day-Wed').addClass('inactive-dashboard-toggle-btn');
             $('#day-Thu').addClass('inactive-dashboard-toggle-btn');
             $('#day-Fri').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sat').addClass('inactive-dashboard-toggle-btn');
             $('#day-Sun').removeClass('inactive-dashboard-toggle-btn');
             $('#day-Tue').addClass('inactive-dashboard-toggle-btn');
           
        });

        // This will submit the filters and reload the page
        $("#filter-form").on('submit', function(e) {
            event.preventDefault(e);
            $('.load_this_div').css({opacity : 0.2});
            var formdata = $("#filter-form").serialize();
            $.ajax({
                cache: false,
                type: "POST",
                url: '/agency/media-plan/customise-filter',
                dataType: 'json',
                data: formdata,
                beforeSend: function(data) {
                    var toastr_options = {
                        "preventDuplicates": true,
                        "tapToDismiss": false,
                        "hideDuration": "1",
                        "timeOut": "300000000", //give a really long timeout, we should be done before that
                    }
                    var msg = "Setting up filters, please wait"
                    toastr.info(msg, null, toastr_options)
                },
                success: function (data) {
                    toastr.clear();
                    if (data.status === 'success') {
                        toastr.success("Filters set, retrieving results");
                        location.href = '/agency/media-plan/customise/' + data.redirect_url;
                    } else {
                        toastr.error('An unknown error has occurred, please try again');
                        $('.load_this_div').css({opacity: 1});
                        return;
                    }
                },
                error : function (xhr) {
                    toastr.clear();
                    if(xhr.status === 500){
                        toastr.error('An unknown error has occurred, please try again');
                        $('.load_this_div').css({opacity: 1});
                        return;
                    }else{
                        toastr.error('An unknown error has occurred, please try again');
                        $('.load_this_div').css({opacity: 1});
                        return;
                    }
                }
            })
        });


        $(window).on('ariaAccordion.initialised', function (event, element) {
          console.log('initialised');
        });

        $('.accordion-group').first().ariaAccordion({
          contentRole: ['document', 'application', 'document'],
          slideSpeed: 400
        });

        $('.accordion-group').eq(1).ariaAccordion({
          contentRole: ['document', 'application', 'document'],
          expandOnlyOne: true,
          slideSpeed: 800
        });

        $('.accordion-group').last().ariaAccordion({
          slideSpeed: 200
        });
      });
    </script>
     <script type="text/javascript">
        $(document).ready(function() {
            var url = window.location.href;
            var trim = url.split('/');
            var fifthSegment = trim[6];

            var step = 1;
            var plans = [];
            var dplans = [];

            function movePlanByDuration(prog_id, key, plan_station, plan_programe, plan_time, plan_day, plan_aud){

                var ids = [];
                    var children = document.getElementById("cont").children; //get container element children.
                    for (var i = 0, len = children.length ; i < len; i++) {
                        children[i].className = 'new-class'; //change child class name.
                        ids.push(children[i].id); //get child id.
                    } 
                    ids.includes(key)
                    if(ids.includes(key)){
                        toastr.error("Already selected");
                        return;
                    }
               
              
               var trim = plan_station.split('/');
                        plan_station = trim[0];

                 var new_html = "";
                 let element = document.getElementById("selectedProgTable");


				 new_html += '<tr  class="ri'+prog_id+'"  id="'+key+'">'+
				 '<td>  <input type="hidden" id="dunique'+ prog_id +'" value="'+key+'"/>'+  plan_station  +' </td>'+
                 '<td class="center">'+plan_day+'</td>'+
                 '<td class="center">'+plan_time+'</td>'+
				 '<td class="center">'+plan_programe+'</td>'+
				 '<td class="center">'+plan_aud+'</td>'+
					'<td class="center"><button data-programm="'+prog_id+'" class="plus-btn dBtn" data_15="first15" type="button"><i class="material-icons" style="color: red">delete</i></button>'+
					 '</td>'+
					 '</td>'+
					 '</tr>';
                    let options = {
                        block: "start",
                        inline: "nearest",
                        behaviour: "smooth"
                    }
                    // element.scrollIntoView(options);
                    $(".where-it-is-going").append(new_html);
                    //document.getElementById("ri"+prog_id).classList.add('highlight');
                    let successMsg = plan_station+' - '+plan_programe+' showing on '+plan_day+''+plan_time+' Added successfully';
                    toastr.success(successMsg);
            }
<?php 

$result = json_decode($fayaFound['total_graph'], true);
foreach($result as $key => $Value){  ?>

Highcharts.setOptions({
    lang: {
      decimalPoint: '.',
      thousandsSep: ','
    }
});

     Highcharts.chart('container<?php echo $key;?>', {
  chart: {
      type: 'line',
  },
  title: {
      text: ''
  },
  plotOptions: {
    column: {
            stacking: 'normal',
            dataLabels: {
            		format: '{point.y:,.2f} $us',
                enabled: true,
                color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
            }
        },
              series: {
                  point: {
                      events: {
                          click: function () {
                              var trim = this.category.substring(0,5)
                            //   var day =this.series.name.slice(-3);
                              var name = this.series.name;
                               var Segment = name.split('/');
                              var day =Segment[1];
                              var vid =fifthSegment+day+ this.y + trim.replace(/[:]/g, '');
                              console.log(vid)
                              $('#'+vid).prop('disabled', true);
                              var pla_programe = $("#prog"+vid).text();
                              var ke = $("#unique"+vid).val();
                            if(this.y <1){
                                toastr.error("Cant select zero populations")
                            }else {
                                movePlanByDuration(vid, ke, this.series.name, pla_programe, this.category, day, this.y);
                            }
                                    
                          }
                      }
                  },
              }
          },
  subtitle: {
      text: ' Time belt'
  },
  xAxis: {
        title: {
            text: 'Time Belt'
        },
        categories: ["00:00 - 00:15","00:15 - 00:30", "00:30 - 00:45", "00:45 - 01:00", "01:00 - 01:15", "01:15 - 01:30", "01:30 - 01:45", "01:45 - 02:00", "02:00 - 02:15", "02:15 - 02:30", "02:30 - 02:45", "02:45 - 03:00","03:00 - 00:15", "03:15 - 03:30", "03:30 - 03:45", "03:45 - 04:00","04:00 - 04:15", "04:15 - 04:30", "04:30 - 04:45", "04:45 - 05:00","05:00 - 00:15", "05:15 - 05:30", "05:30 - 05:45", "05:45 - 06:00", "06:00 - 06:15","06:15 - 06:30", "06:30 - 06:45", "06:45 - 07:00", "07:00 - 07:15", "07:15 - 07:30", "07:30 - 07:45", "07:45 - 08:00", "08:00 - 08:15", "08:15 - 08:30", "08:30 - 08:45", "08:45 - 09:00","09:00 - 00:15", "09:15 - 09:30", "09:30 - 09:45", "09:45 - 10:00","10:00 - 10:15", "10:15 - 10:30", "10:30 - 10:45", "10:45 - 11:00","11:00 - 11:15", "11:15 - 11:30", "11:30 - 11:45", "11:45 - 12:00","12:00 - 12:15","12:15 - 12:30", "12:30 - 12:45", "12:45 - 13:00", "13:00 - 13:15", "13:15 - 13:30", "13:30 - 13:45", "13:45 - 14:00","14:00 - 14:15", "14:15 - 14:30", "14:30 - 14:45", "14:45 - 15:00","15:00 - 15:15", "15:15 - 15:30", "15:30 - 15:45", "15:45 - 16:00","16:00 - 16:15", "16:15 - 16:30", "16:30 - 16:45", "16:45 - 17:00", "17:00 - 17:15","17:15 - 17:30", "17:30 - 17:45", "17:45 - 18:00", "18:00 - 18:15", "18:15 - 18:30", "18:30 - 18:45", "18:45 - 19:00", "19:00 - 19:15", "19:15 - 19:30", "19:30 - 19:45", "19:45 - 20:00","20:00 - 20:15", "20:15 - 20:30", "20:30 - 20:45", "20:45 - 21:00","21:00 - 21:15", "21:15 - 21:30", "21:30 - 21:45", "21:45 - 22:00","22:00 - 22:15", "22:15 - 22:30", "22:30 - 22:45", "22:45 - 23:00", "23:00 - 23:15", "23:15 - 23:30", "23:30 - 23:45", "23:45 - 00:00"],
        min: 0,
        max: 96
    },
  yAxis: {
      title: {
          text: 'Audience'
      },
      stackLabels: {
            enabled: true,
            format: '{total:,.f}'
        },
      labels: {
          format: "{value:,.f}",
      }
  }, 
  series: [
      
      <?php 
      $cars = array("00:00 - 00:15"=> 0,"00:15 - 00:30 "=> 0, "00:30 - 00:45"=> 0, "00:45 - 01:00"=> 0, "01:00 - 00:15"=> 0, "01:15 - 01:30"=> 0, "01:30 - 01:45"=> 0, "01:45 - 02:00"  => 0, "02:00 - 02:15"  => 0, "02:15 - 02:30"  => 0, "02:30 - 02:45"  => 0, "02:45 - 03:00"  => 0 ,"03:00 - 00:15"  => 0, "03:15 - 03:30"  => 0, "03:30 - 03:45"=> 0, "03:45 - 04:00"=> 0,"04:00 - 04:15"=> 0, "04:15 - 04:30"=> 0, "04:30 - 04:45"=> 0, "04:45 - 05:00"=> 0,"05:00 - 05:15"  => 0, "05:15 - 05:30"  => 0, "05:30 - 05:45"  => 0, "05:45 - 06:00"  => 0, "06:00 - 06:15"  => 0,"06:15 - 06:30"  => 0, "06:30 - 06:45"  => 0, "06:45 - 07:00"  => 0, "07:00 - 07:15"  => 0, "07:15 - 07:30"  => 0, "07:30 - 07:45"  => 0, "07:45 - 08:00"  => 0, "08:00 - 08:15"  => 0, "08:15 - 08:30"  => 0, "08:30 - 08:45"  => 0, "08:45 - 09:00"  => 0 ,"09:00 - 09:15"  => 0, "09:15 - 09:30"  => 0, "09:30 - 09:45"  => 0, "09:45 - 10:00"  => 0,"10:00 - 10:15"  => 0, "10:15 - 10:30"  => 0, "10:30 - 10:45"  => 0, "10:45 - 11:00"  => 0,"11:00 - 11:15"  => 0, "11:15 - 11:30"  => 0, "11:30 - 11:45"  => 0, "11:45 - 12:00"  => 0, "12:00 - 12:15"  => 0,"12:15 - 12:30"  => 0, "12:30 - 12:45"  => 0, "12:45 - 13:00"  => 0, "13:00 - 13:15"  => 0, "13:15 - 13:30"  => 0, "13:30 - 13:45"  => 0, "13:45 - 14:00"  => 0 ,"14:00 - 14:15"  => 0, "14:15 - 14:30"  => 0, "14:30 - 14:45"  => 0, "14:45 - 15:00"  => 0,"15:00 - 15:15"  => 0, "15:15 - 15:30"  => 0, "15:30 - 15:45"  => 0, "15:45 - 16:00"  => 0,"16:00 - 16:15"  => 0, "16:15 - 16:30"  => 0, "16:30 - 16:45"  => 0, "16:45 - 17:00"  => 0, "17:00 - 17:15"  => 0,"17:15 - 17:30"  => 0, "17:30 - 17:45"  => 0, "17:45 - 18:00"  => 0, "18:00 - 18:15"  => 0, "18:15 - 18:30"  => 0, "18:30 - 18:45"  => 0, "18:45 - 19:00"  => 0, "19:00 - 19:15"  => 0, "19:15 - 19:30"  => 0, "19:30 - 19:45"  => 0, "19:45 - 20:00"  => 0 ,"20:00 - 20:15"  => 0, "20:15 - 20:30"  => 0, "20:30 - 20:45"  => 0, "20:45 - 21:00"  => 0,"21:00 - 21:15"  => 0, "21:15 - 21:30"  => 0, "21:30 - 21:45"  => 0, "21:45 - 22:00"  => 0,"22:00 - 22:15"  => 0, "22:15 - 22:30"  => 0, "22:30 - 22:45"  => 0, "22:45 - 23:00"  => 0, "23:00 - 23:15"  => 0, "23:15: - 23:30"  => 0, "23:30 - 23:45"  => 0, "23:45 - 00:00" => 0);

      foreach($Value as $station => $value){ 
            $array = array();
            $dates = array();
         foreach($value as $some){
            $dates[substr($some['start_time'], 0, 5) . " - ". substr($some['end_time'], 0, 5)] =  $some['total_audience'];
         }  
         $ar3 = array_merge($cars,  $dates);
         $keys = array_keys($ar3);
         for($i=0; $i < count($keys); ++$i) {
             $array[] = $ar3[$keys[$i]];
         }?>
      {
      name: '<?php echo $station. "/". $key; ?>',
      data: <?php echo json_encode($array); ?>,
      showInLegend:true,
  },
  <?php } ?>
  ]
});

<?php } ?>



 })
</script>




@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <style>
        .graph_option {
            transition: 0.3s;
        }
        .graph_option:hover {
            cursor: pointer;
            background: #cfd8dc;
        }
    </style>
@stop