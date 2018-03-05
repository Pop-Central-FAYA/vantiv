@extends('layouts.new_app')

@section('title')
    <title> Faya | Profile-Management </title>
@stop

@section('styles')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>User Management</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>User Management</a></li>
                        <li><a href="#">Update Profile</a></li>
                    </ul>
                </div>
                <div class="Add-Clients">
                    <form action="{{ route('profile.update.details') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-12 ">
                            <h2>User Details</h2>
                            <div class="col-12 form-inner">
                                <div class="input-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" required class="form-control" id="first_name"
                                           name="first_name" placeholder="@lang('app.first_name')" value="{{ $user_details['first_name'] }}">
                                </div>
                                <div class="input-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" required class="form-control" id="last_name"
                                           name="last_name" placeholder="@lang('app.last_name')" value="{{ $user_details['last_name'] }}">
                                </div>
                                <div class="input-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="text" required name="phone" value="{{ $user_details['phone'] }}"  placeholder="Phone">
                                </div>
                                <div class="input-group">
                                    <label for="address">Address</label>
                                    <input type="text" required name="address" value="{{ $user_details['address'] }}"  placeholder="Address">
                                </div>
                                <div class="input-group">
                                    <label for="country_id">Nationality</label>
                                    <select name="country_id" required class="form-control Role">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->country_code }}"
                                            @if($user_details['nationality'] === $country->country_code)
                                                selected
                                            @endif
                                            >{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h2>Login Details</h2>
                            <div class="input-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" required value="{{ $user_details['email'] }}" readonly  placeholder="Email">
                            </div>
                            <div class="input-group">
                                <label for="username">Username</label>
                                <input type="text" required class="form-control" id="username"
                                       name="username" placeholder="username" value="{{ $user_details['username'] }}">
                            </div>
                            <div class="input-group">
                                <label for="password_confirmation">Re-Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
                            </div>
                            <div class="input-group">
                                <label for="password">Password</label>
                                <input type="password" name="password" placeholder=Password">
                            </div>
                        </div>
                        <div class="col-12">
                            <h2>User Details</h2>
                            <div class="col-12 form-inner">
                                <div class="input-group">
                                    <label for="location">Location</label>
                                    <input type="text" name="location" required value="{{ $user_details['location'] }}"  placeholder="Location">
                                </div>
                                <div class="input-group">
                                    <div class="custom-file-upload">
                                        <label for="profile">Profile Image</label>
                                        <input type="file" id="file" name="image_url" />
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="input-group">
                            <button type="sumbit" class="btn btn-danger btn-lg">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--old one--}}


    <!-- Main content -->


@stop

@section('scripts')

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}
    <script src="https://unpkg.com/flatpickr"></script>

    <script>
        flatpickr(".flatpickr", {
            altInput: true,
        });
    </script>


@stop