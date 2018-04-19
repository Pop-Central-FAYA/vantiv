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
                            <li class="active"><a href="#all">All Adslots</a></li>
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
                                            @if(Session::get('broadcaster_id'))
                                                <th>Action</th>
                                            @endif
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
            <hr>
            <div class="row">
                <div class="col-12">

                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">
                                <h3>Position Management</h3>
                                <p><br></p>
                                <a href="{{ route('position.create') }}" class="btn btn-success btn-lg">Add Position</a>
                                <p><br></p>
                                <div class="box-body">
                                    <table id="example1" class="table table-bordered table-striped all_campaign">
                                        <thead>
                                        <th>S/N</th>
                                        <th>Position</th>
                                        <th>Surge</th>
                                        @if(Session::get('broadcaster_id'))
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        @endif
                                        </thead>
                                        <tbody>
                                        @foreach($all_positions as $all_position)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ ucfirst($all_position->position) }}</td>
                                                <td>{{ $all_position->percentage }}</td>
                                                @if(Session::get('broadcaster_id'))
                                                    <td><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#edit{{ $all_position->id }}">Edit</button></td>
                                                    <td><button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#delete{{ $all_position->id }}">Delete</button></td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
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
                                <h4>You have a premium percent of {{ $adslot['percentage'] }}% on this slot</h4>
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
    @foreach ($all_positions as $all_position)
        {{--edit modal--}}
        <div class="modal fade" id="edit{{ $all_position->id }}" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Edit: {{ $all_position->position }}</h4>
                    </div>
                    <form action="{{ route('position.update', ['id' => $all_position->id]) }}" method="POST">
                        {{ csrf_field()}}
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="position">Position</label>
                                    <input type="text" name="position" class="form-control" value="{{ $all_position->position }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="percentage">Percentage</label>
                                    <input type="text" name="percentage" class="form-control" value="{{ $all_position->percentage }}">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update</button>
                            <button class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{--delete modal--}}
        <div class="modal fade" id="delete{{ $all_position->id }}" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="myModalLabel">Delete: {{ $all_position->position }}</h4>
                    </div>
                    <div class="modal-body">
                        <p><h3>Are you sure you want to delete this position?</h3></p>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('position.delete', ['id' => $all_position->id]) }}" class="btn btn-danger">Delete</a>
                        <button class="btn btn-default" data-dismiss="modal">Close</button>
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

        var broadcaster_id = "<?php echo Session::get('broadcaster_id') ?>";
        if(broadcaster_id){
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
        }else{
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
                ]
            });
        }

    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop