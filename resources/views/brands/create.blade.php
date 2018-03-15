@extends('layouts.new_app')

@section('title')
    <title>Create Brand</title>
@endsection

@section('content')

<div class="main-section">
    <div class="container">
        <div class="row">
            <div class="col-12 heading-main">
                <h1>Create Brands</h1>
                <ul>
                    <li><a href="#"><i class="fa fa-edit"></i>Broadcaster</a></li>
                    <li><a href="#">Create Brands</a></li>
                </ul>
            </div>
            <div class="Add-brand">
                <h2>Create Brands</h2>
                <form action="{{ route('brand.store') }}" method="post" enctype="multipart/form-data" data-parsley-validate="">
                    {{ csrf_field() }}
                    <div class="input-group">
                        <label for="brand_name">Name</label>
                        <input type="text" name="brand_name" value=""  placeholder="Brand Name" required>
                    </div>
                    <div class="input-group">
                        <label>Clients</label>
                        <select name="clients" class="Role" required>
                            @foreach ($clients as $client)
                                <option value="{{ $client[0]->id }}">
                                    {{ $client[0]->firstname . ' ' . $client[0]->lastname }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group">
                        <input type="Submit" name="Submit" value="Add Brand" />
                    </div>
                    <div class="input-group">
                        <label>Brand Logo</label>
                        <input type="file" name="image_url" required>
                    </div>
                </form>
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
            $("#txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $("#txtToDate").datepicker({
                numberOfMonths: 2,
                onSelect: function(selected) {
                    $("#txtFromDate").datepicker("option","maxDate", selected)
                }
            });

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
