@extends('layouts.new_app')

@section('title')
    <title>File Position</title>
@endsection


@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>File Position List</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Broadcaster</a></li>
                        <li><a href="#">File Position</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <a href="{{ route('position.create') }}" class="btn btn-success btn-lg">Add Position</a>
                </div>
            </div>
            <p><br></p>

            <div class="row">
                <div class="col-12">

                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table id="example1" class="table table-bordered table-striped all_campaign">
                                        <thead>
                                            <th>S/N</th>
                                            <th>Position</th>
                                            <th>Percentage</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </thead>
                                        <tbody>
                                            @foreach($all_positions as $all_position)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $all_position->position }}</td>
                                                    <td>{{ $all_position->percentage }}</td>
                                                    <td><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#edit{{ $all_position->id }}">Edit</button></td>
                                                    <td><button type="button" class="btn btn-danger btn-xs" data-toggle="modal" data-target="#delete{{ $all_position->id }}">Delete</button></td>
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

@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}


@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop