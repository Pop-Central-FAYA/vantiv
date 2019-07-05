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
                <div class="tab_content col_10 campaign_summary" id="summary">
                    <div class="clearfix">
                        <div class="clearfix mb">
                            <div class="column col_3">
                                <span class="weight_medium small_faint">Campaign</span>
                                <p class="weight_medium">{{ $campaign_mpo->campaign->name }}</p>
                            </div>
                            <div class="column col_3">
                                <span class="small_faint">Start Date</span>
                                <p class="weight_medium">{{ $campaign_mpo->campaign->start_date }}</p>
                            </div>
                            <div class="column col_3">
                                <span class="small_faint">Client</span>
                                <p class="weight_medium">{{ $campaign_mpo->campaign->client->company_name }}</p>
                            </div>
                            <div class="column col_3">
                                <span class="small_faint">Media Type</span>
                                <p class="weight_medium"> Tv </p>
                            </div>
                        </div>

                        <div class="clearfix mb">
                            <div class="column col_3">
                                <span class="weight_medium small_faint">Budget</span>
                                <p class="weight_medium">N{{ $campaign_mpo->campaign->budget }}</p>
                            </div>
                            <div class="column col_3">
                                <span class="small_faint">End Date</span>
                                <p class="weight_medium">{{ $campaign_mpo->campaign->stop_date }}</p>
                            </div>
                            <div class="column col_3">
                                <span class="small_faint">Brand</span>
                                <p class="weight_medium">{{ ucfirst($campaign_mpo->campaign->brand->name) }}</p>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="tab_content" id="slots">
                    <mpo-slot-list
                        :adslots="{{json_encode($campaign_mpo->campaign_mpo_time_belts)}}"
                    ></mpo-slot-list>
                </div>
            </div>
        </div> 

    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
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
