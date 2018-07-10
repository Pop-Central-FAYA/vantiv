@extends('layouts.new_app')

@section('title')
    <title>Agency | Add Clients</title>
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
                    <h1>Clients Management</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Clients Management</a></li>
                        <li><a href="#">Create Client</a></li>
                    </ul>
                </div>
                <div class="Add-Clients changing">
                    <form action="{{ url('agency/clients/create') }}" method="POST" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="col-12 ">
                            <h2>User Details</h2>
                            <div class="col-12 form-inner">
                                <div class="input-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <input type="text" class="form-control" id="first_name" name="first_name" required placeholder="@lang('app.first_name')" value="{{ $edit ? $user->first_name : '' }}">
                                    @if($errors->has('first_name'))
                                        <strong>
                                            <span class="error-block" style="color: red;">{{ $errors->first('first_name') }}</span>
                                        </strong>
                                    @endif
                                </div>
                                <div class="input-group{{ $errors->has('address') ? ' has-error' : '' }}">
                                    <input type="text" name="address" value="{{ $edit ? $user->address : '' }}" required placeholder="Address">
                                    @if($errors->has('address'))
                                        <strong>
                                            <span class="error-block" style="color: red;">{{ $errors->first('address') }}</span>
                                        </strong>
                                    @endif
                                </div>
                                <div class="input-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    <input type="text" class="form-control" id="last_name" name="last_name" required placeholder="@lang('app.last_name')" value="{{ $edit ? $user->last_name : '' }}">
                                    @if($errors->has('last_name'))
                                        <strong>
                                            <span class="error-block" style="color: red;">{{ $errors->first('last_name') }}</span>
                                        </strong>
                                    @endif
                                </div>
                                <div class="input-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                                    <input type="text" name="phone" value="{{ $edit ? $user->phone : '' }}" required placeholder="Phone">
                                    @if($errors->has('phone'))
                                        <strong>
                                            <span class="error-block" style="color: red;">{{ $errors->first('phone') }}</span>
                                        </strong>
                                    @endif
                                </div>
                                <div class="input-group{{ $errors->has('country_id') ? ' has-error' : '' }}">
                                    <select name="country_id" required class="form-control Role">
                                        @foreach ($countries as $country)
                                            <option value="{{ $country->country_code }}">{{ $country->name }}</option>
                                        @endforeach
                                    </select>

                                    @if($errors->has('country_id'))
                                        <strong>
                                            <span class="error-block" style="color: red;">{{ $errors->first('country_id') }}</span>
                                        </strong>
                                    @endif
                                </div>
                                <div class="input-group{{ $errors->has('location') ? ' has-error' : '' }}">
                                    <input type="text" name="location" value="{{ $edit ? $user->location : '' }}" required  placeholder="Location">
                                    @if($errors->has('location'))
                                        <strong>
                                            <span class="error-block" style="color: red;">{{ $errors->first('location') }}</span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <h2>Login Details</h2>
                            <div class="input-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <input type="email" name="email" value="{{ $edit ? $user->email : '' }}" required placeholder="Email">
                                @if($errors->has('email'))
                                    <strong>
                                        <span class="error-block" style="color: red;">{{ $errors->first('email') }}</span>
                                    </strong>
                                @endif
                            </div>
                            <div class="input-group">
                                <input type="text" class="form-control" id="password" required name="username" placeholder="username" value="{{ $edit ? $user->username : '' }}">
                            </div>
                            <div class="input-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <input type="password" name="password_confirmation" required id="password_confirmation" @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @else placeholder="Confirm Password" @endif>
                                @if($errors->has('password_confirmation'))
                                    <strong>
                                        <span class="error-block" style="color: red;">{{ $errors->first('password_confirmation') }}</span>
                                    </strong>
                                @endif
                            </div>
                            <div class="input-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <input type="password" name="password" required @if ($edit) placeholder="@lang('app.leave_blank_if_you_dont_want_to_change')" @else placeholder="Password" @endif>
                                @if($errors->has('password'))
                                    <strong>
                                        <span class="error-block" style="color: red;">{{ $errors->first('password') }}</span>
                                    </strong>
                                @endif
                            </div>
                            @if ($edit)
                                <button type="submit" class="btn btn-primary" id="update-login-details-btn">
                                    <i class="fa fa-refresh"></i>
                                    @lang('app.update_details')
                                </button>
                            @endif
                        </div>
                        <div class="col-12">
                            <h2>Brand Details</h2>
                            <div class="col-12 form-inner">
                                <input type="hidden" name="broadcaster_id" value="{{ null }}">
                                <input type="hidden" name="client_type_id" value="2">
                                <input type="hidden" name="agency_id" value="">
                                <div class="input-group{{ $errors->has('image_url') ? ' has-error' : '' }}">
                                    <div class="custom-file-upload">
                                        <label for="brand_logo">Brand Logo</label>
                                        <input type="file" id="file" name="image_url" />
                                        @if($errors->has('image_url'))
                                            <strong>
                                                <span class="error-block" style="color: red;">{{ $errors->first('image_url') }}</span>
                                            </strong>
                                        @endif
                                    </div>
                                </div>
                                <div class="input-group{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                                    <label for="brand_name">Brand</label>
                                    <input type="text" name="brand_name" value=""  placeholder="Brands">
                                    @if($errors->has('brand_name'))
                                        <strong>
                                            <span class="error-block" style="color: red;">{{ $errors->first('brand_name') }}</span>
                                        </strong>
                                    @endif
                                </div>

                                <div class="input-group">
                                    <label>Sub Industry</label>
                                    <select name="sub_industry" id="sub_industry">

                                    </select>
                                </div>

                                <div class="input-group">
                                    <label for="brand_name">Industry</label>
                                    <select name="industry" id="industry">
                                        <option value="">Select Industry</option>
                                        @foreach($industries as $industry)
                                            <option value="{{ $industry->sector_code }}">{{ $industry->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="input-group">
                                    <input type="Submit" name="Submit" class="update" value="Create Client">
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{--old one--}}
    <div class="modal_contain" id="new_client">
        <h2 class="sub_header mb4"></h2>
        <form action="#" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="align_right">
                <input type="submit" value="Create Client" class="btn uppercased update">
            </div>

        </form>
    </div>

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

    <script>
        $(document).ready(function () {
            // $("#state").change(function() {
            $('#industry').on('change', function(e){
                $(".changing").css({
                    opacity: 0.5
                });
                $('.update').attr("disabled", true);
                var industry = $("#industry").val();
                var url = '/walk-in/brand';
                $.ajax({
                    url: url,
                    method: "GET",
                    data: {industry: industry},
                    success: function(data){
                        if(data.error === 'error'){
                            $(".changing").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);
                        }else{
                            $(".changing").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);

                            $('#sub_industry').empty();

                            $('#sub_industry').append(' Please choose one');

                            $.each(data, function(index, title){
                                $("#sub_industry").append('' + '<option value ="'+ title.sub_sector_code + '"  > ' + title.name + '  </option>');
                            });
                        }

                    }
                });
            });
        });

    </script>


@stop