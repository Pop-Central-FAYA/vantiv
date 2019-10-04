@extends('dsp_layouts.faya_app')

@section('title')
<title>Vantage | Create Media Plan</title>
@stop

@section('content')
<div class="main_contain">
    <!-- header -->
    @if(Session::get('broadcaster_id'))
        @include('partials.new-frontend.broadcaster.header')
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    @endif

    <!-- subheader -->
    <!-- <div class="sub_header clearfix mb pt">
        <div class="column col_6">
            {{-- <h2 class="sub_header">Selected stations & programs</h2> --}}
            {{-- <p><a href="#ex1" rel="modal:open">Open Modal</a></p> --}}
        </div>
    </div> -->
    <div>
        <v-app>
            <v-content>
                <media-plan-details
                    :durations="{{ json_encode($default_material_length) }}"
                    :time-belts="{{ json_encode($fayaFound) }}"
                    :plan="{{ json_encode($media_plan) }}"
                    :clients="{{ json_encode($clients) }}"
                >
                </media-plan-details>
            </v-content>
        </v-app>
    </div>
</div>
@stop
