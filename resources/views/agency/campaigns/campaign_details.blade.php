@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Campaign Details</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">{{ $campaign_details['campaign_det']['campaign_name'] }}</h2>
            </div>
        </div>


        <!-- main stats -->
        <div class="the_frame clearfix mb">

            <div class="clearfix client_personal campaign_filter">
                <div class="column col_3">
                    <span class="small_faint">Clients</span>

                    <div class="select_wrap">
                        <select name="client" id="client">
                            @foreach($all_clients as $all_client)
                                <option value="{{ $all_client->user_id }}"
                                @if($all_client->user_id === $campaign_details['campaign_det']['company_user_id'])
                                    selected
                                @endif
                                >{{ $all_client->company_name ? $all_client->company_name : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="column col_3">
                    <span class="small_faint">Campaign Name</span>
                    <div class="show_this"></div>
                    <div class="select_wrap load_this" id="hide_this">
                        <select name="campaign" id="campaign">
                            @foreach($all_campaigns as $all_campaign)
                                <option value="{{ $all_campaign->campaign_id }}"
                                @if($all_campaign->campaign_id === $campaign_details['campaign_det']['campaign_id'])
                                    selected
                                @endif
                                >{{ $all_campaign->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="column col_3">
                    <span class="small_faint block_disp">Media Type</span>

                    <div class="select_wrap">
                        <select class="js-example-basic-multiple" name="channel" id="channel" multiple="multiple">
                            @foreach($campaign_details['campaign_det']['channel'] as $channel)
                                <option value="{{ $channel->id }}">{{ $channel->channel }}</option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="column col_3 check_this">
                    <span class="small_faint block_disp">Media Channel</span>


                </div>
            </div>
        </div>


        <!-- main charts -->
        <div class="clearfix mb3">

            <div class="column col_8 the_frame main_campaign_chart">

                <!-- filter -->
                <div class="filters clearfix">
                    <div class="column col_6">
                        <h4 class="small_faint uppercased weight_medium">Compliance</h4>
                    </div>
                    <div class="column col_4 clearfix">
                        <div class="col_5 column">
                            <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
                        </div>

                        <div class="col_5 column">
                            <input type="text" name="stop_date" class="flatpickr" placeholder="Stop Date">
                        </div>

                        <div class="col_1 column">
                            <button type="button" id="dashboard_filter_campaign" class="btn">Filter</button>
                        </div>
                    </div>
                    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                </div>
                <!-- end -->

            </div>

            <div class="column col_4">
                <div class="the_frame clearfix _stat mb">
                    <div class="column col_6 the_stats">
                        <span class="weight_medium small_faint uppercased m-b block_disp">Target CPM</span>
                        <h3>23</h3>
                    </div>

                    <div class="column col_6 the_stats">
                        <span class="weight_medium small_faint uppercased m-b block_disp">Reached CPM</span>
                        <h3>23</h3>
                    </div>
                </div>

                <div class="the_frame _pie">
                    <h4 class="small_faint uppercased weight_medium">Media Mix</h4>
                </div>
            </div>

        </div>


        <!-- campaign details -->
        <div class="the_frame client_dets mb4">

            <!-- tab links -->
            <div class="tab_header m4 border_bottom clearfix">
                <a href="#summary">Summary</a>
                <a href="#slots">Ad Slots</a>
                <a href="#files">Files</a>
                <a href="#comp">Compliance</a>
            </div>

            {{--{{ dd($campaign_details) }}--}}

            <div class="tab_contain default_summary">

                <!-- summary -->
                <div class="tab_content col_10 campaign_summary" id="summary">

                    <div class="clearfix mb">
                        <div class="column col_6">
                            <span class="weight_medium small_faint">Campaign</span>
                            <h2>{{ $campaign_details['campaign_det']['campaign_name'] }}</h2>
                        </div>

                        <div class="column col_6">
                            <span class="weight_medium small_faint">Budget</span>
                            <h2>N{{ $campaign_details['campaign_det']['campaign_cost'] }}</h2>
                        </div>
                    </div>

                    <div class="clearfix mb">
                        <div class="column col_4">
                            <span class="small_faint">Client</span>
                            <p class="weight_medium">{{ $campaign_details['campaign_det']['company_name'] }}</p>
                        </div>

                        <div class="column col_4">
                            <span class="small_faint">Start Date</span>
                            <p class="weight_medium">{{ $campaign_details['campaign_det']['start_date'] }}</p>
                        </div>

                        <div class="column col_4">
                            <span class="small_faint">Media Type</span>
                            <p class="weight_medium">@foreach($campaign_details['campaign_det']['channel'] as $channel) {{ $channel->channel }} @endforeach</p>
                        </div>
                    </div>

                    <div class="clearfix mb3">
                        <div class="column col_4">
                            <span class="small_faint">Brand</span>
                            <p class="weight_medium">{{ ucfirst($campaign_details['campaign_det']['brand']) }}</p>
                        </div>

                        <div class="column col_4">
                            <span class="small_faint">End Date</span>
                            <p class="weight_medium">{{ $campaign_details['campaign_det']['end_date'] }}</p>
                        </div>

                        <div class="column col_4">
                            <span class="small_faint">Media Channel</span>
                            @foreach($campaign_details['broadcasters'] as $broadcaster)
                                <p class="weight_medium">{{ $broadcaster->brand }}</p>
                            @endforeach
                        </div>
                    </div>

                    <div class="clearfix mb">
                        <div class="column col_4">
                            <span class="small_faint">Target Information</span>
                            <div>
                                <p class="weight_medium"><span class="small_faint">GRP</span> - </p>
                                <p class="weight_medium"><span class="small_faint">Weight</span> - </p>
                                <p class="weight_medium"><span class="small_faint">Reach</span> - </p>
                            </div>
                        </div>

                        <div class="column col_4">
                            <span class="small_faint">Market</span>
                            <div>
                                <p class="weight_medium"><span class="small_faint">Location</span> - @foreach($campaign_details['campaign_det']['location'] as $location) {{ $location->region.',' }} @endforeach</p>
                                <p class="weight_medium"><span class="small_faint">Audience</span> - @foreach($campaign_details['campaign_det']['target_audience'] as $audience) {{ $audience->audience.',' }} @endforeach</p>
                                <p class="weight_medium"><span class="small_faint">Age</span> - {{ $campaign_details['campaign_det']['age'] }}</p>
                            </div>
                        </div>

                        <div class="column col_4">
                            <span class="small_faint">LSM</span>
                            <p class="weight_medium">A, B, C1, C2</p>
                        </div>
                    </div>

                </div>
                <!-- end -->


                <!-- Ad slots -->
                <div class="tab_content" id="slots">
                    <!-- filter -->
                    <div class="filters border_bottom clearfix">
                        <div class="column col_8 date_filter">
                            <a href="" class="active">ALL</a>
                            <a href="">M</a>
                            <a href="">T</a>
                            <a href="">W</a>
                            <a href="">T</a>
                            <a href="">F</a>
                            <a href="">S</a>
                            <a href="">S</a>
                        </div>

                        <div class="column col_4 clearfix">
                            <div class="col_8 column">
                                <div class="header_search">
                                    <form>
                                        <input type="text" placeholder="Search...">
                                    </form>
                                </div>
                            </div>

                            <div class="col_4 column">
                                <div class="select_wrap">
                                    <select>
                                        <option>All Time</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table>
                        <tr>
                            <th>Day</th>
                            <th>Day Parts</th>
                            <th>Target Audience</th>
                            <th>Region</th>
                            <th>Min Age</th>
                            <th>Max Age</th>
                            <th>Hourly Range</th>
                        </tr>

                        @foreach($campaign_details['file_details'] as $file_detail)
                            <tr>
                                <td>{{ $file_detail['day'] }}</td>
                                <td>{{ $file_detail['day_part'] }}</td>
                                <td>{{ $file_detail['target_audience'] }}</td>
                                <td>{{ $file_detail['region'] }}</td>
                                <td>{{ $file_detail['minimum_age'] }}</td>
                                <td>{{ $file_detail['maximum_age'] }}</td>
                                <td>{{ $file_detail['hourly_range'] }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <!-- end -->

                {{--files--}}
                <div class="tab_content" id="files">
                    <!-- filter -->
                    <table>
                        <tr>
                            <th>Files</th>
                            <th>Description</th>
                        </tr>

                        @foreach($campaign_details['file_details'] as $file_detail)
                            <tr>
                                <td><video src="{{ $file_detail['file'] }}" width="150" height="100" controls></video></td>
                                <td>{{  $file_detail['file_name'] ? $file_detail['file_name'] : '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>

                <!-- Complaince -->
                <div class="tab_content" id="comp">
                    <!-- filter -->
                    <div class="filters border_bottom clearfix">
                        <div class="column col_8 date_filter">
                            <a href="" class="active">ALL</a>
                            <a href="">M</a>
                            <a href="">T</a>
                            <a href="">W</a>
                            <a href="">T</a>
                            <a href="">F</a>
                            <a href="">S</a>
                            <a href="">S</a>
                        </div>

                        <div class="column col_4 clearfix">
                            <div class="col_8 column">
                                <div class="header_search">
                                    <form>
                                        <input type="text" placeholder="Search...">
                                    </form>
                                </div>
                            </div>

                            <div class="col_4 column">
                                <div class="select_wrap">
                                    <select>
                                        <option>All Time</option>
                                        <option>This Month</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end -->

                    <table>
                        <tr>
                            <th>Media Type</th>
                            <th>Media Channel</th>
                            <th>Date</th>
                            <th>Booked Spot</th>
                            <th>Aired Spot</th>
                            <th>View Playtimes</th>
                        </tr>

                        @foreach($campaign_details['file_details'] as $file_detail)
                            <tr>
                                <td>@foreach($campaign_details['campaign_det']['channel'] as $channel) {{ $channel->channel }} @endforeach</td>
                                <td>
                                    {{ $file_detail['broadcast_station'] }}
                                </td>
                                <td>12 June, 2018</td>
                                <td>08:00 - 08:30</td>
                                <td>08:00 - 08:30</td>
                                <td><a href="" class="weight_medium">View Media</a></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                <!-- end -->

            </div>
            
            <div class="tab_contain new_summary" style="display: none;">
                
            </div>

        </div>

    </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script>
        $('document').ready(function () {
            flatpickr(".flatpickr", {
                altInput: true,
            });
            $('body').delegate("#client", "change", function (e) {
                var user_id = $("#client").val();
                var campaign_id = "<?php echo $campaign_details['campaign_det']['campaign_id']; ?>";
                $(".load_this").css({
                    opacity: 0.3
                });
                $.ajax({
                    url: '/agency/campaign-details/'+user_id,
                    method: "GET",
                    data: {
                        user_id: user_id, campaign_id : campaign_id,
                    },
                    success: function (data) {
                        console.log(data);
                        var big_html = '<div class="select_wrap show_this" id="show_this">\n' +
                                        ' <select name="campaign" id="campaign"><option value="">Select Campaign</option>';
                        big_html +=  '';
                        $.each(data.campaign, function (index, value) {
                           big_html += '<option value="'+value.campaign_id+'">'+value.name+'</option>';
                        });

                        big_html += '</select>\n' +
                            '      </div>';

                        var chanel_html = '';
                        $.each(data.channel, function (index, value) {
                            chanel_html += '<span> '+value.brand+' <a href=""></a></span>';
                        });

                        $("#hide_this").hide();
                        $(".show_this").show();
                        $(".show_this").html(big_html);

                        $(".load_this").css({
                            opacity: 1
                        });
                    },
                    error: function () {
                        $(".load_this").css({
                            opacity: 1
                        });
                    }
                });
            });

            $('.js-example-basic-multiple').select2();
            //placeholder for target audienct
            $('#channel').select2({
                placeholder: "Please select Media Type"
            });

            $('body').delegate("#campaign", "change", function (e) {
                var campaign_id = $("#campaign").val();
                window.location = '/agency/campaigns/campaign-details/'+campaign_id;

            });

        });
        jQuery( document ).ready(function( $ ) {
            $('body').delegate("#channel", "change", function () {
                var channel = $("#channel").val();
                var campaign_id = "<?php echo $campaign_details['campaign_det']['campaign_id'] ?>";

                $.ajax({
                    url: '/agency/campaigns/media-channel/'+campaign_id,
                    method: "GET",
                    data: {
                        channel: channel, campaign_id: campaign_id,
                    },
                    success: function(data) {

                        var big_html = '<span class="small_faint block_disp">Media Channel</span><div class="select_wrap"><select class="js-example-basic-multiple_1" name="media" id="media" multiple="multiple">';

                            $.each(data, function (index, value) {
                                big_html += '<option value="'+value.broadcaster_id+'">'+value.broadcaster+'</option>';
                            });
                            big_html += '</select></div>';

                            $(".check_this").html(big_html);

                        $('.js-example-basic-multiple_1').select2();

                        $('#media').select2({
                            placeholder: "Please select Media Channel"
                        });

                    }
                });
            });
        })

        jQuery( document ).ready(function( $ ) {
            $('body').delegate("#media", "change", function () {
                var channel = $("#media").val();
                console.log(channel);
                if(channel != null){
                    var campaign_id = "<?php echo $campaign_details['campaign_det']['campaign_id'] ?>";
                    $.ajax({
                        url: '/agency/campaigns/compliance-graph',
                        method: 'GET',
                        data: {
                            channel: channel, campaign_id: campaign_id,
                        },
                        success: function(data){
                            console.log(data);
                            Highcharts.chart('container', {

                                chart: {
                                    type: 'column'
                                },

                                title: {
                                    text: ''
                                },

                                xAxis: {
                                    categories: ['2018-04-05']
                                },

                                yAxis: {
                                    allowDecimals: false,
                                    min: 0,
                                    title: {
                                        text: 'Total Amount'
                                    }
                                },
                                credits: {
                                    enabled: false
                                },
                                exporting: { enabled: false },
                                tooltip: {
                                    formatter: function () {
                                        return '<b>' + this.x + '</b><br/>' +
                                            this.series.name + ': ' + this.y + '<br/>' +
                                            'Total: ' + this.point.stackTotal;
                                    },

                                },

                                plotOptions: {
                                    column: {
                                        stacking: 'normal'
                                    },
                                    series: {
                                        pointPadding: 0,
                                        groupPadding: 0.1,
                                        pointWidth: 60,
                                    }
                                },

                                series: data.data
                            });
                        }
                    })
                }else{
                    Highcharts.chart('container', {

                        chart: {
                            type: 'column'
                        },

                        title: {
                            text: ''
                        },

                        xAxis: {
                            categories: []
                        },

                        yAxis: {
                            allowDecimals: false,
                            min: 0,
                            title: {
                                text: 'Total Amount'
                            }
                        },
                        credits: {
                            enabled: false
                        },
                        exporting: { enabled: false },
                        tooltip: {
                            formatter: function () {
                                return '<b>' + this.x + '</b><br/>' +
                                    this.series.name + ': ' + this.y + '<br/>' +
                                    'Total: ' + this.point.stackTotal;
                            },

                        },

                        plotOptions: {
                            column: {
                                stacking: 'normal'
                            },
                            series: {
                                pointPadding: 0,
                                groupPadding: 0.1,
                                pointWidth: 60,
                            }
                        },

                        series: []
                    });
                }
            })
        })


    </script>
@stop


@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <style>
        .highcharts-grid path { display: none;}
        .highcharts-legend {
            display: none;}
    </style>
@stop
