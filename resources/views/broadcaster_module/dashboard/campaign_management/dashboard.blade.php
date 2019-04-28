@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Dashboard </title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Dashboard</h2>
            </div>
            @if(Auth::user()->companies()->count() > 1)
                <div class="column col_6">
                    <select class="publishers" name="companies[]" id="publishers" multiple="multiple" >
                        @foreach(Auth::user()->companies as $company)
                            <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

    {{--sidebar--}}
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        <!-- main stats -->
        <div class="the_stats the_frame clearfix mb4">
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.scheduler') || Auth::user()->hasRole('ssp.media_buyer'))
                <div class="column col_3" id="campaign_count">
                    <span class="weight_medium small_faint uppercased">Active Campaigns</span>
                    <h3><a href="{{ route('campaign.list', ['status' => 'active']) }}">{{ count($active_campaigns) }}</a></h3>
                </div>

            <div class="column col_3" id="filtered_campaign_count" style="display: none;">

            </div>
            @endif
            
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.media_buyer'))
            <div class="column col_3" id="campaign_on_hold">
                <span class="weight_medium small_faint uppercased">Campaigns on hold</span>
                <h3><a href="{{ route('campaign.list', ['status' => 'on_hold']) }}" style="color: red;">{{ count($campaign_on_hold) }}</a></h3>
            </div>
            <div class="column col_3" id="filtered_campaign_on_hold" style="display: none;">

            </div>
            @endif

            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.media_buyer'))
            <div class="column col_2" id="walkin_count">
                <span class="weight_medium small_faint uppercased">All Walk-Ins</span>
                <h3><a href="{{ route('walkins.all') }}">{{ count($walkins) }}</a></h3>
            </div>
            <div class="column col_2" id="filtered_walkin_count" style="display: none;">

            </div>
            @endif
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.scheduler'))
            <div class="column col_2" id="pending_mpo_count">
                <span class="weight_medium small_faint uppercased">Pending MPO's</span>
                <h3><a href="{{ route('pending-mpos') }}" style="color: red;">{{ count($pending_mpos) }}</a></h3>
            </div>
            <div class="column col_2" id="filtered_mpo_count" style="display: none;">

            </div>
            @endif
            @if(Auth::user()->hasRole('ssp.super_admin') || Auth::user()->hasRole('ssp.admin') || Auth::user()->hasRole('ssp.media_buyer'))
            <div class="column col_2" id="brand_count">
                <span class="weight_medium small_faint uppercased">All Brands</span>
                <h3><a href="{{ route('brand.all') }}">{{ count($brands) }}</a></h3>
            </div>
            <div class="column col_2" id="filtered_brand_count" style="display: none;">

            </div>
            @endif
        </div>

        <!-- channel summary -->
        @include('broadcaster_module.dashboard.includes.media_type_tiles')

        <!-- client charts -->
        <div class="clearfix periodic_rev" id="chart-container">
                <h3><p>Periodic Revenue Chart</p></h3>
                <br>
                <div class="row">
                    <div class="clearfix mb3">
                        <form method="" action="" id="filter-form">
                            {{ csrf_field() }}

                            <div class="input_wrap column col_2">
                                <div class="select_wrap">
                                    <select name="report_type">
                                        <option @if($monthly_reports["report_type"] == "station_revenue") selected @endif value="station_revenue">Revenue</option>
                                        <option @if($monthly_reports["report_type"] == "spots_sold") selected @endif value="spots_sold">Ad Slots</option>
                                        <option @if($monthly_reports["report_type"] == "active_campaigns") selected @endif value="active_campaigns">Campaigns</option>
                                    </select>
                                </div>
                            </div>
            
                            @if(Auth::user()->companies()->count() > 1)
                                <div class="input_wrap column col_2">
                                    <div class="select_wrap">
                                        <select name="station_id[]" multiple>
                                            <option value="">All Stations</option>
                                            @foreach($stations as $station)
                                                <option value="{{ $station->id }}">{{ $station->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="input_wrap column col_2">
                                <div class="select_wrap">
                                    <select name="year">
                                        @foreach($year_list as $year)
                                            <option @if($current_year == $year) selected @endif value="{{ $year }}">
                                                {{ $year }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
                <br>
                {{--Periodic revenue chart goes here--}}
                <div class="row">
                    <div id="periodicChart" style="min-width: 310px; height: 400px; margin: 0 auto">
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
    <script src="{{ asset('js/dashboard-graphs.js') }}"></script>

    <script>
        $(document).ready(function( $ ) {

            //Every user should have access to this
            var monthly_reports = {!! json_encode($monthly_reports) !!};
            var dashboard_graph = new DashboardGraph($('#chart-container'));
            dashboard_graph.initChart(monthly_reports);

            //Every user should have access to this
            var campaigns_by_media_type = {!! json_encode($reports_by_media_type['campaigns']) !!};
            var dashboard_tiles = new DashboardTiles();
            dashboard_tiles.initTiles(campaigns_by_media_type);

        } );
    </script>

@endsection

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
@endsection
