@extends('layouts.new_app')

@section('title')
    <title>Create Broadcaster User</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Broadcaster User</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Broadcaster</a></li>
                        <li><a href="#">Create Broadcaster User</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="Add-brand">
                        <h2>Create Broadcaster User</h2>

                        <form action="{{ route('broadcaster.post.user') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="first_name">First Name</label>
                                        <input type="text" name="first_name" placeholder="First Name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="last_name">Last Name</label>
                                        <input type="text" name="last_name" placeholder="Last Name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="email"> Email</label>
                                        <input type="email" name="email" placeholder="Email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="phone_number"> Phone Number</label>
                                        <input type="text" name="phone_number" placeholder="Phone Number" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="brand_name">Username</label>
                                        <input type="text" name="username" value=""  placeholder="Username" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label>Profile</label>
                                        <input type="file" name="image_url" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="brand_name">Address</label>
                                        <textarea type="text" class="form-control" name="address" value=""  placeholder="Address" required></textarea>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label>Country</label>
                                        <select name="country" class="form-control" id="" required>
                                            @foreach($countries as $country)
                                                <option value="{{ $country->country_code }}">{{ $country->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="brand_name">Location</label>
                                        <input type="text" class="form-control" name="location" value=""  placeholder="Location" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="Submit" name="Submit" value="Add User">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}


@stop

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop