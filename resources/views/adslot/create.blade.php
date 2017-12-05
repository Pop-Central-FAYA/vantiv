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
                        <label for="hourly_range">Hourly Range</label>
                        <select name="hourly_range" class="form-control" id="hourly_range">
                            @foreach($hour as $hours)
                                <option value="{{ $hours->id }}">{{ $hours->time_range }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" id="dynamic_data" style="padding-bottom: 10px;">
                        <div class="row">
                            <table class="table table-hover" id="dynamic_field">
                                <thead>
                                <th> &nbsp;<p>Hourly Range Breakdown</p></th>
                                <th> 60 Seconds</th>
                                <th> 45 Seconds</th>
                                <th> 30 Seconds</th>
                                <th> 15 Seconds</th>
                                <th>Premium</th>
                                <th></th>
                                </thead>
                                <tbody>
                                <tr class="b">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <input type="text" class="form-control timepicker" name="from[]" placeholder="FROM" >
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control timepicker" name="to[]" placeholder="TO" >
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="price_60[]" id="price_601" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="price_45[]" id="price_451" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="price_30[]" price="price_301" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="price_15[]" price="price_151" class="form-control">
                                    </td>
                                    <td>
                                        <input type="checkbox" name="premium[]">
                                    </td>
                                    <td>
                                        <button id="add_more" type="button" class="btn btn-primary btn-xs">Add More +</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <br>
                        </div>
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
    {!! HTML::script('assets/js/jquery.timepicker.min.js') !!}
    <script>


        $(document).ready(function(){
            $("#txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $('input.timepicker').timepicker({
                timeFormat: 'HH:mm:ss',
                minTime: '11:45:00', // 11:45:00 AM,
                maxHour: 20,
                maxMinutes: 30,
                startTime: new Date(0,0,0,15,0,0), // 3:00:00 PM - noon
                interval: 15 // 15 minutes
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

            var i = 0;
            $("#add_more").click(function(){
                var n = $(".b").length + 1;
                if(n > 4){
                    return false;
                }
                $("#dynamic_field").append('<tr class="b"><td><div class="row"><div class="col-md-6"><input type="text" class="form-control timepicker" name="from[]" placeholder="FROM"></div><div class="col-md-6"><input type="text" class="form-control timepicker" name="to[]" placeholder="TO"></div></div></td><td><input type="text" name="price_60[]" class="form-control"></td><td><input type="text" name="price_45[]" class="form-control"></td><td><input type="text" name="price_30[]" class="form-control"></td><td><input type="text" name="price_15[]" class="form-control"></td><td><input type="checkbox" name="premium[]'+n+'"></td></tr>')

            });

        });


    </script>


@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}">
@stop