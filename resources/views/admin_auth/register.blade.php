@extends('layouts.signup_layout')

@section('stylesheets')

    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/parsley.css') }}">

@endsection

@section('content')

@section('title', trans('New Admin'))

<section class="content">

    <div class="div col-md-8 col-md-offset-2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Admin Register</h3>
            </div>
            <div class="panel-body">

                <form method="POST" action="{{ route('admin.post') }}" enctype="multipart/form-data" data-parsley-validate="">

                    {{ csrf_field() }}

                    <div class="row col-md-12">

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" class="form-control" required>
                                @if ($errors->has('email')) <p class="help-block" style="color: red">{{ $errors->first('email') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" class="form-control" required>
                                @if ($errors->has('password')) <p class="help-block" style="color: red">{{ $errors->first('password') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                                @if ($errors->has('password_confrimation')) <p class="help-block" style="color: red">{{ $errors->first('password_confirmation') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" class="form-control" required>
                                @if ($errors->has('first_name')) <p class="help-block" style="color: red">{{ $errors->first('first_name') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" class="form-control" required>
                                @if ($errors->has('last_name')) <p class="help-block" style="color: red">{{ $errors->first('last_name') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="address">Address</label>
                                <textarea rows="5" name="address" class="form-control" required></textarea>
                                @if ($errors->has('address')) <p class="help-block" style="color: red">{{ $errors->first('address') }}</p> @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Username</label>
                                <input type="text" name="username" class="form-control" required>
                                @if ($errors->has('username')) <p class="help-block" style="color: red">{{ $errors->first('username') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                                @if ($errors->has('phone')) <p class="help-block" style="color: red">{{ $errors->first('phone') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="country_id">Country</label>
                                <select name="country_id" class="form-control" required>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->country_code }}">{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('country_id')) <p class="help-block" style="color: red">{{ $errors->first('country_id') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="image_url">Avatar</label>
                                <input type="file" name="image_url" class="form-control" required>
                                @if ($errors->has('image_url')) <p class="help-block" style="color: red">{{ $errors->first('image_url') }}</p> @endif
                            </div>

                            <div class="form-group">
                                <label for="image_url">Location</label>
                                <input type="text" name="location" class="form-control">
                                @if ($errors->has('location')) <p class="help-block" style="color: red">{{ $errors->first('location') }}</p> @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i>
                                Sign Up
                            </button>

                            <a href="/" class="btn btn-primary pull-right">
                                <span class="glyphicon glyphicon-chevron-left"></span>
                                Back to Home
                            </a>
                        </div>
                    </div>

                </form>


            </div>
        </div>

    </div>

</section>

@endsection

@section('scripts')

    <script src="https://unpkg.com/flatpickr"></script>
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>
    <script>
        flatpickr(".flatpickr", {
            altInput: true
        });
    </script>

@stop