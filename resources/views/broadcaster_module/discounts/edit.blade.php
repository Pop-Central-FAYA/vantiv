@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Edit Discount</title>
@stop

@section('content')

    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <div class="main_contain">
        {{--Header--}}
        @include('partials.new-frontend.broadcaster.header')
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Edit Discount</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color pt">
            <form action="{{ route('discount.update', ['id' => $discount->id]) }}" method="post">
                {{ csrf_field() }}
                <div class="margin_center col_11 clearfix pt4 create_fields">

                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('name') ? ' has-error' : '' }}">
                            <label class="small_faint">Name</label>
                            <div class="">
                                <input type="text" name="name" value="{{ $discount->name }}" required placeholder="Discount Name">

                                @if($errors->has('name'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('name') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                        @if(Auth::user()->companies->count() > 1)
                            <div class="input_wrap column col_4">
                                <label class="small_faint">Publisher</label>
                                <div class="select_wrap{{ $errors->has('company') ? ' has-error' : '' }}">
                                    <select required name="company">
                                        <option value="{{ $discount->company->id }}">{{ $discount->company->name }}</option>
                                    </select>

                                    @if($errors->has('company'))
                                        <strong>
                                        <span class="help-block">
                                            {{ $errors->first('company') }}
                                        </span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="clearfix mb3">
                        <div class="input_wrap column col_4 {{ $errors->has('percentage') ? ' has-error' : '' }}">
                            <label class="small_faint">Percentage</label>
                            <div class="">
                                <input type="text" name="percentage" value="{{ $discount->percentage }}" required placeholder="Discount Percentage">

                                @if($errors->has('percentage'))
                                    <strong>
                                        <span class="help-block">
                                            {{ $errors->first('percentage') }}
                                        </span>
                                    </strong>
                                @endif
                            </div>
                        </div>
                    </div>

                {{--<a id="add_custom" class="btn small_btn">Add Custom</a>--}}
                <!-- end -->

                    <div class="mb4 align_right pt">
                        <input type="submit" value="Update Discount" class="btn uppercased mb4">
                    </div>

                </div>
            </form>
        </div>
        <!-- main frame end -->
    </div><!-- main contain -->
@stop

@section('scripts')
    <script type="text/javascript" src="{{ asset('new_frontend/js/wickedpicker.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('new_frontend/js/aria-accordion.js') }}"></script>
    <script src="https://unpkg.com/flatpickr"></script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('new_frontend/css/wickedpicker.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="{{ asset('new_frontend/css/aria-accordion.css') }}" rel="stylesheet">
@stop
