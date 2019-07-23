@extends('layouts.ssp.layout')

@section('title')
    <title> Torch | Rate Cards </title>
@stop

@section('content')
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Rate Cards</h2>
            </div>
        </div>

        <!-- campaign details -->
        <div class="the_frame client_dets border_top_color mb4">
            <!-- filter -->
            <div class="filters clearfix">

                <div class="column col_4 date_filter">
                    <a href="" class="active">ALL</a>
                </div>

                <div class="column col_4">
                    <div class="header_search">

                        <input type="text" name="program" id="searchTable" class="key_search" placeholder="Search ...">

                    </div>
                </div>

                <div class="column col_4 clearfix">

                    <div class="col_8 right align_right">
                        @if(Auth::user()->hasPermissionTo('create.rate_card'))
                        <a href="{{ route('rate_card.management.create') }}" class="btn small_btn"><span class="_plus"></span> Create Rate Card</a>
                        @endif
                    </div>

                </div>

            </div>
            <!-- end -->

            <table class="display rate_card">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Revenue</th>
                    <th>15 Seconds</th>
                    <th>30 Seconds</th>
                    <th>45 Seconds</th>
                    <th>60 Seconds</th>
                    @if(Auth::user()->companies()->count() > 1)
                        <th>Station</th>
                    @endif
                    @if(Auth::user()->hasPermissionTo('update.rate_card'))
                        <th>Edit</th>
                    @endif
                </tr>
                </thead>
            </table>

        </div>
    </div><!-- main contain -->

@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    {{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        $(document).ready(function () {
            var can_edit = '<?php echo Auth::user()->hasPermissionTo('update.rate_card') ?>';
            //rate card data table
            var RateCardList =  $('.rate_card').DataTable({
                dom: 'Blfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                aaSorting: [],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                oLanguage: {
                    sLengthMenu: "_MENU_"
                },
                ajax: {
                    url: '/rate-card-management/data-table',
                },
                columns: getColumn(),
            });

            $('.key_search').on('keyup', function(){
                RateCardList.search($(this).val()).draw() ;
            });

            function getColumn() {
                var column = [
                    {data: 'title', name: 'title'},
                    {data: 'revenue', name: 'revenue'},
                    {data: 'price_15', name: 'price_15'},
                    {data: 'price_30', name: 'price_30'},
                    {data: 'price_45', name: 'price_45'},
                    {data: 'price_60', name: 'price_60'},
                ];
                if(can_edit){
                    column.push({data: 'edit', name: 'edit'})
                }
                if(companies > 1){
                    column.push({data: 'station', name: 'station'})
                }
                return column;
            }
        })
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <style>
        .highcharts-grid path { display: none;}
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
