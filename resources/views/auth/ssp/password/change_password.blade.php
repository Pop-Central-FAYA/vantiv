@extends('layouts.old_auth')

@section('title')
    <title> Torch | Reset Password </title>
@stop

@section('content')

    <div class="form-wrap col-md-6 auth-form" id="login">
        <div style="text-align: center; margin-bottom: 25px;">
            <a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt="{{ settings('app_name') }}"></a>
        </div>

        <form role="form" action="{{ route('change_password.process', ['user_id' => $user->id]) }}" method="POST" id="password-change-form" autocomplete="off">
            <input type="hidden" value="<?= csrf_token() ?>" name="_token">

            @if (Input::has('to'))
                <input type="hidden" value="{{ Input::get('to') }}" name="to">
            @endif

            <div class="form-group input-icon{{ $errors->has('password') ? ' has-error' : '' }}">
                <label for="password" class="sr-only">New Password</label>
                <i class="fa fa-user"></i>
                <input type="password" name="password" id="password" class="form-control" placeholder="New password">
                @if($errors->has('password'))
                    <span class="help-block">
                        <strong>
                            {{ $errors->first('password') }}
                        </strong>
                    </span>
                @endif
            </div>
            <div class="form-group input-icon{{ $errors->has('re_password') ? ' has-error' : '' }}">
                <label for="re-password" class="sr-only">Re New Password</label>
                <i class="fa fa-user"></i>
                <input type="password" name="re_password" id="re_password" class="form-control" placeholder="Re type Password">
                @if($errors->has('re_password'))
                    <span class="help-block">
                        <strong>
                            {{ $errors->first('re_password') }}
                        </strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <button type="submit" style="background: #00c4ca;" class="btn btn-danger btn-lg btn-block" id="btn-login">
                    Change Password
                </button>
            </div>

        </form>

    </div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/as/login.js') !!}
@stop
