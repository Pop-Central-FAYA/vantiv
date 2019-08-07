@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Campaign MPO Details</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <a href="{{ route('agency.campaign.new.details', ['id' => $campaign_mpo->campaign_id]) }}"><h2 class="sub_header back">{{ $campaign_mpo->campaign->name }}</h2></a>
            </div>
        </div>

        <!-- main stats -->
        <div class="the_frame clearfix mb">
            <div class="tab_header m4 border_bottom clearfix">
                <a href="#summary">Summary</a>
                <a href="#slots">Ad Slots</a>
            </div>

            <div class="tab_contain default_summary" id="app">
                <!-- summary -->
                <div class="tab_content col_12" id="summary">
                    <campaign-summary :campaign="{{json_encode($campaign_details)}}"></campaign-summary>
                </div>
                <!-- end -->

                <div class="tab_content" id="slots">
                    <mpo-slot-list
                        :adslots="{{json_encode($campaign_mpo->campaign_mpo_time_belts)}}"
                        :assets="{{ json_encode($assets) }}"
                        :time_belts="{{ json_encode($time_belts) }}"
                    ></mpo-slot-list>
                </div>
            </div>
        </div> 

    </div>
@stop

@section('styles')
    <style>
        .back {
            text-decoration: none;
            color: #00C4CA;
        }
        a {
            text-decoration: none; 
        }
    </style>
@stop
