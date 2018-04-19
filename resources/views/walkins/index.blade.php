@extends('layouts.new_app')

@section('title')
    <title>All Walkins</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">

            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Walk-Ins</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Walk-Ins</a></li>
                        <li><a href="#">All Walk-Ins</a></li>
                    </ul>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="nav-tabs-custom">
                        <div class="tab-content">
                            <div class="active tab-pane" id="all">

                                <div class="box-body">
                                    <table class="table table-bordered table-striped campaign">
                                        <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone Number</th>
                                            <th>Campaign</th>
                                            <th>Delete</th>
                                        </tr>
                                        </thead>
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

    @foreach($walkins as $walkin)
        <div class="modal fade deleteModal{{ $walkin->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="text-center">Are you sure you want to delete?</h2><br>
                    </div>

                    <div class="modal-body">
                        <h5>
                            <b style="color: red">Warning!!!</b>
                            Deleting this means you might not be able to fully undo this oeration
                        </h5>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-large btn-danger" data-dismiss="modal" style="color:white; font-size: 20px; padding: 0.5% 3%; margin-top:4%; border-radius: 10px;">
                            Cancel
                        </button>
                        <a href="{{ route('walkins.delete', ['id' => $walkin->id]) }}" type="submit"
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

        var DataCampaign = $('.campaign').DataTable({
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: '/walk-in/all-walk-in/data',
                data: function (d) {
                    d.start_date = $('input[name=txtFromDate_tvc]').val();
                    d.stop_date = $('input[name=txtToDate_tvc]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'full_name', name: 'full_name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'campaign', name: 'campaign'},
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