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
                        <h3><a href="{{ route('agency.campaign.all',['status'=>'active']) }}">{{ $count_active_campaigns }}</a></h3>
                    </div>

                    <div class="column col_3">
                        <span class="weight_medium small_faint uppercased">Campaigns On Hold</span>
                        <h3><a href="{{ route('agency.campaign.all',['status'=>'on_hold']) }}" style="color: red;">{{ $count_campaigns_on_hold }}</a></h3>
                    </div>

                    <div class="column col_3">
                        <span class="weight_medium small_faint uppercased">All Clients</span>
                        <h3><a href="{{ route('clients.list') }}">{{ $count_all_clients }}</a></h3>
                    </div>

                    <div class="column col_3">
                        <span class="weight_medium small_faint uppercased">All Brands</span>
                        <h3><a href="{{ route('brand.all') }}">{{ $count_all_brands }}</a></h3>
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
                            <li class="pie_legend active"><span class="weight_medium">{{ round($tv_rating['percentage_active']) }}%</span> Active</li>
                            <li class="pie_legend pending"><span class="weight_medium">{{ round($tv_rating['percentage_pending']) }}%</span> Pending</li>
                            <li class="pie_legend finished"><span class="weight_medium">{{ round($tv_rating['percentage_finished']) }}%</span> Finished</li>
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
    <script>
        <?php echo "var tv_active = ".round($tv_rating['percentage_active']) . ";\n"; ?>
        <?php echo "var tv_pending = ".round($tv_rating['percentage_pending']) . ";\n"; ?>
        <?php echo "var tv_finished = ".round($tv_rating['percentage_finished']) . ";\n"; ?>
        <?php echo "var radio_active = ".round($radio_rating['percentage_active']) . ";\n"; ?>
        <?php echo "var radio_pending = ".round($radio_rating['percentage_pending']) . ";\n"; ?>
        <?php echo "var radio_finished = ".round($radio_rating['percentage_finished']) . ";\n"; ?>

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
@stop