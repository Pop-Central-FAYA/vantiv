@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Create Campaign

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> New Media</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-1 hidden-sm hidden-xs"></div>
            <div class="col-md-9 " style="padding:2%">
                <form class="campform" method="POST" action="{{ route('campaign.store2', ['id' => 1]) }}">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="name" placeholder="Campaign Name" value="{{ isset(((object) $step2)->name) ? ((object) $step2)->name: "" }}" required placeholder="Name">
                            <input type="hidden" name="user_id" value="">
                        </div>

                        <div class="col-lg-6 col-md-6 hidden-sm hidden-xs"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="brand" value="{{ isset(((object) $step2)->brand) ? ((object) $step2)->brand : "" }}" required placeholder="Brand">
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="product" value="{{ isset(((object) $step2)->product) ? ((object) $step2)->product : "" }}" required placeholder="Product">
                        </div>
                    </div>

                    <div class="row">
                        <dvi class="col-md-3">
                            <label style="margin-left:10%">Channel:</label>
                            <select style="width: 100%" name="channel">
                                <option value="TV"
                                        @if(isset(((object) $step2)->channel) === "TV")
                                        selected
                                        @endif
                                >TV</option>
                                <option value="Radio"
                                        @if(isset(((object) $step2)->channel) === "Radio")
                                        selected
                                        @endif
                                >Radio</option>

                            </select>
                        </dvi>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group date styledate">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" placeholder="start-date" value="{{ isset(((object) $step2)->start_date) ?((object) $step2)->start_date : "" }}" required name="start_date" class="form-control" id="txtFromDate" />
                            </div>

                            <div class="input-group date styledate">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" placeholder="stop-date" value="{{ isset(((object) $step2)->end_date) ? ((object) $step2)->end_date : "" }}" required name="end_date" class="form-control" id="txtToDate" />
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10%">
                        <h3> Day Parts </h3>
                        <div class="col-md-8">
                            <div class="form-group">
                                <p>
                                    @foreach($day_parts as $day_part)
                                    <label>
                                        <input type="checkbox" name="dayparts[]" value="{{ $day_part->id }}" class="minimal-red" />
                                        {{ $day_part->day_parts }}
                                    </label>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="container">
                        <p align="right">
                            <button class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button>
                        </p>
                    </div>
                </form>
            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->
        </div>
        <!-- /.row -->


    </section>
@endsection
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

        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop