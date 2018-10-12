{{--edit modal for agency--}}
@foreach($agency_discounts as $agency_discount)
<div class="modal_contain" id="delete_discount_agency{{ $agency_discount->id }}">
    <h2 class="sub_header mb4">Delete Discount</h2>
    <p>Are you sure you want to delete this item ?</p>

    <div class="align_right pt3">
        <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
        <a href="{{ url('discount/' . $agency_discount->id . '/delete') }}" class="color_red">Delete</a>
    </div>
</div>
@endforeach

{{--brand update--}}
@foreach($brand_discounts as $brand_discount)
    <div class="modal_contain" id="delete_discount_brand{{ $brand_discount->id }}">
        <h2 class="sub_header mb4">Delete Discount</h2>
        <p>Are you sure you want to delete this item ?</p>

        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <a href="{{ url('discount/' . $brand_discount->id . '/delete') }}" class="color_red">Delete</a>
        </div>
    </div>
@endforeach

{{--time update--}}
@foreach($time_discounts as $time_discount)
    <div class="modal_contain" id="delete_discount_time{{ $time_discount->id }}">
        <h2 class="sub_header mb4">Delete Discount</h2>
        <p>Are you sure you want to delete this item ?</p>

        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <a href="{{ url('discount/' . $time_discount->id . '/delete') }}" class="color_red">Delete</a>
        </div>
    </div>
@endforeach

{{--dayparts update--}}
@foreach($daypart_discounts as $daypart_discount)
    <div class="modal_contain" id="delete_discount_daypart{{ $daypart_discount->id }}">
        <h2 class="sub_header mb4">Delete Discount</h2>
        <p>Are you sure you want to delete this item ?</p>

        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <a href="{{ url('discount/' . $daypart_discount->id . '/delete') }}" class="color_red">Delete</a>
        </div>
    </div>
@endforeach

{{--price update--}}
@foreach($price_discounts as $price_discount)
    <div class="modal_contain" id="delete_discount_price{{ $price_discount->id }}">
        <h2 class="sub_header mb4">Delete Discount</h2>
        <p>Are you sure you want to delete this item ?</p>

        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <a href="{{ url('discount/' . $price_discount->id . '/delete') }}" class="color_red">Delete</a>
        </div>
    </div>
@endforeach