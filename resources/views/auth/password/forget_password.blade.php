@extends('layouts.auth')

@section('page-title', trans('app.login'))

@section('content')

    <div class="form-wrap col-md-6 auth-form" id="login">
        <div style="text-align: center; margin-bottom: 25px;">
            <a href="{{ route('dashboard') }}"><img src="{{ asset('new_assets/images/logo.png') }}" alt="{{ settings('app_name') }}"></a>
        </div>

        @include('partials/messages')

        <form role="form" action="{{ route('forget_password.process') }}" method="POST" id="login-form" autocomplete="off">
            <input type="hidden" value="<?= csrf_token() ?>" name="_token">

            @if (Input::has('to'))
                <input type="hidden" value="{{ Input::get('to') }}" name="to">
            @endif

            <div class="form-group input-icon">
                <label for="username" class="sr-only">Email</label>
                <i class="fa fa-user"></i>
                <input type="email" name="email" id="username" class="form-control" placeholder="Enter your email">
            </div>
            <div class="form-group">
                <button type="submit" style="background: #00c4ca;" class="btn btn-danger btn-lg btn-block" id="btn-login">
                    Send
                </button>
            </div>

        </form>

    </div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/as/login.js') !!}
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop