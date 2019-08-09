@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Company Management</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @include('partials.new-frontend.agency.header')
        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Company Management</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <!-- main frame end -->
           <company-index  
                :company-data="{{ json_encode($company) }}" 
                :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}">
            </company-index>
            
@stop

