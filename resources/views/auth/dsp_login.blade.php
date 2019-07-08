@extends('dsp_layouts.auth')

@section('title')
    <title>FAYA.</title>
@stop

@section('content')

    <form role="form" action="{{ route('post.login') }}" method="POST" id="login-form" autocomplete="off">

        <input type="hidden" value="<?= csrf_token() ?>" name="_token">

        <div class="align_center m-b">
            <h2 class="m-b">Welcome To Vantage</h2>
            <p class="mb4">Enter email and password to login</p>

            <br>
            <div class="auth_input">
                <input type="email" name="email" equired=“required” id="username" placeholder="Email">
                <input type="password" name="password" equired=“required” id="password" placeholder="Password">
            </div>
        </div>

        <div class="clearfix mb4 remember_box">
            <div class="column col_6">
                <input type="checkbox" id="persist">
                <label for="persist">Remember me</label>
            </div>

            <div class="column col_6 align_right">
                <a href="{{ route('dsp.password.forgot') }}">Forgot Password?</a>
            </div>
        </div>

        <input type="submit" value="Log into your account" class= "btn full">


    </form>
@stop

