@extends('layouts.new_app')

@section('title')
    <title> Inventory </title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">

                <div class="col-12 heading-main">
                    <h1>Upload Inventory</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Dashboard</a></li>
                        <li><a href="#">Upload Inventory</a></li>
                    </ul>
                </div>

                <form action="{{ route('upload_inventory.store', ['id' => $id]) }}" enctype="multipart/form-data" method="post">
                    {{ csrf_field() }}

                    <div class="Add-brand">
                        <div class="row">
                            <div class="input-group{{ $errors->has('days') ? ' has-error' : '' }}" style="margin-right: 20px;">
                                <label for="days">Select Days</label>
                                <select name="days" id="" required class="Role form-control">
                                    <option value="">Select Day</option>
                                    @foreach($days as $day)
                                        <option value="{{ $day->id }}">{{ $day->day }}</option>
                                    @endforeach
                                </select>

                                @if($errors->has('days'))
                                    <strong>
                                        <span class="help-block"><{{ $errors->first('days') }}/span>
                                    </strong>
                                @endif
                            </div>
                            <div class="input-group{{ $errors->has('channels') ? ' has-error' : '' }}" style="margin-right: 20px;">
                                <label for="channel">Select Broadcast Channel</label>
                                <select name="channels" id="" required class="Role form-control">
                                    <option value="">Select Channel</option>
                                    @foreach($channels as $channel)
                                        <option value="{{ $channel->id }}">{{ $channel->channel }}</option>
                                    @endforeach
                                </select>

                                @if($errors->has('channels'))
                                    <strong>
                                        <span class="help-block">{{ $errors->first('channels') }}</span>
                                    </strong>
                                @endif
                            </div>
                        </div>

                        <p><br></p>
                        <p><br></p>
                        <p><br></p>
                        <div class="row">
                            <div class="input-group{{ $errors->has('upload') ? ' has-error' : '' }}" style="margin-right: 20px;">
                                <label for="days">Select File (.xlsx, .csv): </label>
                                <input type="file" name="upload" class="form-control">

                                @if($errors->has('upload'))
                                    <strong>
                                        <span class="help-block">{{ $errors->first('upload') }}</span>
                                    </strong>
                                @endif
                            </div>
                        </div>

                        <p><br></p>
                        <p><br></p>
                        <div class="row">
                            <div class="input-group" style="margin-right: 20px;">
                                <button type="submit" class="btn btn-primary btn-lg">Upload Inventory</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>

        </div>
    </div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>


@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}">
@stop