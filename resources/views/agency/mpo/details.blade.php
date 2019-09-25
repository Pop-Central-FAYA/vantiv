@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | MPO Preview</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header"></h2>
            </div>
        </div>
        <!-- campaign details -->
        <div class="the_frame client_dets mb4">
            <v-app>
                <v-content>
                    <mpo-details
                        :mpo="{{ json_encode($mpo) }}"
                    >
                    </mpo-details>
                </v-content>
            </v-app>
        </div>
    </div>
@stop
