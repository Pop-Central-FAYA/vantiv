@extends('layouts.faya_app')
@section('title')
    <title>FAYA | Agency - All Brands</title>
@stop
@section('content')
    <div class="main_contain">
        <!-- header -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Brands</h2>
            </div>
        </div>

        <div class="similar_table pt3">
            <!-- table header -->
            <div class="_table_header clearfix m-b">
                <span class="weight_medium small_faint block_disp column col_4 padd">Brand</span>
                <span class="weight_medium small_faint block_disp column col_2">All Campaigns</span>
                <span class="weight_medium small_faint block_disp column col_2">Total Expense</span>
                <span class="weight_medium small_faint block_disp column col_3">Last Campaign</span>
                <span class="weight_medium block_disp column col_1 color_trans">.</span>
            </div>

            <!-- table item -->
            @foreach($all_brands as $all_brand)
                <div class="_table_item the_frame clearfix">
                    <div class="padd column col_4">
                        <span class="client_ava"><img src="{{ $all_brand['image_url'] ? asset(decrypt($all_brand['image_url'])) : '' }}"></span>
                        <p>{{ ucfirst($all_brand['brand']) }}</p>
                        <span class="small_faint">Added {{ date('M j, Y', strtotime($all_brand['date'])) }}</span>
                    </div>
                    <div class="column col_2">{{ $all_brand['campaigns'] }}</div>
                    <div class="column col_2">&#8358; {{ $all_brand['total'] }}</div>
                    <div class="column col_3">{{ ucfirst($all_brand['last_campaign']) }}</div>
                    <div class="column col_1">
                        <span class="more_icon">
                            <!-- more links -->
                            <div class="list_more">
                                <span class="more_icon"></span>

                                <div class="more_more">
                                    <a href="">Details</a>
                                    <a href="#brand{{ $all_brand['id'] }}" class="modal_click">Edit</a>
                                    {{--<a href="" class="color_red">Delete</a>--}}
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
        @endforeach
        <!-- table item end -->
        </div>
        <p><br></p>
        {{ $all_brands->links('pagination.general') }}

    </div>
@stop

@section('scripts')
    {{--modal for editing brands--}}
    @foreach($all_brands as $all_brand)
        <div class="modal_contain" id="brand{{ $all_brand['id'] }}">
            <h2 class="sub_header mb4">Edit Brand : {{ $all_brand['brand'] }}</h2>
            <form action="{{ route('agency.brands.update', ['id' => $all_brand['id']]) }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="clearfix">
                    <div class="input_wrap column col_7{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                        <label class="small_faint">Brand Name</label>
                        <input type="text" name="brand_name" value="{{ $all_brand['brand'] }}"  placeholder="e.g Coca Cola">
                        @if($errors->has('brand_name'))
                            <strong>
                                <span class="error-block" style="color: red;">{{ $errors->first('brand_name') }}</span>
                            </strong>
                        @endif
                    </div>
                    <div class='column col_5 file_select align_center pt3{{ $errors->has('brand_logo') ? ' has-error' : '' }}' style="height: 70px;">
                        <input type="file" id="file" name="brand_logo" />
                        <span class="small_faint block_disp mb3">Brand Logo</span>
                        @if($errors->has('brand_logo'))
                            <strong>
                                <span class="error-block" style="color: red;">{{ $errors->first('brand_logo') }}</span>
                            </strong>
                        @endif
                    </div>
                </div>

                <input type="hidden" name="walkin_id" value="{{ $all_brand['client_id'] }}">

                <div class="input_wrap">
                    <label class="small_faint">Industry</label>

                    <div class="select_wrap">
                        <select name="industry" id="industry">
                            <option value="">Select Industry</option>
                            @foreach($industries as $industry)
                                @if($industry->sector_code === $all_brand['industry_id'])
                                    <option value="{{ $industry->sector_code }}"
                                            @if($industry->sector_code === $all_brand['industry_id'])
                                            selected
                                            @endif
                                    >{{ $industry->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="input_wrap">
                    <label class="small_faint">Sub Industry</label>

                    <div class="select_wrap">
                        <select name="sub_industry" id="sub_industry">
                            @foreach($sub_industries as $sub_industry)
                                @if($sub_industry->sub_sector_code === $all_brand['sub_industry_id'])
                                    <option value="{{ $sub_industry->sub_sector_code }}"
                                            @if($sub_industry->sub_sector_code === $all_brand['sub_industry_id'])
                                            selected
                                            @endif
                                    >{{ $sub_industry->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="align_right">
                    <input type="submit" value="Update Brand" class="btn uppercased update">
                </div>

            </form>
        </div>
    @endforeach
@stop
