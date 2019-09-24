@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Company Management</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Company</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <!-- main frame end -->
           <company-details 
                :company-data="{{ json_encode($company) }}" 
                :routes="{{ json_encode($url) }}" 
                :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}">
            </company-details>
        </div>
        <div class="row">
       <!-- subheader -->
                <div class="col-md-10">
                   
                </div>

                @if(Auth::user()->can('update.company'))
                    <div class="col-md-2 text-right px-2">
                        <company-edit 
                            :company-data="{{ json_encode($company) }}" 
                            :routes="{{ json_encode($url) }}" 
                            :permission-list="{{ json_encode(Auth::user()->getAllPermissions()->pluck('name')) }}">
                        </company-edit>
                    </div>
                @endif

            </div>

          <!-- main frame end -->
         
    </div>
@stop

