@foreach($fayaFound['programs_stations'] as $value)
    @foreach($default_material_length as $duration)
        <div class="modal_contain" style="width: 1000px;" id="program_modal_{{ $duration.$value->id }}">
            <div class="the_frame clearfix mb border_top_color pt load_this_div">
            <form action="{{ route('media_plan.program.store') }}" data-get_id="{{ $duration.'_'.$value->id }}" class="submit_form" method="post" id="submit_{{ $duration.'_'.$value->id }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="margin_center col_11 clearfix pt4 create_fields">

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('program_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Program</label>
                            <div class="">
                                <input type="text" name="program_name" required placeholder="Program Name">

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
                                    <input type="number" name="unit_rate[]" placeholder="Enter Unit Rate">
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
                        @foreach($days as $day)
                            <div class="clearfix m-b b dynamic" id="dynamic_field_{{ $day }}">
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
                    <!-- end -->
                    </div>
                    <!-- end -->
                    <!-- end -->
                    <div class="mb4 align_right pt">
                        <input type="submit" value="Create Program" id="submit_15{{ $value->id }}" class="btn uppercased mb4">
                    </div>

                </div>
            </form>
            </div>
        </div>
    @endforeach
@endforeach
