@extends('layouts.faya_app')

@section('title')
    <title> FAYA | ON HOLD CAMPAIGNS</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">On Hold Campaigns</h2>
            </div>
        </div>

        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_8 p-t">
                    <p class="uppercased weight_medium">On Hold Campaigns</p>
                </div>
            </div>

            <!-- campaigns table -->
            <div class="similar_table pt3">
                <!-- table header -->
                <div class="_table_header clearfix m-b">
                    <span class="small_faint block_disp padd column col_4">Campaign Name</span>
                    <span class="small_faint block_disp column col_2">Brand</span>
                    <span class="small_faint block_disp column col_2">Total Budget</span>
                    <span class="small_faint block_disp column col_2">Date Created</span>
                    <span class="block_disp column col_2 color_trans">.</span>
                </div>

                <!-- table item -->
                @foreach($campaigns as $campaign)
                    <div class="_table_item the_frame clearfix">
                        <div class="padd column col_4">
                            {{ $campaign['name'] }}
                        </div>
                        <div class="column col_2">{{ $campaign['brand'] }}</div>
                        <div class="column col_2">&#8358; {{ $campaign['budget'] }}</div>
                        <div class="column col_2">{{ date('M j, Y', strtotime($campaign['date_created'])) }}</div>
                        <div class="column col_2">

                            <!-- more links -->
                            <div class="list_more">
                                <span class="more_icon"></span>

                                <div class="more_more">
                                    <a href="#edit_client" class="modal_click">Edit</a>
                                    <a href="">Submit</a>
                                </div>
                            </div>

                        </div>
                    </div>
            @endforeach
            <!-- table item end -->
            </div>
            <!-- end -->
        </div>

    </div>
@stop

@section('styles')
    <style>
        ._table_item > div:first-child {
            padding-top: 12px;
            font-size: 16px;
        @import;
        }
    </style>
@stop
