@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Client List</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Client</h2>
            </div>
        </div>


        <!-- main stats -->
        <div class="the_frame clearfix mb ">
            <div class="border_bottom clearfix client_name">
                <a href="{{ route('clients.list') }}" class="back_icon block_disp left"></a>
                <div class="left">
                    <h2 class='sub_header'>{{ $client[0]->company_name }}</h2>
                    <p class="small_faint">{{ $client[0]->location }}</p>
                </div>

                <span class="client_ava right"><img src="{{ $client[0]->company_logo ? asset(decrypt($client[0]->company_logo)) : '' }}"></span>
            </div>

            <div class="clearfix client_personal">
                <div class="column col_3">
                    <span class="small_faint">Account Executive</span>
                    <p class='weight_medium'>{{ $user_details[0]->firstname.' '.$user_details[0]->lastname }}</p>
                </div>

                <div class="column col_3">
                    <span class="small_faint">Email</span>
                    <p class='weight_medium'>{{ $user_details[0]->email }}</p>
                </div>

                <div class="column col_3">
                    <span class="small_faint">Phone</span>
                    <p class='weight_medium'>{{ $user_details[0]->phone_number }}</p>
                </div>

                <div class="column col_3">
                    <span class="small_faint">Joined</span>
                    <p class='weight_medium'>{{ date('M j, Y', strtotime($user_details[0]->time_created)) }}</p>
                </div>
            </div>
        </div>

        <!-- client charts -->
        <div class="the_frame mb client_charts content_month">
            <form action="{{ route('client.month', ['client_id' => $client_id]) }}" id="client_month" method="get">

                <div class="filters chart_filters border_bottom clearfix">
                    <div class="column col_6 date_filter">
                        <a href="">1M</a>
                        <a id="yearly_client" href="{{ route('client.year', ['client_id' => $client_id]) }}">1Y</a>
                    </div>

                    <div class="column col_2 m-b">
                        <input type="text" class="flatpickr" id="start_date" name="start_date" placeholder="Start Date">
                    </div>
                    <div class="column col_2 m-b">
                        <input type="text" class="flatpickr" id="stop_date" name="stop_date" placeholder="Stop Date">
                    </div>
                    <div class="column col_2 m-b">
                        <button type="button" id="filterDate" class="btn small_btn">Filter</button>
                        {{--<button type="button" id="filterDate">Filter</button>--}}
                    </div>
                </div>

                <div class="the_stats clearfix mb border_bottom mb" id="this_box">
                    <div class="active_fill column col_4">
                        <span class="small_faint uppercased weight_medium">Total Campaigns</span>
                        <h3>{{ count($all_campaign_this_month) }}</h3>
                    </div>

                    <div class="column col_4">
                        <span class="small_faint uppercased weight_medium">Total Spent</span>
                        <h3>&#8358; {{ $total_this_month ? number_format($total_this_month[0]->total, 2) : 0 }}</h3>
                    </div>

                    <div class="column col_4">
                        <span class="small_faint uppercased weight_medium">Brands</span>
                        <h3>{{ $brand_this_month[0]->brand }}</h3>

                        <a href="" class="weight_medium small_font view_brands">View Brands</a>
                    </div>
                </div>

                <div class="the_stats clearfix mb border_bottom mb" id="show_this" style="display: none">
                    <div class="active_fill column col_4">
                        <span class="small_faint uppercased weight_medium">Total Campaigns</span>
                        <h3>{{ count($all_campaign_this_month) }}</h3>
                    </div>

                    <div class="column col_4">
                        <span class="small_faint uppercased weight_medium">Total Spent</span>
                        <h3>&#8358; {{ $total_this_month ? number_format($total_this_month[0]->total, 2) : 0 }}</h3>
                    </div>

                    <div class="column col_4">
                        <span class="small_faint uppercased weight_medium">Brands</span>
                        <h3>{{ $brand_this_month[0]->brand }}</h3>

                        <a href="" class="weight_medium small_font view_brands">View Brands</a>
                    </div>
                </div>


                <div class="main_chart padd">
                    <p><br></p><br>
                    <div id="container" style="min-width: 310px; height: 350px; margin: 0 auto"></div>
                </div>

            </form>

        </div>


        <div class="the_frame client_dets mb4">

            <div class="tab_header m4 border_bottom clearfix">
                <a href="#history">Campaign History</a>
                <a href="#brands">Brands</a>
            </div>

            <div class="tab_contain">
                <div class="tab_content" id="history">

                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Brand</th>
                            <th>Product</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Budget</th>
                            <th>Ad Slots</th>
                            <th>Status</th>
                        </tr>
                        @foreach($all_campaigns as $all_campaign)
                            <tr>
                                <td>243</td>
                                <td><a href="{{ route('agency.campaign.details', ['id' => $all_campaign['camp_id']]) }}">{{ $all_campaign['name'] }}</a></td>
                                <td>{{ ucfirst($all_campaign['brand']) }}</td>
                                <td>{{ $all_campaign['product'] }}</td>
                                <td>{{ $all_campaign['start_date'] }}</td>
                                <td>{{ $all_campaign['end_date'] }}</td>
                                <td>&#8358;{{ $all_campaign['budget'] }}</td>
                                <td>{{ $all_campaign['adslots'] }}</td>
                                @if($all_campaign['status'] === 'active')
                                    <td><span class="span_state status_success">Active</span></td>
                                @elseif($all_campaign['status'] === 'expired')
                                    <td><span class="span_state status_danger">Expired</span></td>
                                @else
                                    <td><span class="span_state status_pending">Pending</span></td>
                                @endif
                            </tr>
                        @endforeach
                    </table>

                </div>
                <!-- end -->

                <!-- brand -->
                <div class="tab_content" id="brands">

                    <div class="similar_table p_t">
                        <div class="filters clearfix mb">
                            <div class="right col_6 clearfix">
                                <div class="col_7 column">
                                    <div class="header_search">
                                        <form>
                                            <input type="text" placeholder="Search...">
                                        </form>
                                    </div>
                                </div>

                                <div class="col_5 column">
                                    <a href="" class="btn small_btn"><span class="_plus"></span> New Brand</a>
                                </div>
                            </div>
                        </div>
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="weight_medium small_faint block_disp column col_4 padd">Brand</span>
                            <span class="weight_medium small_faint block_disp column col_2">All Campaigns</span>
                            <span class="weight_medium small_faint block_disp column col_2">Total Expense</span>
                            <span class="weight_medium small_faint block_disp column col_3">Last Campaign</span>
                            <span class="weight_medium block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($all_brands as $all_brand)
                        <div class="_table_item the_frame clearfix">
                            <div class="padd column col_4">
                                <span class="client_ava"><img src="{{ $all_brand['image_url'] ? asset(decrypt($all_brand['image_url'])) : '' }}"></span>
                                <p>{{ ucfirst($all_brand['brand']) }}</p>
                                <span class="small_faint">Added {{ date('M j, Y', strtotime($all_brand['date'])) }}</span>
                            </div>
                            <div class="column col_2">{{ $all_brand['campaigns'] }}</div>
                            <div class="column col_2">&#8358; {{ $all_brand['total'] }}</div>
                            <div class="column col_3">{{ $all_brand['last_campaign'] }}</div>
                            <div class="column col_1">
                                <span class="more_icon">
                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>

                                        <div class="more_more">
                                            <a href="{{ route('campaign.brand.client', ['id' => $all_brand['id'], 'client_id' => $client_id]) }}">Details</a>
                                            {{--<a href="" class="color_red">Delete</a>--}}
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </div>
                        @endforeach
                        <!-- table item end -->
                    </div>

                </div>
                <!-- end -->
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        <?php echo "var week_date = ".$week_date. ";\n"; ?>
        <?php echo "var week_amount = ".$week_payment. ";\n"; ?>
        $(document).ready(function() {

            flatpickr(".flatpickr", {
                altInput: true,
            });

            //default chart
            Highcharts.chart('container', {
                chart: {
                    type: 'area'
                },
                xAxis: {
                    categories: week_date
                },
                title:{
                    text:''
                },
                yAxis: {
                    title: {
                        text: 'Number of Campaigns'
                    },
                    labels: {
                        formatter: function () {
                            return this.value / 1000 + 'k';
                        }
                    }
                },
                tooltip: {
                    pointFormat: '<b>{series.name} {point.y:,.0f}</b><br/> '
                },
                plotOptions: {
                    area: {
                        pointStart: 0,
                        marker: {
                            enabled: false,
                            symbol: 'circle',
                            radius: 2,
                            states: {
                                hover: {
                                    enabled: true
                                }
                            }
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                title:{
                    text:''
                },
                exporting: { enabled: false },
                series: [{
                    name: 'Total Budget',
                    color: '#00C4CA',
                    data: week_amount,
                }]
            });

            //filter by date
            $("#filterDate").click(function () {

                $(".content_month").css({
                    opacity: 0.3
                });

                var start_date = $("#start_date").val();
                var stop_date = $("#stop_date").val();
                var url = $("#client_month").attr('action');
                var user_id = "<?php echo $client_id; ?>";

                $.get(url, {'start_date': start_date, 'stop_date': stop_date, 'user_id' : user_id, '_token':$('input[name=_token]').val()}, function(data) {

                    $(".content_month").css({
                        opacity: 1
                    });

                    var big_html =
                        '                    <div class="active_fill column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Campaigns</span>\n' +
                        '                        <h3>'+data.all_campaign.length+'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Spent</span>\n' +
                        '                        <h3>&#8358;'+ data.all_total +'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Brands</span>\n' +
                        '                        <h3>'+data.all_brand.length +'</h3>\n' +
                        '\n' +
                        '                        <a href="" class="weight_medium small_font view_brands">View Brands</a>\n' +
                        '                    </div>\n';

                    $("#this_box").hide();
                    $('#show_this').show();
                    $('#show_this').html(big_html);


                    Highcharts.chart('container', {
                        chart: {
                            type: 'area'
                        },
                        xAxis: {
                            categories: data.monthly_date
                        },
                        title:{
                            text:''
                        },
                        yAxis: {
                            title: {
                                text: 'Number of Campaigns'
                            },
                            labels: {
                                formatter: function () {
                                    return this.value / 1000 + 'k';
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '<b>{series.name} {point.y:,.0f}</b><br/> '
                        },
                        plotOptions: {
                            area: {
                                pointStart: 0,
                                marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        exporting: { enabled: false },
                        series: [{
                            name: 'Total Budget',
                            color: '#00C4CA',
                            data: data.monthly_total,
                        }]
                    });


                })
            })

            //Yearly filter
            $("#yearly_client").click(function () {
                event.preventDefault();
                $(".content_month").css({
                    opacity: 0.3
                });

                var url = $("#yearly_client").attr('href');
                var user_id = "<?php echo $client_id; ?>";

                $.get(url, {'user_id' : user_id, '_token':$('input[name=_token]').val()}, function(data) {

                    $(".content_month").css({
                        opacity: 1
                    });

                    var big_html =
                        '                    <div class="active_fill column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Campaigns</span>\n' +
                        '                        <h3>'+data.all_campaign.length+'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Total Spent</span>\n' +
                        '                        <h3>&#8358;'+ data.all_total +'</h3>\n' +
                        '                    </div>\n' +
                        '\n' +
                        '                    <div class="column col_4">\n' +
                        '                        <span class="small_faint uppercased weight_medium">Brands</span>\n' +
                        '                        <h3>'+data.all_brand.length +'</h3>\n' +
                        '\n' +
                        '                        <a href="" class="weight_medium small_font view_brands">View Brands</a>\n' +
                        '                    </div>\n';

                    $("#this_box").hide();
                    $('#show_this').show();
                    $('#show_this').html(big_html);
                    $("#default").removeClass('active');
                    $("#yearly_client").addClass('active');

                    Highcharts.chart('container', {
                        chart: {
                            type: 'area'
                        },
                        xAxis: {
                            categories: data.monthly_date
                        },
                        title:{
                            text:''
                        },
                        yAxis: {
                            title: {
                                text: 'Number of Campaigns'
                            },
                            labels: {
                                formatter: function () {
                                    return this.value / 1000 + 'k';
                                }
                            }
                        },
                        tooltip: {
                            pointFormat: '<b>{series.name} {point.y:,.0f}</b><br/> '
                        },
                        plotOptions: {
                            area: {
                                pointStart: 0,
                                marker: {
                                    enabled: false,
                                    symbol: 'circle',
                                    radius: 2,
                                    states: {
                                        hover: {
                                            enabled: true
                                        }
                                    }
                                }
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        title:{
                            text:''
                        },
                        exporting: { enabled: false },
                        series: [{
                            name: 'Total Budget',
                            color: '#00C4CA',
                            data: data.monthly_total,
                        }]
                    });


                })

            })

        });
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <style>
        .highcharts-grid path { display: none;}
    </style>
@stop