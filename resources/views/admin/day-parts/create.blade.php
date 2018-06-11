@extends('layouts.new_app')

@section('title')
    <title>Create Day Part</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Day Parts</h1>
                    <ul>
                        <li><a href="{{ route('admin.dayparts') }}"><i class="fa fa-th-large"></i>All day Parts</a></li>
                        <li><a href="#">Create Day Parts</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="Add-brand">
                        <h2>Create Day Parts</h2>

                        <form action="{{ route('admin.daypart.store') }}" method="post" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="input-group{{ $errors->has('day_part') ? ' has-error' : '' }}">
                                        <label for="first_name">Day Part</label>
                                        <input type="text" name="day_part" placeholder="Day Part" class="form-control" required>

                                        @if($errors->has('region'))
                                            <strong>
                                                <span class="help-block">{{ $errors->first('day_part') }}</span>
                                            </strong>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input type="Submit" name="Submit" value="Add Day Part">
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