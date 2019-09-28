@extends('layouts.guest.app')

@section('title')
    <title>Mpo</title>
@stop

@section('content')
    <div class="container">
        <p></p>
        <div class="the_frame client_dets mb4">
            <v-app>
                <v-content>
                    <guest-mpo
                        :files="{{ json_encode($files) }}"
                        :mpo-details="{{ json_encode($mpo_details) }}"
                        :campaign_mpo_time_belts="{{ json_encode($campaign_mpo_time_belts) }}"
                    ></guest-mpo>
                </v-content>
            </v-app>
        </div>
    </div>
@stop