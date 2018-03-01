@extends('layouts.new_app')
@section('title')
    <title>Agency | Dashboard</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Agency Dashboard </h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Agency</a></li>
                        <li><a href="#">Clients Dashboard </a></li>
                    </ul>
                </div>
            </div>
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
                    <canvas id="containerPeriodic_total_per_brand" style="width: 512px; height: 150px"></canvas>
                    {{--<div id="containerPeriodic_total_per_brand" style="min-width: 310px; height: 400px; margin: 0 auto"></div>--}}
                </form>
                <!-- /.box -->

                <!-- DONUT CHART -->

                <!-- /.box -->

            </div>
        </div>
    </div>

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
                    var ctx = document.getElementById("containerPeriodic_total_per_brand");
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.date,
                            datasets: [{
                                label: 'Periodic Spent Report on brands',
                                data: data.amount_price,
                                backgroundColor: [
                                    'rgba(255, 99, 132, 0.2)',
                                    'rgba(54, 162, 235, 0.2)',
                                    'rgba(255, 206, 86, 0.2)',
                                    'rgba(75, 192, 192, 0.2)',
                                    'rgba(153, 102, 255, 0.2)',
                                    'rgba(255, 159, 64, 0.2)'
                                ],
                                borderColor: [
                                    'rgba(255,99,132,1)',
                                    'rgba(54, 162, 235, 1)',
                                    'rgba(255, 206, 86, 1)',
                                    'rgba(75, 192, 192, 1)',
                                    'rgba(153, 102, 255, 1)',
                                    'rgba(255, 159, 64, 1)'
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            scales: {
                                yAxes: [{
                                    gridLines: {
                                        display:false
                                    },
                                    ticks: {
                                        beginAtZero:true
                                    }
                                }]
                            }
                        }
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

    </script>
    <script>
        var ctx = document.getElementById("containerPeriodic_total_per_brand");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: date,
                datasets: [{
                    label: 'Periodic Spent Report on brands',
                    data: amount_price,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255,99,132,1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        gridLines: {
                            display:false
                        },
                        ticks: {
                            beginAtZero:true
                        }
                    }]
                }
            }
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

