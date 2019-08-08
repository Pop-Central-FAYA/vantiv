@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Ad Vendors</title>
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
                    <h2 class="sub_header">Ad Vendors</h2>
                </div>

                @if(Auth::user()->can('create.ad_vendor'))
                    <div class="col-md-2 text-right px-2">
                        <ad-vendor-create></ad-vendor-create>
                    </div>
                @endif

            </div>
            <div class="row my-5">
                @if(Auth::user()->can('view.ad_vendor'))
                    <div class="col-md-12">
                        <v-app>
                            <v-content>
                                <ad-vendor-list></ad-vendor-list>
                            </v-content>
                        </v-app>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop