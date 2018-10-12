@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Campaign Step 3</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Create New Campaign</h2>
            </div>
        </div>


        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color">

            <div class="margin_center col_7 clearfix pt4 create_fields">

                <div class="create_gauge clearfix">
                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress active">
                        <span class="one_point"></span>
                    </div>

                    <div class="_progress">
                        <span class="one_point"></span>
                    </div>
                </div>

                <!-- progress bar -->
                <div class="create_gauge">
                    <div class=""></div>
                </div>


                <p class='weight_medium m-b'>Upload Media Steps</p>
                <p class="small_faint col_9 mb4"><ul>Step 1 : Upload your content to fit in the slot</ul>
                <ul>Step 2 : Select time slot from the time slot drop down</ul>
                <ul>Step 3 : Wait for the content to complete</ul>
                <ul>Step 4 : Click on the upload button.</ul>
                <br>
                </p>

                @if((Session::get('first_step')) != null)
                    @if($broadcaster_details[0]->channel_id === 'nzrm6hchjats36')
                        <div class="upload_block clearfix">

                            <div class=" align_center _block_one">
                                <p class="small_faint">TV Content</p><br>
                                <form method="GET" action="{{ route('campaign.store3', ['id' => $id]) }}" id="form-data" enctype="multipart/form-data">
                                    <div class="dashed_upload file_select mb">
                                        <input type="file" name="file" class="cloudinary_fileupload" >
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
                                    <br>
                                    <div class="column col_12 align_right">
                                        <button type="button" id="button_submit" class="btn uppercased _proceed">Submit <span class=""></span></button>
                                    </div>
                                </form>
                            </div>

                            <div class="_block_two align_center gallery">
                                @include('partials.show_file_tv')
                            </div>
                        </div>
                    @else
                        <div class="upload_block clearfix">

                            <div class=" align_center _block_one">
                                <p class="small_faint">Radio Content</p><br>
                                <form method="GET" action="{{ route('campaign.store3', ['id' => $id]) }}" id="form-data" enctype="multipart/form-data">
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
                                @include('partials.show_file_radio')
                            </div>
                        </div>
                    @endif
                @endif


                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_6">
                        <a href="{{ route('campaign.create2', ['id' => $id]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>

                    <div class="column col_6 align_right">
                        <a href="{{ route('campaign.create3_1', ['id' => $id]) }}" class="btn uppercased _proceed modal_click">Proceed <span class=""></span></a>
                    </div>
                </div>

            </div>
        </div>
        <!-- main frame end -->


    </div>
@stop

@section('scripts')
    <script src="{{ asset('new_frontend/js/jquery.ui.widget.js') }}" type="text/javascript"></script>
    <script src="{{ asset('new_frontend/js/load-image.all.min.js') }}"></script>
    <script src="{{ asset('new_frontend/js/canvas-to-blob.min.js') }}"></script>
    <script src="{{ asset('new_frontend/js/jquery.iframe-transport.js') }}" type="text/javascript"></script>
    <script src="{{ asset('new_frontend/js/jquery.fileupload.js') }}" type="text/javascript"></script>
    <script src="{{ asset('new_frontend/js/jquery.fileupload-process.js') }}"></script>
    <script src="{{ asset('new_frontend/js/jquery.fileuploaded-image.js') }}" type="text/javascript"></script>
    <script src="{{ asset('new_frontend/js/jquery.fileupload-validate.js') }}"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/cloudinary-jquery-file-upload/2.5.0/cloudinary-jquery-file-upload.js"></script>

    <script>

        $(document).ready(function(){

            $("body").delegate('#time','change', function() {
                $(".image_show_upload").show();
            });

            var cloudName = 'drwrickhm';
            var unsignedUploadPreset = 'wjqyenpq';

            //video upload to cludinary
            $.cloudinary.config({
                cloud_name: cloudName
            });

            $('.cloudinary_fileupload').unsigned_cloudinary_upload(unsignedUploadPreset,
                {
                    cloud_name: cloudName,
                    tags: 'browser_uploads',
                    acceptFileTypes: /(\.|\/)(mp4|mkv|avi|flv)$/i,
                }, {
                    multiple: false
                }
            )
                .bind('cloudinarydone', function(e, data) {
                    // console.log(data.loaded: ${data.loaded},data.total: ${data.total})
                })
                .bind('fileuploadprogress', function(e, data) {
                    // console.log(fileuploadprogress data.loaded: ${data.loaded},data.total: ${data.total});
                })
                .bind('cloudinaryprogress', function(e, data) {
                    var maths = Math.round((data.loaded * 100.0) / data.total);
                    var big_html = '<div class="progress-bar" role="progressbar" aria-valuenow="'+maths+'"'+
                        'aria-valuemin="0" aria-valuemax="100" style="width:'+maths+'%">'+
                        '<span class="sr-only">'+maths+'% Complete</span>'+
                        '</div>';
                    $('.progress').html(big_html);
                    // console.log(cloudinaryprogress data.loaded: ${data.loaded},data.total: ${data.total});
                })
                .bind('cloudinarydone', function(e, data) {
                    console.log(data.result);
                    toastr.success('You are one step closer, please select the right timeslot for your content and hit the submit button to complete your upload');
                    $(".progress").hide();
                    var vid_show = '<video width="350" height="300" controls>\n' +
                        '  <source src="'+data.result.secure_url+'" type="video/mp4">\n' +
                        '</video>' ;
                    $(".gallery").html(vid_show);
                    var user_id = "<?php echo $id; ?>";
                    var channel = 'nzrm6hchjats36';

                    $("#button_submit").click(function () {
                        var time_slotss = $("#time").val();
                        var url1 = $("#form-data").attr('action');
                        var time_slot = parseInt(time_slotss);

                        if(data.result.format === "mp4" || data.result.format === "mkv" || data.result.format === "avi" || data.result.format === "flv"){
                            if(time_slot >= Math.round(data.result.duration)){
                                $.ajax({
                                    url: url1,
                                    method: "GET",
                                    data: {'time_picked' : time_slot, 'duration' : data.result.duration, 'image_url' : data.result.secure_url, 'file_name' : data.result.original_filename, 'user_id' : user_id, 'public_id' : data.result.public_id, 'channel' : channel, 'file_format' : data.result.format},
                                    success: function(result){
                                        if(result.error === 'error'){
                                            toastr.error('You are trying to upload a file of '+data.result.duration+' seconds into a '+time_slot+' seconds slot');
                                            return;
                                        }else if(result.error_number === 'error_number'){
                                            toastr.error('You have reached the maximum number of files that can be uploaded, please hit the proceed button');
                                            return;
                                        }else if(result.error_check_image === 'error_check_image'){
                                            toastr.error('You cannot upload this content more than once');
                                            return;
                                        }else if(result.success === 'success'){
                                            toastr.success('Your upload for '+time_slot+' seconds was successful');
                                            location.reload();
                                        }
                                    }
                                })

                            }else{

                                toastr.error('You are trying to upload a file of '+data.result.duration+' seconds into a '+time_slot+'seconds slot');
                            }
                        }else{
                            toastr.error('We only accept mp4, mkv, avi, flv for this upload')
                            return;
                        }

                    });

                });


            //audio upload
            $('.cloudinary_fileupload_radio').unsigned_cloudinary_upload(unsignedUploadPreset,{
                    acceptFileTypes: /(\.|\/)(mp3|wav|aiff|aac|wma|ogg)$/i
                },
                {
                    cloud_name: cloudName,
                    tags: 'browser_uploads',
                }, {
                    multiple: false
                }
            )
                .bind('cloudinarydone', function(e, data) {
                    // console.log(data.loaded: ${data.loaded},data.total: ${data.total})
                })
                .bind('fileuploadprogress', function(e, data) {
                    // console.log(fileuploadprogress data.loaded: ${data.loaded},data.total: ${data.total});
                })
                .bind('cloudinaryprogress', function(e, data) {
                    var maths = Math.round((data.loaded * 100.0) / data.total);
                    var big_html = '<div class="progress-bar" role="progressbar" aria-valuenow="'+maths+'"'+
                        'aria-valuemin="0" aria-valuemax="100" style="width:'+maths+'%">'+
                        '<span class="sr-only">'+maths+'% Complete</span>'+
                        '</div>';
                    $('.progress-radio').html(big_html);
                    // console.log(cloudinaryprogress data.loaded: ${data.loaded},data.total: ${data.total});
                })
                .bind('cloudinarydone', function(e, data) {

                    if (data.files.error){
                        alert(data.files[0].error);
                        return;
                    }

                    toastr.success('You are one step closer, please select the right timeslot for your content and hit the submit button to complete your upload');
                    $(".progress").hide();
                    var vid_show = '<audio controls>\n' +
                        '  <source src="'+data.result.secure_url+'" >\n' +
                        '</audio>' ;
                    $(".gallery_radio").html(vid_show);
                    var user_id = "<?php echo $id; ?>";
                    var channel = 'nzrm64hjatseog6';

                    $("#button_submit_radio").click(function () {
                        var time_slotss = $("#time_radio").val();
                        var url1 = $("#form-data").attr('action');
                        var time_slot = parseInt(time_slotss);
                        if(data.result.format === "mp3" || data.result.format === "wav" || data.result.format === "aiff" || data.result.format === "aac" || data.result.format === "wma" || data.result.format === "ogg"){
                            if(time_slot >= Math.round(data.result.duration)){
                                $.ajax({
                                    url: url1,
                                    method: "GET",
                                    data: {'time_picked' : time_slot, 'duration' : data.result.duration, 'image_url' : data.result.secure_url, 'file_name' : data.result.original_filename, 'user_id' : user_id, 'public_id' : data.result.public_id, 'channel' : channel, 'file_format' : data.result.format},
                                    success: function(result){
                                        if(result.error === 'error'){
                                            toastr.error('You are trying to upload a file of '+data.result.duration+' seconds into a '+time_slot+' seconds slot');
                                            return;
                                        }else if(result.error_number === 'error_number'){
                                            toastr.error('You have reached the maximum number of files that can be uploaded, please hit the proceed button');
                                            return;
                                        }else if(result.error_check_image === 'error_check_image'){
                                            toastr.error('You cannt upload this content more than once');
                                            return;
                                        }else if(result.success === 'success'){
                                            toastr.success('Your upload for '+time_slot+' seconds was successful');
                                            location.reload();
                                        }
                                    }
                                })

                            }else{

                                toastr.error('You are trying to upload a file of '+data.result.duration+' seconds into a '+time_slot+'seconds slot');
                            }
                        }else{
                            toastr.error('We only accept mp3, wav, aiff, aac, wma, ogg for this upload')
                            return;
                        }

                    });

                });


        });

    </script>
@stop
