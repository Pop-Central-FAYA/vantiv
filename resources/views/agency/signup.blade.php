@extends('layouts.signup_layout')

@section('stylesheets')

    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

@section('title', trans('app.agency-signup'))

<section class="content">

    <div class="div col-md-8 col-md-offset-2">

        <form method="POST" action="{{ route('agency.signup') }}" enctype="multipart/form-data">

            {{ csrf_field() }}

            <div class="row col-md-12">

                <h3>Agency SignUp</h3>
                <hr/>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="text" name="email" required class="form-control">
                        @if ($errors->has('email')) <p class="help-block" style="color: red">{{ $errors->first('email') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" required class="form-control">
                        @if ($errors->has('password')) <p class="help-block" style="color: red">{{ $errors->first('password') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" required name="password_confirmation" class="form-control">
                        @if ($errors->has('password_confrimation')) <p class="help-block" style="color: red">{{ $errors->first('password_confirmation') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" required name="first_name" class="form-control">
                        @if ($errors->has('first_name')) <p class="help-block" style="color: red">{{ $errors->first('first_name') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" required name="last_name" class="form-control">
                        @if ($errors->has('last_name')) <p class="help-block" style="color: red">{{ $errors->first('last_name') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea rows="5" required name="address" class="form-control"></textarea>
                        @if ($errors->has('address')) <p class="help-block" style="color: red">{{ $errors->first('address') }}</p> @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label for="phone">Username</label>
                        <input type="text" required name="username" class="form-control">
                        @if ($errors->has('username')) <p class="help-block" style="color: red">{{ $errors->first('username') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" required class="form-control">
                        @if ($errors->has('phone')) <p class="help-block" style="color: red">{{ $errors->first('phone') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="country_id">Country</label>
                        <select name="country_id" required class="form-control">
                            @foreach ($countries as $country)
                                <option value="{{ $country->country_code }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('country_id')) <p class="help-block" style="color: red">{{ $errors->first('country_id') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="image_url">Avatar</label>
                        <input type="file" name="image_url" required class="form-control">
                        @if ($errors->has('image_url')) <p class="help-block" style="color: red">{{ $errors->first('image_url') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="image_url">Location</label>
                        <input type="text" name="location" required class="form-control">
                        @if ($errors->has('location')) <p class="help-block" style="color: red">{{ $errors->first('location') }}</p> @endif
                    </div>

                    <div class="form-group">
                        <label for="sector_id">Sector</label>
                        <select name="sector_id" required class="form-control">
                            @foreach ($sectors as $sector)
                                <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('country_id')) <p class="help-block" style="color: red">{{ $errors->first('country_id') }}</p> @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Sign Up
                    </button>
                </div>
            </div>

        </form>

    </div>

</section>

@stop

@section('scripts')

    <script src="https://unpkg.com/flatpickr"></script>

@stop