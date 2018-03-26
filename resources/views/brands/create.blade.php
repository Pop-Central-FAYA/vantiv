@extends('layouts.new_app')

@section('title')
    <title>Create Brand</title>
@endsection

@section('content')

<div class="main-section">
    <div class="container">
        <div class="row">
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
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>

    <script>
        $(document).ready(function(){

            $('input[type=radio][name=premium]').change(function() {
                if (this.value == 'true') {
                    $("#premium").show();
                }
                else if (this.value == 'false') {
                    $("#premium").hide();
                }
            });

        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/parsley.css') }}">
@stop
