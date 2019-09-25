@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Ad Vendors</title>
@stop

@section('content')

    <div class="main_contain" id="load_this_div">
    <div class="container-fluid media-asset-management">
            <div class="row">
       <!-- subheader -->
       <div class="col-md-10">
                    <h2 class="sub_header">Ad Vendors</h2>
                </div>
            </div>
            <div class="row my-5">
                        @if(Auth::user()->can('view.client'))
                            <div class="col-md-12">
                                <v-app>
                                    <v-content>
                                    <ad-vendor-details :ad-vendor-data="{{ json_encode($ad_vendor) }}" :mpos="{{ json_encode($mpos) }}"></ad-vendor-details>
                                    </v-content>
                                </v-app>
                            </div>
                        @endif
                    </div>
                </div>
    </div>
@stop