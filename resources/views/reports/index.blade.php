@extends('layouts.app')

@section('content')

@section('title', trans('app.reports'))

<section class="content-header">
    <h1>
        Ad Management <small>Discount</small>

    </h1>
    <ol class="breadcrumb" style="font-size: 16px">

        <li><a href="#"><i class="fa fa-edit"></i> Reports</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> Generate Reports</a> </li>

    </ol>
</section>

<!-- Main content -->

<section class="content">
    <div class="row">
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <div class="col-md-8 Campaign" style="padding:2%"></div>
        <!-- /.col -->
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <!-- /.col -->

        <div class="row" style="padding: 5%">
            <div class="col-xs-12">

                <div class="col-md-11">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" style="background:#eee">
                            <li class="active"><a href="#agency" data-toggle="tab">Agency</a></li>
                            <li ><a href="#brand" data-toggle="tab">Campaigns</a></li>
                            <li><a href="#time" data-toggle="tab">Revenues</a></li>
                            <li><a href="#daypart" data-toggle="tab">Playtimes</a></li>
                            <li><a href="#price" data-toggle="tab">Compliance</a></li>
                            <li>
                                <a href="#pslot" data-toggle="tab">
                                    <i class="fa fa-plus-circle"></i>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div class="active tab-pane" id="agency">

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
                                            <input type="submit" class="search-btn" value="Apply" style="float:left" >
                                        </div>
                                    </div>
                                </div>

                                <div class="row" style="padding: 5%">
                                    <div class="col-xs-12">
                                        <div class="box">
                                            <div class="box-body">
                                                <table id="example1" class="table table-bordered table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Name</th>
                                                            <th>Owner</th>
                                                            <th>Duration</th>
                                                            <th>Revenue</th>
                                                            <th>Playtimes</th>
                                                            <th>Compliance</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                    <tfoot></tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="brand">

                            </div>

                            <div class="tab-pane" id="time">

                            </div>

                            <div class="tab-pane" id="daypart">



                            </div>

                            <div class="tab-pane" id="price">

                            </div>

                            <div class="tab-pane" id="pslot">


                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /.col -->

            </div>
        </div>
    </div>

</section>


@stop

@section('scripts')

@stop