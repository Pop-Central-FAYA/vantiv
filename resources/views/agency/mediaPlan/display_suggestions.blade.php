@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @if(Session::get('broadcaster_id'))
            @include('partials.new-frontend.broadcaster.header')
            @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        @else
            @include('partials.new-frontend.agency.header')
        @endif
   

<button id="hide">Hide</button>
<button id="show">Show</button>

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Stations and Programmes</h2>
            </div>
        </div>


        <div>
<div class="the_frame client_dets mb4">
    
    <div class="filters border_bottom clearfix">
        <div class="column col_6 p-t">
            <p class="uppercased weight_medium mt2">Available Stations and Times</p>
        </div>
        <div class="column col_6 clearfix">
            <div class="col_3 column">
                <label for="days">Days</label>
                <select name="" id="days">
                    <option value="monday">Monday</option>
                    <option value="tuesday">Tuesday</option>
                    <option value="wednesday">Wednesday</option>
                    <option value="thursday">Thursday</option>
                    <option value="friday">Friday</option>
                    <option value="saturday">Saturday</option>
                    <option value="sunday">Sunday</option>
                </select>
            </div>
            <div class="col_3 column">
                <label for="days">States</label>
                <select name="" id="days">
                    @foreach ($filterValues['state_list'] as $state)
                    <option value="{{ $state }}">{{ $state }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col_3 column">
                <label for="days">Time</label>
                <select name="" id="days">
                    @foreach($filterValues['day_parts'] as $day_part)
                        <option value="{{ $day_part }}">{{ $day_part }}</option>
                    @endforeach
                    <option value="">30mins</option>
                </select>                    
            </div>
            <div class="col_3 column">
                <button class="filter-btn" id="filter-btn"><i class="material-icons left">search</i>Filter</button>
            </div>
        </div>
    </div>

<!-- campaigns table -->
<div class="accordion-group scroll-y">
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
                            {{ $sum_audience}}
                            </td>
                            {{-- <td width="25%">
                                <button class="btn small_btn"> Details </button>
                            </td> --}}   
                        </tr>
                    </tbody>
                </table>
            </header>
            <div class="accordion-group__accordion-panel">
                <div class="accordion-group__accordion-content">
                    <table class="table table-hover">
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
                        <tbody>
                            
                            @foreach($Value as $Programe)

                            <tr>
                            @php 
                                $vid =$Programe->media_plan_id. $Programe->day. $Programe-> total_audience. str_replace(':', '', $Programe-> start_time) ;
                             @endphp
                                <td id="stat{{ $vid }}"> <input type="hidden" id="unique{{ $vid }}" value="{{ $Programe ->id}}"  /> {{ $Programe->station }}</td>
                                <td id="day{{ $vid }}"class="center">{{ $Programe ->day}} </td>
                                <td id="time{{ $vid }}" class="center">{{ $Programe -> start_time}} - {{ $Programe -> end_time}}</td>
                                <td id="prog{{ $vid }}"class="center">{{ $Programe -> program }}</td>
                                <td id="aud{{$vid }}"class="center">{{ $Programe ->total_audience}}</td>
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

<div class="the_frame client_dets mb4">

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
        <div class="filters border_bottom clearfix accordion-group__accordion-btn">
                    <div class="column col_8 p-t">
                        <p class="uppercased weight_medium">{{ $key }}</p>
                    </div>
                
        </div>

        <div class="accordion-group__accordion-panel">
        <div class="accordion-group__accordion-content">
        
        <div id="container{{ $key }}" style="min-width: 310px; height: 500px; padding: 30 auto"></div>




        </div>
        </div>
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
{{--             <div class="col_5 column">
                <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
            </div>

            <div class="col_5 column">
                <input type="text" name="stop_date" class="flatpickr" placeholder="End Date">
            </div>

            <div class="col_1 column">
                <button type="button" id="" class="btn small_btn">Filter</button>
            </div> --}}
        </div>
    </div>

    <!-- campaigns table -->
    <table class="table table-striped table-bordered bootstrap-datatable" id="selectedProgTable">
        <thead>
            <tr>
                <th>Station</th>
                <th>Time Belt</th>
                <th>Day</th>
                <th>Program</th>
                <th>Audience</th>
                <th>Actions </th>
              
            </tr>
        </thead>     
        <tbody class="where-it-is-going" id = "cont" >
        @foreach($fayaFound['selected'] as $Programe)
        @php 
       $vid =$Programe->media_plan_id. $Programe->day. $Programe-> total_audience. str_replace(':', '', $Programe-> start_time) ;
     @endphp
   <tr class="ri{{$vid}}" id="{{$Programe -> id}} " >
    <td id="stat{{ $vid }}"> {{ $Programe->station }}</td>
    <td id="time{{ $vid }}" class="center">{{ $Programe -> start_time}} - {{ $Programe -> end_time}}</td>
    <td id="day{{ $vid }}"class="center">{{ $Programe ->day}} </td>
    <td id="prog{{ $vid }}"class="center">{{ $Programe -> program }}</td>
    <td id="aud{{$vid }}"class="center">{{ $Programe ->total_audience}}</td>
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

{{--     <div class="the_frame client_dets mb4">
        <div class="col_4 column">
            <button type="button" id="mpo_filters" class="btn small_btn show">Create Plan</button>
        </div>  
    </div> --}}

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
        function goBack() {
            window.history.back();
        }
    </script>
    <script>
        $(document).ready(function () {
            //flatpickr
            flatpickr(".flatpickr", {
                altInput: true,
            });
            //select for target audience
            $('.js-example-basic-multiple').select2();
            //placeholder for target audienct
            $('#region').select2({
                placeholder: "Please select region"
            });

            $('#lsm').select2({
                placeholder: "Please select LSM"
            });

            $('#social_class').select2({
                placeholder: "Please select social class"
            });

            $('#state').select2({
                placeholder: "Please select state"
            });

            $(".checkbox_region").click( function (){
                var checkbox_region = $(this).data("region");
                var $inputs = $('.checkbox_region');
                if(checkbox_region === "naem6hqwjhjatseog8" && $(this).is(':checked')){
                    $(".checkbox_region").prop('checked', false);
                    $("#naem6hqwjhjatseog8").prop('checked', true);
                    $inputs.not(this).prop('disabled',true);
                }else{
                    $(".checkbox_region").prop('disabled', false);
                }
            });

            // fetch all brands when a clientSis selected
            $('body').delegate('#clients','change', function(e){
                var clients = $("#clients").val();
                if(clients != ''){
                    $(".load_stuff").css({
                        opacity: 0.3
                    });
                    $("#industry").val('');
                    $("#sub_industry").val('');
                    var url = '/client/get-brands/'+clients;
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: {clients: clients},
                        success: function(data){
                            if(data.brands){
                                var big_html = '<select name="brand" id="brand">\n';
                                if(data.brands != ''){
                                    big_html += '<option value="">Select Brand</option>';
                                    $.each(data.brands, function (index, value) {
                                        big_html += '<option value="'+value.id+'">'+value.name+'</option>';
                                    });
                                    big_html += '</select>';
                                    $(".brand_hide").hide();
                                    $(".brand_select").show();
                                    $(".brand_select").html(big_html);
                                    $(".load_stuff").css({
                                        opacity: 1
                                    });
                                }else{
                                    big_html += '<option value="">Please Select a Client</option></section>';
                                    $(".brand_hide").hide();
                                    $(".brand_select").show();
                                    $(".brand_select").html(big_html);
                                    $(".load_stuff").css({
                                        opacity: 1
                                    });
                                }
                            }else{
                                $(".load_stuff").css({
                                    opacity: 1
                                });
                                toastr.error('An error occurred, please contact the administrator')
                            }

                        }
                    });
                }else{
                    $("#industry").val('');
                    $("#sub_industry").val('');
                }
            });

            //fetch all industry and sub-industry attached to a brand
            $('body').delegate('#brand','change', function(e) {
                var brand = $("#brand").val();
                if (brand != '') {
                    $(".load_stuff").css({
                        opacity: 0.5
                    });
                    $('.next').attr("disabled", true);
                    var url = '/brand/get-industry';

                    
                    $.ajax({
                        url: url,
                        method: "GET",
                        data: {brand: brand},
                        success: function (data) {
                            if (data.error === 'error') {
                                $(".load_stuff").css({
                                    opacity: 1
                                });
                                toastr.error('An error occured, please contact the administratot ')
                            } else {
                                $(".load_stuff").css({
                                    opacity: 1
                                });

                                $("#industry").val(data.industry.name);
                                $("#sub_industry").val(data.sub_industry.name);
                            }

                        }
                    });
                } else {
                    $("#industry").val('');
                    $("#sub_industry").val('');
                }
            });
        });
    </script>

     <script src="{{asset('new_frontend/js/aria-accordion.js')}}"></script>
    <script>
      $(document).ready(function () {
        'use strict';

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

			   $('#'+prog_id).prop('disabled', true);
               
            });


			$("body").delegate(".dBtn", "click", function() {
			 var value_button = $(this).attr("data_25");
			 var prog_id = $(this).data("programm")
             var ey = $("#dunique"+prog_id).val();
             console.log("rm", prog_id)
              $('#'+prog_id).prop('disabled', false);
              $( "tr" ).remove(".ri"+prog_id);
		     });



			$("body").delegate(".save", "click", function() {

                    var ids = [];
                    var children = document.getElementById("cont").children; //get container element children.
                    for (var i = 0, len = children.length ; i < len; i++) {
                        children[i].className = 'new-class'; //change child class name.
                        ids.push(children[i].id); //get child id.
                    }

                   $('.save').prop('disabled', true);                


                   if(ids.length == 0 ){
                          toastr.error("Select the staion you want to add")
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
                            success:function(data){
                                $('.save').prop('disabled', false);
                                toastr.success("Plans successfully saved!")
                                    console.log(data)

                            } 
                        });
                    }
   
		  });
            


			$("body").delegate(".show", "click", function() {
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
                            success:function(data){
                                     toastr.success("Plans successfully selected!")
                                    setTimeout(function() {
                                        location.href = '/agency/media-plan/createplan/'+fifthSegment;
                                       }, 4000);

                            }
                        });
                    }
                
			 console.log(plans)
			
			 
		  });

            
            function movePlanByDuration(prog_id, key, plan_station, plan_programe, plan_time, plan_day, plan_aud){

                var ids = [];
                    var children = document.getElementById("cont").children; //get container element children.
                    for (var i = 0, len = children.length ; i < len; i++) {
                        children[i].className = 'new-class'; //change child class name.
                        ids.push(children[i].id); //get child id.
                    } 
                    ids.includes(key)
                    if(ds.includes(key)){
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
@stop
