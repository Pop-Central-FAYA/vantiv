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
                <h2 class="sub_header">Stations and Programmes</h2>
            </div>
        </div>

        <div class="clearfix mb4 _shrink-sides">
            <div class="column col_6">
                <button id="view-table"  class="btn full block_disp uppercased align_center">Table</button>
            </div>

            <div class="column col_6">
                <button id="view-graph"  class="btn full block_disp uppercased align_center">Graph</button>
            </div>
        </div>

    <div>

    <div class="the_frame client_dets mb4">
    
    <div class="filters border_bottom clearfix">
        <div class="column col_6 p-t">
            <div class="column col_6">
                <p class="uppercased weight_medium mt2">Available Stations and Times</p>
            </div>
        </div>
        <div class="column col_6 clearfix">
            <form method="POST" action="" id="filter-form">
                {{ csrf_field() }}
                <input type="hidden" name="mediaPlanId" value="{{ $mediaPlanId }}">
                <div class="col_3 column">
                    <label for="days">Days</label>
                    <select name="days" id="days">
                        <option value="all" selected="true">All</option>
                        @foreach ($filterValues['days'] as $day)
                            <option value="{{$day}}" @if(isset($selectedFilters['days']) && $selectedFilters['days'] === $day) selected="selected" @endif>{{$day}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col_3 column">
                    <label for="states">States</label>
                    <select name="states" id="states">
                        <option value="all" selected="true">All</option>
                        @foreach ($filterValues['state_list'] as $state)
                        <option value="{{$state}}" @if(isset($selectedFilters['states']) && $selectedFilters['states'] === $state) selected="selected" @endif>{{$state}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col_3 column">
                    <label for="day_parts">Day Parts</label>
                    <select name="day_parts" id="day_parts">
                        <option value="all" selected="true">All</option>
                        @foreach($filterValues['day_parts'] as $day_part)
                            <option value="{{$day_part}}" @if(isset($selectedFilters['day_parts']) && $selectedFilters['day_parts'] === $day_part) selected="selected" @endif>{{$day_part}}</option>
                        @endforeach
                    </select>                    
                </div>
                <div class="col_3 column">
                    <button type="submit" class="filter-btn" id="filter-btn"><i class="material-icons left">search</i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- campaigns table -->
    <div id="timebelts-table" class="accordion-group scroll-y">
    <table class="display default_mpo filter_mpo fixed_headers" id="default_mpo_table">
        <thead>
            <tr>
                {{-- <th width="25%">All</th> --}}
                <th>Station</th>
                <th width="50%">Audience</th>
                {{-- <th width="25%"> </th> --}}
            </tr>
        </thead>
     </table>
    @foreach($fayaFound['stations'] as $key => $Value)
        @php 
            $sum_audience = $Value->sum('total_audience'); 
        @endphp

        <section class="accordion-group__accordion">
            <header class="accordion-group__accordion-head">
                <table lass="display default_mpo filter_mpo" id="default_mpo_table">
                    <tbody class="accordion-group__accordion-btn">
                        <tr class="clickable">
                            {{-- <td width="25%"><input type="checkbox" /> </td> --}}
                            <td width="">{{ $key }}</td>
                            <td width="50%">
                            {{ number_format($sum_audience) }}
                            </td>
                            {{-- <td width="25%">
                                <button class="btn small_btn"> Details </button>
                            </td> --}}   
                        </tr>
                    </tbody>
                </table>
            </header>
            <div class="accordion-group__accordion-panel">
                <div class="accordion-group__accordion-content" style="overflow-x: hidden; background: #b0bec5;">
                    <table class="table table-hover" style="margin-left: 45px">
                        <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time Belt</th>
                                <th>Program</th>
                                <th>Audience</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            
                            @foreach($Value as $Programe)

                            <tr>
                            @php 
                                $vid =$Programe->media_plan_id. $Programe->day. $Programe-> total_audience. str_replace(':', '', $Programe-> start_time) ;
                             @endphp
                                {{-- <td id="stat{{ $vid }}"> <input type="hidden" id="unique{{ $vid }}" value="{{ $Programe->id}}"  /> {{ $Programe->station }}</td> --}}
                                <td id="day{{ $vid }}"class="center">{{ $Programe->day}} </td>
                                <td id="time{{ $vid }}" class="center">{{ substr($Programe->start_time,0,5)}} - {{ substr($Programe->end_time,0,5)}}</td>
                                <td id="prog{{ $vid }}"class="center">{{ $Programe->program }}</td>
                                <td id="aud{{$vid }}"class="center">{{ number_format($Programe->total_audience)}}</td>
                                <td class="center">
                               
                                    <button data-program="{{$vid}}" class="plus-btn aBtn" data_15="first15" id="{{$vid}}" type="button"><i class="material-icons">add</i></button>	
                                </td>
                            </tr>
                            @endforeach
                        </tbody>                   
                    </table>                        
                </div>
            </div>
          </section>
      
        @endforeach

    </div>
              

<!-- end -->
    </div>

  </div><!-- be -->

  <div>

    <div id="timebelts-graph" class="the_frame client_dets mb4" style="display:none">

    <div class="filters border_bottom clearfix">
    <div class="column col_8 p-t">
        <p class="uppercased weight_medium">Graph</p>
    </div>
    <div class="column col_4 clearfix">

    </div>
 
    </div>

        <div class="accordion-group">
            @foreach($fayaFound['total_graph'] as $key => $Value)
                <section class="accordion-group__accordion">
                    <header class="accordion-group__accordion-head">
                        <div class="filters border_bottom clearfix accordion-group__accordion-btn graph_option">
                            <div class="column col_8 p-t hover">
                                <p class="uppercased weight_medium">{{ $key }}</p>
                            </div>
                        </div>

                        <div class="accordion-group__accordion-panel">
                            <div class="accordion-group__accordion-content">
                                <div id="container{{ $key }}" style="min-width: 310px; height: 500px; padding: 30 auto"></div>
                            </div>
                        </div>
                    </header>
                </section>
            @endforeach
        </div>

    </div>

    </div> <!-- be -->

    <div class="the_frame client_dets mb4">

    <div class="filters border_bottom clearfix">
        <div class="column col_8 p-t">
            <p class="uppercased weight_medium">Selected Stations and Times</p>
        </div>
        <div class="column col_4 clearfix">
        </div>
    </div>

    <!-- campaigns table -->
    <table class="table table-striped table-bordered bootstrap-datatable" id="selectedProgTable">
        <thead>
            <tr>
                <th>Station</th>
                <th>Day</th>
                <th>Time Belt</th>
                <th>Program</th>
                <th>Audience</th>
                <th></th>
            </tr>
        </thead>     
        <tbody class="where-it-is-going" id = "cont" >
            @foreach($fayaFound['selected'] as $Programe)
            @php 
           $vid =$Programe->media_plan_id. $Programe->day. $Programe-> total_audience. str_replace(':', '', $Programe-> start_time) ;
            @endphp
            <tr class="ri{{$vid}}" id="{{ $Programe->id }} " >
                <td id="stat{{ $vid }}"> {{ $Programe->station }}</td>
                <td id="day{{ $vid }}"class="center">{{ $Programe->day}} </td>
                <td id="time{{ $vid }}" class="center">{{ substr($Programe->start_time,0,5) }} - {{ substr($Programe->end_time,0,5) }}
                </td>
                <td id="prog{{ $vid }}"class="center">{{ $Programe->program }}</td>
                <td id="aud{{$vid }}"class="center">{{ $Programe->total_audience}}</td>
                <td class="center">
                    <button data-programm="{{$vid}} " class="plus-btn dBtn" data_15="first15" type="button"><i class="material-icons" style="color: red">delete</i></button>	
                </td>
            </tr>
@endforeach
        </tbody>
    </table>  

    <!--  end -->

</div>
    <div class="action_footer client_dets mb4 mt4">
      <div class="col_6 column">
        <button type="button" id="back_btn" class="btn small_btn show" onclick="goBack()">Back</button>
      </div>
      <div class="col_6 column">
        <button type="button" id="mpo_filters" class="btn small_btn right show">Create Plan</button>
        <button type="button" id="save_progress" class="btn small_btn right save mr-2">Save</button>
      </div>
    </div>
    <br><br><br><br><br><br><br>

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
            $("body").delegate(".aBtn", "click", function() {
			   var value_button = $(this).attr("data_15");
			   var prog_id = $(this).data("program");
			   var plan_station = $("#stat"+prog_id).text();
               var plan_time = $("#time"+prog_id).text();
			   var plan_programe = $("#prog"+prog_id).text();
			   var plan_day = $("#day"+prog_id).text();
			   var plan_aud = $("#aud"+prog_id).text();
               var key = $("#unique"+prog_id).val();
			   movePlanByDuration(prog_id, key, plan_station, plan_programe,plan_time, plan_day, plan_aud);
                toastr.success('Added successfully');
			   $('#'+prog_id).prop('disabled', true);
               
            });


			$("body").delegate(".dBtn", "click", function() {
			 var value_button = $(this).attr("data_25");
			 var prog_id = $(this).data("programm")
             var ey = $("#dunique"+prog_id).val();
              $('#'+prog_id).prop('disabled', false);
              $( "tr" ).remove(".ri"+prog_id);
                toastr.success('Removed successfully');
		     });



			$("body").delegate(".save", "click", function() {
                    $("#load_this").css({
                        opacity : 0.2
                    });
                    var ids = [];
                    var children = document.getElementById("cont").children; //get container element children.
                    for (var i = 0, len = children.length ; i < len; i++) {
                        children[i].className = 'new-class'; //change child class name.
                        ids.push(children[i].id); //get child id.
                    }
                   $('.save').prop('disabled', true);
                   if(ids.length == 0 ){
                          toastr.error("Select the staion you want to add")
                       $("#load_this").css({
                           opacity : 1
                       });
                            $('.save').prop('disabled', false);  
                        }else{
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: "/agency/media-plan/select_plan",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "mediaplan": fifthSegment,
                                "data": JSON.stringify(ids)
                            },
                            beforeSend: function(data) {
                                // run toast showing progress
                                toastr_options = {
                                    "progressBar": true,
                                    // "showDuration": "300",
                                    "preventDuplicates": true,
                                    "tapToDismiss": false,
                                    "hideDuration": "1",
                                    "timeOut": "300000000"
                                };
                                msg = "Saving suggestion, please wait";
                                toastr.info(msg, null, toastr_options)
                            },
                            success:function(data){
                                toastr.clear();
                                if(data.status === 'success'){
                                    $('.save').prop('disabled', false);
                                    $("#load_this").css({
                                        opacity : 1
                                    });
                                    toastr.success("Plans successfully saved!")
                                }else{
                                    toastr.error("The current operation failed");
                                    $("#load_this").css({
                                        opacity : 1
                                    });
                                    $('.save').prop('disabled', false);
                                }
                            },
                            error : function (xhr) {
                                toastr.clear();
                                if(xhr.status === 500){
                                    toastr.error('An unknown error has occurred, please try again');
                                    $('.load_this').css({
                                        opacity: 1
                                    });
                                    $('.save').prop('disabled', false);
                                    return;
                                }else if(xhr.status === 503){
                                    toastr.error('The request took longer than expected, please try again');
                                    $('.load_this').css({
                                        opacity: 1
                                    });
                                    $('.save').prop('disabled', false);
                                    return;
                                }else{
                                    toastr.error('An unknown error has occurred, please try again');
                                    $('.load_this').css({
                                        opacity: 1
                                    });
                                    $('.save').prop('disabled', false);
                                    return;
                                }
                            }
                        });
                   }
   
		  });
            


			$("body").delegate(".show", "click", function() {
                $("#load_this").css({
                    opacity : 0.2
                });
                var ids = [];
                    var children = document.getElementById("cont").children; //get container element children.
                    for (var i = 0, len = children.length ; i < len; i++) {
                        children[i].className = 'new-class'; //change child class name.
                        ids.push(children[i].id); //get child id.
                    }
                $('.show').prop('disabled', true);                
                   if(ids.length == 0 ){
                       if ($('#where-it-is-going').length < 1) {
                      c

                            $('.show').prop('disabled', false); 
                     
                            }else{
                         location.href = '/agency/media-plan/createplan/'+fifthSegment;
                            }
                            
                        }else{
                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: "/agency/media-plan/select_plan",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "mediaplan": fifthSegment,
                                "data": JSON.stringify(ids)
                            },
                            beforeSend: function(data) {
                                // run toast showing progress
                                toastr_options = {
                                    "progressBar": true,
                                    // "showDuration": "300",
                                    "preventDuplicates": true,
                                    "tapToDismiss": false,
                                    "hideDuration": "1",
                                    "timeOut": "300000000"
                                };
                                msg = "Creating suggestion, please wait";
                                toastr.info(msg, null, toastr_options)
                            },
                            success:function(data){
                                toastr.clear();
                                if(data.status === 'success'){
                                    $('.save').prop('disabled', false);
                                    $("#load_this").css({
                                        opacity : 1
                                    });
                                    toastr.success("Plans successfully created!")
                                    setTimeout(function() {
                                        location.href = '/agency/media-plan/createplan/'+fifthSegment;
                                    }, 2000);
                                }else{
                                    toastr.error("The current operation failed");
                                    $("#load_this").css({
                                        opacity : 1
                                    });
                                    $('.save').prop('disabled', false);
                                }
                            },
                            error : function (xhr) {
                                toastr.clear();
                                if(xhr.status === 500){
                                    toastr.error('An unknown error has occurred, please try again');
                                    $('.load_this').css({
                                        opacity: 1
                                    });
                                    $('.save').prop('disabled', false);
                                    return;
                                }else if(xhr.status === 503){
                                    toastr.error('The request took longer than expected, please try again');
                                    $('.load_this').css({
                                        opacity: 1
                                    });
                                    $('.save').prop('disabled', false);
                                    return;
                                }else{
                                    toastr.error('An unknown error has occurred, please try again');
                                    $('.load_this').css({
                                        opacity: 1
                                    });
                                    $('.save').prop('disabled', false);
                                    return;
                                }
                            }
                        });
                    }

		  });

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
                 '<td class="center">'+plan_time+'</td>'+
                 '<td class="center">'+plan_day+'</td>'+
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
                    // document.getElementById("ri"+prog_id).classList.add('highlight');
         
            }
<?php 

$result = json_decode($fayaFound['total_graph'], true);
foreach($result as $key => $Value){  ?>

     Highcharts.chart('container<?php echo $key;?>', {
  chart: {
      type: 'line',
  },
  title: {
      text: 'Suggested'
  },
  plotOptions: {
              series: {
                  point: {
                      events: {
                          click: function () {
                              var trim = this.category.substring(0,8)
                              var day =this.series.name.slice(-3);

                              var vid =fifthSegment+day+ this.y + trim.replace(/[:]/g, '');
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
              categories: ["00:00:00 - 00:15:00","00:15:00 - 00:30:00", "00:30:00 - 00:45:00", "00:45:00 - 01:00:00", "01:00:00 - 00:15:00", "01:15:00 - 01:30:00", "01:30:00 - 01:45:00", "01:45:00 - 02:00:00", "02:00:00 - 02:15:00", "02:15:00 - 02:30:00", "02:30:00 - 02:45:00", "02:45:00 - 03:00:00","03:00:00 - 00:15:00", "03:15:00 - 03:30:00", "03:30:00 - 03:45:00", "03:45:00 - 04:00:00","04:00:00 - 00:15:00", "04:15:00 - 04:30:00", "04:30:00 - 04:45:00", "04:45:00 - 05:00:00","05:00:00 - 00:15:00", "05:15:00 - 05:30:00", "05:30:00 - 05:45:00", "05:45:00 - 06:00:00", "06:00:00 - 06:15:00","06:15:00 - 06:30:00", "06:30:00 - 06:45:00", "06:45:00 - 07:00:00", "07:00:00 - 07:15:00", "07:15:00 - 07:30:00", "07:30:00 - 07:45:00", "07:45:00 - 08:00:00", "08:00:00 - 08:15:00", "08:15:00 - 08:30:00", "08:30:00 - 08:45:00", "08:45:00 - 09:00:00","09:00:00 - 00:15:00", "09:15:00 - 09:30:00", "09:30:00 - 09:45:00", "09:45:00 - 10:00:00","10:00:00 - 10:15:00", "10:15:00 - 10:30:00", "10:30:00 - 10:45:00", "10:45:00 - 11:00:00","11:00:00 - 11:15:00", "11:15:00 - 11:30:00", "11:30:00 - 11:45:00", "11:45:00 - 12:00:00","12:00:00 - 12:15:00","12:15:00 - 12:30:00", "12:30:00 - 12:45:00", "12:45:00 - 13:00:00", "13:00:00 - 13:15:00", "13:15:00 - 13:30:00", "13:30:00 - 13:45:00", "13:45:00 - 14:00:00","14:00:00 - 14:15:00", "14:15:00 - 14:30:00", "14:30:00 - 14:45:00", "14:45:00 - 15:00:00","15:00:00 - 15:15:00", "15:15:00 - 15:30:00", "15:30:00 - 15:45:00", "15:45:00 - 16:00:00","16:00:00 - 16:15:00", "16:15:00 - 16:30:00", "16:30:00 - 16:45:00", "16:45:00 - 17:00:00", "17:00:00 - 17:15:00","17:15:00 - 17:30:00", "17:30:00 - 17:45:00", "17:45:00 - 18:00:00", "18:00:00 - 18:15:00", "18:15:00 - 18:30:00", "18:30:00 - 18:45:00", "18:45:00 - 19:00:00", "19:00:00 - 19:15:00", "19:15:00 - 19:30:00", "19:30:00 - 19:45:00", "19:45:00 - 20:00:00","20:00:00 - 20:15:00", "20:15:00 - 20:30:00", "20:30:00 - 20:45:00", "20:45:00 - 21:00:00","21:00:00 - 21:15:00", "21:15:00 - 21:30:00", "21:30:00 - 21:45:00", "21:45:00 - 22:00:00","22:00:00 - 22:15:00", "22:15:00 - 22:30:00", "22:30:00 - 22:45:00", "22:45:00 - 23:00:00", "23:00:00 - 23:15:00", "23:15:00 - 23:30:00", "23:30:00 - 23:45:00", "23:45:00 - 00:00:00"],
              min: 0,
              max: 96
          },
  yAxis: {
      title: {
          text: 'Audience'
      },
      labels: {
          formatter: function () {
              return this.value;
          }
      }
  }, 
  series: [
      
      <?php 
      $cars = array("00:00:00 - 00:15:00"=> 0,"00:15:00 - 00:30:00"=> 0, "00:30:00 - 00:45:00"=> 0, "00:45:00 - 01:00:00"=> 0, "01:00:00 - 00:15:00"=> 0, "01:15:00 - 01:30:00"=> 0, "01:30:00 - 01:45:00"=> 0, "01:45:00 - 02:00:00"  => 0, "02:00:00 - 02:15:00"  => 0, "02:15:00 - 02:30:00"  => 0, "02:30:00 - 02:45:00"  => 0, "02:45:00 - 03:00:00"  => 0 ,"03:00:00 - 00:15:00"  => 0, "03:15:00 - 03:30:00"  => 0, "03:30:00 - 03:45:00"=> 0, "03:45:00 - 04:00:00"=> 0,"04:00:00 - 04:15:00"=> 0, "04:15:00 - 04:30:00"=> 0, "04:30:00 - 04:45:00"=> 0, "04:45:00 - 05:00:00"=> 0,"05:00:00 - 05:15:00"  => 0, "05:15:00 - 05:30:00"  => 0, "05:30:00 - 05:45:00"  => 0, "05:45:00 - 06:00:00"  => 0, "06:00:00 - 06:15:00"  => 0,"06:15:00 - 06:30:00"  => 0, "06:30:00 - 06:45:00"  => 0, "06:45:00 - 07:00:00"  => 0, "07:00:00 - 07:15:00"  => 0, "07:15:00 - 07:30:00"  => 0, "07:30:00 - 07:45:00"  => 0, "07:45:00 - 08:00:00"  => 0, "08:00:00 - 08:15:00"  => 0, "08:15:00 - 08:30:00"  => 0, "08:30:00 - 08:45:00"  => 0, "08:45:00 - 09:00:00"  => 0 ,"09:00:00 - 09:15:00"  => 0, "09:15:00 - 09:30:00"  => 0, "09:30:00 - 09:45:00"  => 0, "09:45:00 - 10:00:00"  => 0,"10:00:00 - 10:15:00"  => 0, "10:15:00 - 10:30:00"  => 0, "10:30:00 - 10:45:00"  => 0, "10:45:00 - 11:00:00"  => 0,"11:00:00 - 11:15:00"  => 0, "11:15:00 - 11:30:00"  => 0, "11:30:00 - 11:45:00"  => 0, "11:45:00 - 12:00:00"  => 0, "12:00:00 - 12:15:00"  => 0,"12:15:00 - 12:30:00"  => 0, "12:30:00 - 12:45:00"  => 0, "12:45:00 - 13:00:00"  => 0, "13:00:00 - 13:15:00"  => 0, "13:15:00 - 13:30:00"  => 0, "13:30:00 - 13:45:00"  => 0, "13:45:00 - 14:00:00"  => 0 ,"14:00:00 - 14:15:00"  => 0, "14:15:00 - 14:30:00"  => 0, "14:30:00 - 14:45:00"  => 0, "14:45:00 - 15:00:00"  => 0,"15:00:00 - 15:15:00"  => 0, "15:15:00 - 15:30:00"  => 0, "15:30:00 - 15:45:00"  => 0, "15:45:00 - 16:00:00"  => 0,"16:00:00 - 16:15:00"  => 0, "16:15:00 - 16:30:00"  => 0, "16:30:00 - 16:45:00"  => 0, "16:45:00 - 17:00:00"  => 0, "17:00:00 - 17:15:00"  => 0,"17:15:00 - 17:30:00"  => 0, "17:30:00 - 17:45:00"  => 0, "17:45:00 - 18:00:00"  => 0, "18:00:00 - 18:15:00"  => 0, "18:15:00 - 18:30:00"  => 0, "18:30:00 - 18:45:00"  => 0, "18:45:00 - 19:00:00"  => 0, "19:00:00 - 19:15:00"  => 0, "19:15:00 - 19:30:00"  => 0, "19:30:00 - 19:45:00"  => 0, "19:45:00 - 20:00:00"  => 0 ,"20:00:00 - 20:15:00"  => 0, "20:15:00 - 20:30:00"  => 0, "20:30:00 - 20:45:00"  => 0, "20:45:00 - 21:00:00"  => 0,"21:00:00 - 21:15:00"  => 0, "21:15:00 - 21:30:00"  => 0, "21:30:00 - 21:45:00"  => 0, "21:45:00 - 22:00:00"  => 0,"22:00:00 - 22:15:00"  => 0, "22:15:00 - 22:30:00"  => 0, "22:30:00 - 22:45:00"  => 0, "22:45:00 - 23:00:00"  => 0, "23:00:00 - 23:15:00"  => 0, "23:15:00 - 23:30:00"  => 0, "23:30:00 - 23:45:00"  => 0, "23:45:00 - 00:00:00" => 0);

      foreach($Value as $station => $value){ 
            $array = array();
            $dates = array();
         foreach($value as $some){
            $dates[$some['start_time'] . " - ". $some['end_time']] = $some['total_audience'];
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
