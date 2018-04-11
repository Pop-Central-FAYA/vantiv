@extends('layouts.new_app')

@section('title')
    <title>Create File Position</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create File Position</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>File Positions</a></li>
                        <li><a href="#">New File Position</a></li>
                    </ul>
                </div>
                {{--{{ dd($step2) }}--}}

                <div class="Add-brand">
                    <form class="campform" method="POST" action="{{ route('position.store') }}" data-parsley-validate="">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="input-group">
                                <label>Position</label>
                                <input type="text" name="position" value="{{ old('position') }}" required placeholder="Position of files">
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <label>Percentage</label>
                                <input type="text" name="percentage" value="{{ old('percentage') }}" required placeholder="Percentage">
                            </div>
                        </div>

                        <div class="row">
                            <div class="input-group">
                                <input type="Submit" name="Submit" value="Submit" />
                            </div>
                        </div>

                        {{--<div class="input-group">--}}
                        {{--<button>Next <i class="fa fa-play" aria-hidden="true"></i></button>--}}
                        {{--</div>--}}
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="{{ asset('assets/js/parsley.min.js') }}"></script>


@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/parsley.css') }}">
@stop
