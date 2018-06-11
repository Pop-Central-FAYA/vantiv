@extends('layouts.new_app')

@section('title')
    <title>Day Parts</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">

            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Day Parts</h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Dashboard</a></li>
                        <li><a href="#">Day Parts</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <a href="{{ route('admin.daypart.create') }}" class="btn btn-success btn-lg">Add Day Part</a>
            </div>
            <p><br></p>

            <div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table class="table table-bordered table-striped day_part">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Day Parts</th>
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

    @foreach($dayparts as $daypart)
        <div class="modal fade edit{{ $daypart->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <p>Day Part </p><br>
                        <h4>Edit : {{ $daypart->day_parts }}</h4>
                    </div>
                    <form action="{{ route('admin.daypart.update', ['id' => $daypart->id]) }}" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="group">
                                    <input type="text" name="day_part" value="{{ $daypart->day_parts }}" class="form-control">
                                    <p><br></p>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update Day Part</button>
                            <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade delete{{ $daypart->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>Delete : {{ $daypart->day_parts }}</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to proceed?</p>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('admin.daypart.delete', ['id' => $daypart->id]) }}" class="btn btn-danger">Delete</a>
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

        var DataCampaign = $('.day_part').DataTable({
            dom: 'Bfrtip',
            paging: true,
            serverSide: true,
            processing: true,
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ],
            ajax: {
                url: '/day-parts/data',
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