@extends('layouts.ssp.layout')

@section('title')
    <title> Torch | Walk-Ins </title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Walk-Ins</h2>
            </div>
            @if(Auth::user()->companies->count() == 1 || Auth::user()->hasPermissionTo('create.client'))
                <div class="column col_6 align_right">
                    <a href="#new_client" class="btn modal_click">New Walk-In</a>
                </div>
            @endif
        </div>

        <!-- main stats -->
        <!-- main stats -->
        <div class="the_stats the_frame clearfix mb4">
            <div class="active column col_4">
                <span class="small_faint uppercased">Total Walk-Ins</span>
                <h3>{{ $walkin_count }}</h3>
            </div>

            <div class="column col_4">
                <span class="small_faint uppercased">Active Walk-Ins</span>
                <h3>{{ $walkin_count }}</h3>
            </div>

            <div class="column col_4">
                <span class="small_faint uppercased">Inactive Walk-Ins</span>
                <h3>0</h3>
            </div>
        </div>

        <div class="similar_table pt3">
            <!-- table header -->
            <div class="_table_header clearfix m-b">
                <span class="small_faint block_disp padd column col_3">Basic Info</span>
                <span class="small_faint block_disp column col_1">Brands</span>
                <span class="small_faint block_disp column col_2">Total Spend</span>
                <span class="small_faint block_disp column col_2">Active Campaigns</span>
                <span class="small_faint block_disp column col_2">Inactive Campaigns</span>
                @if(Auth::user()->companies->count() > 1)
                    <span class="small_faint block_disp column col_1">Stations</span>
                @endif
                <span class="block_disp column col_1 color_trans">.</span>
            </div>

            <!-- table item -->
            @foreach($clients as $client)
                <div class="_table_item the_frame clearfix">
                    <div class="padd column col_3">
                        <span class="client_ava"><img src="{{ $client->company_logo ? asset($client->company_logo) : '' }}"></span>
                        <p>{{ $client->walkins_company_name }}</p>
                        <span class="small_faint">Added {{ date('M j, Y', strtotime($client->time_created)) }}</span>
                    </div>
                    <div class="column col_1">{{ $client->total_brand }}</div>
                    <div class="column col_2">&#8358; {{ number_format($client->total_spent_so_far, 2) }}</div>
                    <div class="column col_2">{{ $client->active_campaigns }}</div>
                    <div class="column col_2">{{ $client->inactive_campaigns }}</div>
                    @if(Auth::user()->companies->count() > 1)
                        <div class="column col_1">{{ $client->publishers }}</div>
                    @endif
                    <div class="column col_1">
                        <div class="column col_9"></div>
                        <div class="column col_3">
                            <!-- more links -->
                            <div class="list_more">
                                <span class="more_icon"></span>

                                <div class="more_more">
                                    @if(Auth::user()->hasPermissionTo('view.client'))
                                        <a href="{{ route('walkins.details', ['id' => $client->client_id]) }}">Details</a>
                                    @endif
                                    @if(Auth::user()->companies->count() == 1 && $client->walkin_creator == Auth::user()->companies->first()->id)
                                        @if(Auth::user()->hasPermissionTo('update.client'))
                                            <a href="#edit_client{{ $client->client_id }}" class="modal_click">Edit</a>
                                        @endif
                                    @endif
                                    {{--<a href="" class="color_red">Delete</a>--}}
                                </div>
                            </div>

                        </div>
                    </div>
                    
                </div>
        @endforeach
        <!-- table item end -->
        </div>

    </div>
    <!-- new client modal -->
    @if(Auth::user()->hasPermissionTo('create.client'))
        <div class="modal_contain" style="width: 800px; overflow:scroll;" id="new_client">
        <h2 class="sub_header mb4">New Walk-In</h2>
        <div class="progress">

        </div><br>
        <form action="{{ route('walkins.store') }}" method="POST" enctype="multipart/form-data" id="create_walkIns">
            {{ csrf_field() }}
            <div class="clearfix" id="upload_container">
                <div class="input_wrap column col_7{{ $errors->has('company_name') ? ' has-error' : '' }}">
                    <label class="small_faint">Company Name</label>
                    <input type="text" name="company_name" value="" required  placeholder="e.g Coca Cola">
                    @if($errors->has('company_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('company_name') }}</span>
                        </strong>
                    @endif
                </div>
                <div class='column col_5 file_select company_upload_button align_center pt3{{ $errors->has('company_logo') ? ' has-error' : '' }}'>
                    <input type="file" required class="company_logo" />
                    <span class="small_faint block_disp mb3">Company Logo</span>
                    @if($errors->has('company_logo'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('company_logo') }}</span>
                        </strong>
                    @endif
                </div>
                <input type="hidden" name="company_logo" required class="company_logo_url">
                <div class='column col_5 company_uploaded_image align_center pt3' style="display: none;">

                </div>
                <div class="upload_new" style="font-size: 12px; padding-left: 500px; display: none;">
                    <input class="company_logo upload_new" name="company_logo" type="file">
                </div>
            </div>

            <div class="clearfix">
                <div class="input_wrap column col_7{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                    <label class="small_faint">Walk-In Brand</label>
                    <input type="text" name="brand_name" required class="brands_name" id="brands_name" value=""  placeholder="e.g Coca Cola">
                    @if($errors->has('brand_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('brand_name') }}</span>
                        </strong>
                    @endif
                </div>
                <div class='column col_5 file_select align_center brand_upload_button pt3{{ $errors->has('image_url') ? ' has-error' : '' }}'>
                    <input type="file" required class="brand_logo" />
                    <span class="small_faint block_disp mb3">Brand Logo</span>
                    @if($errors->has('image_url'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('image_url') }}</span>
                        </strong>
                    @endif
                </div>
                <input type="hidden" name="image_url" required id="brand_logo_url">
                <div class='column col_5 brand_uploaded_image align_center pt3' style="display: none;">

                </div>
                <div class="upload_new_brand" style="font-size: 12px; padding-left: 500px; display: none;">
                    <input class="brand_logo upload_new_brand" name="brand_logo" type="file">
                </div>
            </div>

            <input type="hidden" name="broadcaster_id" value="{{ null }}">
            <input type="hidden" name="client_type_id" value="2">
            <input type="hidden" name="agency_id" value="">

            <div class="input_wrap{{ $errors->has('email') ? ' has-error' : '' }}">
                <label class="small_faint">Email</label>
                <input type="email" required name="email" placeholder="name@example.com">
                @if($errors->has('email'))
                    <strong>
                        <span class="error-block" style="color: red;">{{ $errors->first('email') }}</span>
                    </strong>
                @endif
            </div>

            <div class="clearfix mb">
                <div class="input_wrap col_6 column{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label class="small_faint">First Name</label>
                    <input type="text" required  name="first_name" placeholder="Enter First Name">
                    @if($errors->has('first_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('first_name') }}</span>
                        </strong>
                    @endif
                </div>

                <div class="input_wrap col_6 column{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <label class="small_faint">Last Name</label>
                    <input type="text" required name="last_name" placeholder="Enter Last Name">
                    @if($errors->has('last_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('last_name') }}</span>
                        </strong>
                    @endif
                </div>
            </div>

            <div class="input_wrap{{ $errors->has('phone') ? ' has-error' : '' }}">
                <label class="small_faint">Phone Number</label>
                <input type="text" required name="phone" id="phone_number_verify" placeholder="234** **** ****">
                @if($errors->has('phone'))
                    <strong>
                        <span class="error-block" style="color: red;">{{ $errors->first('phone') }}</span>
                    </strong>
                @endif
            </div>

            <div class="input_wrap{{ $errors->has('address') ? ' has-error' : '' }}">
                <label class="small_faint">Address</label>
                <input type="text" required name="address" placeholder="Enter Address">
                @if($errors->has('address'))
                    <strong>
                        <span class="error-block" style="color: red;">{{ $errors->first('address') }}</span>
                    </strong>
                @endif
            </div>

            <div class="input_wrap">
                <label class="small_faint">Industry</label>

                <div class="select_wrap">
                    <select name="industry" required id="industry">
                        <option value="">Select Industry</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry->sector_code }}">{{ $industry->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input_wrap">
                <label class="small_faint">Sub Industry</label>

                <div class="select_wrap">
                    <select name="sub_industry" id="sub_industry">

                    </select>
                </div>
            </div>

            <div class="align_right">
                <input type="submit" disabled id="submit_walkins" value="Create Walk-In" class="btn uppercased update button_create">
            </div>

        </form>
    </div>
    @endif

    {{--modal for editing a client--}}
    @if(Auth::user()->hasPermissionTo('update.client'))
        @foreach($clients as $client)
        <div class="modal_contain" id="edit_client{{ $client->client_id }}">
            <h2 class="sub_header mb4">Edit Walk-In : {{ $client->walkins_company_name }}</h2>
            <div class="progress">

            </div><br>
            <form action="{{ route('walkins.update', ['client_id' => $client->client_id]) }}" method="POST" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="clearfix">
                    <div class="input_wrap column col_7{{ $errors->has('company_name') ? ' has-error' : '' }}">
                        <label class="small_faint">Company Name</label>
                        <input type="text" required name="company_name" value="{{ $client->walkins_company_name }}"  placeholder="e.g Coca Cola">
                        @if($errors->has('company_name'))
                            <strong>
                                <span class="error-block" style="color: red;">{{ $errors->first('company_name') }}</span>
                            </strong>
                        @endif
                    </div>
                    <div class='column col_5 company_upload_button align_center pt3{{ $errors->has('company_logo') ? ' has-error' : '' }}'>
                        <img src="{{ asset($client->company_logo) }}" style="width: 100px; height: 100px; padding: 0 0 11px; margin-right: auto; margin-left: auto; margin-top: -65px;">
                    </div>
                    <input type="hidden" name="company_logo" required class="company_logo_url">
                    <div class='column col_5 company_uploaded_image align_center pt3' style="display: none;">

                    </div>
                    <div class="upload_new" style="font-size: 12px; padding-left: 250px; padding-bottom: 10px; ">
                        <input class="company_logo upload_new" type="file">
                    </div>
                </div>

                <div class="input_wrap{{ $errors->has('email') ? ' has-error' : '' }}">
                    <label class="small_faint">Email</label>
                    <input type="email" required name="email" readonly value="{{ $client->email }}" placeholder="name@example.com">
                    @if($errors->has('email'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('email') }}</span>
                        </strong>
                    @endif
                </div>

                <div class="clearfix mb">
                    <div class="input_wrap col_6 column{{ $errors->has('first_name') ? ' has-error' : '' }}">
                        <label class="small_faint">First Name</label>
                        <input type="text" required name="first_name" value="{{ $client->firstname }}" placeholder="Enter First Name">
                        @if($errors->has('first_name'))
                            <strong>
                                <span class="error-block" style="color: red;">{{ $errors->first('first_name') }}</span>
                            </strong>
                        @endif
                    </div>

                    <div class="input_wrap col_6 column{{ $errors->has('last_name') ? ' has-error' : '' }}">
                        <label class="small_faint">Last Name</label>
                        <input type="text" required name="last_name" value="{{ $client->lastname }}" placeholder="Enter Last Name">
                        @if($errors->has('last_name'))
                            <strong>
                                <span class="error-block" style="color: red;">{{ $errors->first('last_name') }}</span>
                            </strong>
                        @endif
                    </div>
                </div>

                <div class="input_wrap{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <label class="small_faint">Phone Number</label>
                    <input type="text" required name="phone" value="{{ $client->phone_number }}" placeholder="*** **** ****">
                    @if($errors->has('phone'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('phone') }}</span>
                        </strong>
                    @endif
                </div>

                <div class="input_wrap{{ $errors->has('address') ? ' has-error' : '' }}">
                    <label class="small_faint">Address</label>
                    <input type="text" required name="address" value="{{ $client->location }}" placeholder="Enter Address">
                    @if($errors->has('address'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('address') }}</span>
                        </strong>
                    @endif
                </div>

                <div class="align_right">
                    <input type="submit" value="Update Client" class="btn uppercased update">
                </div>

            </form>
        </div>
    @endforeach
    @endif
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            // $("#state").change(function() {
            $('#industry').on('change', function(e){
                $(".modal_contain").css({
                    opacity: 0.2
                });
                $('.update').attr("disabled", true);
                var industry = $("#industry").val();
                var url = '/walk-in/brand';
                $.ajax({
                    url: url,
                    method: "GET",
                    data: {industry: industry},
                    success: function(data){
                        if(data.error === 'error'){
                            $(".changing").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);
                        }else{
                            $(".modal_contain").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);

                            $('#sub_industry').empty();

                            $('#sub_industry').append(' Please choose one');

                            $.each(data, function(index, title){
                                $("#sub_industry").append('' + '<option value ="'+ title.sub_sector_code + '"  > ' + title.name + '  </option>');
                            });
                        }

                    }
                });
            });

            $(".brands_name").keyup(function () {
                var brand_name = $("input#brands_name").val();
                var url = '/check-brand-existence';
                $.ajax({
                    url : url,
                    method : 'GET',
                    data: {brand_name: brand_name},
                    success: function (data) {
                        if(data === 'already_exists'){
                            toastr.info('This brand already exists on our platform, by continuing this process means you are aware of its existence');
                        }
                    }
                })
            });

            $(".company_logo").on('change', function () {
                var files = this.files;
                uploadImages(files, 'client-images/', ".company_uploaded_image", ".upload_new", ".company_upload_button", ".company_logo_url", '.progress')
            });

            $(".brand_logo").on('change', function () {
                var files = this.files;
                uploadImages(files, 'brand-images/', ".brand_uploaded_image", ".upload_new_brand", ".brand_upload_button", "#brand_logo_url", '.progress')
            });

            function uploadImages(files, folder, uploaded_image_class, upload_new_image_class, upload_button, url_input_id, progress_bar_class){
                var url = '/presigned-url';
                for (var file, i = 0; i < files.length; i++) {
                    file = files[i];
                    if(file.name && !file.name.match(/.(gif|jpeg|jpg|png|svg)$/i)) {
                        toastr.error('Only Images are allowed');
                        return;
                    }
                    $.ajax({
                        url : url,
                        type : "GET",
                        cache : false,
                        data: {filename : file.name, folder: folder},
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
                                            $(progress_bar_class).html(big_html);
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
                                    toastr.success('Your upload was successful');
                                    var uploadedUrl = 'https:'+data.split('?')[0].substr(6);
                                    $(url_input_id).val(uploadedUrl);
                                    $(upload_button).hide();
                                    $(uploaded_image_class).show();
                                    $(upload_new_image_class).show();
                                    $(uploaded_image_class).html('<img src="'+uploadedUrl+'" style="width: 100px;\n' +
                                        '    height: 100px;\n' +
                                        '    padding: 0 0 11px;\n' +
                                        '    margin-right: auto;\n' +
                                        '    margin-left: auto;\n' +
                                        '    margin-top: -27px; " >');
                                })
                                .fail(function(){
                                    toastr.error('An error occurred, please try again ');
                                })
                        }
                    })
                }
            }

            //store_walkins
            $("#create_walkIns").on('submit', function(e) {
                event.preventDefault(e);
                var phone_number = $("#phone_number_verify").val();
                if(phone_number.length != 13){
                    toastr.error('Phone number invalid, please make sure you enter a valid number with the specified format as the placeholder')
                    return;
                }
                $('.required').each(function(){
                    if( $(this).val() == "" ){
                        toastr.error('All fields are required');
                        return;
                    }
                });
                $('.modal_contain').css({
                    opacity : 0.2
                });
                $('a').css('pointer-events','none');
                $('.button_create').hide();
                var formdata = $("#create_walkIns").serialize();
                var weHaveSuccess = false;
                $.ajax({
                    cache: false,
                    type: "POST",
                    url: '/walk-in/store',
                    dataType: 'json',
                    data: formdata,
                    beforeSend: function(data) {
                        // run toast showing progress
                        toastr_options = {
                            "progressBar": true,
                            // "showDuration": "300",
                            "preventDuplicates": true,
                            "tapToDismiss": false,
                            "hideDuration": "1",
                            "timeOut": "300000000"
                        };
                        msg = "Processing request, please wait"
                        toastr.info(msg, null, toastr_options)
                    },
                    success: function (data) {
                        toastr.clear();
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            location.href = '/walk-in';
                        } else {
                            toastr.error(data.message);
                            $('.modal_contain').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }
                        weHaveSuccess = true;
                    },
                    error : function (xhr) {
                        toastr.clear();
                        if(xhr.status === 500){
                            toastr.error('An unknown error has occurred, please try again');
                            $('.modal_contain').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else if(xhr.status === 503){
                            toastr.error('The request took longer than expected, please try again');
                            $('.modal_contain').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else{
                            toastr.error('An unknown error has occurred, please try again');
                            $('.modal_contain').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }
                    }
                })
            });

            //make sure all required class are validated
            function checkRequiredFields() {
                $('.required').each(function(){
                    if( $(this).val() == "" ){
                        toastr.error('All fields are required');
                        return;
                    }
                });
            };

        });

    </script>
@stop
@section('styles')
    <style>
        .modal_contain {
            background: #fff;
            width: 1000px !important;
            overflow: auto !important;
        }
    </style>
@stop
