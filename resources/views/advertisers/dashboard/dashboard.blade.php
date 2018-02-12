@extends('advertiser_layouts.app')
@section('title')
    <title>Advertiser | Dashboard</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            Advertiser Dashboard
        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Advertiser</a> </li>
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-address-card"></i> Dashboard</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <h3>Company</h3>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <!-- AREA CHART -->
                <div id="load_broad" class="load_broad" style="display: none;"></div>
                <form action="" id="search_by_broad" method="GET">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-4">
                            <label for="channel">Channels:</label>
                            <select class="form-control" name="broadcaster" id="broadcaster">
                                @foreach($broadcaster as $broadcasters)
                                    <option value="{{ $broadcasters->id }}">{{ $broadcasters->brand }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p><br></p>
                    <div id="containerPeriodic_total_per_chanel" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
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
        <div class="row">
            <div id="containerBudgetPacing" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
        </div>
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

        $(document).ready(function () {

        {{--$("#broadcaster").change(function () {--}}
        {{--$("#load_broad").show();--}}
        {{--$(".content").css({--}}
        {{--opacity: 0.5--}}
        {{--});--}}
        {{--$('#load_broad').html('<img src="{{ asset('loader.gif') }}" align="absmiddle"> Please wait while we process your request...');--}}
        {{--var br_id = $("#broadcaster").val();--}}
        {{--var url = $("#search_by_broad").attr('action');--}}
        {{--$.get(url, {'br_id': br_id, '_token':$('input[name=_token]').val()}, function(data) {--}}
        {{--$("#load_broad").hide();--}}
        {{--$(".content").css({--}}
        {{--opacity: 1--}}
        {{--});--}}
        {{--Highcharts.chart('containerPeriodic_total_per_chanel', {--}}
        {{--chart: {--}}
        {{--type: 'column'--}}
        {{--},--}}
        {{--title: {--}}
        {{--text: 'Periodic Spend Report'--}}
        {{--},--}}
        {{--subtitle: {--}}
        {{--text: 'Total against Channels'--}}
        {{--},--}}
        {{--xAxis: {--}}
        {{--categories: data.date,--}}
        {{--crosshair: true--}}
        {{--},--}}
        {{--yAxis: {--}}
        {{--min: 0,--}}
        {{--title: {--}}
        {{--text: 'Total (Naira)'--}}
        {{--}--}}
        {{--},--}}
        {{--tooltip: {--}}
        {{--headerFormat: '<span style="font-size:10px">{point.key}</span><table>',--}}
        {{--pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +--}}
        {{--'<td style="padding:0"><b>{point.y:.1f} Naira</b></td></tr>',--}}
        {{--footerFormat: '</table>',--}}
        {{--shared: true,--}}
        {{--useHTML: true--}}
        {{--},--}}
        {{--plotOptions: {--}}
        {{--column: {--}}
        {{--pointPadding: 0.2,--}}
        {{--borderWidth: 0--}}
        {{--}--}}
        {{--},--}}
        {{--series: [{--}}
        {{--//                            name: data.name,--}}
        {{--data: data.amount_price--}}

        {{--}]--}}
        {{--});--}}
        {{--});--}}
        {{--});--}}

        //            var Datefilter =  $('.agency_campaign_all').DataTable({
        //                paging: true,
        //                serverSide: true,
        //                processing: true,
        //                ajax: {
        //                    url: '/agency/campaigns/all-campaign/data',
        //                    data: function (d) {
        //                        d.start_date = $('input[name=txtFromDate_hvc]').val();
        //                        d.stop_date = $('input[name=txtToDate_hvc]').val();
        //                    }
        //                },
        //                columns: [
        //                    {data: 'id', name: 'id'},
        //                    {data: 'name', name: 'name'},
        //                    {data: 'brand', name: 'brand'},
        //                    {data: 'product', name: 'product'},
        //                    {data: 'start_date', name: 'start_date'},
        //                    {data: 'end_date', name: 'end_date'},
        //                    {data: 'amount', name: 'amount'},
        //                    {data: 'mpo', name: 'mpo'}
        //                ]
        //            });
        //        })
    </script>

    <script>
        $(function () {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Datemask dd/mm/yyyy
            $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
            //Datemask2 mm/dd/yyyy
            $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
            //Money Euro
            $("[data-mask]").inputmask();

            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({timePicker: true, timePickerIncrement: 30, format: 'MM/DD/YYYY h:mm A'});
            //Date range as a button
            $('#daterange-btn').daterangepicker(
                {
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function (start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }
            );

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true
            });

            $('#datepickerend').datepicker({
                autoclose: true
            });

            //iCheck for checkbox and radio inputs
            $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            //Red color scheme for iCheck
            $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
                checkboxClass: 'icheckbox_minimal-red',
                radioClass: 'iradio_minimal-red'
            });
            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-green',
                radioClass: 'iradio_flat-green'
            });

            //Colorpicker
            $(".my-colorpicker1").colorpicker();
            //color picker with addon
            $(".my-colorpicker2").colorpicker();

            //Timepicker
            $(".timepicker").timepicker({
                showInputs: false
            });
        });
    </script>

    <script>

        Highcharts.chart('containerPeriodic_total_per_chanel', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Periodic Spend Report'
            },
            subtitle: {
                text: 'Total against Channels'
            },
            xAxis: {
                categories: date,
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
                pointFormat: '<td style="padding:0"><b>{point.y:.1f} Naira</b></td></tr>',
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
                data: amount
            }]
        });
        // Build the chart
        Highcharts.chart('containerPerProduct', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: 'Periodic Spend Report on Products for <?php echo date('F, Y')?>',
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                name: 'Total',
                colorByPoint: true,
                data: periodic_product
            }]
        });

        Highcharts.chart('containerBudgetPacing', {
            chart: {
                type: 'spline'
            },
            title: {
                text: 'Budget Pacing Report'
            },
            xAxis: {
                categories: date_bud
            },
            yAxis: {
                title: {
                    text: 'Amount(Naira)'
                },
            },
            tooltip: {
                crosshairs: true,
                shared: true
            },
            plotOptions: {
                spline: {
                    marker: {
                        radius: 4,
                        lineColor: '#666666',
                        lineWidth: 1
                    }
                }
            },
            series: [{
                name: '',
                data: amount_bud

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
