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
                            @foreach($days as $day)
                                <option value="{{ $day->id }}">{{ $day->day }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="hourly_range">Hourly Range</label>
                        <select name="hourly_range" class="form-control" id="hourly_range">
                            @foreach($hours as $hour)
                                <option value="{{ $hour->id }}">{{ $hour->time_range }}</option>
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
                                <th> Target Audience</th>
                                <th> Region </th>
                                <th> Day Parts</th>
                                <th> Min Age</th>
                                <th> Max Age</th>
                                <th></th>
                                </thead>
                                <tbody>
                                <tr class="b">
                                    <td>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-group time timepicker" id="timepicker">
                                                    <input name="from_time[]" required class="form-control "/><span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group time timepicker" id="timepicker">
                                                    <input name="to_time[]" required class="form-control"/><span class="input-group-addon"><span class="fa fa-clock-o"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="text" name="price_60[]" required id="price_601" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="price_45[]" required id="price_451" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="price_30[]" required price="price_301" class="form-control">
                                    </td>
                                    <td>
                                        <input type="text" name="price_15[]" required price="price_151" class="form-control">
                                    </td>
                                    <td>
                                        <script type='text/javascript'>
                                            <?php
                                            if(is_array($targets)  and count($targets) > 0  )
                                            {
                                                echo "var tar_audience = ". json_encode($targets) . ";\n";
                                            }
                                            ?>
                                        </script>
                                        <select name="target_audience[]" class="form-control" id="">
                                            @foreach($targets as $target)
                                                <option value="{{ $target->id }}">{{ $target->audience }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <script type='text/javascript'>
                                            <?php
                                            if(is_array($regions)  and count($regions) > 0  )
                                            {
                                                echo "var region = ". json_encode($regions) . ";\n";
                                            }
                                            ?>
                                        </script>
                                        <select name="region[]" class="form-control" id="">
                                            @foreach($regions as $region)
                                                <option value="{{ $region->id }}">{{ $region->region }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <script type='text/javascript'>
                                            <?php
                                            if(is_array($day_parts)  and count($day_parts) > 0  )
                                            {
                                                echo "var day_parts = ". json_encode($day_parts) . ";\n";
                                            }
                                            ?>
                                        </script>
                                        <select name="dayparts[]" class="form-control" id="">
                                            @foreach($day_parts as $daypart)
                                                <option value="{{ $daypart->id }}">{{ $daypart->day_parts }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" required name="min_age[]" placeholder="Min Age" class="form-control">
                                    </td>
                                    <td>
                                        <input type="number" required name="max_age[]" placeholder="Max Age" class="form-control">
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
    <script src="{{ asset("assets/js/jquery.timepicker.min.js") }}"></script>
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

            $("#add_more").click(function(){
                var n = $(".b").length + 1;
                if(n > 4){
                    return false;
                }
                var big_html = '';
                big_html += '<tr class="b"><td><div class="row"><div class="col-md-6"><div class="input-group time timepicker" id="timepicker"><input name="from_time[]" required class="form-control"/><span class="input-group-addon"><span class="fa fa-clock-o"></span></span></div></div><div class="col-md-6"><div class="input-group time timepicker" id="timepicker"><input name="to_time[]" required class="form-control"/><span class="input-group-addon"><span class="fa fa-clock-o"></span></span></div></div></div></td><td><input type="text" name="price_60[]" required class="form-control"></td><td><input type="text" name="price_45[]" required class="form-control"></td><td><input type="text" required name="price_30[]" class="form-control"></td><td><input type="text" required name="price_15[]" class="form-control"></td><td><select name="target_audience[]" class="form-control" id="">';
                $.each(tar_audience, function (index,value)
                {
                    big_html += '<option value ="'+ value.id + '"> ' + value.audience + '</option>';
                });
                big_html += '</select></td><td><select name="region[]" class="form-control" id="">';
                $.each(region, function (index,value)
                {
                    big_html += '<option value ="'+ value.id + '"> ' + value.region + '</option>';
                });
                big_html += '</select></td><td><select name="dayparts[]" class="form-control">';
                $.each(day_parts, function (index,value)
                {
                    big_html += '<option value="'+ value.id +'">'+ value.day_parts +'</option>'
                });
                big_html += '</select></td><td><input type="number" required name="min_age[]" placeholder="Min Age" class="form-control"></td><td><input type="number" required name="max_age[]" placeholder="Max Age" class="form-control"></td></tr>';
                $("#dynamic_field").append(big_html);
            });

            $("body").delegate(".timepicker", "click", function() {
                $(".timepicker").datetimepicker({
                    format: "HH:mm",
                    icons: {
                        up: "fa fa-chevron-up",
                        down: "fa fa-chevron-down"
                    }
                });
            });

        });


    </script>


@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/jquery.timepicker.min.css') }}">
@stop