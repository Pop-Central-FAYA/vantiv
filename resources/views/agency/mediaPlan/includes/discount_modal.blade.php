@foreach($fayaFound['programs_stations'] as $value)
    @foreach($default_material_length as $duration)
        <div class="modal_contain" style="width: 1000px;" id="discount_modal_{{ $duration.'_'.$value->id }}">
            <div class="the_frame clearfix mb border_top_color pt load_this_div">
                <form action="{{ route('media_plan.volume_discount.store') }}" data-get_station_discount="{{ $value->station }}" data-get_volume_id="{{ $duration.'_'.$value->id }}" class="submit_discount_form" method="post" id="submit_discount_{{ $duration.'_'.$value->id }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="margin_center col_11 clearfix pt4 create_fields">

                        <div class="clearfix mb3">
                            <div class="input_wrap column col_8 {{ $errors->has('discount') ? ' has-error' : '' }}">
                                <label class="small_faint">Discount</label>
                                <div class="">
                                    <input type="text" name="discount" class="referesh_discount_{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $value->station)) }}" value="{{ $value->volume_discount }}" required placeholder="Volume Discount">

                                    @if($errors->has('discount'))
                                        <strong>
                                                                        <span class="help-block">
                                                                            {{ $errors->first('discount') }}
                                                                        </span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="station" value="{{ $value->station }}">
                        <div class="mb4 align_right pt">
                            <input type="submit" value="Create Discount" id="submit_15{{ $value->id }}" class="btn uppercased mb4">
                        </div>

                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endforeach
