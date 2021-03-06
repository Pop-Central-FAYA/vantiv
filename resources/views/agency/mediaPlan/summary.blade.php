@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain" id="load_this">
        <!-- header -->
        @if(Session::get('broadcaster_id'))
            @include('partials.new-frontend.broadcaster.header')
            @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Summary</h2>
            </div>
        </div>

        <v-app>
            <v-content>
                <media-plan-summary
                :formatted-plan-data="{{ json_encode($formatted_plan) }}"
                :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}"    
                :user-list="{{ json_encode($users) }}"></media-plan-summary>
            </v-content>
        </v-app>
        <br><br><br><br><br><br><br>
    </div>
@stop
