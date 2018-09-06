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
        </div>

    {{--sidebar--}}
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    <!-- main stats -->
        {{--<div class="the_stats the_frame clearfix mb4">--}}
            {{--<div class="column col_3">--}}
                {{--<span class="weight_medium small_faint uppercased">Active Campaigns</span>--}}
                {{--<h3><a href="{{ route('campaign.all') }}">{{ count($active_campaigns) }}</a></h3>--}}
            {{--</div>--}}

            {{--<div class="column col_3">--}}
                {{--<span class="weight_medium small_faint uppercased">All Walk-Ins</span>--}}
                {{--<h3><a href="{{ route('walkins.all') }}">{{ count($walkins) }}</a></h3>--}}
            {{--</div>--}}

            {{--<div class="column col_3">--}}
                {{--<span class="weight_medium small_faint uppercased">Pending MPO's</span>--}}
                {{--<h3><a href="{{ route('pending-mpos') }}" style="color: red;">{{ count($pending_mpos) }}</a></h3>--}}
            {{--</div>--}}

            {{--<div class="column col_3">--}}
                {{--<span class="weight_medium small_faint uppercased">All Brands</span>--}}
                {{--<h3><a href="{{ route('brand.all') }}">{{ count($brands) }}</a></h3>--}}
            {{--</div>--}}
        {{--</div>--}}


        <!-- client charts -->
        <div class="clearfix">
            <p class="uppercased weight_medium">Periodic Spend Chart</p>
            <br>

            <div id="containerPS" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>

        <p><br></p>

        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_6 p-t">
                    <p class="uppercased weight_medium" style="text-align: center">High Performing Day Parts</p>
                    <p><br></p>
                    <div id="containerDayparts" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                </div>

                <div class="column col_6 p-t">
                    <p class="uppercased weight_medium" style="text-align: center">High Performing Days</p>
                    <p><br></p>
                    <div id="containerDays" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                </div>
            </div>
        </div>

        <p><br></p>
        <p class="uppercased weight_medium">Paid Invoices</p>
        <p><br></p>
        <div class="the_frame client_dets mb4">
            <!-- campaigns table -->
            <table>
                <tr>
                    <th>Invoice Number</th>
                    <th>Campaign Name</th>
                    <th>Customer Name</th>
                    <th>Start Date</th>
                    <th>Due Date</th>
                </tr>
                @foreach($paid_invoices as $paid_invoice)
                    <tr>
                        <td>{{ $paid_invoice['invoice_number'] }}</td>
                        <td><a href="{{ route('broadcaster.campaign.details', ['id' => $paid_invoice['campaign_id']]) }}">{{ $paid_invoice['campaign_name'] }}</a></td>
                        <td>{{ $paid_invoice['customer'] }}</td>
                        <td>{{ $paid_invoice['date'] }}</td>
                        <td>{{ $paid_invoice['date_due'] }}</td>
                        <td><a href="">View</a></td>
                    </tr>
                @endforeach
            </table>
            <!-- end -->
        </div>

        <p><br></p>
        <p class="uppercased weight_medium">High Value Customer</p>
        <p><br></p>
        <div class="the_frame client_dets mb4">
            <!-- campaigns table -->
            <table>
                <tr>
                    <th>Customer Name</th>
                    <th>No of Campaigns</th>
                    <th>No of Slots</th>
                    <th>Amount Spent</th>
                </tr>
                @foreach($high_value_customers as $high_value_customer)
                    <tr>
                        <td>{{ $high_value_customer['customer_name'] }}</td>
                        <td>{{ $high_value_customer['number_of_campaigns'] }}</td>
                        <td>{{ $high_value_customer['total_adslots'] }}</td>
                        <td>{{ number_format($high_value_customer['payment'], 2) }}</td>
                    </tr>
                @endforeach
            </table>
            <!-- end -->
        </div>

    </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
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
        //periodic spend report
        <?php echo "var periodic_month = ".$monthly_periods .";\n"; ?>
        <?php echo "var periodic_price = ".$total_monthly_spend .";\n"; ?>
        <?php echo "var periodic_adslot = ".$adslot_monthly_count .";\n"; ?>

        <?php echo "var high_performing_days = ".$performing_days_data .";\n"; ?>

        <?php echo "var high_performing_dayparts = ".$high_performing_dayparts .";\n"; ?>

        Highcharts.chart('containerPS', {
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: ''
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            xAxis: [{
                categories: periodic_month,
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value}',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: 'Adslot',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: 'Price (Naira)',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },

                labels: {
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                opposite: true
            }],
            tooltip: {
                shared: true
            },
            legend: {
                layout: 'vertical',
                align: 'left',
                x: 120,
                verticalAlign: 'top',
                y: 100,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            },
            series: [{
                name: 'Total Price',
                type: 'column',
                yAxis: 1,
                data: periodic_price,
                tooltip: {
                    valueSuffix: ''
                }

            }, {
                name: 'Number of Adslot',
                type: 'spline',
                data: periodic_adslot,
                tooltip: {
                    valueSuffix: ''
                }
            }]
        });

        //pie chart for performing days
        Highcharts.chart('containerDays', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: ''
            },

            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            series: [{
                name: 'Performing Days',
                colorByPoint: true,
                data: high_performing_days
            }]
        });

        // pie chart for high performing dayparts
        Highcharts.chart('containerDayparts', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: ''
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            credits: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Day Parts',
                colorByPoint: true,
                data: high_performing_dayparts
            }]
        });

        {{--$(document).ready(function( $ ) {--}}

            {{--flatpickr(".flatpickr", {--}}
                {{--altInput: true,--}}
            {{--});--}}

            {{--var Datefilter =  $('.dashboard_campaigns').DataTable({--}}
                {{--dom: 'Bfrtip',--}}
                {{--paging: true,--}}
                {{--serverSide: true,--}}
                {{--processing: true,--}}
                {{--"searching": false,--}}
                {{--aaSorting: [],--}}
                {{--ajax: {--}}
                    {{--url: '/agency/dashboard/campaigns',--}}
                    {{--data: function (d) {--}}
                        {{--d.start_date = $('input[name=start_date]').val();--}}
                        {{--d.stop_date = $('input[name=stop_date]').val();--}}
                    {{--}--}}
                {{--},--}}
                {{--columns: [--}}
                    {{--{data: 'id', name: 'id'},--}}
                    {{--{data: 'name', name: 'name'},--}}
                    {{--{data: 'brand', name: 'brand'},--}}
                    {{--{data: 'date_created', name: 'date_created'},--}}
                    {{--{data: 'budget', name: 'budget'},--}}
                    {{--{data: 'adslots', name: 'adslots'},--}}
                    {{--{data: 'status', name: 'status'},--}}

                {{--],--}}

            {{--});--}}

            {{--$('#dashboard_filter_campaign').on('click', function() {--}}
                {{--Datefilter.draw();--}}
            {{--});--}}
        {{--} );--}}
    </script>

@endsection

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <style>
        .highcharts-grid path { display: none;}
        .highcharts-legend {
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