@extends('layouts.faya_app')

@section('title')
    <title> FAYA | DASHBOARD</title>
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
        <div class="the_stats the_frame clearfix mb4">
            <div class="column col_3">
                <span class="weight_medium small_faint uppercased">Active Campaigns</span>
                <h3>{{ count($active_campaigns) }}</h3>
            </div>

            <div class="column col_3">
                <span class="weight_medium small_faint uppercased">All Clients</span>
                <h3>{{ count($clients) }}</h3>
            </div>

            <div class="column col_3">
                <span class="weight_medium small_faint uppercased">Pending Invoices</span>
                <h3>{{ count($pending_invoices) }}</h3>
            </div>

            <div class="column col_3">
                <span class="weight_medium small_faint uppercased">All Brands</span>
                <h3>{{ count($all_brands) }}</h3>
            </div>
        </div>


        <!-- client charts -->
        <div class="clearfix dashboard_pies">
            <!-- tv -->
            <div class="">
                <div class="pie_icon margin_center">
                    <img src="{{ asset('new_frontend/img/tv.svg') }}">
                </div>
                <p class="align_center">TV</p>

                <div id="tv" style="height: 150px"></div>

                <ul>
                    <li class="pie_legend active"><span class="weight_medium">{{ round($active, 2) }}%</span> Active</li>
                    <li class="pie_legend pending"><span class="weight_medium">{{ round($pending, 2) }}%</span> Pending</li>
                    <li class="pie_legend finished"><span class="weight_medium">{{ round($finished, 2) }}%</span> Finished</li>
                </ul>
            </div>

            <!-- radio -->
            <div class="">
                <div class="pie_icon margin_center">
                    <img src="{{ asset('new_frontend/img/radio.svg') }}">
                </div>
                <p class="align_center">Radio</p>

                <div class="_pie_chart"></div>

                <ul>
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

                <div class="_pie_chart"></div>

                <ul>
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

                <div class="_pie_chart"></div>

                <ul>
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

                <div class="_pie_chart"></div>

                <ul>
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

                <div class="_pie_chart"></div>

                <ul>
                    <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                    <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                    <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
                </ul>
            </div>

        </div>


        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_8 p-t">
                    <p class="uppercased weight_medium">All Campaigns</p>
                </div>

                <div class="column col_4 clearfix">
                    <div class="col_7 column">
                        <div class="header_search">
                            <form>
                                <input type="text" placeholder="Search...">
                            </form>
                        </div>
                    </div>

                    <div class="col_4 column shadow">
                        <div class="select_wrap">
                            <select>
                                <option>All Time</option>
                                <option>This Month</option>
                            </select>
                        </div>
                    </div>

                    <div class="col_1 column">
                        <a href="" class="export_table"></a>
                    </div>
                </div>
            </div>

            <!-- campaigns table -->
            <table>
                <tr>
                    <th></th>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Product</th>
                    <th>Date</th>
                    <th>Budget</th>
                    <th>Ad Slots</th>
                    <th>Comp.</th>
                    <th>Status</th>
                    <th>Invoice</th>
                </tr>

                @foreach($agency_campaigns as $agency_campaign)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>{{ $agency_campaign['id'] }}</td>
                        <td><a href="{{ route('agency.campaign.details', ['id' => $agency_campaign['camp_id']]) }}">{{ $agency_campaign['name'] }}</a></td>
                        <td>{{ ucfirst($agency_campaign['brand']) }}</td>
                        <td>{{ $agency_campaign['product'] }}</td>
                        <td>12 June, 18</td>
                        <td>&#8358;{{ $agency_campaign['budget'] }}</td>
                        <td>{{ $agency_campaign['adslots'] }}</td>
                        <td>0%</td>
                        @if($agency_campaign['status'] === 'expired')
                            <td><span class="span_state status_danger">Finished</span></td>
                        @elseif($agency_campaign['status'] === 'active')
                            <td><span class="span_state status_success">Active</span></td>
                        @else
                            <td><span class="span_state status_pending">Pending</span></td>
                        @endif
                        <td><a href="">View</a></td>
                    </tr>
                @endforeach
            </table>
            <!-- end -->
        </div>

    </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script>
        <?php echo "var tv_active = ".round($active) . ";\n"; ?>
        <?php echo "var tv_pending = ".round($pending) . ";\n"; ?>
        <?php echo "var tv_finished = ".round($finished) . ";\n"; ?>

        Highcharts.chart('tv', {
            chart: {
                type: 'pie',
                options3d: {
                    enabled: true,
                    alpha: 45,
                    beta: 0
                }
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: ''
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    depth: 35,
                    dataLabels: {
                        enabled: false,
                        format: '{point.name}'
                    }
                }
            },
            credits: {
                enabled: false
            },
            exporting: { enabled: false },
            colors: ['#00C4CA', '#E89B0B', '#E8235F'],
            series: [{
                type: 'pie',
                name: 'Browser share',
                data: [
                    ['Active', tv_active],
                    ['Pending', tv_pending],
                    ['Finished', tv_finished],
                ]
            }]
        });
    </script>
@stop
