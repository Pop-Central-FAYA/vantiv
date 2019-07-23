@extends('layouts.ssp.layout')

@section('title')
    <title>Torch | Dashboard </title>
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

    <div class="container-fluid broadcaster-report">
            <!-- Charts -->
            <div id="chart-container" class="row my-5 chart-view">
                <div class="col-12 pb-1 mb-3" style="border-bottom: 2px solid #44c1c9;">
                    <div class="btn-group chart-toggle" role="group" aria-label="Basic example">
                        @foreach($media_type_list as $value)
                            <button id="{{$value}}-toggle" type="button" class="btn btn-info py-1 mr-2 @if($value == $media_type) active-toggle @else inactive-toggle @endif" data-media-type="{{$value}}"><i class="material-icons">{{$value}}</i> {{strtoupper($value)}}</button>
                        @endforeach
                    </div>
                </div>
    
                <!-- client charts -->
                <!-- Filters -->
                <div class="col-12 filter">
                    <form method="" action="" id="filter-form">
                        {{ csrf_field() }}
                        <input type="hidden" id="media-type-input" name="media_type" value="{{$media_type}}">
                        <input type="hidden" id="media-type-input" name="report_type" value="{{$timebelt_revenue['report_type']}}">
                        <!-- Render each filter boxes for each media type -->
                        @foreach($stations as $type => $value)
                            <div id="{{$type}}-filter-container" class="row mb-2 filter-containers" @if($type !== $media_type) style="display: none;" @endif>
                                <div class="col-5">
                                    <div class="row">
                                        <div class="col-4">
                                            <select class="form-control days-filter filter-val @if($type !== $media_type) do-not-send @endif" name="day[]" multiple placeholder="Select Days">
                                                @foreach($days as $day)
                                                    <option value="{{ $day }}" selected >{{ ucfirst($day) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-4">
                                            <select class="form-control day-parts-filter filter-val @if($type !== $media_type) do-not-send @endif" name="day_parts[]" multiple placeholder="Select Day Parts">
                                                @foreach($day_parts as $day_part)
                                                    <option value="{{ $day_part }}" selected>{{ $day_part }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @if(count($value) > 1)
                                            <div class="col-4 pr-0">
                                                <select class="form-control publishers-filter filter-val @if($type !== $media_type) do-not-send @endif" name="station_id[]" multiple placeholder="Select Stations">
                                                    @foreach($value as $company)
                                                        <option value="{{ $company->id }}" selected>{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </form>
                </div>
                <br>
                <!-- {{--Periodic revenue chart goes here--}} -->
                <div class="col-12">
                    <div id="periodicChart" style="min-width: 310px; height: 400px; margin: 0 auto">
                </div>
            </div>
        </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/modules/no-data-to-display.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <!-- SUMO SELECT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.sumoselect/3.0.2/jquery.sumoselect.min.js"></script>
    <script src="{{ asset('js/dashboard-graphs.js') }}"></script>

    <script>
        var timebelt_revenue_data = {!! json_encode($timebelt_revenue) !!};
        var time_belt_revenue = new TimebeltRevenueChart($('#chart-container'));
        time_belt_revenue.initChart(timebelt_revenue_data);
    </script>
@stop

@section('styles')
    <!-- SUMO SELECT -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.sumoselect/3.0.2/sumoselect.min.css" />
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