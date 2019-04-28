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
            </div>
        </div>

        <!-- Charts -->
        <div id="chart-container" class="row my-5 chart-view">
            <div class="col-12 pb-1 mb-3" style="border-bottom: 2px solid #44c1c9;">
                <div class="btn-group chart-toggle" role="group" aria-label="Basic example">
                    <button id="tv-toggle" type="button" class="btn btn-info py-1 mr-2"><i class="material-icons">tv</i> TV</button>
                    <button id="radio-toggle" type="button" class="btn btn-info py-1 inactive-toggle"><i class="material-icons">radio</i> RADIO</button>
                </div>
            </div>

            <!-- client charts -->
            <!-- Filters -->
            <div class="col-12 filter">
                <form method="" action="" id="filter-form">
                    {{ csrf_field() }}
                    <div class="row mb-2">
                        <div class="col-5">
                            <div class="row">
                                @if(count($stations) > 1)
                                    <div class="col-6 pr-0">
                                        <select class="form-control publishers" name="station_id[]" id="publishers" multiple="multiple" >
                                            @foreach(Auth::user()->companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                <div class="col-6">
                                    <select class="form-control single-select" name="report_type" id="other">
                                        <option @if($monthly_reports["report_type"] == "station_revenue") selected @endif value="station_revenue">Revenue</option>
                                        <option @if($monthly_reports["report_type"] == "spots_sold") selected @endif value="spots_sold">Ad Slots</option>
                                        <option @if($monthly_reports["report_type"] == "active_campaigns") selected @endif value="active_campaigns">Campaigns</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="offset-5 col-2 pl-0">
                            <select name="year" class="filter_year single-select" id="filter_year">
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
                </form>
                
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
    <script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
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
    
        $(document).ready(function( $ ) {

            //Every user should have access to this
            var monthly_reports = {!! json_encode($monthly_reports) !!};
            var dashboard_graph = new DashboardGraph($('#chart-container'));
            dashboard_graph.initChart(monthly_reports);

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