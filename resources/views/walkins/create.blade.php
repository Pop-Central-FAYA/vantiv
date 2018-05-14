@extends('layouts.new_app')

@section('title')
    <title>Create Walk-In</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Walk-In</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Walk-In</a></li>
                        <li><a href="#">Create Walk-In</a></li>
                    </ul>
                </div>
            </div>

            <div class="row changing">
                <div class="col-md-12">

                    <div class="Add-brand">
                        <h2>Create Walk-In</h2>

                        <form action="{{ route('walkins.store') }}" method="post" enctype="multipart/form-data">
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
                                        <label for="brand_name">Brand Name</label>
                                        <input type="text" name="brand_name" value=""  placeholder="Brand Name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label>Brand Logo</label>
                                        <input type="file" name="image_url" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="brand_name">Industry</label>
                                        <select name="industry" id="industry">
                                            <option value="">Select Industry</option>
                                            @foreach($industries as $industry)
                                                <option value="{{ $industry->sector_code }}">{{ $industry->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label>Sub Industry</label>
                                        <select name="sub_industry" id="sub_industry">

                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="Submit" class="update" name="Submit" value="Add Walk-In">
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

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop