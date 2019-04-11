@foreach($media_plans_programs as $media_plans_program)
    <div class="modal_contain" style="width: 1000px;" id="edit_program_modal_15{{ strtolower(preg_replace('/[^a-zA-Z0-9]+/', '', $media_plans_program->day.'_'.$media_plans_program->station.'_'.$media_plans_program->start_time)) }}">
        <div class="the_frame clearfix mb border_top_color pt load_this_div">
            <form action="{{ route('media_plan.program.store') }}" method="post" id="update_program{{ $media_plans_program->id }}">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('program_name') ? ' has-error' : '' }}">
                            <label class="small_faint">Program</label>
                            <div class="">
                                <input type="text" name="program_name" required value="{{ $media_plans_program->program_name }}" placeholder="Program Name">

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
                    <input type="hidden" name="station" value="{{ $media_plans_program->station }}">
                    <p class='m-b'>Time Belt</p>
                    <hr>
                    <p><br></p>
                    <div class="clearfix m-b">
                        <!-- start -->
                        <div class="clearfix m-b b remove_div_already_had_{{ $media_plans_program->id }}">
                            <div class="clearfix m-b">
                                <div class="column col_2">
                                    <p>{{ ucfirst($media_plans_program->day) }}</p>
                                </div>
                                <input type="hidden" name="days[]" value="{{ $media_plans_program->day }}">
                                <div class="input_wrap column col_3">
                                    <label class="small_faint">Start Time</label>
                                    <input type="text" id="timepicker" name="start_time[]" value="{{ explode('-',$media_plans_program->actual_time_slot)[0] }}" readonly/>
                                </div>

                                <div class="input_wrap column col_3">
                                    <label class="small_faint">End Time</label>
                                    <input type="text" id="timepicker" name="end_time[]" value="{{ explode('-',$media_plans_program->actual_time_slot)[1] }}" readonly/>
                                </div>
                                <div class="column col_2">
                                    <a href="" style="color:red" data-button_id="{{ $media_plans_program->id }}" class="uppercased color_initial remove_already">Remove</a>
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
                        <input type="submit" value="Update Program" id="submit_15{{ $media_plans_program->id }}" class="btn uppercased mb4">
                    </div>

                </div>
            </form>
        </div>
    </div>
@endforeach
