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
                <!-- summary -->
                <div class="tab_content col_12 campaign_summary" id="summary">
                    <div class="clearfix mb">
                        <div class="column col_5">
                            <div class="clearfix mb">
                                <div class="column col_12 mb" style="margin-left: 1.6%;">
                                    <p class="weight_medium">
                                        <span class="weight_medium small_faint pr-1">Campaign Name:</span>
                                        {{ $campaign_details->name }}
                                    </p>
                                </div>
                                <div class="column col_12 mb">
                                    <p class="weight_medium">
                                        <span class="weight_medium small_faint pr-1">Client:</span>
                                        {{ $campaign_details->client['company_name'] }}
                                    </p>
                                </div>
                                <div class="column col_12 mb">
                                    <p class="weight_medium">
                                        <span class="weight_medium small_faint pr-1">Brand:</span>
                                        {{ ucfirst($campaign_details->brand['name']) }}
                                    </p>
                                </div>
                                <div class="column col_12 mb">
                                    <p class="weight_medium">
                                        <span class="weight_medium small_faint pr-1">Budget:</span>
                                        N{{ $campaign_details->budget }}
                                    </p>
                                </div>
                                <div class="column col_12 mb">
                                    <p class="weight_medium">
                                        <span class="weight_medium small_faint pr-1">Flight Date:</span>
                                        {{ date("M d, y", strtotime($campaign_details->start_date)) }} - {{ date("M d, y", strtotime($campaign_details->stop_date)) }}
                                    </p>
                                </div>
                                @if (count($campaign_details->channel_information) > 0)
                                    <div class="column col_12">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Media Type:</span>
                                            @foreach($campaign_details->channel_information as $key=>$channel) {{ $channel->channel }}@if(($key+1) < count($campaign_details->channel_information)){{', '}} @endif @endforeach 
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="column col_7">
                            <div class="clearfix mb">
                                @if (count($campaign_details->audience_information) > 0)
                                    <div class="column col_12 mb" style="margin-left: 1.6%;">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Gender:</span>
                                            @foreach($campaign_details->audience_information as $key=>$audience) {{ $audience->audience }} @if(($key+1) < count($campaign_details->audience_information)){{','}} @endif @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if (is_array(json_decode($campaign_details->age_groups)))
                                    <div class="column col_12 mb">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Age Groups:</span>
                                            @foreach(json_decode($campaign_details->age_groups) as $key=>$age_group){{ $age_group->min.' - '.$age_group->max.' Yrs' }} @if(($key+1) < count(json_decode($campaign_details->age_groups))){{', '}} @endif @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if (is_array(json_decode($campaign_details->social_class)))
                                    <div class="column col_12 mb">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Social Class:</span>
                                            @foreach(json_decode($campaign_details->social_class) as $key=>$class) {{ $class }}@if(($key+1) < count(json_decode($campaign_details->social_class))){{', '}} @endif @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if (is_array(json_decode($campaign_details->states)))
                                    <div class="column col_12 mb">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">States:</span>
                                            @foreach(json_decode($campaign_details->states) as $key=>$state) {{ $state }}@if(($key+1) < count(json_decode($campaign_details->states))){{', '}} @endif @endforeach
                                        </p>
                                    </div>
                                @endif
                                @if (is_array(json_decode($campaign_details->regions)))
                                    <div class="column col_12 mb">
                                        <p class="weight_medium">
                                            <span class="weight_medium small_faint pr-1">Regions:</span>
                                            @foreach(json_decode($campaign_details->regions) as $key=>$region) {{ $region }} @if(($key+1) < count(json_decode($campaign_details->regions))){{', '}} @endif @endforeach
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end -->

                <div class="tab_content" id="slots">
                    <mpo-slot-list
                        :adslots="{{json_encode($campaign_mpo->campaign_mpo_time_belts)}}"
                        :assets="{{ json_encode($assets) }}"
                        :time_belts="{{ json_encode($time_belts) }}"
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
