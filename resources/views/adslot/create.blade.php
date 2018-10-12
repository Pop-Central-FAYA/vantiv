@extends('layouts.new_app')

@section('title')
    <title>Create Adslots</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">

                <div class="col-12 heading-main">
                    <h1>Create Adslot</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Broadcaster</a></li>
                        <li><a href="#">Create Adslot</a></li>
                    </ul>
                </div>

                <form action="{{ route('adslot.store') }}" method="post">
                    {{ csrf_field() }}

                    <div class="Add-brand">
                        <div class="row">
                            <div class="input-group" style="margin-right: 20px;">
                                <label for="days">Select Days</label>
                                <select name="days" id="" required class="Role form-control">
                                    @foreach($days as $day)
                                        <option value="{{ $day->id }}">{{ $day->day }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group">
                                <label for="hourly_range">Hourly Range</label>
                                <select name="hourly_range" required id="hourly_range" class="Role form-control">
                                    @foreach($hours as $hour)
                                        <option value="{{ $hour->id }}">{{ $hour->time_range }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="Add-brand">
                        <div class="row">
                            <div class="input-group" style="margin-right: 20px;">
                                <label for="channel">Select Broadcast Channel</label>
                                <select name="channel" id="" required class="Role form-control">
                                    <option value="">Select Channel</option>
                                    @foreach($channels as $channel)
                                        <option value="{{ $channel->id }}">{{ $channel->channel }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="input-group" id="dynamic_data" style="padding-bottom: 10px;">
                        <div class="row">
                            <table class="table table-hover" id="dynamic_field">
                                <thead>
                                    <th><p>Hourly Range Breakdown</p></th>
                                    <th>60 Seconds</th>
                                    <th>45 Seconds</th>
                                    <th>30 Seconds</th>
                                    <th>15 Seconds</th>
                                    <th>Target Audience</th>
                                    <th>Region</th>
                                    <th>Day Parts</th>
                                    <th>Min Age</th>
                                    <th>Max Age</th>
                                    <th></th>
                                </thead>
                                <tbody>
                                    <tr class="b">
                                        <td>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="input-group time timepicker" id="timepicker">
                                                        <input name="from_time[]" required class="form-control" />
                                                        <span class="input-group-addon">
                                                            <span class="fa fa-clock-o"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group time timepicker" id="timepicker">
                                                        <input name="to_time[]" required class="form-control"/>
                                                        <span class="input-group-addon">
                                                            <span class="fa fa-clock-o"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><input type="text" name="price_60[]" required id="price_601" class="form-control"></td>
                                        <td><input type="text" name="price_45[]" required id="price_451" class="form-control"></td>
                                        <td><input type="text" name="price_30[]" required price="price_301" class="form-control"></td>
                                        <td><input type="text" name="price_15[]" required price="price_151" class="form-control"></td>
                                        <td>
                                            <script type='text/javascript'>
                                                <?php
                                                    if (is_array($targets) and count($targets) > 0) {
                                                        echo "var tar_audience = " . json_encode($targets) . ";\n";
                                                    }
                                                ?>
                                            </script>
                                            <select name="target_audiences[]" class="form-control" id="">
                                                @foreach($targets as $target)
                                                    <option value="{{ $target->id }}">{{ $target->audience }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <script type='text/javascript'>
                                                <?php
                                                    if (is_array($regions) and count($regions) > 0) {
                                                        echo "var region = " . json_encode($regions) . ";\n";
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
                                                    if (is_array($day_parts) and count($day_parts) > 0) {
                                                        echo "var day_parts = " . json_encode($day_parts) . ";\n";
                                                    }
                                                ?>
                                            </script>
                                            <select name="dayparts[]" class="form-control" id="">
                                                @foreach ($day_parts as $daypart)
                                                    <option value="{{ $daypart->id }}">{{ $daypart->day_parts }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td><input type="number" required name="min_age[]" placeholder="Min Age" class="form-control"></td>
                                        <td><input type="number" required name="max_age[]" placeholder="Max Age" class="form-control"></td>
                                        <td><button id="add_more" type="button" class="btn btn-primary btn-xs">Add More +</button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <br>
                        </div>
                    </div>

                    <div class="input-group">
                        <input type="Submit" class="btn btn-primary" name="Submit" value="Create Adslot" />
                        {{--<button type="submit" class="btn btn-success">Create Adslot</button>--}}
                    </div>
                </form>

            </div>

        </div>
    </div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {{--{!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}--}}
    {{--<script src="{{ asset("assets/js/jquery.timepicker.min.js") }}"></script>--}}
    {{--<script type="text/javascript" src="{{ asset('new_assets/js/bootstrap-datetimepicker.min.js') }}"></script>--}}
    {{--<script type="text/javascript" src="{{ asset('new_assets/js/jquery.timepicker.min.js') }}"></script>--}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.7.1/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script>
        $(document).ready(function(){

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
                if (n > 12) {
                    return false;
                }
                var big_html = '';
                big_html += '<tr class="b"><td><div class="row"><div class="col-md-6"><div class="input-group time timepicker" id="timepicker"><input name="from_time[]" required class="form-control"/><span class="input-group-addon"><span class="fa fa-clock-o"></span></span></div></div><div class="col-md-6"><div class="input-group time timepicker" id="timepicker"><input name="to_time[]" required class="form-control"/><span class="input-group-addon"><span class="fa fa-clock-o"></span></span></div></div></div></td><td><input type="text" name="price_60[]" required class="form-control"></td><td><input type="text" name="price_45[]" required class="form-control"></td><td><input type="text" required name="price_30[]" class="form-control"></td><td><input type="text" required name="price_15[]" class="form-control"></td><td><select name="target_audience[]" class="form-control" id="">';
                $.each(tar_audience, function (index,value) {
                    big_html += '<option value ="'+ value.id + '"> ' + value.audience + '</option>';
                });
                big_html += '</select></td><td><select name="region[]" class="form-control" id="">';
                $.each(region, function (index,value) {
                    big_html += '<option value ="'+ value.id + '"> ' + value.region + '</option>';
                });
                big_html += '</select></td><td><select name="dayparts[]" class="form-control">';
                $.each(day_parts, function (index,value) {
                    big_html += '<option value="'+ value.id +'">'+ value.day_parts +'</option>'
                });
                big_html += '</select></td><td><input type="number" required name="min_age[]" placeholder="Min Age" class="form-control"></td><td><input type="number" required name="max_age[]" placeholder="Max Age" class="form-control"></td></tr>';
                $("#dynamic_field").append(big_html);

            });

            $("body").delegate(".timepicker", "click", function() {
                console.log('hmmmmm');
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