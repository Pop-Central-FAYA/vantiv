@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Dashboard</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Dashboard</h2>
            </div>
        </div>

        <!-- main stats -->
        <!-- CAMPAIGN -->
        <div class="campaigns-dashboard dsp-dashboard" id="campaigns-dashboard">
            <!-- main stats -->
            @if(Auth::user()->hasPermissionTo('view.report'))
                <!-- Campaign Report -->
                <div class="the_stats the_frame clearfix mb3">
                    <div class="column col_3">
                        <span class="weight_medium small_faint uppercased">Active Campaigns</span>
                        <h3><a href="{{ route('agency.campaign.all',['status'=>'active']) }}">{{ count($active_campaigns) }}</a></h3>
                    </div>

                    <div class="column col_3">
                        <span class="weight_medium small_faint uppercased">Campaigns On Hold</span>
                        <h3><a href="{{ route('agency.campaign.all',['status'=>'on_hold']) }}" style="color: red;">{{ count($campaigns_on_hold) }}</a></h3>
                    </div>

                    <div class="column col_3">
                        <span class="weight_medium small_faint uppercased">All Clients</span>
                        <h3><a href="{{ route('clients.list') }}">{{ count($clients) }}</a></h3>
                    </div>

                    <div class="column col_3">
                        <span class="weight_medium small_faint uppercased">All Brands</span>
                        <h3><a href="{{ route('brand.all') }}">{{ count($all_brands) }}</a></h3>
                    </div>
                </div>
                <!-- client charts -->
                <div class="clearfix dashboard_pies mb3">
                    <!-- tv -->
                    <div class="">
                        <div class="pie_icon margin_center">
                            <img src="{{ asset('new_frontend/img/tv.svg') }}">
                        </div>
                        <p class="align_center">TV</p>

                        <div id="tv" class="_pie_chart margin_center" style="height: 100px"></div>

                        <ul style="margin-left: 9px;">
                            <li class="pie_legend active"><span class="weight_medium">{{ round($active) }}%</span> Active</li>
                            <li class="pie_legend pending"><span class="weight_medium">{{ round($pending) }}%</span> Pending</li>
                            <li class="pie_legend finished"><span class="weight_medium">{{ round($finished) }}%</span> Finished</li>
                        </ul>
                    </div>

                    <!-- radio -->
                    <div class="">
                        <div class="pie_icon margin_center">
                            <img src="{{ asset('new_frontend/img/radio.svg') }}">
                        </div>
                        <p class="align_center">Radio</p>

                        <div id="radio" class="_pie_chart margin_center" style="height: 100px"></div>

                        <ul style="margin-left: 9px;">
                            <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                            <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                            <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                        </ul>
                    </div>
                    <!-- newspaper -->
                    <div class="">
                        <div class="pie_icon margin_center">
                            <img src="{{ asset('new_frontend/img/paper.svg') }}">
                        </div>
                        <p class="align_center">Newspaper</p>

                        <div class="_pie_chart" style="height: 100px"></div>

                        <ul style="margin-left: 9px;">
                            <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                            <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                            <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                        </ul>
                    </div>

                    <!-- ooh -->
                    <div class="">
                        <div class="pie_icon margin_center">
                            <img src="{{ asset('new_frontend/img/ooh.svg') }}">
                        </div>
                        <p class="align_center">OOH</p>

                        <div class="_pie_chart" style="height: 100px"></div>

                        <ul style="margin-left: 9px;">
                            <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                            <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                            <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                        </ul>
                    </div>

                    <!-- desktop -->
                    <div class="">
                        <div class="pie_icon margin_center">
                            <img src="{{ asset('new_frontend/img/desktop.svg') }}">
                        </div>
                        <p class="align_center">Desktop</p>

                        <div class="_pie_chart" style="height: 100px"></div>

                        <ul style="margin-left: 9px;">
                            <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                            <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                            <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                        </ul>
                    </div>

                    <!-- mobile -->
                    <div class="">
                        <div class="pie_icon margin_center">
                            <img src="{{ asset('new_frontend/img/mobile.svg') }}">
                        </div>
                        <p class="align_center">Mobile</p>

                        <div class="_pie_chart" style="height: 100px"></div>

                        <ul style="margin-left: 9px;">
                            <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                            <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                            <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                        </ul>
                    </div>

                </div>
                <!-- Media Plan Report -->
                <div class="the_stats the_frame clearfix">
                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">Approved Media Plans</span>
                        
                        <h3><a href="{{ route('agency.media_plans', ['status'=>'approved']) }}">{{ $count_approved_media_plans }}</a></h3>
                    </div>

                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">Pending Media Plans</span>
                        <h3><a href="{{ route('agency.media_plans', ['status'=>'pending']) }}" style="color: red;">{{ $count_pending_media_plans }}</a></h3>
                    </div>

                    <div class="column col_4">
                        <span class="weight_medium small_faint uppercased">Declined Media Plans</span>
                        <h3><a href="{{ route('agency.media_plans', ['status'=>'declined']) }}" style="color: red;">{{ $count_declined_media_plans }}</a></h3>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    {{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        <?php echo "var tv_active = ".round($active) . ";\n"; ?>
        <?php echo "var tv_pending = ".round($pending) . ";\n"; ?>
        <?php echo "var tv_finished = ".round($finished) . ";\n"; ?>
        <?php echo "var radio_active = ".round($active_radio) . ";\n"; ?>
        <?php echo "var radio_pending = ".round($pending_radio) . ";\n"; ?>
        <?php echo "var radio_finished = ".round($finish_radio) . ";\n"; ?>

        Highcharts.chart('tv',{
            chart: {
                renderTo: 'container',
                type: 'pie',
                height: 150,
                width: 150
            },
            title: {
                        text: ''
                    },
            credits: {
                        enabled: false
                    },
            plotOptions: {
                pie: {
                    allowPointSelect: false,
                    dataLabels: {
                        enabled: false,
                        format: '{point.name}'
                    }
                }
            },
            exporting: { enabled: false },
            series: [{
                innerSize: '30%',
                data: [
                    {name: 'Active', y: tv_active, color: '#00C4CA'},
                    {name: 'Pending', y: tv_pending, color: '#E89B0B' },
                    {name: 'Finished', y: tv_finished, color: '#E8235F'}
                ]
            }]
        });

        Highcharts.chart('radio',{
            chart: {
                renderTo: 'container',
                type: 'pie',
                height: 150,
                width: 150,
                backgroundColor:'rgba(255, 255, 255, 0.0)'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    allowPointSelect: false,
                    dataLabels: {
                        enabled: false,
                        format: '{point.name}'
                    }
                }
            },
            exporting: { enabled: false },
            series: [{
                innerSize: '30%',
                data: [
                    {name: 'Active', y: radio_active, color: '#00C4CA'},
                    {name: 'Pending', y: radio_pending, color: '#E89B0B' },
                    {name: 'Finished', y: radio_finished, color: '#E8235F'}
                ]
            }]
        });
    </script>
    {{--datatables--}}
    <script>
        $(document).ready(function( $ ) {
            flatpickr(".flatpickr", {
                altInput: true,
            });
        } );
    </script>
@stop

@section('styles')
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>--}}
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
    <style>
        .inactive-dashboard-toggle-btn {
            background: transparent !important;
            border: 1px solid #44C1C9 !important;
            color: #000 !important;
        }

        .dataTables_filter {
            display: none;
        }

        table.dataTable.no-footer {
            width: 100% !important;
        }

        #DataTables_Table_0_wrapper .dt-buttons button, #DataTables_Table_1_wrapper .dt-buttons button {
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

            -webkit-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
            -moz-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
            box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);

            position: relative;
            display: inline-block;
            text-transform: uppercase;
            !important;
        }
    </style>
@stop
