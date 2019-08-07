@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Campaign Details</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

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
                        :assets="{{json_encode($client_media_assets)}}"
                        :client="{{json_encode($campaign_details->client['company_name'])}}"
                        :brand="{{json_encode($campaign_details->brand['name'])}}"
                        :campaign="{{json_encode($campaign_details)}}"
                    ></campaign-display>
                </v-content>
            </v-app>
        </div>
    </div>
@stop
