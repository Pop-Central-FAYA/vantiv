@extends('dsp_layouts.faya_app')

@section('title')
    <title> FAYA | Dashboard</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Media Plans</h2>
            </div>
        </div>

        <!-- Media Plan stats -->
        @if(Auth::user()->hasRole('media_planner') && Auth::user()->hasRole('finance') && Auth::user()->hasRole('admin'))
        <div class="the_stats the_frame clearfix mb4">
            <div class="column col_4">
                <span class="weight_medium small_faint uppercased">Approved Media Plans</span>
                <h3><a href="#">{{ $count_approved_media_plans }}</a></h3>
            </div>

            <div class="column col_4">
                <span class="weight_medium small_faint uppercased">Pending Media Plans</span>
                <h3><a href="#" style="color: red;">{{ $count_pending_media_plans }}</a></h3>
            </div>

            <div class="column col_4">
                <span class="weight_medium small_faint uppercased">Declined Media Plans</span>
                <h3><a href="#" style="color: red;">{{ $count_declined_media_plans }}</a></h3>
            </div>
        </div>
        @endif


        <!-- MEDIA PLANS -->
        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_4 p-t">
                    <p class="uppercased weight_medium">All Media Plans</p>
                </div>
                <div class="column col_4 clearfix">
                    <input type="text" name="key_search" placeholder="Enter Key Word..." class="key_search">
                </div>
                <div class="column col_4 clearfix">
                   <!--  <div class="col_5 column">
                        <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
                    </div>

                    <div class="col_5 column">
                        <input type="text" name="stop_date" class="flatpickr" placeholder="End Date">
                    </div> -->

                    <div class="col_1 column">
                        <button type="button" id="dashboard_filter_campaign" class="btn small_btn">Filter</button>
                    </div>
                </div>

            </div>

            <!-- media plans table -->
            <table class="display dashboard_campaigns">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Media Type</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <!-- <th>Budget</th> -->
                    <th>Status</th>
                </tr>
                </thead>
            </table>
            <!-- end -->
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    {{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/fixedheader/3.1.2/js/dataTables.fixedHeader.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    {{--datatables--}}
    <script>
        $(document).ready(function( $ ) {
            flatpickr(".flatpickr", {
                altInput: true,
            });
            var Datefilter =  $('.dashboard_campaigns').DataTable({
                dom: 'Blfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                lengthMenu : [[10, 25, 50, -1], [10, 25, 50, "All"]],
                oLanguage: { sLengthMenu: "_MENU_", },
                aaSorting: [],
                ajax: {
                    url: '/media-plan/dashboard/list',
                    data: function (d) {
                        d.start_date = $('input[name=start_date]').val();
                        d.stop_date = $('input[name=stop_date]').val();
                    }
                },
                columns: [
                    {data: 'campaign_name', name: 'name'},
                    {data: 'media_type', name: 'media_type'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    // {data: 'budget', name: 'budget'},
                    {data: 'status', name: 'status'},
                ],
            });

            $('#dashboard_filter_campaign').on('click', function() {
                Datefilter.draw();
            });

            $('.key_search').on('keyup', function(){
                Datefilter.search($(this).val()).draw() ;
            });

        } );
    </script>
@stop

@section('styles')
    {{--<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>--}}
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/fixedheader/3.1.2/css/fixedHeader.dataTables.min.css">
    <style>
        .dataTables_filter {
            display: none;
        }

        #DataTables_Table_0_wrapper .dt-buttons button {
            line-height: 2.5;
            color: #fff;
            cursor: pointer;
            background: #44C1C9;
            -webkit-appearance: none;
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            border: 0;
            padding: 3px 20px 0;
            font-size: 14px;

            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;

            -webkit-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
            -moz-box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);
            box-shadow: 9px 10px 20px 1px rgba(0,159,160,0.21);

            position: relative;
            display: inline-block;
            text-transform: uppercase;
            !important;
        }
    </style>
@stop
