@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Create Campaign

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> New Media</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-1 hidden-sm hidden-xs"></div>
            <div class="col-md-9 " style="padding:2%">
                <form class="campform">
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" placeholder="Name">
                        </div>

                        <div class="col-lg-6 col-md-6 hidden-sm hidden-xs"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" placeholder="Brand">
                        </div>
                        <div class="col-md-6">
                            <input type="text" placeholder="Product">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="input-group date styledate">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" placeholder="Start Date" class="form-control pull-right" id="datepicker" ">
                            </div>

                            <div class="input-group date styledate">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" placeholder="End Date" class="form-control pull-right" id="datepickerend">
                            </div>
                        </div>
                    </div>

                    <div class="row" style="margin-top:10%">
                        <div class="col-md-2">
                            <div class="form-group">
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        BreakFast
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        Late Morning
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        Afternoon
                                    </label>
                                </p>

                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        Prime Time
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        Late Night
                                    </label>
                                </p>
                                <p>
                                    <label>
                                        <input type="checkbox" class="minimal-red">
                                        Overnight
                                    </label>
                                </p>

                            </div>
                        </div>
                    </div>

                </form>

            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->




        </div>
        <!-- /.row -->

        <div class="container">

            <p align="right">
                <a href="create-campaign-page3.html"><button class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button></a>

            </p>
    </section>

    <!-- /.content -->

<!-- /.content-wrapper -->

@endsection