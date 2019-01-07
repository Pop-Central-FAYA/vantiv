@if($broadcaster_details && $broadcaster_details->channel_id === $tv->id)
    <div class="upload_block clearfix">
        <div class=" align_center _block_one">
            <p class="small_faint">TV Content</p><br>
            <form method="GET" action="{{ route('campaign.store3', ['id' => $id]) }}" id="form-data" enctype="multipart/form-data">
                <div class="dashed_upload file_select mb">
                    <input type="file" name="file" id="file_upload" class="tv_content file_upload" >
                    <p class="small_faint">Drag files to upload</p>
                </div>
                <div class="progress">

                </div><br>
                <div class="clearfix mb">
                    <div class="input_wrap column col_12">
                        <label class="small_faint">Time Slot</label>
                        <div class="select_wrap{{ $errors->has('time') ? ' has-error' : '' }}">
                            <select name="time" id="time" required>
                                <option value="">Select Time</option>
                                <option value="15">15 Seconds</option>
                                <option value="30">30 Seconds</option>
                                <option value="45">45 Seconds</option>
                                <option value="60">60 Seconds</option>
                            </select>
                            @if($errors->has('time'))
                                <strong>{{ $errors->first('time') }}</strong>
                            @endif
                        </div>
                    </div>
                </div>
                <input type="hidden" name="file_duration" class="file_duration" id="file_duration" size="5" />
                <audio id="audio"></audio>
                <br>
                <div class="column col_12 align_right">
                    <button type="button" id="button_submit" class="btn uppercased _proceed">Submit <span class=""></span></button>
                </div>
            </form>
        </div>

        <div class="_block_two align_center gallery">
            {{--@include('partials.show_file_tv')--}}
        </div>
    </div>
@else
    <div class="upload_block clearfix">
        <div class=" align_center _block_one">
            <p class="small_faint">Radio Content</p><br>
            <form method="GET" action="{{ route('campaign.store3', ['id' => $id]) }}" id="form-data" >
                {{ csrf_field() }}
                <div class="dashed_upload file_select mb">
                    <input type="file" name="file" class="cloudinary_fileupload_radio" >
                    <p class="small_faint">Drag files to upload</p>
                </div>
                <div class="progress-radio">
                </div><br>
                <div class="clearfix mb">
                    <div class="input_wrap column col_12">
                        <label class="small_faint">Time Slot</label>

                        <div class="select_wrap{{ $errors->has('time') ? ' has-error' : '' }}">
                            <select name="time" id="time_radio" required>
                                <option value="">Select Time</option>
                                <option value="15">15 Seconds</option>
                                <option value="30">30 Seconds</option>
                                <option value="45">45 Seconds</option>
                                <option value="60">60 Seconds</option>
                            </select>

                            @if($errors->has('time'))
                                <strong>{{ $errors->first('time') }}</strong>
                            @endif
                        </div>
                    </div>
                </div>
                <br>
                <div class="column col_12 align_right">
                    <button type="button" id="button_submit_radio" class="btn uppercased _proceed">Submit <span class=""></span></button>
                </div>
            </form>
        </div>

        <div class="_block_two align_center gallery_radio">
            {{--@include('partials.show_file_radio')--}}
        </div>
    </div>
@endif
