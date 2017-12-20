@extends('layouts.app')

@section('content')

    @section('title', 'Faya | Dashboard')
    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Welcome {{ Auth::user()->username }}!

        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-lg-3 col-md-6">
                <div class="panel panel-widget panel-primary">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-7">
                                <div class="title">Total Users</div>
                                <div class="text-huge">26</div>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user-plus fa-5x"></i>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-left">View All Users</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="panel panel-widget panel-success">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-7">
                                <div class="title">Total Broadcaster</div>
                                <div class="text-huge">262</div>
                            </div>
                            <div class="icon">
                                <i class="fa fa-users fa-5x"></i>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-left">View All Broadcaster</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="panel panel-widget panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-7">
                                <div class="title">Total Agency</div>
                                <div class="text-huge">12</div>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user-times fa-5x"></i>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-left">View All Agency</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-md-6">
                <div class="panel panel-widget panel-default">
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-xs-7">
                                <div class="title">Total Agency</div>
                                <div class="text-huge">12</div>
                            </div>
                            <div class="icon">
                                <i class="fa fa-user-times fa-5x"></i>
                            </div>
                        </div>
                    </div>
                    <a href="#">
                        <div class="panel-footer">
                            <span class="pull-left">View All Agency</span>
                            <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                            <div class="clearfix"></div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- /.col (RIGHT) -->
        </div>
        <!-- /.row -->

        {{--Dashboard charts begins here--}}
        <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <div id="containerDayparts" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
            </div>
            <div class="col-md-6">
                <div id="containerDays" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <div id="containerInventory"></div>
            </div>
            <div class="col-md-6">
                <div id="containerTotal"></div>
            </div>
        </div>
        <hr>
        {{--Dashboard charts ends here--}}

        {{--Table for High Value Customer--}}
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
            </div>
        </div>

    </section>
        <!-- /.content -->
    {{--{{ dd($volume, $month) }}--}}
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
        // Bar chart for periodic sales report
        Highcharts.chart('container', {
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: 'Periodic Sales Report'
            },
            xAxis: [{
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                crosshair: true
            }],
            yAxis: [{ // Primary yAxis
                labels: {
                    format: '{value} Naira',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                },
                title: {
                    text: 'Price',
                    style: {
                        color: Highcharts.getOptions().colors[1]
                    }
                }
            }, { // Secondary yAxis
                title: {
                    text: 'Adslots',
                    style: {
                        color: Highcharts.getOptions().colors[0]
                    }
                },
                labels: {
                    format: '{value} ',
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
                name: 'Rainfall',
                type: 'column',
                yAxis: 1,
                data: [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
                tooltip: {
                    valueSuffix: ' mm'
                }

            }, {
                name: 'Temperature',
                type: 'spline',
                data: [7.0, 6.9, 9.5, 14.5, 18.2, 21.5, 25.2, 26.5, 23.3, 18.3, 13.9, 9.6],
                tooltip: {
                    valueSuffix: '°C'
                }
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
                text: 'High Performing Dayparts'
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
                name: 'Brands',
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
                name: 'Brands',
                colorByPoint: true,
                data: day_pie
            }]
        });

        //Barchart for Inventory fill rate
        var chart = Highcharts.chart('containerInventory', {

            title: {
                text: 'Inventory Fill Rate'
            },

            xAxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },

            series: [{
                type: 'column',
                colorByPoint: true,
                data: [29.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4],
                showInLegend: false
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