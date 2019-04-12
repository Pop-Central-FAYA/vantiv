@foreach($fayaFound['programs_stations'] as $value)
    @foreach($default_material_length as $duration)
        <div class="modal_contain" style="width: 1000px;" id="edit_program_modal_{{ $duration.$value->id }}">
        <div class="the_frame clearfix mb border_top_color pt load_this_div">
            <form action="{{ route('media_plan.program.store') }}" data-get_id_update="{{ $duration.'_'.$value->id }}" class="update_form" method="post" id="update_{{ $duration.'_'.$value->id }}">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('program_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Program</label>
                            <div class="">
                                <input type="text" name="program_name" required value="{{ $value->name_of_program }}" placeholder="Program Name">

                                @if($errors->has('program_name'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('program_name') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="station" value="{{ $value->station }}">
                    <p class="m-b">Unit Rates</p>
                    <div class="clearfix mb3">
                        @foreach($default_material_length as $duration)
                            <input type="hidden" name="duration[]" value="{{ $duration }}">
                            <div class="input_wrap column col_5 {{ $errors->has('unit_rate') ? ' has-error' : '' }}">
                                <label class="small_faint">{{ $duration }} Seconds</label>
                                <div class="">
                                            <input type="number"
                                                   @foreach(json_decode($value->duration_lists) as $key => $duration_list)
                                                   @if($duration_list == $duration)
                                                   value="{{ json_decode($value->rate_lists)[$key] }}"
                                                   @endif
                                                   @endforeach
                                                   name="unit_rate[]">
                                    @if($errors->has('unit_rate'))
                                        <strong>
                                            <span class="help-block">
                                                {{ $errors->first('unit_rate') }}
                                            </span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class='m-b'>Time Belt</p>
                    <hr>
                    <p><br></p>
                    <div class="clearfix m-b">
                        <!-- start -->
                        <div class="clearfix m-b b remove_div_already_had_{{ $value->id }}">
                            <div class="clearfix m-b">
                                <div class="column col_2">
                                    <p>{{ ucfirst($value->day) }}</p>
                                </div>
                                <input type="hidden" name="days[]" value="{{ $value->day }}">
                                <div class="input_wrap column col_3">
                                    <label class="small_faint">Start Time</label>
                                    <input type="text" id="timepicker" name="start_time[]" value="{{ $value->actual_time_slot ? explode('-',$value->actual_time_slot)[0] : '' }}" readonly/>
                                </div>

                                <div class="input_wrap column col_3">
                                    <label class="small_faint">End Time</label>
                                    <input type="text" id="timepicker" name="end_time[]" value="{{ $value->actual_time_slot ? explode('-',$value->actual_time_slot)[1] : '' }}" readonly/>
                                </div>
                                <div class="column col_2">
                                    <a href="" style="color:red" data-button_id="{{ $value->id }}" class="uppercased color_initial remove_already">Remove</a>
                                </div>
                            </div>
                        </div>
                    <!-- end -->
                    </div>
                    <hr>
                    <p><br></p>
                    <div class="clearfix m-b">
                        @foreach($days as $day)
                            <div class="clearfix m-b b" id="dynamic_field_{{ $day }}">
                                <div class="clearfix m-b">
                                    <div class="column col_2">
                                        <p>{{ ucfirst($day) }}</p>
                                    </div>
                                    <input type="hidden" name="days[]" value="{{ $day }}">
                                    <div class="input_wrap column col_3">
                                        <label class="small_faint">Start Time</label>
                                        <input type="text" id="timepicker" name="start_time[]" class="t_picker timepicker_{{ $day }}"/>
                                    </div>

                                    <div class="input_wrap column col_3">
                                        <label class="small_faint">End Time</label>
                                        <input type="text" id="timepicker" name="end_time[]" class="t_picker timepicker_{{ $day }}"/>
                                    </div>
                                    <div class="column col_2">
                                        <a href="" id="add_more_{{ $day }}" class="uppercased color_initial">Add More</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- end -->
                    <!-- end -->
                    <div class="mb4 align_right pt">
                        <input type="submit" value="Update Program" id="submit_15{{ $value->id }}" class="btn uppercased mb4">
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
@endforeach
