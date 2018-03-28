@extends('layouts.new_app')

@section('title')
    <title>Broadcaster Users</title>
@endsection


@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Users</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Broadcaster</a></li>
                        <li><a href="#">Users</a></li>
                    </ul>
                </div>

                <div class="col-12">

                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table id="example1" class="table table-bordered table-striped all_user">
                                        <thead>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Action</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($all_users as $all_user)
        <div class="modal fade" id="myModal{{ $all_user->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            Are You Sure you want to delete {{ $all_user->firstname.' '.$all_user->lastname }}</strong>
                        </h4>
                    </div>

                    <div class="modal-body">

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
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

    <script>
        var Datefilter =  $('.all_user').DataTable({
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: '/broadcaster/user-data',
            },
            columns: [
                {data: 's_n', name: 's_n'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'delete', name: 'delete'}
            ]
        });
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop