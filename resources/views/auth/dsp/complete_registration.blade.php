@extends('dsp_layouts.old_auth')

@section('page-title', 'Complete Registration')

@section('content')

    <div class="form-wrap col-md-6 auth-form load_this_div" id="login">
        <div style="text-align: center; margin-bottom: 25px;">
            <a href=""><svg width="119" height="46" xmlns="http://www.w3.org/2000/svg" xmlns:svg="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">'.
                     <!-- Created with SVG-edit - http://svg-edit.googlecode.com/ -->
    <defs>
     <symbol id="svg_2" viewBox="0 0 553.34 216.48" xmlns="http://www.w3.org/2000/svg">
      <title>Vantage+Torch_Logos</title>
      <polygon points="109.8 126.06 143.69 33.21 182.71 33.21 123.02 154.83 109.8 126.06" fill="#64c4ce" id="Fill-1"/>
      <polygon points="153.81 33.21 182.71 33.21 109.17 183.1 101.38 140.09 153.81 33.21" fill="#64c4ce" id="Fill-2"/>
      <polygon class="cls-2" points="76.86 33.21 122.76 127.08 168.66 33.21 76.86 33.21" fill="#575758" id="Fill-3"/>
      <polygon points="123.6 153.64 109.13 183.06 35.66 33.14 64.58 33.21 123.6 153.64" fill="#4eaeaf" id="Fill-4"/>
      <text id="svg_8" font-family="LemonMilk" fill="#1d1d1d" font-size="91.26px" x="151.36" y="154.83">AN
       <tspan id="svg_9" x="287.13" y="154.83">T</tspan>
       <tspan id="svg_10" x="329.38" y="154.83">A</tspan>
       <tspan id="svg_11" x="391.8" y="154.83">GE</tspan></text>
     </symbol>
    </defs>
    <g>
     <title>Layer 1</title>
     <use id="svg_3" xlink:href="#svg_2" transform="matrix(1.1134798870399125,0,0,1.0357963171947533,-11.671895890043723,-23.101256644532413) " y="22.6777" x="2.49327"/>
     <g id="svg_4"/>
    </g></svg></a>
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
