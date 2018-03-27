@extends('layouts.new_app')

@section('title')
    <title>All Adslots</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">

            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Adslots</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Ad Management</a></li>
                        <li><a href="#">Adslot</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">

                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" style="background:#EEE; margin-bottom: 10px">
                            <li class="active"><a href="#all">All ADslots</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">
                                <div class="box-body">
                                    <table class="table table-bordered table-striped adslots">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Day</th>
                                            <th>Time Slot</th>
                                            <th>60 Seconds</th>
                                            <th>45 Seconds</th>
                                            <th>30 Seconds</th>
                                            <th>15 Seconds</th>
                                            <th>Action</th>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <div class="tab-pane" id="nc"></div>

                            <div class="tab-pane" id="ne"></div>

                            <div class="tab-pane" id="nw"></div>

                            <div class="tab-pane" id="se"></div>

                            <div class="tab-pane" id="ss"></div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@foreach($adslots as $adslot)
    <div class="modal fade editModal{{ $adslot['id'] }}" tabindex="-1" role="dialog"  aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="padding: 7%">

                <div class="modal-body">
                    <form method="POST" action="{{ route('adslot.update', ['broadcaster' => $broadcaster, 'adslot' => $adslot['id']]) }}" class="selsec">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <label for="discount">Percentage Premium %</label>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="premium_percent" value="" class="form-control">
                            </div>
                        </div>
                        @if ($adslot['percentage'] != 0)
                            <p>
                                <h4>You have {{ $adslot['percentage'] }}% discount on this slot</h4>
                            </p>
                            <hr>
                        @endif
                        <p align="center">
                        <div class="row">
                            <div class="col-md-5">
                                <td>60</td> Seconds:
                            </div>
                            <div class="col-md-5">
                                <div class="form-group{{ $errors->has('time_60') ? ' has-error' : '' }}">
                                    <input type="number" name="time_60" class="form-control" required value="{{ $adslot['60_seconds'] }}">
                                    @if($errors->has('time_60'))
                                        <strong>
                                            <span class="help-block">{{ $errors->first('time_60') }}</span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </p>
                        <p align="center">
                        <div class="row">
                            <div class="col-md-5">
                                <td>45</td> Seconds:
                            </div>
                            <div class="col-md-5">
                                <div class="form-group{{ $errors->has('time_45') ? ' has-error' : '' }}">
                                    <input type="number" name="time_45" class="form-control" required value="{{ $adslot['45_seconds'] }}">
                                    @if($errors->has('time_45'))
                                        <strong>
                                            <span class="help-block">{{ $errors->first('time_45') }}</span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </p>
                        <p align="center">
                        <div class="row">
                            <div class="col-md-5">
                                <td>30</td> Seconds:
                            </div>
                            <div class="col-md-5">
                                <div class="form-group{{ $errors->has('time_30') ? ' has-error' : '' }}">
                                    <input type="number" name="time_30" class="form-control" required value="{{ $adslot['30_seconds'] }}">
                                    @if($errors->has('time_30'))
                                        <strong>
                                            <span class="help-block">{{ $errors->first('time_30') }}</span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </p>
                        <p align="center">
                        <div class="row">
                            <div class="col-md-5">
                                <td>15</td> Seconds:
                            </div>
                            <div class="col-md-5">
                                <div class="form-group{{ $errors->has('time_15') ? ' has-error' : '' }}">
                                    <input type="number" name="time_15" class="form-control" required value="{{ $adslot['15_seconds'] }}">
                                    @if($errors->has('time_15'))
                                        <strong>
                                            <span class="help-block">{{ $errors->first('time_15') }}</span>
                                        </strong>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </p>
                        <p align="center">
                            <button type="button" data-dismiss="modal" class="btn btn-large btn-danger"  style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Cancel</button>
                            <button id="update_adslot" type="submit" class="btn btn-large btn-success" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">Save</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach
@stop

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}

    <script>
        $(document).ready(function(){
            $("body").delegate(".by_region", "click", function(){
                var id = $(".by_region").attr("region_id");
                console.log(id);
            });
        });

        var Datefilter =  $('.adslots').DataTable({
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: '/adslot/adslot-data',
            },
            columns: [
                {data: 's_n', name: 's_n'},
                {data: 'day', name: 'day'},
                {data: 'time_slot', name: 'time_slot'},
                {data: '60_seconds', name: '60_seconds'},
                {data: '45_seconds', name: '45_seconds'},
                {data: '30_seconds', name: '30_seconds'},
                {data: '15_seconds', name: '15_seconds'},
                {data: 'edit', name: 'edit'},
            ]
        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop