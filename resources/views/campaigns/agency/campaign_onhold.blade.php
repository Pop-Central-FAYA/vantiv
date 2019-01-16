@extends('layouts.faya_app')

@section('title')
    <title> FAYA | ON HOLD CAMPAIGNS</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.agency.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">On Hold Campaigns</h2>
            </div>
        </div>

        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_8 p-t">
                    <p class="uppercased weight_medium">On Hold Campaigns</p>
                </div>
            </div>

            <!-- campaigns table -->
            <div class="similar_table pt3 container_modal_pay">
                <!-- table header -->
                <div class="_table_header clearfix m-b">
                    <span class="small_faint block_disp padd column col_4">Campaign Name</span>
                    <span class="small_faint block_disp column col_2">Brand</span>
                    <span class="small_faint block_disp column col_2">Total Budget</span>
                    <span class="small_faint block_disp column col_2">Date Created</span>
                    <span class="block_disp column col_2 color_trans">.</span>
                </div>

                <!-- table item -->
                @foreach($campaigns as $campaign)
                    <div class="_table_item the_frame clearfix">
                        <div class="padd column col_4">
                            {{ $campaign['name'] }}
                        </div>
                        <div class="column col_2">{{ $campaign['brand'] }}</div>
                        <div class="column col_2">&#8358; {{ $campaign['budget'] }}</div>
                        <div class="column col_2">{{ date('M j, Y', strtotime($campaign['date_created'])) }}</div>
                        <div class="column col_2">

                            <!-- more links -->
                            <div class="list_more">
                                <span class="more_icon"></span>

                                <div class="more_more">
                                    <a href="#edit_campaign{{ $campaign['campaign_id'] }}" class="modal_click">Edit</a>
                                    <a href="#payment{{ $campaign['campaign_id'] }}" class="modal_click">Submit</a>
                                </div>
                            </div>

                        </div>
                    </div>
            @endforeach
            <!-- table item end -->
            </div>
            <!-- end -->
        </div>

        @foreach($campaigns as $campaign)
            <div class="modal_contain" id="edit_campaign{{ $campaign['campaign_id'] }}">
                <h2 class="sub_header mb4">Edit Campaign : {{ $campaign['name'] }}</h2>
                <form action="{{ route('agency.campaign_information.update', ['campaign_id' => $campaign['campaign_id']]) }}" method="POST" >
                    {{ csrf_field() }}
                    <div class="clearfix">
                        <div class="input_wrap column col_12{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="small_faint">Campaign Name</label>
                            <input type="text" name="name" value="{{ $campaign['name'] }}"  placeholder="e.g Coca Cola">
                            @if($errors->has('name'))
                                <strong>
                                    <span class="error-block" style="color: red;">{{ $errors->first('name') }}</span>
                                </strong>
                            @endif
                        </div>

                        <div class="input_wrap column col_12{{ $errors->has('product') ? ' has-error' : '' }}">
                            <label class="small_faint">Product</label>
                            <input type="text" name="product" value="{{ $campaign['product'] }}"  >
                            @if($errors->has('product'))
                                <strong>
                                    <span class="error-block" style="color: red;">{{ $errors->first('product') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <div class="align_right">
                        <input type="submit" value="Update Campaign Information" class="btn uppercased update">
                    </div>

                </form>
            </div>

            <!-- payment modal -->
            <div class="modal_contain payment_modal container_modal_pay" style="height: 400px;" id="payment{{ $campaign['campaign_id'] }}">
                <h2 class="border_bottom align_center">Complete Purchase</h2>

                <div class="padd mb4 pt">
                    <h3 class="weight_medium uppercased">TOTAL: &#8358; {{ $campaign['budget'] }}</h3>
                    <p class="small_faint mb4"></p>

                    <p class="mb">Choose Payment Option</p>
                    <form method="POST" action="{{ route('agency.campaign.update', ['campaign_id' => $campaign['campaign_id']]) }}">
                        {{ csrf_field() }}

                        <input type="hidden" value="{{ $campaign['budget'] }}" name="total"/>
                        <div class="mb4 create_payment">

                            <li class="m-b">
                                <input type="radio" checked name="payment_option" value="Wallet" id="wallet{{ $campaign['campaign_id'] }}">
                                <label class="weight_medium" for="wallet{{ $campaign['campaign_id'] }}">Wallet</label>
                            </li>
                            <br>
                            <div class="column align_right wallet">
                                <button type="submit" class="btn uppercased _proceed">Proceed <span class=""></span></button>
                            </div>
                        </div>
                    </form>
                </div>

                <br>
                <br>
            </div>
            <!-- end -->
        @endforeach
    </div>
@stop

@section('styles')
    <style>
        ._table_item > div:first-child {
            padding-top: 12px;
            font-size: 16px;
        @import;
        }
    </style>
@stop
