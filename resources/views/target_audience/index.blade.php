@extends('layouts.new_app')

@section('title')
    <title>Target Audience</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">

            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Target Audience</h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Dashboard</a></li>
                        <li><a href="#">Target Audience</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <a href="{{ route('admin.target_audience.create') }}" class="btn btn-success btn-lg">Add Target Audience</a>
            </div>
            <p><br></p>

            <div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table class="table table-bordered table-striped target_audience">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Audience</th>
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

    @foreach($target_audiences as $target_audience)
        <div class="modal fade edit{{ $target_audience->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>Edit : {{ $target_audience->audience }}</h4>
                    </div>
                    <form action="" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="group">
                                    <input type="text" name="brand_name" value="{{ $target_audience->audience }}" class="form-control">
                                    <p><br></p>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update Brand</button>
                            <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade delete{{ $target_audience->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>Delete : {{ $target_audience->audience }}</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to proceed?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="" class="btn btn-danger">Delete</a>
                        <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach

@stop

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
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

        var DataCampaign = $('.target_audience').DataTable({
            dom: 'Bfrtip',
            paging: true,
            serverSide: true,
            processing: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            ajax: {
                url: '/target-audiences/data',
                data: function (d) {
                    d.start_date = $('input[name=txtFromDate_tvc]').val();
                    d.stop_date = $('input[name=txtToDate_tvc]').val();
                }
            },
            columns: [
                {data: 's_n', name: 's_n'},
                {data: 'name', name: 'name'},
                {data: 'edit', name: 'edit'},
                {data: 'delete', name: 'delete'}
            ]
        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
@stop