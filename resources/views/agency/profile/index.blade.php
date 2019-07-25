@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Profile Management</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @include('partials.new-frontend.agency.header')
        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Profile Management</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <!-- main frame end -->

            <div class="margin_center col_8 clearfix pt4 create_fields">
                <form action="{{ route('profile.update.details') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="column col_2 align_center">
                        <!-- image upload -->
                        <div class="file_select profile_hold m-b margin_center">
                            <input type="file" id="file" name="image_url" class="upload_profile">
                            <img src="{{ asset($user->avatar ? $user->avatar : '') }}" class="target">
                        </div>

                        <p class="color_base weight_medium">Upload Photo</p>
                    </div>

                    <!-- edit profle fields -->
                    <div class="column col_10 padd">
                        <h3 class="weight_medium mb">Basic Info</h3>

                        <div class="clearfix">
                            <div class="input_wrap column col_6">
                                <label class="small_faint uppercased weight_medium">First Name</label>
                                <input type="text" type="text" required id="first_name"
                                       name="first_name" placeholder="@lang('app.first_name')" value="{{ $user->firstname }}" >
                            </div>

                            <div class="input_wrap column col_6">
                                <label class="small_faint uppercased weight_medium">Last Name</label>
                                <input type="text" required id="last_name"
                                       name="last_name" placeholder="@lang('app.last_name')" value="{{ $user->lastname }}" >
                            </div>
                        </div>

                        <div class="clearfix">
                            <div class="input_wrap column col_6">
                                <label class="small_faint uppercased weight_medium">Email</label>
                                <input type="email" name="email" required value="{{ $user->email }}" readonly placeholder="me@example.com">
                            </div>

                            <div class="input_wrap column col_6">
                                <label class="small_faint uppercased weight_medium">Mobile Number</label>
                                <input type="text" required name="phone" value="{{ $user->phone_number }}" placeholder="+234** **** ****">
                            </div>
                        </div>

                        <div class="clearfix">
                            <div class="input_wrap column col_6">
                                <label class="small_faint uppercased weight_medium">Company</label>
                                <input type="text" type="text" required id="company"
                                       name="company" readonly placeholder="@lang('app.username')" value="{{ $user->companies->first()->name }}" >
                            </div>
                            <div class="input_wrap column col_6">
                                <label class="small_faint uppercased weight_medium">Address</label>
                                <input type="text" required name="address" value="{{ $user->address }}" placeholder="Address">
                            </div>

                        </div>
                        <h3 class="weight_medium">Password</h3>
                        <p class="small_font mb ital light_font">Leave blank if you do not want to change.</p>
                        <div class="clearfix mb4">
                            <div class="input_wrap column col_6{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label class="small_faint uppercased weight_medium">Password</label>
                                <input type="password" autocomplete="off" name="password" placeholder="****">
                                @if($errors->has('password'))
                                    <strong>
                                        <span class="error-message">
                                            {{ $errors->first('password') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                            <div class="input_wrap column col_6{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label class="small_faint uppercased weight_medium">Confirm Password</label>
                                <input type="password" autocomplete="off" name="password_confirmation" id="password_confirmation" placeholder="****">

                                @if($errors->has('password_confirmation'))
                                    <strong>
                                        <span class="error-message">
                                            {{ $errors->first('password_confirmation') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                        @if(Auth::user()->hasPermissionTo('view.profile'))
                            <div class="mb4 align_right">
                                <input type="submit" value="Save Profile" class="btn uppercased mb4">
                            </div>
                        @endif
                    </div>
                    <!-- end -->
                </form>

            </div>
@stop

@section('styles')
    <style>
        .error-message {
            color: red;
            font-size: 10px;
            font-style: italic;
        }
    </style>
@stop
