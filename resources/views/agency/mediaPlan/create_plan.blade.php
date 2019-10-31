@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain load_this_div">
        <!-- header -->
        @if(Session::get('broadcaster_id'))
            @include('partials.new-frontend.broadcaster.header')
            @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Media Plan Information</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color">
            <div class="margin_center col_8 clearfix create_fields">
                <v-app>
                    <v-content>
                        <media-plan-criteria-form 
                            :criterias="{{ json_encode($criterias) }}" 
                            :redirect-urls="{{ json_encode($redirect_urls) }}"
                            :clients="{{ json_encode($clients) }}"
                            :media-plan="{{ json_encode($media_plan, JSON_FORCE_OBJECT) }}"
                        ></media-plan-criteria-form>
                    </v-content>
                </v-app>
            </div>
        </div>
        <!-- main frame end -->
    </div>
@stop