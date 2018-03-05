@extends('layouts.new_app')

@section('title')
    <title>Create Campaign</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create Campaign</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Create Campaign</a></li>
                        <li><a href="#">New Media</a></li>
                    </ul>
                </div>

                <div class="Add-brand">
                    <form class="campform" method="POST" action="{{ route('campaign.store2', ['id' => 1, 'walkins' => $walkins_id]) }}" data-parsley-validate="">
                        {{ csrf_field() }}

                        <div class="input-group">
                            <label>Brands</label>
                            <select name="brand" class="form-control" id="">
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Campaign Name</label>
                            <input type="text" name="name" placeholder="Campaign Name" value="{{ isset(((object) $step2)->name) ? ((object) $step2)->name: "" }}" required placeholder="Name">
                            <input type="hidden" name="user_id" value="">
                        </div>

                        <div class="input-group">
                            <label>Product</label>
                            <input type="text" name="product" value="{{ isset(((object) $step2)->product) ? ((object) $step2)->product : "" }}" required placeholder="Product">
                        </div>

                        <div class="input-group">
                            <label>Industry</label>
                            <select name="industry" id="" class="form-control">
                                @foreach($industry as $ind)
                                    <option value="{{ $ind->name }}">{{ $ind->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Target Audience</label>
                            <select name="target_audience" id="" class="form-control">
                                @foreach($target_audience as $target_audiences)
                                    <option value="{{ $target_audiences->id }}">{{ $target_audiences->audience }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group">
                            <label>Channel</label>
                            <select style="width: 100%" class="form-control" name="channel">
                                @foreach($chanel as $chanels)
                                    <option value="{{ $chanels->id }}"
                                            @if(isset(((object) $step2)->channel) === $chanels->id)
                                            selected
                                            @endif
                                    >{{ $chanels->channel }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="input-group date styledate">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" placeholder="stop-date" value="{{ isset(((object) $step2)->end_date) ? ((object) $step2)->end_date : "" }}" readonly required name="end_date" class="form-control flatpickr" id="txtToDate" />
                        </div>

                        <div class="input-group date styledate">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" placeholder="start-date" value="{{ isset(((object) $step2)->start_date) ?((object) $step2)->start_date : "" }}" readonly required name="start_date" class="form-control flatpickr" id="txtFromDate" />
                        </div>

                        <div class="input-group">
                            <label>Max Age:</label>
                            <input type="number" name="max_age" required class="form-control">
                        </div>

                        <div class="input-group">
                            <label>Min Age:</label>
                            <input type="number" name="min_age" required class="form-control">
                        </div>

                        <div class="input-group">
                            <h3> Day Parts </h3>
                            <div class="form-group">
                                @foreach($day_parts as $day_part)
                                    <label style="margin-left: 10px;">
                                        {{ $day_part->day_parts }}
                                        <input type="checkbox" name="dayparts[]" value="{{ $day_part->id }}" class="minimal-red" required />
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="input-group">
                            <h3>Region</h3>
                            {{--@foreach($regions as $region)--}}
                            <p>
                                <label>
                                    {{ $regions[0]->region }}
                                    <input type="checkbox" name="region[]" value="{{ $regions[0]->id }}" class="minimal-red" required>
                                </label>
                            </p>
                            {{--@endforeach--}}
                        </div>

                        <div class="input-group">
                            <input type="Submit" name="Submit" value="Next" />
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


    <script>
        flatpickr(".flatpickr", {
            altInput: true,
        });
    </script>

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

        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/parsley.css') }}">
@stop
