@extends('layouts.new_app')

@section('title')
    <title>Create Industry</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Industry</h1>
                    <ul>
                        <li><a href="{{ route('industry.index') }}"><i class="fa fa-th-large"></i>All Industries</a></li>
                        <li><a href="#">Create Industry</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="Add-brand">
                        <h2>Create Industry</h2>

                        <form action="{{ route('industry.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="first_name">Industry Name</label>
                                        <input type="text" name="industry_name" placeholder="Industry Name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="last_name">Sector Code</label>
                                        <input type="text" name="sic" placeholder="SIC Code" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="Submit" name="Submit" value="Add Industry">
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