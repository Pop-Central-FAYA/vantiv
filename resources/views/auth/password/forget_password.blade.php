@extends('layouts.auth')

@section('title')
    <title>FAYA.</title>
@stop

@section('content')

    <form role="form" action="{{ route('forget_password.process') }}" method="POST" id="login-form" autocomplete="off">
        <input type="hidden" value="<?= csrf_token() ?>" name="_token">
        <div class="align_center mb4">
            <h2 class="m-b">Reset Your Password</h2>
            <p class="mb4">Enter your email and we'd send you a link to reset your password</p>

            <br>

            <div class="auth_input">
                <input type="email" name="email" id="username" placeholder="Email">
            </div>
        </div>

        <input type="submit" value="Reset my Password" class= "btn full">
    </form>

    <p class="align_center pt">Have an account? <a href="{{ route('login') }}" class="">Sign In</a></p>

@stop

@section('scripts')
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Auth\LoginRequest', '#login-form') !!}
@stop

