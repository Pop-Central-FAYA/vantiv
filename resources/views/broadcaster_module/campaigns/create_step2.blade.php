@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Campaign Step 2</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create New Campaign</h2>
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
                    @if($adslots['count_adslot'] == 0)
                        <p><h2>No Adslot found for this criteria, please go back</h2></p>
                    @else
                        <div class='align_center one_media'>
                            <div><img src="{{ asset($adslots['logo'] ? decrypt($adslots['logo']) : '') }}"></div>
                            <span class="small_faint"></span>
                            <p><br></p>
                            <p><br></p>
                            <p><br></p>
                            <p><br></p>
                            <p>Number of slots: {{ $adslots['count_adslot'] }}</p>
                        </div>
                    @endif
                </div>

                <!-- proceed buttons -->
                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_4">
                        <a href="{{ route('campaign.create') }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="align_center column col_4">

                    </div>
                    @if(!empty($adslots))
                        <div class="column col_4 align_right">
                            <a href="{{ route('campaign.create3', ['id' => $id]) }}" class="btn uppercased _proceed modal_click">Proceed <span class=""></span></a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
        <!-- main frame end -->
    </div>
@stop
