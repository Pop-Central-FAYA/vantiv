@extends('layouts.faya_app')

@section('title')
    <title> FAYA | Discounts </title>
@stop

@section('content')
    @if(Auth::user()->companies->count() == 1)
        @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    @else
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    @endif

    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Discounts</h2>
            </div>
            @if(Auth::user()->companies()->count() > 1)
                <div class="column col_6">
                    <select class="publishers" name="companies[]" id="publishers" multiple="multiple" >
                        @foreach(Auth::user()->companies as $company)
                            <option value="{{ $company->id }}"
                                    @foreach($publishers_id as $publisher_id)
                                    @if($publisher_id->publisher_id == $company->id)
                                    selected
                                @endif
                                @endforeach
                            >{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <!-- campaign details -->
        <div class="the_frame client_dets mb4 when_loading">

            <!-- tab links -->
            <div class="tab_header m4 border_bottom clearfix">
                <a href="#agency">Agency</a>
                <a href="#brands">Brands</a>
                <a href="#time">Time</a>
                <a href="#day">Day Parts</a>
                <a href="#price">Price</a>
            </div>

            <div class="tab_contain">

                <!-- sales reports -->
                <div class="tab_content" id="agency">
                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7 clearfix">
                        </div>
                        <div class="column col_5 clearfix">
                            @if(Auth::user()->companies()->count() == 1)
                            <div class="col_6 right align_right">
                                <a href="#new_discount_agency" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>
                            @endif
                        </div>
                    </div>
                    <!-- end -->
                    <div class="similar_table pt3" id="default_agency">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Agency</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            @if(Auth::user()->companies()->count() > 1)
                                <span class="small_faint block_disp column col_1">Station</span>
                            @endif
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($agency_discounts as $agency_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ $agency_discount->name }}</div>
                                <div class="column col_1">{{ $agency_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($agency_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($agency_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $agency_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($agency_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($agency_discount->value_stop_date)) }}</div>
                                @if(Auth::user()->companies()->count() > 1)
                                    <div class="column col_1"> {{ $agency_discount->station }} </div>
                                @else
                                    <div class="column col_1">

                                        <!-- more links -->
                                        <div class="list_more">
                                            <span class="more_icon"></span>
                                            <div class="more_more">
                                                <a href="#edit_discount_agency{{ $agency_discount->id }}" class="modal_click">Edit</a>
                                                <a href="#delete_discount_agency{{ $agency_discount->id }}" class="modal_click color_red">Delete</a>
                                            </div>
                                        </div>

                                    </div>
                                @endif

                            </div>
                        @endforeach
                        <!-- table item end -->
                    </div>
                    <div class="similar_table pt3" id="filtered_agency" style="display: none;">

                    </div>
                </div>
                <!-- end -->
                <!-- Brands -->
                <div class="tab_content" id="brands">

                    <!-- filter -->
                    <div class="filters clearfix">

                        <div class="column col_7 clearfix">
                        </div>
                        <div class="column col_5 clearfix">
                            @if(Auth::user()->companies()->count() == 1)
                            <div class="col_6 right align_right">
                                <a href="#new_discount_brand" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>
                            @endif


                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3" id="default_brand">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Brand</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            @if(Auth::user()->companies()->count() > 1)
                                <span class="small_faint block_disp column col_1">Station</span>
                            @endif
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($brand_discounts as $brand_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ ucfirst($brand_discount->name) }}</div>
                                <div class="column col_1">{{ $brand_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($brand_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($brand_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $brand_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($brand_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($brand_discount->value_stop_date)) }}</div>
                                @if(Auth::user()->companies()->count() > 1)
                                    <div class="column col_1"> {{ $brand_discount->station }} </div>
                                @else
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>
                                        <div class="more_more">
                                            <a href="#edit_discount_brand{{ $brand_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_brand{{ $brand_discount->id }}" class="modal_click color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>
                                @endif
                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                    <div class="similar_table pt3" id="filtered_brand" style="display: none;">

                    </div>

                </div>
                <!-- end -->
                <!-- Time -->
                <div class="tab_content" id="time">

                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7 clearfix">
                        </div>
                        <div class="column col_5 clearfix">
                            @if(Auth::user()->companies()->count() == 1)
                            <div class="col_6 right align_right">
                                <a href="#new_discount_time" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>
                            @endif

                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3" id="default_time">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Agency</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            @if(Auth::user()->companies()->count() > 1)
                                <span class="small_faint block_disp column col_1">Station</span>
                            @endif
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($time_discounts as $time_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ $time_discount->hourly_range }}</div>
                                <div class="column col_1">{{ $time_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($time_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($time_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $time_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($time_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($time_discount->value_stop_date)) }}</div>
                                @if(Auth::user()->companies()->count() > 1)
                                    <div class="column col_1"> {{ $time_discount->station }} </div>
                                @else
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>
                                        <div class="more_more">
                                            <a href="#edit_discount_time{{ $time_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_time{{ $time_discount->id }}" class="modal_click color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>
                                @endif
                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                    <div class="similar_table pt3" id="filtered_time" style="display: none;">

                    </div>

                </div>
                <!-- end -->
                <!-- Day -->
                <div class="tab_content clearfix" id="day">

                    <!-- filter -->
                    <div class="filters clearfix">

                        <div class="column col_7 clearfix">
                        </div>
                        <div class="column col_5 clearfix">
                            @if(Auth::user()->companies()->count() == 1)
                            <div class="col_6 right align_right">
                                <a href="#new_discount_day" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>
                            @endif

                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3" id="default_daypart">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Day Parts</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            @if(Auth::user()->companies()->count() > 1)
                                <span class="small_faint block_disp column col_1">Station</span>
                            @endif
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($daypart_discounts as $daypart_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ $daypart_discount->day_part }}</div>
                                <div class="column col_1">{{ $daypart_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($daypart_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($daypart_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $daypart_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($daypart_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($daypart_discount->value_stop_date)) }}</div>
                                @if(Auth::user()->companies()->count() > 1)
                                    <div class="column col_1"> {{ $daypart_discount->station }} </div>
                                @else
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>
                                        <div class="more_more">
                                            <a href="#edit_discount_dayparts{{ $daypart_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_daypart{{ $daypart_discount->id }}" class="modal_click color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>
                                @endif
                            </div>
                    @endforeach
                    <!-- table item end -->
                    </div>

                    <div class="similar_table pt3" id="filtered_daypart" style="display: none;">

                    </div>

                </div>
                <!-- end -->
                <!-- Price -->
                <div class="tab_content" id="price">

                    <!-- filter -->
                    <div class="filters clearfix">
                        <div class="column col_7 clearfix">
                        </div>
                        <div class="column col_5 clearfix">
                            @if(Auth::user()->companies()->count() == 1)
                            <div class="col_6 right align_right">
                                <a href="#new_discount_price" class="btn small_btn modal_click"><span class="_plus"></span> New Discount</a>
                            </div>
                            @endif

                        </div>

                    </div>
                    <!-- end -->

                    <div class="similar_table pt3" id="default_price">
                        <!-- table header -->
                        <div class="_table_header clearfix m-b">
                            <span class="small_faint block_disp padd column col_2">Price Range</span>
                            <span class="small_faint block_disp column col_1">% Discount</span>
                            <span class="small_faint block_disp column col_1">% Start Date</span>
                            <span class="small_faint block_disp column col_1">% Stop Date</span>
                            <span class="small_faint block_disp column col_2">Discount Amount</span>
                            <span class="small_faint block_disp column col_2">Amount Start Date</span>
                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>
                            @if(Auth::user()->companies()->count() > 1)
                                <span class="small_faint block_disp column col_1">Station</span>
                            @endif
                            <span class="block_disp column col_1 color_trans">.</span>
                        </div>

                        <!-- table item -->
                        @foreach($price_discounts as $price_discount)
                            <div class="_table_item the_frame clearfix">
                                <div class="column padd col_2">{{ number_format($price_discount->discount_type_value,2) . ' - ' . number_format($price_discount->discount_type_sub_value,2)  }}</div>
                                <div class="column col_1">{{ $price_discount->percent_value }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($price_discount->percent_start_date)) }}</div>
                                <div class="column col_1"> {{ date('Y-m-d', strtotime($price_discount->percent_stop_date)) }}</div>
                                <div class="column col_2">{{ $price_discount->value }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($price_discount->value_start_date)) }}</div>
                                <div class="column col_2"> {{ date('Y-m-d', strtotime($price_discount->value_stop_date)) }}</div>
                                @if(Auth::user()->companies()->count() > 1)
                                    <div class="column col_1"> {{ (new \Vanguard\Services\Company\CompanyDetails($price_discount->broadcaster))->getCompanyDetails()->name }} </div>
                                @else
                                <div class="column col_1">

                                    <!-- more links -->
                                    <div class="list_more">
                                        <span class="more_icon"></span>
                                        <div class="more_more">
                                            <a href="#edit_discount_price{{ $price_discount->id }}" class="modal_click">Edit</a>
                                            <a href="#delete_discount_price{{ $price_discount->id }}" class="modal_click color_red">Delete</a>
                                        </div>
                                    </div>

                                </div>
                                @endif
                            </div>
                        @endforeach
                    <!-- table item end -->
                    </div>

                    <div class="similar_table pt3" id="filtered_price" style="display: none;">

                    </div>

                </div>
                <!-- end -->
            </div>

        </div>


    @if(Auth::user()->companies()->count() == 1)
        <!-- end discount modal -->
        {{--add modal--}}
        @include('broadcaster_module.discounts.add_modal')

        {{--edit modals--}}
        @include('broadcaster_module.discounts.edit_modal')

        {{--delete modal--}}
        @include('broadcaster_module.discounts.delete_modal')
    @endif


    </div><!-- main contain -->

@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        $(document).ready(function () {
            flatpickr(".flatpickr", {
                altInput: true,
            });
            $('.publishers').select2();

            $('body').delegate("#publishers", "change", function () {
                var channels = $("#publishers").val();
                if(channels != null){
                    $('.when_loading').css({
                        opacity: 0.1
                    });
                    $.ajax({
                        url: '/discount/filter',
                        method: 'GET',
                        data: { channel_id : channels },
                        success: function (data) {
                            $("#default_agency").remove();
                            $("#filtered_agency").show();
                            $("#filtered_agency").html(agencies(data.agency_discounts));
                            $("#default_brand").remove();
                            $("#filtered_brand").show();
                            $("#filtered_brand").html(brands(data.brand_discounts));
                            $("#default_time").remove();
                            $("#filtered_time").show();
                            $("#filtered_time").html(time(data.time_discounts));
                            $("#default_daypart").remove();
                            $("#filtered_daypart").show();
                            $("#filtered_daypart").html(dayParts(data.daypart_discounts));
                            $("#default_price").remove();
                            $("#filtered_price").show();
                            $("#filtered_price").html(price(data.price_discounts));
                            $('.when_loading').css({
                                opacity: 1
                            })
                        }

                    })
                }
            });

            if(companies > 1){
                function agencies (agency_discounts)
                {
                    var agency_discount_section = '';
                    agency_discount_section += '<div class="_table_header clearfix m-b">\n' +
                        '                            <span class="small_faint block_disp padd column col_2">Agency</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Discount</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Stop Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Discount Amount</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>\n' +
                    '                                <span class="small_faint block_disp column col_1">Station</span>\n' +
                        '                            <span class="block_disp column col_1 color_trans">.</span>\n' +
                        '                        </div>';
                    $.each(agency_discounts, function (index, value) {
                        agency_discount_section += '<div class="_table_item the_frame clearfix">\n' +
                            '                                <div class="column padd col_2">'+ value.name +'</div>\n' +
                            '                                <div class="column col_1">'+ value.percent_value +'</div>\n' +
                            '                                <div class="column col_1">'+ formatDate(value.percent_start_date) +'</div>\n' +
                            '                                <div class="column col_1">'+ formatDate(value.percent_stop_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ value.value +'</div>\n' +
                            '                                <div class="column col_2">'+ formatDate(value.value_start_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ formatDate(value.value_stop_date) +'</div>\n' +
                        '                                    <div class="column col_1">'+ value.station +'</div>\n' +
                            '                            </div>'
                    })
                    return agency_discount_section;
                }

                function brands(brand_discounts)
                {
                    var brand_discount_section = '';
                    brand_discount_section += '<div class="_table_header clearfix m-b">\n' +
                        '                            <span class="small_faint block_disp padd column col_2">Brand</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Discount</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Stop Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Discount Amount</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>\n' +
                    '                                <span class="small_faint block_disp column col_1">Station</span>\n' +
                        '                            <span class="block_disp column col_1 color_trans">.</span>\n' +
                        '                        </div>';
                    $.each(brand_discounts, function (index, value) {
                        brand_discount_section += '<div class="_table_item the_frame clearfix">\n' +
                            '                                <div class="column padd col_2">'+ value.name +'</div>\n' +
                            '                                <div class="column col_1">'+ value.percent_value +'</div>\n' +
                            '                                <div class="column col_1">'+ formatDate(value.percent_start_date) +'</div>\n' +
                            '                                <div class="column col_1">'+ formatDate(value.percent_stop_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ value.value +'</div>\n' +
                            '                                <div class="column col_2">'+ formatDate(value.value_start_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ formatDate(value.value_stop_date) +'</div>\n' +
                        '                                    <div class="column col_1">'+ value.station +'</div>\n' +
                            '                            </div>';
                    })
                    return brand_discount_section;
                }

                function time(time_discounts)
                {
                    var time_discount_section = '';
                    time_discount_section += '<div class="_table_header clearfix m-b">\n' +
                        '                            <span class="small_faint block_disp padd column col_2">Agency</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Discount</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Stop Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Discount Amount</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>\n' +
                    '                                <span class="small_faint block_disp column col_1">Station</span>\n' +
                        '                            <span class="block_disp column col_1 color_trans">.</span>\n' +
                        '                        </div>';
                    $.each(time_discounts, function (index, value) {
                        time_discount_section += '<div class="_table_item the_frame clearfix">\n' +
                            '                                <div class="column padd col_2">'+ value.hourly_range +'</div>\n' +
                            '                                <div class="column col_1">'+ value.percent_value +'</div>\n' +
                            '                                <div class="column col_1"> '+ formatDate(value.percent_start_date) +'</div>\n' +
                            '                                <div class="column col_1"> '+ formatDate(value.percent_stop_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ value.value +'</div>\n' +
                            '                                <div class="column col_2">'+ formatDate(value.value_start_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ formatDate(value.value_stop_date) +'</div>\n' +
                        '                                    <div class="column col_1">'+ value.station +'</div>\n' +
                            '                            </div>'
                    })
                    return time_discount_section;
                }

                function dayParts(daypart_discounts)
                {
                    var daypart_discount_section = '';
                    daypart_discount_section += '<div class="_table_header clearfix m-b">\n' +
                        '                            <span class="small_faint block_disp padd column col_2">Day Parts</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Discount</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Stop Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Discount Amount</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>\n' +
                    '                                <span class="small_faint block_disp column col_1">Station</span>\n' +
                        '                            <span class="block_disp column col_1 color_trans">.</span>\n' +
                        '                        </div>';
                    $.each(daypart_discounts, function (index, value) {
                        daypart_discount_section += '<div class="_table_item the_frame clearfix">\n' +
                            '                                <div class="column padd col_2">'+ value.day_part +'</div>\n' +
                            '                                <div class="column col_1">'+ value.percent_value +'</div>\n' +
                            '                                <div class="column col_1"> '+ formatDate(value.percent_start_date) +'</div>\n' +
                            '                                <div class="column col_1"> '+ formatDate(value.percent_stop_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ value.value +'</div>\n' +
                            '                                <div class="column col_2"> '+ formatDate(value.value_start_date) +'</div>\n' +
                            '                                <div class="column col_2"> '+ formatDate(value.value_stop_date) +'</div>\n' +
                        '                                    <div class="column col_1"> '+ value.station +'</div>\n' +
                            '                            </div>';
                    })
                    return daypart_discount_section;
                }

                function price(price_discounts)
                {
                    var price_discount_section = '';
                    price_discount_section += '<div class="_table_header clearfix m-b">\n' +
                        '                            <span class="small_faint block_disp padd column col_2">Price Range</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Discount</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_1">% Stop Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Discount Amount</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Start Date</span>\n' +
                        '                            <span class="small_faint block_disp column col_2">Amount Stop Date</span>\n' +
                    '                                <span class="small_faint block_disp column col_1">Station</span>\n' +
                        '                            <span class="block_disp column col_1 color_trans">.</span>\n' +
                        '                        </div>';
                    $.each(price_discounts, function (index, value) {
                        price_discount_section += '<div class="_table_item the_frame clearfix">\n' +
                            '                                <div class="column padd col_2">'+ $.number(value.discount_type_value, 2)+' - '+ $.number(value.discount_type_sub_value, 2)+'</div>\n' +
                            '                                <div class="column col_1">'+ value.percent_value +'</div>\n' +
                            '                                <div class="column col_1"> '+ formatDate(value.percent_start_date) +'</div>\n' +
                            '                                <div class="column col_1"> '+ formatDate(value.percent_stop_date) +'</div>\n' +
                            '                                <div class="column col_2">'+ value.value +'</div>\n' +
                            '                                <div class="column col_2"> '+ formatDate(value.value_start_date) +'</div>\n' +
                            '                                <div class="column col_2"> '+ formatDate(value.value_stop_date) +'</div>\n' +
                        '                                    <div class="column col_1"> '+ value.station +' </div>\n' +
                            '                            </div>';
                    });
                    return price_discount_section;
                }
            }
        })

        function formatDate(date){
            var splited_date = date.split(' ');
            return splited_date[0];
        }


    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />

    <style>
        ._table_item > div:first-child {
            padding-top: 12px;
            font-size: 16px;
            @import;
        }
    </style>
@stop
