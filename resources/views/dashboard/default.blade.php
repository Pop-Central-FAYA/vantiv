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

                <div class="row">
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
                        <div class="col-md-12">
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
                    <hr>

                    {{--Table for Paid Invoice--}}
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

        console.log(periodic_adslot);
        // Bar chart for periodic sales report
        Highcharts.chart('container', {
            chart: {
                zoomType: 'xy'
            },
            title: {
                text: 'Periodic Sales Report'
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
                    format: '{value}',
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

        //Barchart for Inventory fill rate
        //inventory fill rate
        Highcharts.chart('containerInventory', {
            title: {
                text: 'Inventory Fill Rate '
            },
            xAxis: {
                categories: ['Apples', 'Oranges', 'Pears', 'Bananas', 'Plums']
            },
            labels: {
                items: [{
                    html: 'Inventory Fill Rate',
                    style: {
                        left: '50px',
                        top: '18px',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                }]
            },
            series: [{
                type: 'column',
                name: 'Jane',
                data: [3, 2, 1, 3, 4]
            }, {
                type: 'column',
                name: 'John',
                data: [2, 3, 5, 7, 6]
            }, {
                type: 'column',
                name: 'Joe',
                data: [4, 3, 3, 9, 0]
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