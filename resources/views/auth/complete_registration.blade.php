@extends('layouts.old_auth')

@section('page-title', 'Complete Registration')

@section('content')

    <div class="form-wrap col-md-6 auth-form load_this_div" id="login">
        <div style="text-align: center; margin-bottom: 25px;">
            <a href=""><img src="{{ asset('new_assets/images/logo.png') }}" alt="{{ settings('app_name') }}"></a>
        </div>

        {{--@include('partials/messages')--}}

        <form role="form" id="complete_registration" autocomplete="off">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            @if (Input::has('to'))
                <input type="hidden" value="{{ Input::get('to') }}" name="to">
            @endif
            <div class="form-group input-icon">
                <label for="password" class="sr-only">First Name</label>
                <i class="fa fa-user"></i>
                <input type="text" name="firstname" id="firstname" class="form-control" placeholder="First Name">
            </div>
            <div class="form-group input-icon">
                <label for="password" class="sr-only">Last Name</label>
                <i class="fa fa-user"></i>
                <input type="text" name="lastname" id="lastname" class="form-control" placeholder="Last Name">
            </div>
            <div class="form-group input-icon">
                <label for="password" class="sr-only">Email</label>
                <i class="fa fa-user"></i>
                <input type="email" name="email" id="email" readonly value="{{ $user->email }}" class="form-control" placeholder="Email">
            </div>
            <div class="form-group input-icon">
                <label for="password" class="sr-only">New Password</label>
                <i class="fa fa-user"></i>
                <input type="password" name="password" id="password" class="form-control" placeholder="New password">
            </div>
            <div class="form-group input-icon">
                <label for="re-password" class="sr-only">Re New Password</label>
                <i class="fa fa-user"></i>
                <input type="password" name="re_password" id="re_password" class="form-control" placeholder="Re type Password">
            </div>
            <div class="form-group">
                <button type="submit" style="background: #00c4ca;" class="btn btn-danger btn-lg btn-block button_create" id="btn-login">
                    Complete Registration
                </button>
            </div>

        </form>

    </div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/as/login.js') !!}
    <script>
        <?php echo "var user =".json_encode($user).";\n"; ?>
        $(document).ready(function () {
            $("#complete_registration").on('submit', function(e) {
                event.preventDefault(e);
                $('.load_this_div').css({
                    opacity : 0.2
                });
                $('a').css('pointer-events','none');
                $('.button_create').hide();
                var firstname = $("#firstname").val();
                var lastname = $("#lastname").val();
                var password = $("#password").val();
                var re_password = $("#re_password").val();
                validate(firstname, lastname, password, re_password);
                var weHaveSuccess = false;
                var formdata = $("#complete_registration").serialize();
                $.ajax({
                    cache: false,
                    type: "POST",
                    url: '/user/complete-account/store/'+user.id,
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
                        msg = "Processing Invite, please wait"
                        toastr.info(msg, null, toastr_options)
                    },
                    success: function (data) {
                        console.log(data);
                        toastr.clear();
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            location.href = '/login';
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

            function validate(firstname, lastname, password, re_password) {
                if( firstname === ""){
                    toastr.error('first name field cannot be empty');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if( lastname === ""){
                    toastr.error('last name field cannot be empty');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if( password === ""){
                    toastr.error('password field cannot be empty');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if( re_password === ""){
                    toastr.error('re password field cannot be empty');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
            }
        });
    </script>
@stop
