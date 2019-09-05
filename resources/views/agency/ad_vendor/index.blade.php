@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Ad Vendors</title>
@stop

@section('content')

    <!-- main container -->
    <div class="main_contain" id="load_this_div">
        <div class="container-fluid media-asset-management">
            <div class="row">
                <!-- subheader -->
                <div class="col-md-10">
                    <h2 class="sub_header">Ad Vendors</h2>
                </div>

                @if(Auth::user()->can('create.ad_vendor'))
                    <ad-vendor-create 
                        :routes="{{ json_encode($routes) }}"
                        :publishers="{{ json_encode($publishers) }}"></ad-vendor-create>
                @endif

                @if(Auth::user()->can('update.ad_vendor'))
                    <ad-vendor-view :publishers="{{ json_encode($publishers) }}"></ad-vendor-view>
                @endif
            </div>
            <div class="row my-5">
                @if(Auth::user()->can('view.ad_vendor'))
                    <div class="col-md-12">
                        <v-app>
                            <v-content>
                                <ad-vendor-list :routes="{{ json_encode($routes) }}"></ad-vendor-list>
                            </v-content>
                        </v-app>
                    </div>
                @endif
            </div>

        </div>
    </div>
@stop