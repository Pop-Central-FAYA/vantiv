@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Media Assets</title>
@stop

@section('styles')
    <!-- App.js -->
    <!-- <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" type="text/css" rel="stylesheet"> -->
@stop

@section('content')

    <!-- main container -->
    <div class="main_contain" id="load_this_div">
        <!-- header -->
        @include('partials.new-frontend.agency.header')

        <div class="container-fluid media-asset-management">
            <div class="row">
                <!-- subheader -->
                <div class="col-md-10">
                    <h2 class="sub_header">Media Assets</h2>
                </div>
                @if(Auth::user()->hasPermissionTo('create.asset'))
                    <div class="col-md-2 text-right px-2">
                        <media-asset-upload 
                            :clients="{{ json_encode($clients) }}"
                            :brands="{{ json_encode($brands) }}"
                        >
                        </media-asset-upload>
                    </div>
                @endif
            </div>
            <div class="row my-5">
                @if(Auth::user()->hasPermissionTo('view.asset'))
                    <div class="col-md-12">
                        <v-app>
                            <v-content>
                            <media-asset-display></media-asset-display>
                            </v-content>
                        </v-app>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
@stop