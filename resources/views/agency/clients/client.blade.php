@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Clients</title>
@stop

@section('content')

    <div class="main_contain" id="load_this_div">
        <!-- header -->
    @include('partials.new-frontend.agency.header')
    <div class="container-fluid media-asset-management">
            <div class="row">
       <!-- subheader -->
       <div class="col-md-10">
                    <h2 class="sub_header">Client</h2>
                </div>
            </div>
            <div class="row my-5">
                        @if(Auth::user()->can('view.client'))
                            <div class="col-md-12">
                                <v-app>
                                    <v-content>
                                    <client-details  :client-data="{{ json_encode($client) }}"  :campaigns="{{ json_encode($campaign_list) }}" :brands="{{ json_encode($brands) }}"></client-details>
                                    </v-content>
                                </v-app>
                            </div>
                        @endif
                    </div>
                </div>
    </div>
@stop