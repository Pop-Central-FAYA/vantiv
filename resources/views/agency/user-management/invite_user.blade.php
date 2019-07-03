@extends('dsp_layouts.faya_app')

@section('title')
    <title>FAYA | Invite User</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
    @if(Session::get('broadcaster_id'))
        @include('partials.new-frontend.broadcaster.header')
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    @else
        @include('partials.new-frontend.agency.header')
    @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Invite User(s)</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_this_div">

            <div class="margin_center col_7 clearfix pt4 create_fields">

                <form id="invite_user_form">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="create_gauge">
                        <div class=""></div>
                    </div>
                    <div class="clearfix mb">
                        <div class="input_wrap column col_12">
                            <label class="small_faint">Email(s)</label>
                            <input style="display: inline-block;width: 100%;" type="text" name="emails" id="emails" placeholder="Enter email addresses. Add commas for multiples">
                        </div>

                    </div>

                    <div class="input_wrap">
                        <label class="small_faint">Roles</label>

                        <div class="select_wrap">
                            <select class="js-example-basic-multiple" id="roles" name="roles[]" multiple="multiple" >
                                <option value=""></option>
                                <option value="boss"></option>
                                @foreach($roles as $role)
                                    <option value="{{ $role['role'] }}">
                                        {{ $role['label'] }}
                                    </option>
                                   
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->companies->count() > 1)
                        <div class="input_wrap">
                        <label class="small_faint">Company</label>

                        <div class="select_wrap">
                            <select class="js-example-basic-multiple" id="companies" name="companies[]" multiple="multiple" >
                                <option value=""></option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="mb4 clearfix pt4 mb4">

                        <div class="column col_12 align_right">
                            <button type="submit"  class="btn uppercased button_create">Invite User(s) <span class=""></span></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>
        <!-- main frame end -->


    </div>
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            //select for target audience
            $('.js-example-basic-multiple').select2();
            //placeholder for target audienct
            $('#roles').select2({
                placeholder: "Please select Role(s)"
            });

            $('#companies').select2({
                placeholder: "Please select Company(s)"
            });

            $("#invite_user_form").on('submit', function(e) {
                event.preventDefault(e);
                $('.load_this_div').css({
                    opacity : 0.2
                });
                $('a').css('pointer-events','none');
                $('.button_create').hide();
                var email = $("#emails").val();
                var roles = $("select#roles").val();
                var companies = $("select#companies").val();
                if( email === ""){
                    toastr.error('email field cannot be empty');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if(roles === null){
                    toastr.error('role field cannot be empty');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                var email_list = email.split(",");
                var trimed_email_list = [];
                $.each(email_list, function (index, value) {
                   var cleaned_email = value.trim();
                   trimed_email_list.push(cleaned_email);
                   if(validateEmail(cleaned_email) === false){
                       toastr.error('please ensure the email(s) is / are valid');
                       $('.load_this_div').css({
                           opacity : 1
                       });
                       $('a').css('pointer-events','');
                       $('.button_create').show();
                        return;
                   }
                });
                var weHaveSuccess = false;
                var form_request = {
                    "_token": "{{ csrf_token() }}",
                    'email' : trimed_email_list,
                    'roles' : roles,
                    'companies' : companies,
                };
           
                $.ajax({
                    cache: false,
                    type: "POST",
                    url: '/user/invite/store',
                    dataType: 'json',
                    data: form_request,
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
                        msg = "Processing Invite, please wait"
                        toastr.info(msg, null, toastr_options)
                    },
                    success: function (data) {
                        console.log(data);
                        toastr.clear();
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            location.href = '';
                        } else {
                            toastr.error(data.message);
                            $('.load_this_div').css({
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
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else if(xhr.status === 503){
                            toastr.error('The request took longer than expected, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else{
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }
                    }
                })
            });

            function validateEmail(email) {
                var checking_email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return checking_email.test(String(email).toLowerCase());
            }
        });
    </script>
@stop

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop


