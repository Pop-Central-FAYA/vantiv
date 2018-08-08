@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Broadcaster Welcome</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->

    <!-- subheader -->

        <!-- main frame -->
        <p><br></p>
        <p><br></p>
        <p><br></p>
        <div class="the_frame col_10 clearfix mb border_top_color">

            <div class="margin_center col_5 clearfix pt4 create_fields">

                <!-- progress bar -->
                <div class="create_gauge">
                    <div class=""></div>
                </div>


                <p class='weight_medium m-b' style="text-align: center"><h1>Welcome {{ $broadcaster_info[0]->brand }}</h1></p><br>

                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_6">
                        <a href="{{ route('bradcaster.campaign_management') }}" class="btn uppercased ">Campaign Management</a>
                    </div>

                    <div class="column col_6 align_right">
                        <a href="#" class="btn uppercased ">Inventory Management</a>
                    </div>
                </div>

            </div>
        </div>
        <!-- main frame end -->

    </div>
@stop