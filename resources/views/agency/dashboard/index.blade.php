@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Dashboard</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Dashboard</h2>
            </div>
        </div>

        <!-- main stats -->
        <!-- CAMPAIGN -->
        <div class="campaigns-dashboard dsp-dashboard" id="campaigns-dashboard">
            <!-- main stats -->
            @if(Auth::user()->hasPermissionTo('view.report'))
                <dashboard
                    :redirect-urls="{{ json_encode($redirect_urls) }}"
                    :media-channels="{{ json_encode($media_channels) }}"
                    :campaign-summary="{{ json_encode($campaign_summary) }}"
                    :media-plan-summary="{{ json_encode($media_plan_summary) }}"
                ></dashboard>
            @endif
        </div>
    </div>
@stop