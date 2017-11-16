@extends('layouts.app')

@section('content')

@section('title', 'Faya | Dashboard')
<!-- Content Header (Page header) -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Welcome {{ Auth::user()->username }}!

    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> All Hourly Range</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>Add AddSlot</h3>
            </div>
            <div class="panel-body">
                <form action="{{ route('adslot.store') }}" method="post">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="days">Select Days</label>
                        <select name="days" class="form-control" id="">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="overall_price">Overall Price</label>
                        <input type="number" name="overall_price" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="radio-inline"><input type="radio" value="true" name="premium">Premium</label>
                        <label class="radio-inline"><input type="radio" value="false" checked name="premium">Not Premium</label>
                    </div>
                    <div class="form-group" id="premium" style="display: none">
                        <label for="premium_date">Premium Dates</label><br>
                        <div class="row">
                            <div class="col-md-6">
                                From: <input type="text" name="start_date" class="form-control" id="txtFromDate" />
                            </div>
                            <div class="col-md-6">
                                To: <input type="text" name="end_date" class="form-control" id="txtToDate" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="hourly_range">Hourly Range</label>
                        <select name="hourly_range" class="form-control" id="hourly_range">
                            @foreach($hour as $hours)
                                <option value="{{ $hours->id }}">{{ $hours->time_range }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="dynamic_data" style="padding-bottom: 10px;">
                        @foreach($seconds as $second)
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="time[]" value="{{ $second->time_in_seconds }}" readonly class="form-control">
                                </div>
                                <div class="col-2">
                                    <input type="hidden" name="time_id[]" value="{{ $second->id }}">
                                </div>
                                <div class="col-md-6">
                                    <input type="number" name="price[]" required placeholder="Enter price for {{ $second->time_in_seconds }}s" class="form-control">
                                </div>
                                </br>
                            </div>
                            </br>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Create Adslot</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.col (RIGHT) -->
    </div>
    <!-- /.row -->

</section>
<!-- /.content -->
@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
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

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop