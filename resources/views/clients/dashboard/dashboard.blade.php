@extends('layouts.new_app')
@section('title')
    <title>Agency | Dashboard</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Agency Dashboard

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-address-card"></i> Dashboard</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <h3>Company</h3>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <!-- AREA CHART -->
                <div id="load_broad" class="load_broad" style="display: none;"></div>
                <form action="{{ route('agency.dashboard.data') }}" id="search_by_brand" method="GET">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            <label for="channel">Brands:</label>
                            <select class="form-control" name="brand" id="brand">
                                @foreach($brand as $brands)
                                    <option value="{{ $brands->id }}">{{ $brands->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p><br></p>
                    <div id="containerPeriodic_total_per_brand" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                </form>
                <!-- /.box -->

                <!-- DONUT CHART -->

                <!-- /.box -->

            </div>
            <!-- /.col (LEFT) -->
            <div class="col-md-6">
                <!-- LINE CHART --><p><br></p>
                <p><br></p>
                <p><br></p>
                <div id="containerPerProduct" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
                <!-- /.box -->

            </div>
            <!-- /.col (RIGHT) -->
        </div>
        <!-- /.row -->
        <hr>

    </section>
@stop
@section('scripts')
    <!-- Select2 -->
    <script src="{{ asset('agency_asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="{{ asset('agency_asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('agency_asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('agency_asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>
    <!-- bootstrap time picker -->
    <script src="{{ asset('agency_asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="{{ asset('agency_asset/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('agency_asset/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ asset('agency_asset/plugins/fastclick/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('agency_asset/dist/js/app.min.js') }}"></script>

    <!-- DataTables -->
    <script src="{{ asset('agency_asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('agency_asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>

    <script>
        <?php echo "var date = ".$date . ";\n"; ?>
        <?php echo "var amount_price = ".$amount . ";\n"; ?>
        <?php echo "var name = ".$name .";\n"; ?>

        $(document).ready(function () {

            $("#brand").change(function () {
                $("#load_broad").show();
                $(".content").css({
                    opacity: 0.5
                });
                $('#load_broad').html('<img src="{{ asset('loader.gif') }}" align="absmiddle"> Please wait while we process your request...');
                var br_id = $("#brand").val();
                var url = $("#search_by_brand").attr('action');
                $.get(url, {'br_id': br_id, '_token':$('input[name=_token]').val()}, function(data) {
                    $("#load_broad").hide();
                    $(".content").css({
                        opacity: 1
                    });
                    Highcharts.chart('containerPeriodic_total_per_brand', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Periodic Spend Report'
                        },
                        subtitle: {
                            text: 'Total against Brands'
                        },
                        xAxis: {
                            categories: data.date,
                            crosshair: true
                        },
                        yAxis: {
                            min: 0,
                            title: {
                                text: 'Total (Naira)'
                            }
                        },
                        tooltip: {
                            headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.1f} Naira</b></td></tr>',
                            footerFormat: '</table>',
                            shared: true,
                            useHTML: true
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.2,
                                borderWidth: 0
                            }
                        },
                        series: [{
//                            name: data.name,
                            data: data.amount_price

                        }]
                    });
                });
            })

            var Datefilter =  $('.agency_campaign_all').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/agency/campaigns/all-campaign/data',
                    data: function (d) {
                        d.start_date = $('input[name=txtFromDate_hvc]').val();
                        d.stop_date = $('input[name=txtToDate_hvc]').val();
                    }
                },
                columns: [
                    {data: 'id', name: 'id'},
                    {data: 'name', name: 'name'},
                    {data: 'brand', name: 'brand'},
                    {data: 'product', name: 'product'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                    {data: 'amount', name: 'amount'},
                    {data: 'mpo', name: 'mpo'}
                ]
            });
        })

        Highcharts.chart('containerPeriodic_total_per_brand', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Periodic Spend Report'
            },
            subtitle: {
                text: 'Total against Brands'
            },
            xAxis: {
                categories:date,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Total (Naira)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.1f} Naira</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
//                name: name,
                data:amount_price

            }]
        });
    </script>

@stop

@section('style')
    <style>
        .load_broad {
            position: fixed;
            top: 50%;
            left: 50%;
            margin-left: -50px; /* half width of the spinner gif */
            margin-top: -50px; /* half height of the spinner gif */
            text-align:center;
            z-index:1234;
            overflow: auto;
            width: 500px; /* width of the spinner gif */
            height: 500px; /*hight of the spinner gif +2px to fix IE8 issue */
        }
    </style>
@stop

