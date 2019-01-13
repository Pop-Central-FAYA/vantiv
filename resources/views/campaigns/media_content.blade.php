@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Media Content Management</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @if(Session::get('broadcaster_id'))
        @include('partials.new-frontend.broadcaster.header')
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    @else
        @include('partials.new-frontend.agency.header')
    @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Manage Media Content</h2>
            </div>
        </div>


        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color">

            <div class="margin_center col_9 clearfix pt4 create_fields">

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

                @if((Session::get('campaign_information')) != null)
                    @if(Session::get('broadcaster_id'))
                        @include('campaigns.includes.broadcaster_media')
                    @else
                        @include('campaigns.includes.agency_media')
                    @endif
                @endif

                <div class="mb4 clearfix pt4 mb4">
                    <div class="column col_6">
                        <a href="{{ route('campaign.advert_slot', ['id' => $id]) }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                    </div>
                    @if(Session::get('agency_id'))
                        <div class="column col_6 align_right">
                            <a href="{{ route('campaign.broadcaster_select', ['id' => $id ]) }}" class="btn uppercased _proceed tv_campaign_proceed" id="proceed_button">Proceed <span class=""></span></a>
                        </div>
                    @else
                        <div class="column col_6 align_right">
                            <a href="{{ route('campaign.adslot_preprocess', ['id' => $id ]) }}" class="btn uppercased _proceed tv_campaign_proceed" id="proceed_button">Proceed <span class=""></span></a>
                        </div>
                    @endif
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
            //get the duraion of the file
            //register canplaythrough event to #audio element to can get duration
            var f_duration =0;  //store duration
            document.getElementById('audio').addEventListener('canplaythrough', function(e){
                //add duration in the input field #f_du
                f_duration = Math.round(e.currentTarget.duration);
                document.getElementById('file_duration').value = f_duration;
                URL.revokeObjectURL(obUrl);
            });

            //when select a file, create an ObjectURL with the file and add it in the #audio element
            var obUrl;
            document.getElementById('file_upload').addEventListener('change', function(e){
                var file = e.currentTarget.files[0];
                //check file extension for audio/video type
                if(file.name.match(/\.(avi|mp3|mp4|mpeg|ogg)$/i)){
                    obUrl = URL.createObjectURL(file);
                    document.getElementById('audio').setAttribute('src', obUrl);
                }
            });

            //Uploading video content for tv
            $(".tv_content").on('change', function () {
                var url = '/presigned-url';
                for (var file, i = 0; i < this.files.length; i++) {
                    file = this.files[i];
                    if(file.name && !file.name.match(/.(mp4|mkv|avi|flv|vob)$/i)) {
                        toastr.error('Invalid Video format');
                        return;
                    }
                    var splitedName = file.name.split(".");
                    var video_format = splitedName[splitedName.length - 1];
                    var channel = "<?php echo $tv->id ?>";
                    $(".tv_campaign_proceed").prop('disabled', true);
                    processUploads(file, 'campaign-tv-contents', channel, url, video_format);
                }
            });

            function processUploads (file, folder_name, channel, url, video_format) {
                $.ajax({
                    url : url,
                    type : "GET",
                    cache : false,
                    data: {filename : file.name, folder: 'campaign-tv-contents/'},
                    success: function (data) {
                        $.ajax({
                            xhr: function() {
                                var xhr = new window.XMLHttpRequest();
                                xhr.upload.addEventListener("progress", function(evt) {
                                    if (evt.lengthComputable) {
                                        var percentComplete = evt.loaded / evt.total;
                                        percentComplete = parseInt(percentComplete * 100);
                                        var big_html = '<div class="progress-bar" role="progressbar" aria-valuenow="'+percentComplete+'"'+
                                            'aria-valuemin="0" aria-valuemax="100" style="width:'+percentComplete+'%">'+
                                            '<span class="sr-only">'+percentComplete+'% Complete</span>'+
                                            '</div>';
                                        $('.progress').html(big_html);
                                        if (percentComplete === 100) {
                                            $('.progress').fadeOut(1000);
                                        }

                                    }
                                }, false);

                                return xhr;
                            },
                            url : data,
                            type : "PUT",
                            data : file,
                            dataType : "text",
                            cache : false,
                            contentType : file.type,
                            processData : false,
                        })
                            .done(function(){
                                toastr.success('Your upload was successful, please select the right time slot for your content and click on the submit button to complete your upload');
                                var uploadedUrl = 'https:'+data.split('?')[0].substr(6);
                                var vid_show = '<video width="350" height="300" controls>\n' +
                                    '  <source src="'+uploadedUrl+'" type="video/mp4">\n' +
                                    '</video>' ;
                                $(".gallery").html(vid_show);
                                var user_id = "<?php echo $id; ?>";
                                var channel = 'nzrm6hchjats36';
                                $("#proceed_button").hide();
                                $("#button_submit").click(function () {
                                    var time_slots_string = $("#time").val();
                                    var file_duration = $(".file_duration").val();
                                    var url1 = $("#form-data").attr('action');
                                    var time_slot = parseInt(time_slots_string);
                                    if(time_slot >= file_duration){
                                        $.ajax({
                                            url: url1,
                                            method: "GET",
                                            data: {'time_picked' : time_slot, 'duration' : file_duration, 'file_url' : uploadedUrl, 'file_name' : file.name, 'user_id' : user_id, 'channel' : channel, 'file_format' : video_format},
                                            success: function(result){
                                                //console.log(result);
                                                if(result.error === 'error'){
                                                    toastr.error('You are trying to upload a file of '+file_duration+' seconds into a '+time_slot+' seconds slot');
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

                                        toastr.error('You are trying to upload a file of '+file_duration+' seconds into a '+time_slot+'seconds slot');
                                    }

                                });
                            })
                            .fail(function(){
                                toastr.error('An error occurred, please try again ');
                                location.reload();
                            })
                    }
                })
            }

        });

    </script>
@stop

