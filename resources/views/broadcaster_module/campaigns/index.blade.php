@extends('layouts.faya_app')

@section('title')
    <title> FAYA | CAMPAIGNS</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <!-- <h2 class="sub_header">Active Campaigns</h2> -->
                <h2 class="sub_header">Campaigns</h2>
            </div>
        </div>

        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column select_wrap col_2 clearfix">
                    <select name="filter_user" class="filter_user" id="filter_user">
                        <option value="">All Campaigns</option>
                        <option value="agency">Agency Campaigns</option>
                        <option value="broadcaster">Walk-In Campaigns</option>
                    </select>
                </div>
                <div class="column select_wrap col_2 clearfix">
                    <select name="filter_status" class="filter_status" id="filter_status">
                        <option value="">All Statuses</option>
                        <option value="on_hold" @if($status === "on_hold") selected="selected" @endif>On Hold</option>
                        <option value="pending" @if($status === "pending") selected="selected" @endif>Pending</option>
                        <option value="active" @if($status === "active") selected="selected" @endif>Active</option>
                        <option value="expired" @if($status === "expired") selected="selected" @endif>Finished</option>
                    </select>
                </div>

                <div class="column col_3 clearfix">
                    <input type="text" name="key_search" placeholder="Enter Key Word..." class="key_search">
                </div>
                <div class="column col_5 clearfix">
                    <div class="col_5 column">
                        <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
                    </div>

                    <div class="col_5 column">
                        <input type="text" name="stop_date" class="flatpickr" placeholder="End Date">
                    </div>

                    <div class="col_1 column">
                        <button type="button" id="dashboard_filter_campaign" class="btn small_btn">Filter</button>
                    </div>
                </div>
            </div>

            <!-- campaigns table -->
            <table class="display dashboard_campaigns">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Start Date</th>
                    <th>Budget</th>
                    <th>Ad Slots</th>
                    <th>Status</th>
                    @if(Auth::user()->companies()->count() > 1)
                        <th>Station</th>
                    @endif
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
    <script src="https://unpkg.com/flatpickr"></script>
    {{--datatables--}}
    <script>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>

        $(document).ready(function( $ ) {
            flatpickr(".flatpickr", {altInput: true});

            function getColumns() {
                var columns = [
                    {data: 'name', name: 'name'},
                    {data: 'brand', name: 'brand'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'budget', name: 'budget'},
                    {data: 'adslots', name: 'adslots'},
                    {data: 'status', name: 'status'},
                ];
                if(companies > 1){
                    columns.push({data: 'station', name: 'station'});
                }
                return columns;
            }

            var campaignFilter =  $('.dashboard_campaigns').DataTable({
                dom: 'Blfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                aaSorting: [],
                aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                oLanguage: {
                    sLengthMenu: "_MENU_"
                },
                ajax: {
                    // url: '/campaign/all-active-campaigns/data',
                    url: '/campaign/all-campaigns/data',
                    data: function (d) {
                        d.start_date = $('input[name=start_date]').val();
                        d.stop_date = $('input[name=stop_date]').val();
                        d.filter_user = $('#filter_user').val();
                        d.status = $('#filter_status').val();
                    }
                },
                columns: getColumns(),
            });

            $('#dashboard_filter_campaign').on('click', function() {
                campaignFilter.draw();
            });

            $('.key_search').on('keyup', function(){
                campaignFilter.search($(this).val()).draw() ;
            });

            $('#filter_user').on('change', function() {
                campaignFilter.draw();
            });
        } );
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <style>
        .highcharts-grid path { display: none;}

        .highcharts-legend {
            display: none;
        }

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

            -webkit-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
            -moz-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
            box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);

            position: relative;
            display: inline-block;
            text-transform: uppercase;
        !important;

        }
    </style>
@stop
