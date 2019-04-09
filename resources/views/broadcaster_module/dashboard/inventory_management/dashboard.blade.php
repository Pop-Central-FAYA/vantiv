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
            <div class="column col_6">
                <h3 class="sub_header">Total Revenue</h3><br>
                <h3>NGN 0.00</h3>
            </div>
        </div>

    {{--sidebar--}}
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    <!-- main stats -->
        <div class="clearfix mb3">
            <div class="input_wrap column col_4">
                <label class="small_faint">Days</label>
                <div class="select_wrap{{ $errors->has('days') ? ' has-error' : '' }}">
                    <select required name="days">
                        <option>All Days</option>
                        @foreach($days as $day)
                            <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                        @endforeach
                    </select>

                    @if($errors->has('days'))
                        <strong>
                            <span class="help-block">
                                {{ $errors->first('days') }}
                            </span>
                        </strong>
                    @endif
                </div>
            </div>
            @if(Auth::user()->companies()->count() > 1)
                <div class="input_wrap column col_4">
                    <label class="small_faint">Stations</label>
                    <div class="select_wrap{{ $errors->has('company') ? ' has-error' : '' }}">
                        <select required name="company">
                            <option>All Stations</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                            @endforeach
                        </select>

                        @if($errors->has('company'))
                            <strong>
                                <span class="help-block">
                                    {{ $errors->first('company') }}
                                </span>
                            </strong>
                        @endif
                    </div>
                </div>
            @endif
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
        Highcharts.chart('time_belt', {
            chart: {
                type: 'spline'
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
            xAxis: {
                title: {
                    text: 'Time Belt'
                },
                categories: ['00h00-00h15', '00h15-00h30', '00h30-00h45', '00h45-01h00']
            },
            yAxis: {
                title: {
                    text: 'Revenue'
                },
                labels: {
                    formatter: function () {
                        return this.value + '';
                    }
                }
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                name: 'Revenue',
                marker: {
                    symbol: 'square'
                },
                data: [7.0, 6.9, 9.5, 14.5]

            }]
        });
    </script>
@stop

@section('styles')
    <style>
        .highcharts-grid path { display: none;}
        .highcharts-legend {
            display: none;
        }
    </style>
@stop

