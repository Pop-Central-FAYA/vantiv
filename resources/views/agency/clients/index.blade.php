@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Clients</title>
@stop

@section('content')

    <div class="main_contain" id="load_this_div">
        <!-- header -->
    <div class="container-fluid media-asset-management">
            <div class="row">
       <!-- subheader -->
       <div class="col-md-10">
                    <h2 class="sub_header">All Clients</h2>
                </div>

                @if(Auth::user()->can('create.client'))
                    <div class="col-md-2 text-right px-2">
                        <clients-create></clients-create>
                    </div>
                @endif

            </div>

            <div class="row my-5">
                        @if(Auth::user()->can('view.client'))
                            <div class="col-md-12">
                                <v-app>
                                    <v-content>
                                       <clients-list></clients-list>
                                    </v-content>
                                </v-app>
                            </div>
                        @endif
                    </div>
                </div>
    </div>
@stop
