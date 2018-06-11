@extends('layouts.new_app')

@section('title')
    <title>Create Target Audience</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Target Audience</h1>
                    <ul>
                        <li><a href="{{ route('target_audience.index') }}"><i class="fa fa-th-large"></i>All Target Audience</a></li>
                        <li><a href="#">Create Target Audience</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="Add-brand">
                        <h2>Create Audience</h2>

                        <form action="{{ route('admin.target_audience.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <label for="first_name">Audience</label>
                                        <input type="text" name="audience" placeholder="Target Audience" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="Submit" name="Submit" value="Add Target Audience">
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