@extends('layouts.new_app')

@section('title')
    <title>All Broadcasters</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">

            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Broadcasters</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Dashboard</a></li>
                        <li><a href="#">All Broadcasters</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <a href="{{ route('broadcaster.register.form') }}" class="btn btn-success btn-lg">Add New Broadcaster</a>
            </div>
            <p><br></p>

            <div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table class="table table-bordered table-striped broadcaster">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Media Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Details</th>
                                            <th>Inventory</th>
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

        var DataCampaign = $('.broadcaster').DataTable({
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: '/admin-broadcaster/broadcaster-data',
                data: function (d) {
                    d.start_date = $('input[name=txtFromDate_tvc]').val();
                    d.stop_date = $('input[name=txtToDate_tvc]').val();
                }
            },
            columns: [
                {data: 's_n', name: 's_n'},
                {data: 'media_name', name: 'media_name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'details', name: 'details'},
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