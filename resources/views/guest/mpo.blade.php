@extends('layouts.guest.app')

@section('title')
    <title>Mpo</title>
@stop

@section('content')
    <div class="container sub_header clearfix mb pt">
        <div class="column col_4">
            <h2 class="sub_header">
                <img src="{{ $company->logo }}" height="50" weight="50"  alt="">
            </h2>
        </div>
        <div class="column col_4">
            <h2 class="sub_header text-center">
                <p>Campaign Mpo</p>
            </h2>
        </div>
        <div class="column col_4">
            <h2 class="sub_header text-right">
                {!! AssetsHelper::logo() !!}
            </h2>
        </div>
    </div>
    <p></p>
    <div class="container">
        <div class="the_frame client_dets mb4">
            <v-app>
                <v-content>
                    <guest-mpo
                        :files="{{ json_encode($files) }}"
                        :mpo_id="{{ json_encode($campaign_mpo->id) }}"
                        :campaign_mpo_time_belts="{{ json_encode($campaign_mpo_time_belts) }}"
                    ></guest-mpo>
                </v-content>
            </v-app>
        </div>
    </div>
@stop