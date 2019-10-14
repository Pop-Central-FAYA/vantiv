@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Dashboard</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Media Plans</h2>
            </div>
        </div>

        <!-- MEDIA PLANS -->
        <div class="the_frame client_dets mb4">
            <!-- media plans table -->
            <v-app>
                <v-content>
                    <media-plan-list :plans="{{ json_encode($plans) }}" :clients="{{ json_encode($clients) }}"></media-plan-list>
                </v-content>
            </v-app>
            <!-- end -->
        </div>
    </div>
@stop
