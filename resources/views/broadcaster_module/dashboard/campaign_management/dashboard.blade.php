@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Dashboard </title>
@stop

@section('content')
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Dashboard</h2>
            </div>
            @if(Auth::user()->companies()->count() > 1)
                <div class="column col_6">
                    <select class="publishers" name="companies[]" id="publishers" multiple="multiple" >
                        @foreach(Auth::user()->companies as $company)
                            <option value="{{ $company->id }}"
                                    selected
                            >{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

    {{--sidebar--}}
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        <!-- main stats -->
        <div class="the_stats the_frame clearfix mb4">
            <div class="column col_3" id="campaign_count">
                <span class="weight_medium small_faint uppercased">Active Campaigns</span>
                <h3><a href="{{ route('campaign.all') }}">{{ count($active_campaigns) }}</a></h3>
            </div>
            <div class="column col_3" id="filtered_campaign_count" style="display: none;">

            </div>
            <div class="column col_2" id="walkin_count">
                <span class="weight_medium small_faint uppercased">All Walk-Ins</span>
                <h3><a href="{{ route('walkins.all') }}">{{ count($walkins) }}</a></h3>
            </div>
            <div class="column col_2" id="filtered_walkin_count" style="display: none;">

            </div>
            <div class="column col_2" id="pending_mpo_count">
                <span class="weight_medium small_faint uppercased">Pending MPO's</span>
                <h3><a href="{{ route('pending-mpos') }}" style="color: red;">{{ count($pending_mpos) }}</a></h3>
            </div>
            <div class="column col_2" id="filtered_mpo_count" style="display: none;">

            </div>
            <div class="column col_2" id="brand_count">
                <span class="weight_medium small_faint uppercased">All Brands</span>
                <h3><a href="{{ route('brand.all') }}">{{ count($brands) }}</a></h3>
            </div>
            <div class="column col_2" id="filtered_brand_count" style="display: none;">

            </div>
            <div class="column col_3" id="campaign_on_hold">
                <span class="weight_medium small_faint uppercased">Campaigns on hold</span>
                <h3><a href="{{ route('broadcaster.campaign.hold') }}" style="color: red;">{{ count($campaign_on_hold) }}</a></h3>
            </div>
            <div class="column col_3" id="filtered_campaign_on_hold" style="display: none;">

            </div>
        </div>

        <!-- channel summary -->
        @if(Auth::user()->companies()->count() > 1)
            @include('broadcaster_module.dashboard.includes.company_channels')
        @endif

        <!-- client charts -->
        @if(Auth::user()->companies()->count() > 1)
            <div class="clearfix">
                <h3><p>Periodic Revenue Chart</p></h3>
                <br>
                {{--Periodic revenue chart goes here--}}
                <div id="periodicChart" style="min-width: 310px; height: 400px; margin: 0 auto">
            </div>
        @else
            <div class="clearfix">
                <h4><p>Total Volume of campaign</p></h4>
                <br>
                {{--Total Volume of campaigns graph goes here--}}
                <div id="containerTotal" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
            </div>
        @endif


        <p><br></p>

        <div class="the_frame client_dets mb4">

            <div class="filters border_bottom clearfix">
                <div class="column col_2 p-t">
                    <p class="uppercased weight_medium">All Campaigns</p>
                </div>
                <div class="column select_wrap col_3 clearfix">
                    <select name="filter_user" class="filter_user" id="filter_user">
                        <option value="">All Campaigns</option>
                        <option value="agency">Agency Campaigns</option>
                        <option value="broadcaster">Walk-In Campaigns</option>
                    </select>
                </div>
                <div class="column col_3 clearfix">
                    <input type="text" name="key_search" placeholder="Enter Key Word..." class="key_search">
                </div>
                <div class="column col_4 clearfix">
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
            <table class="display dashboard_campaigns dashboard_campaigns_filtered">
                <thead>
                <tr>
                    <th>ID</th>
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
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

    <script>
        <?php echo "var channels_with_details =".json_encode($user_channel_with_other_details). ";\n";?>
        //Bar chart for Total Volume of Campaigns
        <?php echo "var campaign_volume = ".$volume . ";\n"; ?>
        <?php echo "var campaign_month = ".$month . ";\n"; ?>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        <?php echo "var months =".json_encode($periodic_revenues['month_list']).";\n"; ?>
        <?php echo "var periodic_revenue_data =".json_encode($periodic_revenues['formated_periodic_revenue_chart']).";\n"; ?>

        $(document).ready(function( $ ) {

            flatpickr(".flatpickr", {
                altInput: true,
            });

            $(".dashboard_campaigns_filtered").dataTable().fnDestroy();

            var campaignFilter =  $('.dashboard_campaigns').DataTable({
                dom: 'Blfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                aaSorting: [],
                oLanguage: {
                    sLengthMenu: "_MENU_"
                },
                ajax: {
                    url: '/agency/dashboard/campaigns',
                    data: function (d) {
                        d.start_date = $('input[name=start_date]').val();
                        d.stop_date = $('input[name=stop_date]').val();
                        d.filter_user = $('#filter_user').val();
                    }
                },
                columns: getColumns(),

            });

            function getColumns()
            {
                if(companies > 1){
                    return [
                                    {data: 'id', name: 'id'},
                                    {data: 'name', name: 'name'},
                                    {data: 'brand', name: 'brand'},
                                    {data: 'start_date', name: 'start_date'},
                                    {data: 'budget', name: 'budget'},
                                    {data: 'adslots', name: 'adslots'},
                                    {data: 'status', name: 'status'},
                                    {data: 'station', name: 'station'}
                                ]
                }else{
                    return [
                                    {data: 'id', name: 'id'},
                                    {data: 'name', name: 'name'},
                                    {data: 'brand', name: 'brand'},
                                    {data: 'start_date', name: 'start_date'},
                                    {data: 'budget', name: 'budget'},
                                    {data: 'adslots', name: 'adslots'},
                                    {data: 'status', name: 'status'},
                                ]
                }
            }

            $('#dashboard_filter_campaign').on('click', function() {
                campaignFilter.draw();
            });

            $('.key_search').on('keyup', function(){
                campaignFilter.search($(this).val()).draw() ;
            })

            $('#filter_user').on('change', function() {
                campaignFilter.draw();
            });

            function channel_pie(channel, percent_active, percent_pending, percent_finished){
                Highcharts.chart(channel,{
                    chart: {
                        renderTo: 'container',
                        type: 'pie',
                        height: 150,
                        width: 150
                    },
                    title: {
                        text: ''
                    },
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: false,
                            dataLabels: {
                                enabled: false,
                                format: '{point.name}'
                            }
                        }
                    },
                    exporting: { enabled: false },
                    series: [{
                        innerSize: '30%',
                        data: [
                            {name: 'Active', y: percent_active, color: '#00C4CA'},
                            {name: 'Pending', y: percent_pending, color: '#E89B0B' },
                            {name: 'Finished', y: percent_finished, color: '#E8235F'}
                        ]
                    }]
                });
            }
            if(companies > 1) {
                $.each(channels_with_details, function (index, value) {
                    if (value.channel_details.channel === 'TV') {
                        channel_pie('tv', value.campaign_status_percentage.percentage_active, value.campaign_status_percentage.percentage_pending, value.campaign_status_percentage.percentage_finished)
                    } else if (value.channel_details.channel === 'Radio') {
                        channel_pie('radio', value.campaign_status_percentage.percentage_active, value.campaign_status_percentage.percentage_pending, value.campaign_status_percentage.percentage_finished)
                    }
                });

                $('.publishers').select2();
                $('body').delegate("#publishers", "change", function () {
                    $(".dashboard_campaigns").dataTable().fnDestroy();
                    var channels = $("#publishers").val();
                    if(channels != null){
                        $('body').css({
                            opacity : 0.1
                        });
                        var campaignFilter =  $('.dashboard_campaigns_filtered').DataTable({
                            dom: 'Blfrtip',
                            paging: true,
                            serverSide: true,
                            processing: true,
                            aaSorting: [],
                            oLanguage: {
                                sLengthMenu: "_MENU_"
                            },
                            ajax: {
                                url: '/campaign-details/filter/company',
                                data: function (d) {
                                    d.company_id = channels;
                                    d.start_date = $('input[name=start_date]').val();
                                    d.stop_date = $('input[name=stop_date]').val();
                                    d.filter_user = $('#filter_user').val();
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
                        $.ajax({
                            url: '/campaign-management/filter-result',
                            method: 'GET',
                            data: {channel_id: channels},
                            success: function (data) {
                                $("#campaign_count").remove();
                                $("#filtered_campaign_count").show();
                                $("#filtered_campaign_count").html(filterActiveCampaignCount(data.active_campaigns));
                                $("#walkin_count").remove();
                                $("#filtered_walkin_count").show();
                                $("#filtered_walkin_count").html(filterWalkinCount(data.walkIns));
                                $("#pending_mpo_count").remove();
                                $("#filtered_mpo_count").show();
                                $("#filtered_mpo_count").html(filterPendingMpoCount(data.pending_mpos));
                                $("#brand_count").remove();
                                $("#filtered_brand_count").show();
                                $("#filtered_brand_count").html(filterBrandCount(data.brands));
                                $("#campaign_on_hold").remove();
                                $("#filtered_campaign_on_hold").show();
                                $("#filtered_campaign_on_hold").html(filterCampaignOnHoldCount(data.campaign_on_hold));
                                Highcharts.chart('periodicChart', {
                                    chart: {
                                        type: 'column'
                                    },
                                    title: {
                                        text: ''
                                    },
                                    xAxis: {
                                        categories: data.periodic_revenues['month_list']
                                    },
                                    credits: {
                                        enabled: false
                                    },
                                    exporting: {
                                        enabled: false
                                    },
                                    series:data.periodic_revenues['formated_periodic_revenue_chart']
                                });
                                $('body').css({
                                    opacity : 1
                                });
                            }
                        })
                    }
                });

                function filterActiveCampaignCount(campaigns)
                {
                    var filter_campaign_section = '';
                    filter_campaign_section += '<span class="weight_medium small_faint uppercased">Active Campaigns</span>\n' +
                        '                <h3><a href="/campaign/active-campaigns">'+campaigns.length+'</a></h3>';
                    return filter_campaign_section;
                }

                function filterWalkinCount(walkins)
                {
                    var filter_walkin_section = '';
                    filter_walkin_section += '<span class="weight_medium small_faint uppercased">All WalkIns</span>\n' +
                        '                <h3><a href="/walk-in">'+walkins.length+'</a></h3>';
                    return filter_walkin_section;
                }

                function filterPendingMpoCount(mpos)
                {
                    var filter_pending_mpo_section = '';
                    filter_pending_mpo_section += '<span class="weight_medium small_faint uppercased">Pending MPO´s</span>\n' +
                        '                <h3><a href="/mpos/pending" style="color: red;">'+mpos.length+'</a></h3>';
                    return filter_pending_mpo_section;
                }

                function filterBrandCount(brands)
                {
                    var filter_brand_section = '';
                    filter_brand_section += '<span class="weight_medium small_faint uppercased">All Brands</span>\n' +
                        '                <h3><a href="/brands">'+brands.length+'</a></h3>';
                    return filter_brand_section;
                }

                function filterCampaignOnHoldCount(campaign_onholds)
                {
                    var filter_campaign_on_hold_section = '';
                    filter_campaign_on_hold_section += '<span class="weight_medium small_faint uppercased">Campaigns On Hold</span>\n' +
                        '                <h3><a href="/campaign/campaign-on-hold/broadcaster/data" style="color: red;">'+campaign_onholds.length+'</a></h3>';
                    return filter_campaign_on_hold_section;
                }
                Highcharts.chart('periodicChart', {
                    chart: {
                        type: 'column'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: months
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series:periodic_revenue_data
                });

             }else{
                var chart = Highcharts.chart('containerTotal', {

                    title: {
                        text: ''
                    },

                    xAxis: {
                        categories: campaign_month
                    },
                    credits: {
                        enabled: false
                    },
                    exporting: {
                        enabled: false
                    },
                    series: [{
                        type: 'column',
                        colorByPoint: true,
                        data: campaign_volume,
                        showInLegend: false
                    }]

                });
            }
        } );
    </script>

@endsection

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
@endsection
