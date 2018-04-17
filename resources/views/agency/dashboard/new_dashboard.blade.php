@extends('layouts.new_app')
@section('title')
    <title>Faya - Agency Dashboard</title>
@stop

@section('content')
    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Agency Dashboard </h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Agency</a></li>
                        <li><a href="#">Dashboard </a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <p><h2><i class="fa fa-user"></i> All Clients</h2></p>
                        </div>
                        <div class="panel-body">
                            <h1>{{ $count_client }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <p><h2><i class="fa fa-th"></i></i> All Campaigns</h2></p>
                        </div>
                        <div class="panel-body">
                            <h1>{{ $count_campaigns }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <p><h2>All Brands</h2></p>
                        </div>
                        <div class="panel-body">
                            <h1>{{ $count_brands }}</h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            <p><h2>All Invoices</h2></p>
                        </div>
                        <div class="panel-body">
                            <h1>{{ $count_invoice }}</h1>
                        </div>
                    </div>
                </div>
            </div>


            {{--<div class="col-12 chart-top">--}}
            <div class="row">
                <div class="col-12">
                    <div class="Sales">
                        <h2>Periodic Spend Report</h2>
                        <p>Total amount spent on channel</p>
                        <div class="row">
                            <div class="col-4 content">
                                <form action="{{ route('agency.dashboard.broad')  }}" id="search_by_broad" method="GET">
                                    {{ csrf_field() }}
                                    <label for="channel">Channels:</label>
                                    <select class="form-control" name="broadcaster" id="broadcaster">
                                        @foreach($broadcaster as $broadcasters)
                                            <option value="{{ $broadcasters->id }}">
                                                {{ $broadcasters->brand }}
                                            </option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                        </div>
                        <p><br></p>
                        <div id="containerPeriodic_total_per_chanel" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <!-- AREA CHART -->
                    <h2>Periodic Spend Report</h2>
                    <p>Total amount spent on brand</p>
                    <form action="{{ route('agency.dashboard.data') }}" id="search_by_brand" method="GET">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-4">
                                <label for="channel">Brands:</label>
                                <select class="form-control" name="brand" id="brand">
                                    @foreach($brand as $brands)
                                        <option value="{{ $brands->id }}">{{ $brands->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </form>
                    <p><br></p>
                    <div id="containerPeriodic_total_per_brand" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

                </div>
            </div>
            <hr>
            <div class="row">
                <h2>Percentage Periodic Spent Report on Products for <?php echo date('F, Y')?> </h2><br>
            </div>
            <div class="row">
                <p>Percentege spent on product per month</p>
            </div>
            <div class="row">
                <div class="col-md-4 content_month">
                    <form action="{{ route('agency.month') }}" method="get" id="filter_month">
                        <label for="month">Months:</label>
                        <select name="month" class="form-control" id="month">
                            @foreach($months as $month)
                                <option value="{{ $month }}"
                                        @if($current_month === $month)
                                        selected
                                        @endif
                                >{{ $month }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="Our-Visitors">
                        <div id="containerPerProduct" style="min-width: 900px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-12">
                    <div class="col-12 Total-rev">
                        <h2>Budget Pacing Report</h2>
                        <div id="containerBudgetPacing" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>
                <hr>
                <p><br></p>
                {{--</div>--}}
            <div class="row">
                <div class="col-12 recents">
                    <div class="col-12">
                        <div class="col-12 recents-inner">
                            <div class="recent-head">
                                <h1>recent invoice</h1>

                            </div>
                            <div class="summary">
                                <p>Total approved invoices {{ $invoice_approval }},upapproved {{ $invoice_unapproval }}.</p>
                                <a style="text-decoration: none;" href="{{ route('invoices.all') }}">All Invoices<i class="fa fa-arrow-right" aria-hidden="true"></i></a> </div>
                            <table class="table">
                                <thead>
                                    <th>Invoice#</th>
                                    <th>Customer Name</th>
                                    <th>Brand</th>
                                    <th>Amount</th>
                                    <th>Refunded Amount</th>
                                    <th>Status</th>
                                </thead>
                                <tbody>
                                @foreach($all_invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice['invoice_number'] }}</td>
                                        <td>{{ $invoice['campaign_name'] }}</td>
                                        <td>{{ $invoice['campaign_brand'] }}</td>
                                        <td>&#8358;{{ $invoice['actual_amount_paid'] }}</td>
                                        <td>&#8358;{{ $invoice['refunded_amount'] }}</td>
                                        <td>
                                            @if ($invoice['status'] == 1)
                                                <label style="font-size: 16px" class="label label-success">
                                                    Approved
                                                </label>
                                            @elseif ($invoice['status'] == 0)
                                                <label style="font-size: 16px" class="label label-warning">
                                                    Pending
                                                </label>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>

@stop
@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('agency_asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('agency_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('agency_asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('agency_asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('agency_asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="{{ asset('agency_asset/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('agency_asset/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('agency_asset/plugins/fastclick/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('agency_asset/dist/js/app.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>

    <script>
        <?php echo "var date = ".$date . ";\n"; ?>
        <?php echo "var amount = ".$amount . ";\n"; ?>
        <?php echo "var name = ".$name .";\n"; ?>
        <?php echo "var b_pacing =".$b_pacing ."\n"; ?>
        <?php echo "var periodic_data =".$periodic_data ."\n"; ?>
        <?php echo "var brand_date =".$bra_dates ."\n"; ?>
        <?php echo "var brand_name =".$bra_na ."\n"; ?>
        <?php echo "var brand_amount =".$bra_am ."\n"; ?>

        $(document).ready(function () {

            $("#broadcaster").change(function () {
                // $("#load_broad").show();
                $(".content").css({
                    opacity: 0.5
                });

                $('#load_broad').html('<img src="{{ asset('loader.gif') }}" align="absmiddle"> Please wait while we process your request...');
                var br_id = $("#broadcaster").val();
                var url = $("#search_by_broad").attr('action');
                $.get(url, {'br_id': br_id, '_token':$('input[name=_token]').val()}, function(data) {
                    $(".content").css({
                        opacity: 1
                    });
                    var chart = Highcharts.chart('containerPeriodic_total_per_chanel', {

                        title: {
                            text: 'Periodic Spent Report'
                        },
                        credits: {
                            enabled: false
                        },

                        xAxis: {
                            categories: data.date
                        },

                        series: [{
                            type: 'column',
                            colorByPoint: true,
                            data: data.amount_price,
                            showInLegend: false
                        }]

                    });
                });
            });

            var chart = Highcharts.chart('containerPeriodic_total_per_chanel', {

                title: {
                    text: 'Periodic Spent Report'
                },
                credits: {
                    enabled: false
                },

                xAxis: {
                    categories: date
                },

                series: [{
                    type: 'column',
                    colorByPoint: true,
                    data: amount,
                    showInLegend: false
                }]

            });

            $("#month").change(function () {

                $(".content_month").css({
                    opacity: 0.5
                });

                var month = $("#month").val();
                var url = $("#filter_month").attr('action');

                $.get(url, {'month': month, '_token':$('input[name=_token]').val()}, function(data) {

                    $(".content_month").css({
                        opacity: 1
                    });

                    Highcharts.chart('containerPerProduct', {
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
                            name: 'Products',
                            colorByPoint: true,
                            data: data.pro_month
                        }]
                    });
                });

            });

            $("#brand").change(function () {
                $(".content").css({
                    opacity: 0.5
                });
                var br_id = $("#brand").val();
                var url = $("#search_by_brand").attr('action');
                $.get(url, {'br_id': br_id, '_token':$('input[name=_token]').val()}, function(data) {
                    $("#load_broad").hide();
                    $(".content").css({
                        opacity: 1
                    });

                    Highcharts.chart('containerPeriodic_total_per_brand', {

                        title: {
                            text: 'Periodic Spent Report'
                        },
                        credits: {
                            enabled: false
                        },

                        xAxis: {
                            categories: data.date
                        },

                        series: [{
                            type: 'column',
                            colorByPoint: true,
                            data: data.amount_price,
                            showInLegend: false
                        }]

                    });

                });
            });

            Highcharts.chart('containerPerProduct', {
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
                    name: 'Products',
                    colorByPoint: true,
                    data: periodic_data
                }]
            });

            Highcharts.chart('containerPeriodic_total_per_brand', {

                title: {
                    text: 'Periodic Spent Report'
                },
                credits: {
                    enabled: false
                },

                xAxis: {
                    categories: brand_date
                },

                series: [{
                    type: 'column',
                    colorByPoint: true,
                    data: brand_amount,
                    showInLegend: false
                }]

            });

            Highcharts.chart('containerBudgetPacing', {
                chart: {
                    zoomType: 'x'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    type: 'datetime'
                },
                yAxis: {
                    title: {
                        text: 'Amount'
                    }
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    area: {
                        fillColor: {
                            linearGradient: {
                                x1: 0,
                                y1: 0,
                                x2: 0,
                                y2: 1
                            },
                            stops: [
                                [0, Highcharts.getOptions().colors[0]],
                                [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                            ]
                        },
                        marker: {
                            radius: 2
                        },
                        lineWidth: 1,
                        states: {
                            hover: {
                                lineWidth: 1
                            }
                        },
                        threshold: null
                    }
                },

                series: [{
                    type: 'area',
                    name: 'Budget Pacing',
                    data: b_pacing
                }]
            });

        })

    </script>

@stop