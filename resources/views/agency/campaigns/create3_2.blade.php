@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Create Campaign Step 4 </title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create New Campaign</h2>
            </div>
        </div>


        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color">

            <div class="margin_center col_7 clearfix pt4 create_fields">

                <!-- progress bar -->
                <div class="create_gauge clearfix">
                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress">
                        <span class="one_point"></span>
                    </div>
                </div>


                <!-- media houses -->
                <div class="media_houses mb3 clearfix">
                    <p>Please Select a Media Channel</p><br>

                    @foreach($adslot_search_results as $adslot_search_result)
                        <div class='align_center one_media broad_click'>
                            <input type="hidden" name="broadcaster" value="{{ $adslot_search_result['broadcaster'] }}" id="broadcaster">
                            <div><a href="{{ route('agency_campaign.step4', ['id' => $id, 'broadcaster' => $adslot_search_result['broadcaster'], 'start_date' => current($campaign_dates_for_first_week), 'end_date' => end($campaign_dates_for_first_week)]) }}"><img src="{{ asset($adslot_search_result['logo'] ? $adslot_search_result['logo'] : '') }}"></a></div>
                            <span class="small_faint">{{ $adslot_search_result['boradcaster_brand'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_6">
                        <a href="{{ route('agency_campaign.step3', ['id' => $id]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>
                </div>

            </div>
        </div>

        <!-- main frame end -->

    </div>

@stop

