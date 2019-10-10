@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Campaign Details</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">{{ $campaign_details->name }}</h2>
            </div>
        </div>
        <!-- campaign details -->
        <div class="the_frame client_dets mb4">
            <v-app>
                <v-content>
                    <campaign-display
                        :files="{{json_encode($campaign_files)}}"
                        :mpos="{{json_encode($campaign_details->campaign_mpos)}}"
                        :campaign-time-belts="{{ json_encode($campaign_details->time_belts) }}"
                        :grouped-campaign-time-belts="{{ json_encode($campaign_details->grouped_time_belts) }}"
                        :assets="{{json_encode($client_media_assets)}}"
                        :client="{{json_encode($campaign_details->client->name)}}"
                        :brand="{{json_encode($campaign_details->brand['name'])}}"
                        :campaign="{{json_encode($campaign_details)}}"
                        :time-belts="{{json_encode($time_belt_range)}}"
                        :ad-vendors="{{ json_encode($ad_vendors) }}"
                    ></campaign-display>
                </v-content>
            </v-app>
        </div>
    </div>
@stop
