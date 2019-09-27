@extends('dsp_layouts.faya_app')

@section('title')
    <title> Vantage | Users</title>
@stop

@section('content')

    <div class="main_contain" id="load_this_div">
        <!-- header -->
    <div class="container-fluid media-asset-management">
            <div class="row">
       <!-- subheader -->
       <div class="col-md-10">
                    <h2 class="sub_header">All Users</h2>
                </div>

                @if(Auth::user()->can('create.user'))
                    <div class="col-md-2 text-right px-2">
                        <invite-user  
                                :roles="{{ json_encode($roles) }}"
                                :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}">
                        ></invite-user>
                    </div>
                @endif

            </div>

            <div class="row my-5">
                        @if(Auth::user()->can('view.user'))
                            <div class="col-md-12">
                                <v-app>
                                    <v-content>
                                       <users-list 
                                            :roles="{{ json_encode($roles) }}"
                                            :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}">
                                     </users-list>
                                    </v-content>
                                </v-app>
                            </div>
                        @endif
                    </div>
                </div>
    </div>
@stop
