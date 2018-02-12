@extends('layouts.app')

@section('title', trans('app.mpo'))

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('asset/plugins/datepicker/datepicker3.css') }}" />

@endsection


@section('content')

    <section class="content-header">
        <h1>
            All Media Purchase orders
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> MPOs</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> All MPOs</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">

            <div class="col-md-2 hidden-sm hidden-xs"></div>

            <div class="col-md-8 Campaign" style="padding:2%">

                <div class="row">
                    <h4 style="margin-left: 17px;font-weight: bold">Search by date</h4>
                    <div class="col-md-10" style="margin-top: -2%">
                        <div class="input-group date styledate" style="width:30% !important">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" placeholder="Start Date" class="form-control pull-right" id="datepicker">
                        </div>

                        <div class="input-group date styledate" style="width:30% !important">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" placeholder="End Date" class="form-control pull-right" id="datepickerend" >
                        </div>
                        <div class="input-group" style="">
                            <input type="submit" class="search-btn" value="search" style="float:left" >
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-2 hidden-sm hidden-xs"></div>

            <div class="row" style="padding: 5%">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">All MPOs</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            @if(count($mpo_data) === 0)

                                <h4>OOPs!!!, You have mpos on your system, please create one</h4>

                            @else
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Product</th>
                                        <th>Submitted</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($mpo_data as $mpo)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $mpo['name'] }}</td>
                                            <td>{{ $mpo['brand'] }}</td>
                                            <td>{{ $mpo['product'] }}</td>
                                            <td>{{ date('Y-m-d', strtotime($mpo['time_created'])) }}</td>
                                            <td>&#8358; {{ $mpo['amount'] }}</td>
                                            <td>
                                                @if ($mpo['is_mpo_accepted'] == 1)
                                                    <label style="font-size: 16px" class="label label-success">
                                                        Approved
                                                    </label>
                                                @elseif ($mpo['is_mpo_accepted'] == 0)
                                                    <label style="font-size: 16px" class="label label-warning">
                                                        Pending
                                                    </label>
                                                @elseif ($mpo['is_mpo_accepted'] == 2)
                                                    <label style="font-size: 16px" class="label label-danger">
                                                        Rejected
                                                    </label>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                    <tfoot>

                                    </tfoot>
                                </table>
                            @endif
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            </div>
        </div>
    </section>

@stop


@section('scripts')

    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

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


    </script>

@stop