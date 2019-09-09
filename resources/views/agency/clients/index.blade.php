@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Clients</title>
@stop

@section('content')

    <div class="main_contain">
    <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">All Clients</h2>
            </div>
        </div>

        <div class="the_frame client_dets mb4">
        @if(Auth::user()->can('view.client'))
            <!-- campaigns table -->
            <v-app>
                <v-content>
                <clients-list></clients-list>
                </v-content>
            </v-app>
            <!-- end -->
            @endif
        </div>
    </div>
@stop