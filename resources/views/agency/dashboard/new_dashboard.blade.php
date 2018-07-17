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
                    <div class="col_5 column">
                        <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
                    </div>

                    <div class="col_5 column">
                        <input type="text" name="stop_date" class="flatpickr" placeholder="Stop Date">
                    </div>

                    <div class="col_1 column">
                        <button type="button" id="dashboard_filter_campaign" class="btn small_btn">Filter</button>
                    </div>
                </div>
            </div>

            <!-- campaigns table -->
            <table class="display dashboard_campaigns">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Date Created</th>
                    <th>Budget</th>
                    <th>Ad Slots</th>
                    <th>Status</th>
                </tr>
                </thead>
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
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    {{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        <?php echo "var tv_active = ".round($active) . ";\n"; ?>
        <?php echo "var tv_pending = ".round($pending) . ";\n"; ?>
        <?php echo "var tv_finished = ".round($finished) . ";\n"; ?>

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
    </script>
    {{--datatables--}}
    <script>
        $(document).ready(function( $ ) {
            flatpickr(".flatpickr", {
                altInput: true,
            });
            var Datefilter =  $('.dashboard_campaigns').DataTable({
                dom: 'Bfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    url: '/agency/dashboard/campaigns',
                    data: function (d) {
                        d.start_date = $('input[name=start_date]').val();
                        d.stop_date = $('input[name=stop_date]').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'brand', name: 'brand'},
                    {data: 'date_created', name: 'date_created'},
                    {data: 'budget', name: 'budget'},
                    {data: 'adslots', name: 'adslots'},
                    {data: 'status', name: 'status'},
                ],
            });

            $('#dashboard_filter_campaign').on('click', function() {
                Datefilter.draw();
            });
        } );
    </script>
@stop

@section('styles')
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>--}}
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
@stop
