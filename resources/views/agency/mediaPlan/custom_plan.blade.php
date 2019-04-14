@extends('layouts.faya_app')

@section('title')
    <title> FAYA | MP </title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">All MP</h2>
            </div>

            @if(Auth::user()->companies()->count() > 1)
                <div class="column col_6">
                    <select class="publishers" name="companies[]" id="publishers" multiple="multiple" >
                        @foreach(Auth::user()->companies as $company)
                            <option value="{{ $company->id }}"
                                @foreach($companies_id as $company_id)
                                    @if($company_id->company_id == $company->id)
                                        selected
                                    @endif
                                @endforeach
                            >{{ $company->name }}</option>
                        @endforeach

                    </select>
                </div>
            @endif
        </div>

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
    <div class="accordion-group loading">
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
		<section class="accordion-group__accordion">
			<header class="accordion-group__accordion-head">
				<table lass="display default_mpo filter_mpo" id="default_mpo_table">
					<tbody>

						<tr  class="clickable">
							<td width="25%"><input type="checkbox"></td>
							<td width="25%">AIT</td>
							<td width="25%">434000</td>
							<td width="25%">
                                <button class="btn small_btn accordion-group__accordion-btn"> Details </button> <button class="btn small_btn"> Add </button>
                            </td>
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
                                <th>Time Belt</th>
                                <th>Day of the week</th>
                                <th>Program</th>
                                <th>Audience</th>
                                <th></th>
							</tr>
						</thead>
					    <tbody>
								
                            <?php for ($x = 5; $x <= 8; $x++) {?>

                            <tr>
                                <td id="statbtn_15<?php echo $x; ?>"> AIT </td>
                                <td id="timebtn_15<?php echo $x; ?>" class="center">21:30 - 13:00</td>
                                <td id="daybtn_15<?php echo $x; ?>"class="center">Thursday  </td>
                                <td id="progbtn_15<?php echo $x; ?>"class="center">Supper Story </td>
                                <td id="audbtn_15<?php echo $x; ?>"class="center">21000</td>
                            <td class="center">
                                <button data-program="btn_15<?php echo $x; ?>" class="btn btn-info aBtn" data_15="first15" id="btn_15<?php echo $x; ?>" type="button">ADD</button>	
                                </td>
                            </tr>

                        <?php }?>
													 
					</table>
											 
				</div>
			</div>
		</section>
        <section class="accordion-group__accordion">
			<header class="accordion-group__accordion-head">
				<table lass="display default_mpo filter_mpo" id="default_mpo_table">
					<tbody>

						<tr  class="clickable">
							<td width="25%"><input type="checkbox"></td>
							<td width="25%">NTA</td>
							<td width="25%">434000</td>
							<td width="25%"><button class="btn small_btn accordion-group__accordion-btn"> Details </button> <button class="btn small_btn"> Add </button> </td>
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
									<th>Time Belt</th>
									<th>Day of the week</th>
									<th>programe</th>
									<th>Audience</th>
									<th>Action</th>
							    </tr>
							</thead>
							<tbody>
                                <?php for ($x = 1; $x <= 4; $x++) {?>

                                <tr>
    								<td id="statbtn_15<?php echo $x; ?>"> NTA </td>
    								<td id="timebtn_15<?php echo $x; ?>" class="center">21:30 - 13:00</td>
    								<td id="daybtn_15<?php echo $x; ?>"class="center">Thursday  </td>
    								<td id="progbtn_15<?php echo $x; ?>"class="center">Supper Story </td>
    								<td id="audbtn_15<?php echo $x; ?>"class="center">21000</td>
		                            <td class="center">
				                       <button data-program="btn_15<?php echo $x; ?>" class="btn btn-info aBtn" data_15="first15" id="btn_15<?php echo $x; ?>" type="button">ADD</button>	
			                        </td>
		                        </tr>

                                <?php }?>								 
					    </table>						 
				</div>
			</div>
		</section>
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
                        <th>Time Belt</th>
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

@stop

@section('scripts')
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="{{asset('js/aria-accordion.js')}}"></script>
    <script>
      $(document).ready(function () {
        'use strict';

        $(window).on('ariaAccordion.initialised', function (event, element) {
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
     <script>
        $(document).ready(function() {
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
                plans.push([{ "program_id": prog_id, "station": plan_station, "program": plan_programe, "day": plan_day}]);
			   movePlanByDuration(prog_id, plan_station, plan_programe,plan_time, plan_day, plan_aud);
			   $('#'+prog_id).prop('disabled', true);
            });

			$("body").delegate(".dBtn", "click", function() {
			 var value_button = $(this).attr("data_25");
			 var prog_id = $(this).data("programm")
			 document.getElementById('ri'+prog_id).remove();
			 toastr.success('Added successfully');
			 $('#'+prog_id).prop('disabled', false);
			 
		  });

        $("body").delegate(".show", "click", function() {
            $.ajax({
                url: '',
                dataType: 'json',
                type: 'post',
                contentType: 'application/json',
                data: plans,
                processData: false,
                success: function( data, textStatus, jQxhr ){
                    $('#response pre').html( JSON.stringify( data ) );
                }

            });
		  });
            
        function   movePlanByDuration(prog_id, plan_station, plan_programe, plan_time, plan_day, plan_aud){
             var new_html = "";
             new_html += '<tr  id="ri'+prog_id+'">'+
             '<td>'+plan_station+' </td>'+
             '<td class="center">'+plan_time+'</td>'+
             '<td class="center">'+plan_programe+'</td>'+
             '<td class="center">'+plan_day+'</td>'+
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
    <style>
        .highcharts-grid path { display: none; }
        .highcharts-legend {
            display: none;
        }

        .dataTables_filter {
            display: none;
        }
    </style>
@stop
