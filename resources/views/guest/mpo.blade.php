@extends('layouts.guest.app')

@section('title')
    <title>Mpo</title>
@stop

@section('content')
    <div class="container sub_header clearfix mb pt">
        <div class="column col_3">
            <h2 class="sub_header">
                <img src="{{ $company->logo }}" height="80" weight="80"  alt="">
            </h2>
        </div>
        <div class="column col_6">
            <h2>Media Purchase Order from {{ $company->name }}</h2>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3>Client : {{ $campaign->client->name }}</h3>
            </div>
            <div class="col-md-6">
                <h3>Brand : {{ $campaign->brand->name }}</h3>
            </div>
        </div>
        <p></p>
        <div class="row">
            <div class="col-md-6">
                <h3>Campaign : {{ $campaign->name }}</h3>
            </div>
            <div class="col-md-6">
                <h3>Flight Date : {{ date('Y-m-d', strtotime($campaign->start_date)) }} - {{ date('Y-m-d', strtotime($campaign->stop_date)) }}</h3>
            </div>
        </div>
        <p></p>
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