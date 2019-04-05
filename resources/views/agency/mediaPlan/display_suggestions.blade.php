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

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Suggested stations & programs</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_7 clearfix create_fields">

                <div class="the_stats the_frame clearfix mb4">
                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">TV </span>
                        <h3><a href="{{ route('campaign.all') }}">{{ $fayaFound['total_tv'] }}</a></h3>
                    </div>

                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">Radio</span>
                        <h3><a href="{{ route('agency.campaigns.hold') }}" style="color: red;">{{ $fayaFound['total_radio'] }}</a></h3>
                    </div>

                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">Total Audience</span>
                        <h3><a href="{{ route('clients.list') }}">{{ $fayaFound['total_audiences'] }}</a></h3>
                    </div>
                </div>

                
            </div>
        </div>
        <!-- main frame end -->


        
<div class="the_frame client_dets mb4">

<div class="filters border_bottom clearfix">
            <div class="column col_8 p-t">
                <p class="uppercased weight_medium">Sugested Plans</p>
            </div>
        <div class="column col_4 clearfix">
            <div class="col_5 column">
                <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
            </div>

            <div class="col_5 column">
                <input type="text" name="stop_date" class="flatpickr" placeholder="End Date">
            </div>

            <div class="col_1 column">
                <button type="button" id="mpo_filters" class="btn small_btn">Filter</button>
            </div>
        </div>
</div>

<!-- campaigns table -->
<div class="accordion-group">
<table lass="display default_mpo filter_mpo" id="default_mpo_table">
                                            <thead>
                                                    <tr>
                                                        <th width="25%">All</th>
                                                        <th width="25%">Station</th>
                                                        <th width="25%">Audience</th>
                                                             <th width="25%"> actions</th>
                                                    </tr>
                                                </thead>
                                                </table>
                @foreach($fayaFound['stations'] as $key => $Value)
                    <section class="accordion-group__accordion">
                        <header class="accordion-group__accordion-head">
                                    <table lass="display default_mpo filter_mpo" id="default_mpo_table">
                                            <tbody>

                                                    <tr  class="clickable">
                                                            <td width="25%"><input type="checkbox"></td>
                                                            <td width="25%">{{ $key }}</td>
                                                            <td width="25%">890</td>
                                                                 <td width="25%"><button class="btn small_btn accordion-group__accordion-btn"> Details </button> <!--<button class="btn small_btn"> Add </button>--> </td>
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
                                        <th>Day of the week</th>
                                            <th>Time belt</th>
                                           
                                            <th>programe</th>
                                                 <th>Audience</th>
                                                 <th></th>
                                        </tr>
                                            </thead>
                                                                     <tbody>
                                            
                                                                     @foreach($Value as $Programe)
                                            <tr>
                        <td id="stat{{ $Programe->id }}"> {{ $Programe->station }}</td>
                        <td id="day{{ $Programe->id }}"class="center">{{ $Programe ->day}} </td>
                        <td id="time{{ $Programe->id }}" class="center">{{ $Programe -> start_time}} - {{ $Programe -> end_time}}</td>
                        
                        <td id="prog{{ $Programe->id }}"class="center">{{ $Programe -> program }}</td>
                        <td id="aud{{ $Programe->id }}"class="center">{{ $Programe ->total_audience}}</td>
                            <td class="center">
                            <button data-program="{{ $Programe->id }}" class="btn btn-info aBtn" data_15="first15" id="{{ $Programe->id }}" type="button">ADD</button>	
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






<div class="the_frame client_dets mb4">

<div class="filters border_bottom clearfix">
            <div class="column col_8 p-t">
                <p class="uppercased weight_medium">Selected Plan</p>
            </div>
        <div class="column col_4 clearfix">
            <div class="col_5 column">
                <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
            </div>

            <div class="col_5 column">
                <input type="text" name="stop_date" class="flatpickr" placeholder="End Date">
            </div>

            <div class="col_1 column">
                <button type="button" id="mpo_filters" class="btn small_btn">Filter</button>
            </div>
        </div>
</div>

<!-- campaigns table -->
<table class="table table-striped table-bordered bootstrap-datatable">
                <thead>
                <tr>
                    <th>Station</th>
                    <th>Time belt</th>
                    <th>Day of the week</th>
                    <th>Programme</th>
                    <th>Audience</th>
                    <th>Actions </th>
                  
                </tr>
            </thead>     
                  <tbody class="where-it-is-going">
                    
                  </tbody>
              </table>  

<!--  end -->













</div>




<div class="the_frame client_dets mb4">



            <div class="col_4 column">
                <button type="button" id="mpo_filters" class="btn small_btn show">Create Plan</button>
            </div>
       
</div>
<br><br><br><br><br><br><br>


    </div>
@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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

     <script src="{{asset('js/aria-accordion.js')}}"></script>
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
                console.log(url);
                var trim = url.split('/');
                console.log(trim);
                var fifthSegment = trim[6];
                console.log(fifthSegment);



            var step = 1;
            var plans = [];
            $("body").delegate(".aBtn", "click", function() {
               
			   var value_button = $(this).attr("data_15");
               
			   var prog_id = $(this).data("program");
			   var plan_station = $("#stat"+prog_id).text();
               var plan_time = $("#time"+prog_id).text();
			   var plan_programe = $("#prog"+prog_id).text();
			   var plan_day = $("#day"+prog_id).text();
			   var plan_aud = $("#aud"+prog_id).text();

			
			 

                plans.push({'program_id': prog_id});
                
                

			   movePlanByDuration(prog_id, plan_station, plan_programe,plan_time, plan_day, plan_aud);

			   $('#'+prog_id).prop('disabled', true);
               
            });


			$("body").delegate(".dBtn", "click", function() {
             
			 var value_button = $(this).attr("data_25");
			 var prog_id = $(this).data("programm")

			 document.getElementById('ri'+prog_id).remove() 
             
			 $('#'+prog_id).prop('disabled', false);
			 
		  });



			$("body").delegate(".show", "click", function() {
                var requesData = {
                    "_token": "{{ csrf_token() }}",
                    "data":plans
                };
                

                var RequestData = JSON.stringify(requesData);

                        $.ajax({
                            type: "POST",
                            dataType: 'json',
                            url: "/agency/media-plan/select_plan",
                            data: {
                                "_token": "{{ csrf_token() }}",
                                "data": JSON.stringify(plans)
                            },
                            success:function(data){
                          
                                swal("Success!", "Plans successfully selected!", "success");
                                setTimeout(() => {
                                    location.href = '/agency/media-plan/createplan/'+fifthSegment;
                                }, 2000);
                            }
                           
                        });
                
                
			 console.log(plans)
			
			 
		  });
            
            function   movePlanByDuration(prog_id, plan_station, plan_programe, plan_time, plan_day, plan_aud){
                
                 var new_html = "";
				 new_html += '<tr  id="ri'+prog_id+'">'+
				 '<td>'+plan_station+' </td>'+
                 '<td class="center">'+plan_time+'</td>'+
                 '<td class="center">'+plan_day+'</td>'+
				 '<td class="center">'+plan_programe+'</td>'+
				 '<td class="center">'+plan_aud+'</td>'+
					'<td class="center"><button data-programm="'+prog_id+'" class="btn btn-danger dBtn" data_15="first15" type="button">Remove</button>'+
					 '</td>'+
					 '</td>'+
					 '</tr>';
                    
                    $(".where-it-is-going").append(new_html);
					
            }
            
        })
</script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop