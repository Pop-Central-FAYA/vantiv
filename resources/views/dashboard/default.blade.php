@extends('layouts.new_app')

@section('title')
    <title>Dashboard</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Broadcaster Report</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Dashboard</a></li>
                        {{--<li><a href="#">Reports</a></li>--}}
                    </ul>
                </div>
            </div>

                {{--<div class="row">--}}
                    {{--Dashboard charts begins here--}}
                <div class="row">
                    <div class="col-md-12">
                        <canvas id="containerPeriodic" style="width: 512px; height: 150px"></canvas>
                        {{--<div id="containerPeriodic" style="min-width: 310px; height: 400px; margin: 0 auto"></div>--}}
                    </div>
                </div>
            <p><br></p>
            <p><br></p>
            <hr>

                    {{--<div class="row">--}}
                <div class="row">
                    <div class="col-md-6">
                        <div id="containerDayparts" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>
                    <div class="col-md-6">
                        <div id="containerDays" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                    </div>
                </div>
            <p><br></p>
            <p><br></p>
                    {{--</div>--}}
                    <hr>
                    {{--<div class="row">--}}
                 <div class="row">
                     <div class="col-md-12">
                         <div id="containerTotal"></div>
                         {{--<div id="containerTotal"></div>--}}
                     </div>
                 </div>
                    <p><br></p>
                    <p><br></p>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-body table-responsive no-padding">
                                <h4 class="text-center"><p>High Value Customers</p></h4>
                                <table class="table table-hover" style="font-size:16px">
                                    <tr>
                                        <th>S/N</th>
                                        <th>Customer Name</th>
                                        <th>Number of campaigns</th>
                                        <th>Number of Adslots</th>
                                        <th>Total Amount/Revenue</th>
                                    </tr>
                                    @foreach($campaign as $camp)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $camp['customer_name'] }}</td>
                                            <td>{{ $camp['number_of_campaign'] }}</td>
                                            <td>{{ $camp['total_adslot'] }}</td>
                                            <td>&#8358;{{ number_format($camp['payment'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            {{--</div>--}}
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="box-body table-responsive no-padding">
                                <h4 class="text-center"><p>Paid Invoices</p></h4>
                                <table class="table table-hover" style="font-size:16px">
                                    <tr>
                                        <th>S/N</th>
                                        <th>Campaign Name</th>
                                        <th>Customer</th>
                                        <th>Date</th>
                                        <th>Due Date</th>
                                    </tr>
                                    @foreach($invoice as $invoices)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $invoices['campaign_name'] }}</td>
                                            <td>{{ $invoices['customer'] }}</td>
                                            <td>{{ $invoices['date'] }}</td>
                                            <td>{{ $invoices['date_due'] }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                            {{--</div>--}}
                        </div>
                    </div>

            </div>
    </div>

        </div>
    </div>

@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>

    <script>

        <?php echo "var campaign_volume = ".$volume . ";\n"; ?>
        <?php echo "var campaign_month = ".$month . ";\n"; ?>
        <?php echo "var day_parts = ".$high_dayp .";\n"; ?>
        <?php echo "var day_pie = ".$days .";\n"; ?>
        <?php echo "var periodic_month = ".$mon .";\n"; ?>
        <?php echo "var periodic_price = ".$price .";\n"; ?>
        <?php echo "var periodic_adslot = ".$adslot .";\n"; ?>

        //Bar chart on periodic sales report using chart.js
        var chartData = {
            labels: periodic_month,
            datasets: [{
                type: 'bar',
                label: 'Number of Adslots',
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                // borderColor: window.chartColors.blue,
                borderWidth: 2,
                fill: false,
                data: periodic_adslot
            }, {
                type: 'bar',
                label: 'Total Amount',
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255,99,132,1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                // backgroundColor: window.chartColors.red,
                data: periodic_price,
                borderColor: 'white',
                borderWidth: 2
            }]
        };

        var ctx = document.getElementById('containerPeriodic').getContext('2d');
        window.myMixedChart = new Chart(ctx, {
            type: 'bar',
            data: chartData,
            options: {
                responsive: true,
                title: {
                    display: true,
                    text: 'Periodic Sales Report'
                },
                tooltips: {
                    mode: 'index',
                    intersect: true
                }
            }
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
                text: 'High Performing Dayparts'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            credits: {
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
                data: day_parts
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
                text: 'High Performing Days'
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
            series: [{
                name: 'Performing Days',
                colorByPoint: true,
                data: day_pie
            }]
        });

        //Bar chart for Total Volume of Campaigns
        var chart = Highcharts.chart('containerTotal', {

            title: {
                text: 'Total Volume of Campaigns'
            },

            xAxis: {
                categories: campaign_month
            },

            series: [{
                type: 'column',
                colorByPoint: true,
                data: campaign_volume,
                showInLegend: false
            }]

        });


    </script>
@stop