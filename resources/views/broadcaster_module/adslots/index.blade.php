@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Adslots</title>
@stop

@section('content')

    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')

    <div class="main_contain">
        {{--Header--}}
    @include('partials.new-frontend.broadcaster.header')
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Ad Slots</h2>
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

                        <input type="text" name="day" id="searchTable" placeholder="Search...">

                    </div>
                </div>

                <div class="column col_4 clearfix">

                    <div class="col_8 right align_right">
                        <a href="{{ route('adslot.create') }}" class="btn small_btn"><span class="_plus"></span> Create Ad Slots</a>
                    </div>

                    <div class="right col_4">
                        <div class="select_wrap">
                            <select>
                                <option>Filter</option>
                                <option>This Month</option>
                            </select>
                        </div>
                    </div>

                </div>

            </div>
            <!-- end -->

            <table class="adslots">
                <thead>
                <tr>
                    <th>Day</th>
                    <th>Time Slots</th>
                    <th>60 Seconds</th>
                    <th>45 Seconds</th>
                    <th>30 Seconds</th>
                    <th>15 Seconds</th>
                    <th></th>
                </tr>
                </thead>
            </table>

        </div>

        <!-- edt ad slot modal -->
        @foreach($adslots as $adslot)
            <div class="modal_contain modal_click" id="edit_slot{{ $adslot['id'] }}">
            <h2 class="sub_header mb4">Edit Ad Slot | {{ $adslot['time_slot'] }} | {{ $adslot['day'] }}</h2>

            @if ($adslot['percentage'] != 0)
                <p class="small_faint weight_medium">
                <h4>You have a premium percent of {{ $adslot['percentage'] }}% on this slot</h4>
                </p>
            @endif

            <form method="post" action="{{ route('adslot.update', ['adslot' => $adslot['id']]) }}" id="adslot_update{{ $adslot['id'] }}">
                {{ csrf_field() }}

                <div class="input_wrap">
                    <label class="small_faint weight_medium">Premium Percentage</label>
                    <input type="number" name="premium_percent" id="premium_percent{{ $adslot['id'] }}" value="" placeholder="Enter Percentage Value %" />
                </div>

                <div class="clearfix">
                    <div class="input_wrap column col_6">
                        <label class="small_faint weight_medium">60 Seconds (&#8358;)</label>
                        <input type="number" name="time_60" id="time_60{{ $adslot['id'] }}" value="{{ $adslot['60_seconds'] }}" placeholder="Enter Price" />
                    </div>

                    <div class="input_wrap column col_6">
                        <label class="small_faint weight_medium">45 Seconds (&#8358;)</label>
                        <input type="number" name="time_45" id="time_45{{ $adslot['id'] }}" value="{{ $adslot['45_seconds'] }}" placeholder="Enter Price" />
                    </div>

                </div>

                <div class="clearfix">
                    <div class="input_wrap column col_6">
                        <label class="small_faint weight_medium">30 Seconds (&#8358;)</label>
                        <input type="number" name="time_30" id="time_30{{ $adslot['id'] }}" value="{{ $adslot['30_seconds'] }}" placeholder="Enter Price" />
                    </div>

                    <div class="input_wrap column col_6">
                        <label class="small_faint weight_medium">15 Seconds (&#8358;)</label>
                        <input type="number" name="time_15" id="time_15{{ $adslot['id'] }}" value="{{ $adslot['15_seconds'] }}" placeholder="Enter Price" />
                    </div>
                </div>


                <div class="align_right pt3">
                    <a href="" class="padd color_initial light_font simplemodal-close">Cancel</a>
                    <button type="button" data-adslot_id="{{ $adslot['id'] }}" class="btn uppercased update_slot">Update Ad Slot</button>
                </div>

            </form>
        </div>
    @endforeach
        <!-- end ad slot modal -->


    </div><!-- main contain -->
@stop

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    {{--datatables--}}
    <script>
        $(document).ready(function( $ ) {
            $("body").delegate(".modal_click", "click", function() {
                var href = $(this).attr("href");
                $(href).modal();
                return false;
            });

            $(".update_slot").click(function () {
                var adslot_id = $(this).data("adslot_id");
                var premium_percent = $("#premium_percent"+adslot_id).val();
                var time_60 = $("#time_60"+adslot_id).val();
                var time_45 = $("#time_45"+adslot_id).val();
                var time_30 = $("#time_30"+adslot_id).val();
                var time_15 = $("#time_15"+adslot_id).val();
                var url = $("#adslot_update"+adslot_id).attr("action");
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        premium_percent: premium_percent,
                        time_60: time_60,
                        time_45: time_45,
                        time_30: time_30,
                        time_15: time_15,
                        '_token': $('input[name=_token]').val()
                    },
                    success: function (data) {
                        if(data.success === "prices_update"){
                            toastr.success("Prices updated for this slot");
                            location.reload();
                        }
                        if(data.error_no_changes === "no_changes"){
                            toastr.info("No changes made");
                        }
                        if(data.success_price === "prices_update"){
                            toastr.success("Percentage price deleted for this slot");
                            location.reload();
                        }
                        if(data.error_percentage === "error_percentage"){
                            toastr.error("You cannot apply this percentage");
                        }
                        if(data.success_percentage === "percentage_applied"){
                            toastr.success("Percentage applied to prices successfully...");
                            location.reload();
                        }
                        if(data.error_apply_percentage === "error_applying_percentage"){
                            toastr.error("Error applying percentage to price");
                        }
                        if(data.success_update_new_percentage === "price_update_new_percentage"){
                            toastr.success("Prices updated with the new percentage...");
                            location.reload();
                        }
                        if(data.error_updating_percentage_price === "error_updating_percentage_price")
                        {
                            toastr.error("Error updating price with the new percentage...");
                        }
                    }

                })
                // console.log(premium_percent, time_60, time_45, time_30, time_15, url, adslot_id);

            });

            var Datefilter =  $('.adslots').DataTable({
                dom: 'Bfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                "searching": false,
                aaSorting: [],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                ajax: {
                    url: '/adslot/data',
                    data: function (d) {
                        d.days = $('input[name=day]').val();
                    }
                },
                columns: [
                    {data: 'day', name: 'day'},
                    {data: 'time_slot', name: 'time_slot'},
                    {data: '60_seconds', name: '60_seconds'},
                    {data: '45_seconds', name: '45_seconds'},
                    {data: '30_seconds', name: '30_seconds'},
                    {data: '15_seconds', name: '15_seconds'},
                    {data: 'edit', name: 'edit'}
                ],
            });

            $('#searchTable').on( 'keyup', function () {
                Datefilter.search( this.value ).draw();
            });

        } );
    </script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <style>

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