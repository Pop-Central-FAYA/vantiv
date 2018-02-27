@extends('layouts.new_app')

@section('title')
    <title>Faya | Profile</title>
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
                    <form action="" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-12 ">
                            <h2>User Details</h2>
                            <div class="col-12 form-inner">
                                <div class="input-group">
                                    <input type="text" required class="form-control flatpickr" readonly name="birthday" value="" placeholder="Date of Birth">
                                </div>
                                <div class="input-group">
                                    <input type="text" required name="phone" value=""  placeholder="Phone">
                                </div>
                                <div class="input-group">
                                    <input type="text" required class="form-control" id="first_name"
                                           name="first_name" placeholder="@lang('app.first_name')" value="">
                                </div>
                                <div class="input-group">
                                    <input type="text" required name="address" value=""  placeholder="Address">
                                </div>
                                <div class="input-group">
                                    <input type="text" required class="form-control" id="last_name"
                                           name="last_name" placeholder="@lang('app.last_name')" value="">
                                </div>

                                <div class="input-group">
                                    <select name="country_id" required class="form-control Role">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->country_code }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h2>Login Details</h2>
                            <div class="input-group">
                                <input type="email" name="email" required value=""  placeholder="Email">
                            </div>
                            <div class="input-group">
                                <input type="text" required class="form-control" id="password"
                                       name="username" placeholder="username" value="">
                            </div>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
                            </div>
                            <div class="input-group">
                                <input type="password" name="password" placeholder=Password">
                            </div>
                        </div>
                        <div class="col-12">
                            <h2>User Details</h2>
                            <div class="col-12 form-inner">
                                <input type="hidden" name="broadcaster_id" value="{{ null }}">
                                <input type="hidden" name="client_type_id" value="2">
                                <input type="hidden" name="agency_id" value="">
                                <div class="input-group">
                                    <input type="text" name="location" value=""  placeholder="Location">
                                </div>
                                <div class="input-group">
                                    <div class="custom-file-upload">
                                        <input type="file" id="file" name="image_url" />
                                    </div></div>
                                <div class="input-group">
                                    <input type="Submit" name="Submit" value="Update Profile">
                                </div>

                            </div>
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