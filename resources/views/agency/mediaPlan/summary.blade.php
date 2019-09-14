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
        
        <media-plan-summary  
        :summary-data="{{ json_encode($summary) }}" 
        :summary-details="{{ json_encode($media_plan) }}" 
        :routes="{{ json_encode($routes) }}"  
        :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}"    
        :user-list="{{ json_encode($users) }}"></media-plan-summary>
        <br><br><br><br><br><br><br>
    </div>
@stop
