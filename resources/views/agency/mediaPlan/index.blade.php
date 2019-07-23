@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Dashboard</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Media Plans</h2>
            </div>
        </div>

        <!-- MEDIA PLANS -->
        <div class="the_frame client_dets mb4">
            <!-- media plans table -->
            <v-app>
                <v-content>
                    <media-plan-list :plans="{{ json_encode($plans) }}"></media-plan-list>
                </v-content>
            </v-app>
            <!-- end -->
        </div>
    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
@stop
