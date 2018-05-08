@extends('layouts.new_app')

@section('title')
    <title>All Industries</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">

            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Industries</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Dashboard</a></li>
                        <li><a href="#">All Industries</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <a href="{{ route('industry.create') }}" class="btn btn-success btn-lg">Add New Industry</a>
            </div>
            <p><br></p>

            <div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table class="table table-bordered table-striped industry">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Industry</th>
                                            <th>Sector Code</th>
                                            <th>Edit</th>
                                            <th>Delete</th>
                                        </tr>
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

    @foreach($industries as $industry)
        <div class="modal fade deleteModal{{ $industry->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="text-center">Are you sure you want to delete <b>{{ $industry->name }}</b>?</h2><br>
                    </div>

                    <div class="modal-body">
                        <h5>
                            <b style="color: red">Warning!!!</b>
                            Deleting this means you might not be able to fully undo this operation
                        </h5>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-large btn-danger" data-dismiss="modal" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">
                            Cancel
                        </button>
                        <a href="{{ route('industry.delete', ['id' => $industry->id]) }}" type="submit"
                           class="btn btn-large btn-success" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">
                            Delete
                        </a>
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
        $(function () {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });

        var DataCampaign = $('.industry').DataTable({
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: '/industry/get-data',
                data: function (d) {
                    d.start_date = $('input[name=txtFromDate_tvc]').val();
                    d.stop_date = $('input[name=txtToDate_tvc]').val();
                }
            },
            columns: [
                {data: 's_n', name: 's_n'},
                {data: 'name', name: 'name'},
                {data: 'sector_code', name: 'sector_code'},
                {data: 'edit', name: 'edit'},
                {data: 'delete', name: 'name'}
            ]
        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
@stop