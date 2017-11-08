@extends('layouts.app')

@section('content')

        <!-- Content Header (Page header) -->
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Create Campaign

            </h1>
            <ol class="breadcrumb">
                <li><a href="index.html"><i class="fa fa-dashboard"></i> Dashboard</a> </li>
                <li><a href="#"><i class="fa fa-th"></i> Create Campaign</a> </li>

            </ol>
        </section>

        <!-- Main content -->

        <section class="content">
            <div class="row">
                <div class="col-md-3">
                    <div class="box box-solid">
                        <div class="box-header with-border">
                            <h4 class="box-title">Campaign Info</h4>
                        </div>
                        <div class="box-body">
                            <!-- the events -->
                            <div id="external-events">
                                <div class="external-event bg-green">Break Fast</div>
                                <div class="external-event bg-yellow">Late Morning</div>
                                <div class="external-event bg-aqua">Afternoon</div>
                                <div class="external-event bg-light-blue">Primetime</div>
                                <div class="external-event bg-red">Overnight</div>
                                <div class="checkbox">

                                </div>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /. box -->

                </div>
                <!-- /.col -->
                <div class="col-md-7">
                    <div class="box box-primary">
                        <div class="box-body no-padding">
                            <!-- THE CALENDAR -->
                            <div id="calendar"></div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /. box -->
                </div>
                <!-- /.col -->
                <div class="col-lg-2 col-md-2 hidden-sm hidden-xs "></div>



            </div>
            <!-- /.row -->

            <div class="container">

                <p align="right">
                    <a href="create-campaign-page2.html"><button class="btn campaign-button" style="margin-right:15%">Next <i class="fa fa-play" aria-hidden="true"></i></button></a>

                </p>
        </section>

        <!-- /.content -->

@stop