@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Dashboard </title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb3 pt">
            <div class="column col_6">
                <h2 class="sub_header">Dashboard</h2>
            </div>
        </div>

    {{--sidebar--}}
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    <!-- main stats -->
    <div class="clearfix mb3">
        <div class="clearfix mt3">
            <div class="column col_1">
                <button class="btn full block_disp uppercased align_center media-type-toggle-btn">TV</button>
            </div>

            <!-- <div class="column col_1">
                <button id="view-graph" class="btn full block_disp uppercased align_center media-type-toggle-btn inactive-media-type-toggle-btn">RADIO</button>
            </div> -->
        </div>

        <hr class="border_top_color clearfix mb3" />

        <div class="clearfix mb3">
            <form method="POST" action="" id="filter-form">
                {{ csrf_field() }}
                <div class="input_wrap column col_2">
                    <label class="small_faint">Days</label>
                    <div class="select_wrap{{ $errors->has('day') ? ' has-error' : '' }}">
                        <select name="day">
                            <option value="">All Days</option>
                            @foreach($days as $day)
                                <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                            @endforeach
                        </select>

                        @if($errors->has('day'))
                            <strong>
                                <span class="help-block">
                                    {{ $errors->first('day') }}
                                </span>
                            </strong>
                        @endif
                    </div>
                </div>

                <div class="input_wrap column col_2">
                    <label class="small_faint">Day Parts</label>
                    <div class="select_wrap{{ $errors->has('day_parts') ? ' has-error' : '' }}">
                        <select name="day_parts">
                            <option value="">All Day Parts</option>
                            @foreach($day_parts as $day_part)
                                <option value="{{ $day_part }}">{{ $day_part }}</option>
                            @endforeach
                        </select>

                        @if($errors->has('day_parts'))
                            <strong>
                                <span class="help-block">
                                    {{ $errors->first('day_parts') }}
                                </span>
                            </strong>
                        @endif
                    </div>
                </div>

                @if(Auth::user()->companies()->count() > 1)
                    <div class="input_wrap column col_2">
                        <label class="small_faint">Stations</label>
                        <div class="select_wrap{{ $errors->has('station_id') ? ' has-error' : '' }}">
                            <select name="station_id">
                                <option value="">All Stations</option>
                                @foreach($stations as $station)
                                    <option value="{{ $station->id }}">{{ $station->name }}</option>
                                @endforeach
                            </select>

                            @if($errors->has('station_id'))
                                <strong>
                                    <span class="help-block">
                                        {{ $errors->first('station_id') }}
                                    </span>
                                </strong>
                            @endif
                        </div>
                    </div>
                @endif

                <div class="input_wrap column col_2">
                    <!-- <button type="submit" class="filter-btn" id="filter-btn"><i class="material-icons">search</i>FILTER</button> -->
                    <button type="submit" class="btn full block_disp uppercased align_center">
                        <i class="material-icons">search</i>
                        FILTER
                    </button>
                </div>
            </form>
        </div>

        <div class="clearfix">
            <h4><p>Inventory Revenue</p></h4>
            <br>
            <div id="time_belt" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/series-label.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script>
        const numberFormatter = new Intl.NumberFormat('en-US', {
            // style: 'currency',
            // currency: 'NGN',
            minimumFractionDigits: 2
        })

        class TimebeltRevenueChart {

            constructor(chart_element) {
                this.chart_element = chart_element;
                this.chart_object = null;
            }

            initChart(data) {
                if (data.revenue !== null && data.time_belts !== null && data.revenue.length > 0 && data.time_belts.length > 0) {
                        this.chart_object = Highcharts.chart(this.chart_element, {
                        chart: {type: 'spline'},
                        title: {text: ''},
                        credits: {enabled: false},
                        exporting: {enabled: false},
                        xAxis: {
                            title: {text: 'Time Belt'},
                            categories: data.time_belts
                        },
                        yAxis: {
                            title: {text: 'Revenue'},
                            labels: {
                                formatter: function() {
                                    return (String.fromCharCode(0x20A6) + numberFormatter.format(this.value)).replace(".00", "");
                                },
                            }
                        },
                        tooltip: {crosshairs: true,shared: true},
                        plotOptions: {spline: {marker: {radius: 4, lineColor: '#666666', lineWidth: 1}}},
                        series: [{
                            name: 'Revenue',
                            marker: {symbol: 'square'},
                            data: data.revenue
                        }]
                    });
                    return this.chart_object;
                }
                return null;
            }

            refreshChart(data) {
                if (data.revenue !== null && data.revenue.length > 0 && this.chart_object !== null) {
                    var update_options = {
                        name: 'Revenue',
                        marker: {symbol: 'square'},
                        data: data.revenue
                    }
                    this.chart_object.xAxis[0].update({categories: data.time_belts}, false);
                    this.chart_object.series[0].update(update_options, true);
                }
                
            }
        }

        // below is the code to perform filtering for the revenue
        $("#filter-form").on('submit', function(e) {
            event.preventDefault(e);
            $('.load_this_div').css({opacity : 0.2});
            var formdata = $("#filter-form").serialize();
            $.ajax({
                cache: false,
                type: "GET",
                url: '/inventory-management/filter-timebelts-report',
                dataType: 'json',
                data: formdata,
                beforeSend: function(data) {
                    var toastr_options = {
                        "preventDuplicates": true,
                        "tapToDismiss": false,
                        "hideDuration": "1",
                        "timeOut": "300000000", //give a really long timeout, we should be done before that
                    }
                    var msg = "Filtering revenue data"
                    toastr.info(msg, null, toastr_options)
                },
                success: function(data) {
                    toastr.clear();
                    if (data.status === 'success') {
                        if (data.data.revenue !== null && data.data.revenue.length > 0) {
                            toastr.success("Filtered result retrieved");
                            time_belt_revenue.refreshChart(data.data);
                        } else {
                            toastr.error("No results were retrived, try another combination please");
                        }
                        
                    } else {
                        toastr.error('An unknown error has occurred, please try again');
                        return;
                    }
                },
                error : function (xhr) {
                    toastr.clear();
                    toastr.error('An unknown error has occurred, please try again');
                }
            })
        });


        var timebelt_revenue_data = {!! json_encode($timebelt_revenue) !!};
        var time_belt_revenue = new TimebeltRevenueChart('time_belt');
        time_belt_revenue.initChart(timebelt_revenue_data);

    </script>
@stop

@section('styles')
    <style>
        .highcharts-grid path { display: none;}
        .highcharts-legend {
            display: none;
        }
        .inactive-media-type-toggle-btn {
            background: transparent !important;
            border: 1px solid #44C1C9 !important;
            color: #000 !important;
        }

        .media-type-toggle-btn {
            cursor: default !important;
        }
    </style>
@stop