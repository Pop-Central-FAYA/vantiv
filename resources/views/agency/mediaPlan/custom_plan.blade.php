@extends('layouts.faya_app')

@section('title')
    <title> FAYA | MP</title>
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
							<section class="accordion-group__accordion">
								<header class="accordion-group__accordion-head">
											<table lass="display default_mpo filter_mpo" id="default_mpo_table">
													<tbody>

															<tr  class="clickable">
																	<td width="25%"><input type="checkbox"></td>
																	<td width="25%">AIT</td>
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
													<th>Time belt</th>
													<th>Day of the week</th>
													<th>programe</th>
														 <th>Audience</th>
														 <th></th>
												</tr>
													</thead>
																			 <tbody>
													
                                                                             <?php 
for ($x = 5; $x <= 8; $x++) {
?>
				
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
													<th>Time belt</th>
													<th>Day of the week</th>
													<th>programe</th>
														 <th>Audience</th>
														 <th>Action</th>
												</tr>
													</thead>
																			 <tbody>
                               
                                                    <?php 
                                                    for ($x = 1; $x <= 4; $x++) {?>
				
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

@stop

@section('scripts')
    <script type="text/javascript" src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script src="https://unpkg.com/flatpickr"></script>
    {{--datatables--}}
    <script>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        $(document).ready(function( $ ) {
            if(companies > 1){
                $('.publishers').select2();
                $('body').delegate("#publishers", "change", function () {
                    $(".default_mpo").dataTable().fnDestroy();
                    var channels = $("#publishers").val();
                    if(channels != null){
                        $('.when_loading').css({
                            opacity: 0.1
                        });
                        var Datefilter =  $('.filter_mpo').DataTable({
                            dom: 'Blfrtip',
                            paging: true,
                            serverSide: true,
                            processing: true,
                            aaSorting: [],
                            aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                            buttons: [
                                'copy', 'csv', 'excel', 'pdf', 'print'
                            ],
                            oLanguage: {
                                sLengthMenu: "_MENU_"
                            },
                            ajax: {
                                url: '/mpo/filter',
                                data: function (d) {
                                    d.channel_id = channels;
                                    d.start_date = $('input[name=start_date]').val();
                                    d.stop_date = $('input[name=stop_date]').val();
                                }
                            },
                            columns: getColumns(),
                        });
                        Datefilter.draw();
                        $('#mpo_filters').on('click', function() {
                            Datefilter.draw();
                        });
                    }
                })
            }

            flatpickr(".flatpickr", {
                altInput: true,
            });

            $(".filter_mpo").dataTable().fnDestroy();

            var Datefilter =  $('.default_mpo').DataTable({
                dom: 'Blfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                aaSorting: [],
                aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                oLanguage: {
                    sLengthMenu: "_MENU_"
                },
                ajax: {
                    url: '/mpos/all-data',
                    data: function (d) {
                        d.start_date = $('input[name=start_date]').val();
                        d.stop_date = $('input[name=stop_date]').val();
                    }
                },
                columns: getColumns(),
            });

            $('#mpo_filters').on('click', function() {
                Datefilter.draw();
            });

            function getColumns()
            {
                if(companies > 1){
                    return [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'brand', name: 'brand'},
                        {data: 'date_created', name: 'date_created'},
                        {data: 'budget', name: 'budget'},
                        {data: 'status', name: 'status'},
                        {data: 'station', name: 'station'}
                    ]
                }else{
                    return [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'brand', name: 'brand'},
                        {data: 'date_created', name: 'date_created'},
                        {data: 'budget', name: 'budget'},
                        {data: 'status', name: 'status'},
                    ]
                }
            }




        } );


        //new

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
     <script>
        $(document).ready(function() {
            var step = 1;
            var plans = [];
            alert("The paragraph was clicked.");
            console.log("NOW ITS WORKIN")
            $("body").delegate(".aBtn", "click", function() {
                console.log("NOW ITS NOT WORKIN")
              

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

			 document.getElementById('ri'+prog_id).remove() 
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
                
                
			 console.log(plans)
			
			 
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <style>
        .highcharts-grid path { display: none;}
        .highcharts-legend {
            display: none;
        }

        .dataTables_filter {
            display: none;
        }

        #DataTables_Table_0_wrapper .dt-buttons button {
            line-height: 2.5;
            color: #fff;
            cursor: pointer;
            background: #44C1C9;
            -webkit-appearance: none;
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            border: 0;
            padding: 3px 20px 0;
            font-size: 14px;

            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;

            -webkit-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
            -moz-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
            box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);

            position: relative;
            display: inline-block;
            text-transform: uppercase;
        !important;

        }
    </style>
@stop
