@extends('layouts.new_app')
@section('title')
    <title>Agency | Create Campaigns</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create Campaigns</h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Agency</a></li>
                        <li><a href="{{ route('agency.campaign.all') }}">All Campaign</a></li>
                    </ul>
                </div>
                <div class="Create-campaign">
                    <form class="campform" method="POST" action="{{ route('agency_campaign.store1', ['id' => $id]) }}">
                        {{ csrf_field() }}
                        <div class="col-12 ">
                            <h2>Campaign Details</h2>
                            <hr>
                            <p><br></p>
                            <p><br></p>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label class="col-md-2">Campaign Name:</label>
                                    <div class="col-md-6">
                                        <input type="text" name="name" class="form-control" value="{{ isset(((object) $step1)->name) ? ((object) $step1)->name: "" }}" required  placeholder="Campaign Name">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <label class="col-md-2">Product:</label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="product" value="{{ isset(((object) $step1)->product) ? ((object) $step1)->product : "" }}" required placeholder="Product">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            @if(isset($step1))
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label class="col-md-2">Brands:</label>
                                        <div class="col-md-4">
                                            <select name="brand" class="Role form-control">
                                                @foreach($brands as $b)
                                                    <option value="{{ $b->id }}"
                                                        @if($step1->brand === $b->id)
                                                        selected
                                                        @endif
                                                    >{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label class="col-md-2">Brands:</label>
                                        <div class="col-md-4">
                                            <select name="brand" class="Role form-control">
                                                @foreach($brands as $b)
                                                    <option value="{{ $b->id }}">{{ $b->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <p><br></p>
                            @if(isset($step1))
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="brand" class="col-md-2">Industry:</label>
                                        <div class="col-md-4">
                                            <select name="industry" class="Role form-control">
                                                @foreach($industry as $ind)
                                                    <option value="{{ $ind->name }}"
                                                        @if($step1->industry === $ind->name)
                                                        selected
                                                        @endif
                                                    >{{ $ind->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="brand" class="col-md-2">Industry:</label>
                                        <div class="col-md-4">
                                            <select name="industry" class="Role form-control">
                                                @foreach($industry as $ind)
                                                    <option value="{{ $ind->name }}">{{ $ind->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <p><br></p>
                            @if(isset($step1))
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="targer_audience" class="col-md-2">Target Audience:</label>
                                        <div class="col-md-4">
                                            <select name="target_audience" class="Role form-control">
                                                @foreach($target as $target_audiences)
                                                    <option value="{{ $target_audiences->id }}"
                                                        @if($step1->target_audience === $target_audiences->id)
                                                            selected
                                                        @endif
                                                    >{{ $target_audiences->audience }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="targer_audience" class="col-md-2">Target Audience:</label>
                                        <div class="col-md-4">
                                            <select name="target_audience" class="Role form-control">
                                                @foreach($target as $target_audiences)
                                                    <option value="{{ $target_audiences->id }}">{{ $target_audiences->audience }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <p><br></p>
                            @if(isset($step1))
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="channel" class="col-md-2">Channel:</label>
                                        <div class="col-md-4">
                                            <select name="channel" class="Role form-control">
                                                @foreach($chanel as $chanels)
                                                    <option value="{{ $chanels->id }}"
                                                            @if($step1->channel === $chanels->id)
                                                            selected
                                                            @endif
                                                    >{{ $chanels->channel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="channel" class="col-md-2">Channel:</label>
                                        <div class="col-md-4">
                                            <select name="channel" class="Role form-control">
                                                @foreach($chanel as $chanels)
                                                    <option value="{{ $chanels->id }}">{{ $chanels->channel }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="start_date" class="col-md-2">Start Date:</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control flatpickr" value="{{ isset(((object) $step1)->start_date) ?((object) $step1)->start_date : "" }}" required name="start_date"  id="datepicker" placeholder="Start-Date">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="stop_date" class="col-md-2">Stop Date:</label>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control flatpickr" value="{{ isset(((object) $step1)->end_date) ? ((object) $step1)->end_date : "" }}" required name="end_date" id="datepicker1" placeholder="Stop-Date">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            <div class="col-12 form-inner">
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="min_age" class="col-md-2">Min Age:</label>
                                            <div class="col-md-6">
                                                <input type="number" name="min_age" value="{{ isset(((object) $step1)->min_age) ?((object) $step1)->min_age : "" }}" required class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="max_age" class="col-md-2">Max Age:</label>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control" name="max_age" required value="{{ isset(((object) $step1)->max_age) ?((object) $step1)->max_age : "" }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <p><br></p>
                            @if(isset($step1))
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="day_parts" class="col-md-2">Day Parts:</label>
                                        <div class="col-md-8">
                                            All
                                            <p>
                                                <input type="checkbox" id="checkAll"
                                                       value="" class="minimal-red"  /></p>
                                            @foreach($day_part as $day_parts)
                                                <input type="checkbox" class="checked_this" name="dayparts[]"
                                                       @foreach($step1->dayparts as $daypart)
                                                       @if($daypart === $day_parts->id)
                                                       checked
                                                       @endif
                                                       @endforeach
                                                       value="{{ $day_parts->id }}">{{ $day_parts->day_parts }}
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="day_parts" class="col-md-2">Day Parts:</label>
                                        <div class="col-md-8">
                                            All
                                            <p>
                                            <input type="checkbox" id="checkAll"
                                                   value="" class="minimal-red"  /></p>
                                            @foreach($day_part as $day_parts)
                                                <input type="checkbox" name="dayparts[]" class="checked_this" value="{{ $day_parts->id }}">{{ $day_parts->day_parts }}
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <p><br></p>
                            @if(isset($step1))
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="region" class="col-md-2">Region:</label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="region[]"
                                                   @foreach($step1->region as $regions)
                                                       @if($regions === $region[0]->id)
                                                            checked
                                                       @endif
                                                   @endforeach
                                                   value="{{ $region[0]->id }}">{{ $region[0]->region }}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 form-inner">
                                    <div class="form-group">
                                        <label for="region" class="col-md-2">Region:</label>
                                        <div class="col-md-8">
                                            <input type="checkbox" name="region[]" value="{{ $region[0]->id }}">{{ $region[0]->region }}
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <p><br></p>
                                <div class="input-group">
                                    <input type="Submit" style="background: #00c4ca" class="btn btn-danger btn-lg" name="Submit" value="Next >>">
                                </div>

                            </div>

                    </form>
                </div>
            </div>
        </div>
    </div>


@stop
@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('agency_asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('agency_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('agency_asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('agency_asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('agency_asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="{{ asset('agency_asset/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('agency_asset/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('agency_asset/plugins/fastclick/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('agency_asset/dist/js/app.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="https://unpkg.com/flatpickr"></script>

    <script>
        flatpickr(".flatpickr", {
            altInput: true,
        });
    </script>
    <script>

        $(document).ready(function(){
            $("#checkAll").click(function () {
                $('input:checkbox.checked_this').not(this).prop('checked', this.checked);
            });

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
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
@stop

