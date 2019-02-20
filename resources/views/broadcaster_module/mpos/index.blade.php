@extends('layouts.faya_app')

@section('title')
    <title> FAYA | MPO'S</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">All MPOS</h2>
            </div>

            @if(Auth::user()->companies()->count() > 1)
                <div class="column col_6">
                    <select class="publishers" name="companies[]" id="publishers" multiple="multiple" >
                        @foreach(Auth::user()->companies as $company)
                            <option value="{{ $company->id }}"
                                    @foreach($companies_id as $company_id)
                                        @if($company_id->company_id == $company->id)
                                            selected
                                        @endif
                                    @endforeach
                            >{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_8 p-t">
                    <p class="uppercased weight_medium">All MPOS</p>
                </div>
                <div class="column col_4 clearfix">
                    <div class="col_5 column">
                        <input type="text" name="start_date" class="flatpickr" placeholder="Start Date">
                    </div>

                    <div class="col_5 column">
                        <input type="text" name="stop_date" class="flatpickr" placeholder="End Date">
                    </div>

                    <div class="col_1 column">
                        <button type="button" id="mpo_filters" class="btn small_btn">Filter</button>
                    </div>
                </div>
            </div>

            <!-- campaigns table -->
            <table class="display default_mpo filter_mpo" id="default_mpo_table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Date Created</th>
                    <th>Budget</th>
                    <th>Status</th>
                    @if(Auth::user()->companies()->count() > 1)
                        <th>Stations</th>
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
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script src="https://unpkg.com/flatpickr"></script>
    {{--datatables--}}
    <script>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        $(document).ready(function( $ ) {
            if(companies > 1){
                $('.publishers').select2();
                $('body').delegate("#publishers", "change", function () {
                    $(".default_mpo").dataTable().fnDestroy();
                    var channels = $("#publishers").val();
                    if(channels != null){
                        $('.when_loading').css({
                            opacity: 0.1
                        });
                        var Datefilter =  $('.filter_mpo').DataTable({
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
                                url: '/mpo/filter',
                                data: function (d) {
                                    d.channel_id = channels;
                                    d.start_date = $('input[name=start_date]').val();
                                    d.stop_date = $('input[name=stop_date]').val();
                                }
                            },
                            columns: getColumns(),
                        });
                        Datefilter.draw();
                        $('#mpo_filters').on('click', function() {
                            Datefilter.draw();
                        });
                    }
                })
            }

            flatpickr(".flatpickr", {
                altInput: true,
            });

            $(".filter_mpo").dataTable().fnDestroy();

            var Datefilter =  $('.default_mpo').DataTable({
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
                    url: '/mpos/all-data',
                    data: function (d) {
                        d.start_date = $('input[name=start_date]').val();
                        d.stop_date = $('input[name=stop_date]').val();
                    }
                },
                columns: getColumns(),
            });

            $('#mpo_filters').on('click', function() {
                Datefilter.draw();
            });

            function getColumns()
            {
                if(companies > 1){
                    return [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'brand', name: 'brand'},
                        {data: 'date_created', name: 'date_created'},
                        {data: 'budget', name: 'budget'},
                        {data: 'status', name: 'status'},
                        {data: 'station', name: 'station'}
                    ]
                }else{
                    return [
                        {data: 'id', name: 'id'},
                        {data: 'name', name: 'name'},
                        {data: 'brand', name: 'brand'},
                        {data: 'date_created', name: 'date_created'},
                        {data: 'budget', name: 'budget'},
                        {data: 'status', name: 'status'},
                    ]
                }
            }
        } );
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
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
