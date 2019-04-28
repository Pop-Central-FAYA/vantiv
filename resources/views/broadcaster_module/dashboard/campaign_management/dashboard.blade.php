@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Dashboard </title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @include('partials.new-frontend.broadcaster.header')

        <!-- subheader -->
            <div class="sub_header clearfix mb pt">
                <div class="column col_6">
                    <h2 class="sub_header">Dashboard</h2>
                </div>
            </div>
        <!-- Sidebar -->
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    
    <!-- Channel Summary -->
    <div class="container-fluid broadcaster-report">
        <div class="row stats">
            <div class="card-deck">
                <div class="card custom-card-group">
                    @foreach($top_media_type_revenue as $data)
                        <div class="card-body p-2">
                            <div class="row">
                                <div class="col-4">
                                    <img src="{{ $data->logo }}">
                                </div>
                                <div class="col-8 px-0">
                                    <span class="text-muted">{{ $data->name }} is your highest {{ strtoupper($data->type) }} earner</span>
                                    <h3 class="text-success text-center mt-2"><b>{{number_format($data->revenue)}}</b></h3>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="card-body p-2 mt-4">
                        <div class="row">
                            <div class="col-4">
                                <img src="{{ $top_revenue_by_client->company_logo }}">
                            </div>
                            <div class="col-8 px-0">
                                <span class="text-muted">{{ $top_revenue_by_client->client_name }} is your highest spender</span>
                                <h3 class="text-success text-center mt-2"><b>{{number_format($top_revenue_by_client->actual_revenue)}}</b></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- The different cards -->
                <!-- Television Card -->
                @foreach($campaigns['detailed_counts'] as $media_type => $data)
                <div class="card text-center">
                    <div class="card-header bg-white p-0">
                        <h5 class="bg-white position-relative">
                            <i class="material-icons">{{$media_type}}</i>
                            <span>{{strtoupper($media_type)}}</span>
                        </h5>
                    </div>
                    <div class="card-body mt-2">
                        <h3 class="card-title my-3 text-muted">{{$campaigns['total'][$media_type]}}</h3>
                        <div class="row dashboard_pies">
                            <div class="col-7">
                                <div id="pie-chart-{{$media_type}}" class="_pie_chart" style="height: 130px"></div>
                            </div>
                            <div class="col-5 pt-5">
                                <ul id="legend-{{$media_type}}">
                                    <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                                    <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                                    <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted bg-white border-0 mb-3">
                        <div class="row">
                            <div class="col-4 px-1">
                                <h3 class="text-muted">{{count($clients_and_brands['walkin_clients'][$media_type])}}</h3>
                                <span>Walk Ins</span>
                            </div>
                            <div class="col-4 px-1">
                                <h3 class="text-danger">{{$mpos['detailed_counts'][$media_type]['pending']}}</h3>
                                <span>Pending MPOs</span>
                            </div>
                            <div class="col-4 px-1">
                                <h3 class="text-muted">{{$clients_and_brands['brands'][$media_type]->num}}</h3>
                                <span>Brands</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Radio Card -->
                {{-- <div class="card text-center">
                    <div class="card-header bg-white p-0">
                        <h5 class="bg-white position-relative">
                            <i class="material-icons">radio</i>
                            <span>RADIO</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title my-3 text-muted">74 Campaigns</h3>
                        <div class="row dashboard_pies">
                            <div class="col-7">
                                <div id="radio" class="_pie_chart" style="height: 130px"></div>
                            </div>
                            <div class="col-5 pt-5">
                                <ul>
                                    <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                                    <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                                    <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted bg-white border-0 mb-3">
                        <div class="row">
                            <div class="col-4 px-1">
                                <h3 class="text-muted">16</h3>
                                <span>Walk Ins</span>
                            </div>
                            <div class="col-4 px-1">
                                <h3 class="text-danger">11</h3>
                                <span>Pending MPOs</span>
                            </div>
                            <div class="col-4 px-1">
                                <h3 class="text-muted">32</h3>
                                <span>Brands</span>
                            </div>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>

        <!-- Charts -->
        <div class="row my-5 chart-view">
            <div class="col-12 pb-1 mb-3" style="border-bottom: 2px solid #44c1c9;">
                <div class="btn-group chart-toggle" role="group" aria-label="Basic example">
                    <button id="tv-toggle" type="button" class="btn btn-info py-1 mr-2"><i class="material-icons">tv</i> TV</button>
                    <button id="radio-toggle" type="button" class="btn btn-info py-1 inactive-toggle"><i class="material-icons">radio</i> RADIO</button>
                </div>
            </div>

            <!-- client charts -->
            <!-- Filters -->
            <div class="col-12 filter">
                <div class="row mb-2">
                    <div class="col-5">
                        <div class="row">
                            <div class="col-6 pr-0">
                                <select class="form-control publishers" name="companies[]" id="publishers" multiple="multiple" >
                                    @foreach(Auth::user()->companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select class="form-control single-select" name="other" id="other">
                                    <option value="">Revenue</option>
                                    <option value="">Ad Slots</option>
                                    <option value="">Ratings</option>
                                    <option value="">Campaigns</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="offset-5 col-2 pl-0">
                        <select name="filter_year" class="filter_year single-select" id="filter_year">
                            @foreach($year_list as $year)
                                <option
                                    @if($current_year == $year)
                                        selected
                                    @endif
                                    value="{{ $year }}">
                                    Jan - Dec, {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <br>
            <!-- {{--Periodic revenue chart goes here--}} -->
            <div class="col-12">
                <div id="periodicChart" style="min-width: 310px; height: 400px; margin: 0 auto">
            </div>
        </div>
    </div>

    
    </div>


        <p><br></p>

    </div>
@stop



@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!-- SUMO SELECT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sumoselect/3.0.2/jquery.sumoselect.min.js"></script>
    <script src="{{ asset('js/dashboard-graphs.js') }}"></script>

    <script>
        <?php echo "var channels_with_details =".json_encode($user_channel_with_other_details). ";\n";?>
        //Bar chart for Total Volume of Campaigns
        <?php echo "var campaign_volume = ".$volume . ";\n"; ?>
        <?php echo "var campaign_month = ".$month . ";\n"; ?>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        <?php if(Auth::user()->companies()->count() > 1){ ?>
        <?php echo "var months =".json_encode($periodic_revenues['month_list']).";\n"; ?>
        <?php echo "var periodic_revenue_data =".json_encode($periodic_revenues['formated_periodic_revenue_chart']).";\n"; ?>
        <?php echo "var current_year =".$current_year.";\n"; ?>
        <?php } ?>

        $(document).ready(function( $ ) {

            const numberFormatter = new Intl.NumberFormat('en-US', {});

            if(companies > 1) {
                // $.each(channels_with_details, function (index, value) {
                //     if (value.channel_details.channel === 'TV') {
                //         channel_pie('tv', value.campaign_status_percentage.percentage_active, value.campaign_status_percentage.percentage_pending, value.campaign_status_percentage.percentage_finished)
                //     } else if (value.channel_details.channel === 'Radio') {
                //         channel_pie('radio', value.campaign_status_percentage.percentage_active, value.campaign_status_percentage.percentage_pending, value.campaign_status_percentage.percentage_finished)
                //     }
                // });

                
                $('.single-select').SumoSelect({
                    placeholder: 'Select One',
                    csvDispCount: 3,
                });

                $('.publishers').SumoSelect({
                    placeholder: 'Select Publishers',
                    csvDispCount: 3,
                    selectAll: true,
                    captionFormat: '{0} Publishers Selected',
                    captionFormatAllSelected: 'All publishers selected!',
                    okCancelInMulti: true, 
                });

                $('body').delegate("#publishers", "change", function () {
                    $(".dashboard_campaigns").dataTable().fnDestroy();
                    var channels = $("#publishers").val();
                    var year = current_year;
                    if(channels != null){
                        // $('body').css({
                        //     opacity : 0.1
                        // });
                        var campaignFilter =  $('.dashboard_campaigns_filtered').DataTable({
                            dom: 'Blfrtip',
                            paging: true,
                            serverSide: true,
                            processing: true,
                            aaSorting: [],
                            oLanguage: {
                                sLengthMenu: "_MENU_"
                            },
                            ajax: {
                                url: '/campaign-details/filter/company',
                                data: function (d) {
                                    d.company_id = channels;
                                    d.start_date = $('input[name=start_date]').val();
                                    d.stop_date = $('input[name=stop_date]').val();
                                    d.filter_user = $('#filter_user').val();
                                    d.year = $('#filter_year').val();
                                }
                            },
                            columns: getColumns(),
                        });
                        $('#dashboard_filter_campaign').on('click', function() {
                            campaignFilter.draw();
                        });
                        $('.key_search').on('keyup', function(){
                            campaignFilter.search($(this).val()).draw() ;
                        });
                        $('#filter_user').on('change', function() {
                            campaignFilter.draw();
                        });
                        $.ajax({
                            url: '/campaign-management/filter-result',
                            method: 'GET',
                            data: {channel_id: channels, year: year},
                            success: function (data) {
                                $("#campaign_count").remove();
                                $("#filtered_campaign_count").show();
                                $("#filtered_campaign_count").html(filterActiveCampaignCount(data.active_campaigns));
                                $("#walkin_count").remove();
                                $("#filtered_walkin_count").show();
                                $("#filtered_walkin_count").html(filterWalkinCount(data.walkIns));
                                $("#pending_mpo_count").remove();
                                $("#filtered_mpo_count").show();
                                $("#filtered_mpo_count").html(filterPendingMpoCount(data.pending_mpos));
                                $("#brand_count").remove();
                                $("#filtered_brand_count").show();
                                $("#filtered_brand_count").html(filterBrandCount(data.brands));
                                $("#campaign_on_hold").remove();
                                $("#filtered_campaign_on_hold").show();
                                $("#filtered_campaign_on_hold").html(filterCampaignOnHoldCount(data.campaign_on_hold));
                                periodicRevenueChat(data.periodic_revenues['month_list'], data.periodic_revenues['formated_periodic_revenue_chart'])

                                $('body').css({
                                    opacity : 1
                                });
                            }
                        })
                    }
                });

                $('body').delegate('#filter_year', 'change', function () {
                    var year = $('#filter_year').val();
                    var channels = $("#publishers").val();
                    $(".periodic_rev").css({
                        opacity: 0.1
                    });
                    $.ajax({
                        url: '/periodic-revenue/filter-year',
                        method: 'GET',
                        data: {channel_id: channels, year: year},
                        success: function (data) {
                            if(data.formated_periodic_revenue_chart.length != 0){
                                periodicRevenueChat(data.month_list, data.formated_periodic_revenue_chart);
                                $(".periodic_rev").css({
                                    opacity: 1
                                });
                            }else{
                                periodicRevenueChat(data.month_list, data.formated_periodic_revenue_chart);
                                $(".periodic_rev").css({
                                    opacity: 1
                                });
                                toastr.info('No data for the selected year');
                            }
                        }
                    })
                });

                periodicRevenueChat(months, periodic_revenue_data)

                function periodicRevenueChat(month_list, periodic_revenue_data){
                    Highcharts.chart('periodicChart', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: month_list
                        },
                        credits: {
                            enabled: false
                        },
                        exporting: {
                            enabled: false
                        },
                        series: periodic_revenue_data
                    });
                }

             }else{
                var chart = Highcharts.chart('containerTotal', {

                    title: {text: ''},
                    yAxis: {
                            title: {text: 'Number of Campaigns'},
                            labels: {
                                formatter: function() {
                                    return numberFormatter.format(this.value).replace(".00", "");
                                },
                            }
                        },
                    xAxis: {
                        categories: campaign_month
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                        type: 'column',
                        colorByPoint: true,
                        data: campaign_volume,
                        showInLegend: false
                    }]

                });
            }

            $('#tv-toggle').on('click', function() {
                $('#tv-toggle').removeClass('inactive-toggle');
                $('#radio-toggle').addClass('inactive-toggle');
            });

            $('#radio-toggle').on('click', function() {
                $('#tv-toggle').addClass('inactive-toggle');
                $('#radio-toggle').removeClass('inactive-toggle');
            });

            //Every user should have access to this
            var campaigns_by_media_type = {!! json_encode($campaigns) !!};
            var dashboard_tiles = new DashboardTiles();
            dashboard_tiles.initTiles(campaigns_by_media_type);
        });
    </script>

@endsection

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <!-- SUMO SELECT -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.sumoselect/3.0.2/sumoselect.min.css" />
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
@endsection