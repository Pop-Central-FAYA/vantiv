@extends('layouts.app')

@section('content')

    <!-- Content Header (Page header) -->
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            All Campaigns

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> All Campaigns</a> </li>

        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <div class="col-md-8 Campaign" style="padding:2%">
                <div class="row">
                    <h4 style="margin-left: 17px;font-weight: bold">Search</h4>
                    <div class="col-md-12" style="margin-top: -2%">
                        <div class="input-group date styledate" style="width:30% !important">
                            <input type="text" placeholder="Search here..." class="form-control pull-right"  >
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
                    <div class="col-md-11">
                        <div class="nav-tabs-custom">
                            <div class="tab-content">
                                <div class="active tab-pane" id="all">
                                    <!-- Post -->
                                    <!-- /.post -->
                                    <div class="box-body">
                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Name</th>
                                                <th>Brand</th>
                                                <th>Product</th>
                                                <th>Start Date</th>
                                                <th>End Date</th>
                                                <th>Adslots</th>
                                                <th>Compliance</th>
                                                <th>Status</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($campaign as $campaigns)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $campaigns->name }}</td>
                                                        <td></td>
                                                        <td>{{ $campaigns->product }}</td>
                                                        <td>{{ date('d-m-Y', $campaigns->start_date) }}</td>
                                                        <td>{{ date('d-m-Y', $campaigns->stop_date) }}</td>
                                                        <td>{{ count($campaigns->file) }}</td>
                                                        <td></td>
                                                        <td>
                                                            @if($campaigns->stop_date > $campaigns->start_date)
                                                                Running
                                                            @else
                                                                Complete
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                            </tfoot>
                                        </table>
                                        {{--{!! with(new Vanguard\Pagination\HDPresenter($adslot))->render() !!}--}}
                                    </div>
                                </div>
                                <!-- /.tab-pane -->
                                <div class="tab-pane" id="nc">

                                </div>
                                <!-- /.tab-pane -->

                                <div class="tab-pane" id="ne">

                                </div>

                                <div class="tab-pane" id="nw">

                                </div>

                                <div class="tab-pane" id="se">

                                </div>

                                <div class="tab-pane" id="ss">

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
    </section>

@endsection
@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    <script>


        $(document).ready(function(){
            $("#txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $("#txtToDate").datepicker({
                numberOfMonths: 2,
                onSelect: function(selected) {
                    $("#txtFromDate").datepicker("option","maxDate", selected)
                }
            });

        });
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop