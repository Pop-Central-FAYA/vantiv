@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Profile Management</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Profile</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <!-- main frame end -->
           <profile-details 
                :user-data="{{ json_encode($user) }}" 
                :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}">
            </profile-details>
        </div>
        <div class="row">
       <!-- subheader -->
                <div class="col-md-10">
                   
                </div>
               @if(Auth::user()->can('update.profile'))
                    <div class="col-md-1 text-right px-1">
                            <v-content>
                                <edit-profile 
                                    :user-data="{{ json_encode($user) }}" 
                                    :routes="{{ json_encode($routes) }}" 
                                    :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}">
                                </edit-profile>
                            </v-content>
                    </div>
                @endif

            </div>

          <!-- main frame end -->
         
    </div>
@stop


