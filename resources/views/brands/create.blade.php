@extends('layouts.new_app')

@section('title')
    <title>Create Brand</title>
@endsection

@section('content')

<div class="main-section">
    <div class="container">
        <div class="row charging">
            <div class="col-12 heading-main">
                <h1>Create Brand</h1>
                <ul>
                    <li><a href="{{ route('dashboard') }}"><i class="fa fa-edit"></i>Broadcaster</a></li>
                    <li><a href="#">Create Brand</a></li>
                </ul>
            </div>
            <div class="Add-brand">
                <h2>Create Brand</h2>
                @if(!$clients)
                    <p>You cannot create a brand without a client or Walk-In</p>
                @else
                <form action="{{ route('brand.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate="">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="input-group">
                            <label for="brand_name">Name</label>
                            <input type="text" name="brand_name" value=""  placeholder="Brand Name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-group">
                            <label>Clients</label>
                            <select name="clients" class="Role" required>
                                @foreach($clients as $client)
                                    <option value="{{ $client[0]->id }}">
                                        {{ $client[0]->firstname.' '.$client[0]->lastname }}
                                    </option>
                                @endforeach
                            </select>
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
                        <div class="input-group">
                            <label>Brand Logo</label>
                            <input type="file" name="image_url" >
                        </div>
                    </div>

                    <div class="input-group">
                        <input type="Submit" name="Submit" value="Add Brand" />
                    </div>
                </form>
                @endif
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

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/parsley.css') }}">
@stop
