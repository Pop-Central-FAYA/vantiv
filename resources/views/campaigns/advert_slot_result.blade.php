@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Adslot Filter Results</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
    @if(Session::get('broadcaster_id'))
        @include('partials.new-frontend.broadcaster.header')
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    @else
        @include('partials.new-frontend.agency.header')
    @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Adslots Filter Information</h2>
            </div>
        </div>
        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color">

            <div class="margin_center col_7 clearfix pt4 create_fields">

                <div class="create_gauge clearfix">
                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress">
                        <span class="one_point"></span>
                    </div>
                </div>
                <!-- media houses -->
                <div class="media_houses mb3 clearfix">
                    @if(empty($adslots_filter_result))
                        <p><h2>No Adslot found for this criteria, please go back</h2></p>
                    @else
                        @foreach($adslots_filter_result as $adslot)
                            <div class='align_center one_media'>
                                <div><img src="{{ asset($adslot->broadcaster_logo ? $adslot->broadcaster_logo : '') }}"></div>
                                <span class="small_faint"></span>
                                <p><br></p>
                                <p><br></p>
                                <p><br></p>
                                <p><br></p>
                                <p>Number of slots: {{ $adslot->total_slot }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- proceed buttons -->
                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_4">
                        <a href="{{ route('campaign.get_campaign_general_information') }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="align_center column col_4">
                        <p><br></p>
                    </div>
                    @if(!empty($adslots_filter_result))
                        <div class="column col_4 align_right">
                            <a href="" class="btn uppercased _proceed modal_click">Proceed <span class=""></span></a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
        <!-- main frame end -->
    </div>
@stop
