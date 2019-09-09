@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | CAMPAIGNS</title>
@stop

@section('content')

    <div class="main_contain">
    <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">All Campaigns</h2>
            </div>
        </div>

        <div class="the_frame client_dets mb4">
            <!-- campaigns table -->
            <v-app>
                <v-content>
                    <campaign-list :campaigns="{{ json_encode($campaigns) }}" :company-type="{{ json_encode(Auth::user()->company_type)}}"></campaign-list>
                </v-content>
            </v-app>
            <!-- end -->
        </div>
    </div>
@stop