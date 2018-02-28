@extends('layouts.new_app')
@section('title')
    <title>Faya | Agency Dashboard</title>
@stop

@section('content')
    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Agency Dashboard </h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Agency</a></li>
                        <li><a href="#">Dashboard </a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <form action="{{ route('agency.dashboard.broad')  }}" id="search_by_broad" method="GET">
                        {{ csrf_field() }}
                        <label for="channel">Channels:</label>
                        <select class="form-control" name="broadcaster" id="broadcaster">
                            @foreach($broadcaster as $broadcasters)
                                <option value="{{ $broadcasters->id }}">{{ $broadcasters->brand }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

                <div id="load_broad" class="load_broad" style="display: none;"></div>
                {{--<div class="col-12 chart-top">--}}
            <div class="row">
                <div class="col-12">
                    <div class="Sales">
                        <h2>Periodic Spend Report</h2>
                        <p>Total amount spent on chanel</p>
                        <canvas id="containerPeriodic_total_per_chanel" style="width: 512px; height: 150px"></canvas>
                        {{--<div id="containerPeriodic_total_per_chanel" style="min-width: 310px; height: 400px; margin: 0 auto"></div>--}}
                    </div>
                </div>
            </div>
                <hr>
            <div class="row">
                <h2>Percentage Periodic Spent Report on Products for <?php echo date('F, Y')?> </h2>
                <div class="col-12">
                    <div class="Our-Visitors">
                        <canvas id="containerPerProduct" style="width: 900px; height: 276px"></canvas>
                    </div>
                </div>
            </div>
                <hr>
            <div class="row">
                <div class="col-12">
                    <div class="col-12 Total-rev">
                        <h2>Budget Pacing Report</h2>
                        <canvas id="containerBudgetPacing" style="width: 512px; height: 150px"></canvas>
                    </div>
                </div>
            </div>
                <hr>
                <p><br></p>
            <div class="row">
                <div class="col-12">
                    <div class="col-12 revenue-right">
                        <div class="col-lg-3">
                            <div class="text">
                                <div id="all-client"></div>
                                {{--<h1>{{ $count_client }}</h1>--}}
                                <h3><p class="text-center"> <i class="fa fa-user"></i> All Clients</p></h3>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="text">
                                <div id="all-campaign"></div>
                                {{--<h1>{{ $count_campaigns }}</h1>--}}
                                <h3><p class="text-center"> <i class="fa fa-user"></i> All Campaign</p></h3>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="text">
                                <div id="all-brand"></div>
                                {{--<h1>{{ $count_brands }}</h1>--}}
                                <h3><p class="text-center"> <i class="fa fa-user"></i> All Brands</p></h3>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="text">
                                <div id="all-invoice"></div>
                                {{--<h1>{{ $count_invoice }}</h1>--}}
                                <h3><p class="text-center"> <i class="fa fa-user"></i> All Invoices</p></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                {{--</div>--}}
            <div class="row">
                <div class="col-12 recents">
                    <div class="col-12">
                        <div class="col-12 recents-inner">
                            <div class="recent-head">
                                <h1>recent invoice</h1>
                                <div class="reload"><a href="#"> <i class="fa fa-undo"></i></a><a href="#"><i class="fa fa-expand"></i></a> </div>
                            </div>
                            <div class="summary">
                                <p>Total approved invoices {{ $invoice_approval }},upapproved {{ $invoice_unapproval }}.</p>
                                <a href="#">Invoice Summary<i class="fa fa-arrow-right" aria-hidden="true"></i></a> </div>
                            <table class="table">
                                <thead>
                                <th>Invoice#</th>
                                <th>Customer Name</th>
                                <th>Brand</th>
                                <th>Amount</th>
                                <th>Refunded Amount</th>
                                <th>Status</th>
                                </thead>
                                <tbody>
                                @foreach($all_invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice['invoice_number'] }}</td>
                                        <td>{{ $invoice['campaign_name'] }}</td>
                                        <td>{{ $invoice['campaign_brand'] }}</td>
                                        <td>&#8358;{{ $invoice['actual_amount_paid'] }}</td>
                                        <td>&#8358;{{ $invoice['refunded_amount'] }}</td>
                                        <td>
                                            @if ($invoice['status'] == 1)
                                                <label style="font-size: 16px" class="label label-success">
                                                    Approved
                                                </label>
                                            @elseif ($invoice['status'] == 0)
                                                <label style="font-size: 16px" class="label label-warning">
                                                    Pending
                                                </label>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
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

    <script>
        <?php echo "var date = ".$date . ";\n"; ?>
        <?php echo "var amount = ".$amount . ";\n"; ?>
        <?php echo "var name = ".$name .";\n"; ?>
        <?php echo "var amount_bud =".$amount_bud ."\n"; ?>
        <?php echo "var date_bud =".$date_bud ."\n"; ?>
        <?php echo "var periodic_name =".$periodic_name ."\n"; ?>
        <?php echo "var periodic_data =".$periodic_data ."\n"; ?>

        $(document).ready(function () {

            $("#broadcaster").change(function () {
                $("#load_broad").show();
                $(".content").css({
                    opacity: 0.5
                });
                $('#load_broad').html('<img src="{{ asset('loader.gif') }}" align="absmiddle"> Please wait while we process your request...');
                var br_id = $("#broadcaster").val();
                var url = $("#search_by_broad").attr('action');
                $.get(url, {'br_id': br_id, '_token':$('input[name=_token]').val()}, function(data) {
                    $("#load_broad").hide();
                    $(".content").css({
                        opacity: 1
                    });
                    var ctx = document.getElementById("containerPeriodic_total_per_chanel");
                    var myChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.date,
                            datasets: [{
                                label: 'Periodic Spent Report',
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

            //setting a bench mark to get the percentage of all client
            var all_client = "<?php echo $count_client ?>";
            var percentage_client = 0;
            percentage_client = ((all_client / 100) * 100);

            //setting a bench mark to get the percentage of all campaigns
            var all_campaigns = "<?php echo $count_campaigns; ?>";
            var percentage_campaign = 0;
            percentage_campaign = ((all_campaigns / 100) * 100 );

            //setting a bench mark to get the percentage of all brands
            var all_brands = "<?php echo $count_brands ?>";
            var percentage_brand = 0;
            percentage_brand = ((all_brands / 100) * 100);

            //setting a benchmark to get the percentage of all invoice
            var all_invoices = "<?php echo $count_invoice ?>";
            var percentage_invoices = 0;
            percentage_invoices = ((all_invoices / 100) * 100);


            if(percentage_client < 10){
                $("#all-client").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#32CD32',
                    backgroundColor: '#fff',
                    percent: percentage_client,
                    noPercentageSign: true
                });
            }else if(percentage_client >=10 && percentage_client < 80) {
                $("#all-client").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#008000',
                    backgroundColor: '#fff',
                    percent: percentage_client,
                    noPercentageSign: true
                });
            }else{
                $("#all-client").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#ff0000',
                    backgroundColor: '#fff',
                    percent: percentage_client,
                    noPercentageSign: true
                });
            }

            if(percentage_campaign < 10){
                $("#all-campaign").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#32CD32',
                    backgroundColor: '#fff',
                    percent: percentage_campaign,
                    noPercentageSign: true
                });
            }else if(percentage_campaign >=10 && percentage_campaign < 80) {
                $("#all-campaign").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#008000',
                    backgroundColor: '#fff',
                    percent: percentage_campaign,
                    noPercentageSign: true
                });
            }else{
                $("#all-campaign").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#ff0000',
                    backgroundColor: '#fff',
                    percent: percentage_campaign,
                    noPercentageSign: true
                });
            }

            if(percentage_brand < 10){
                $("#all-brand").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#32CD32',
                    backgroundColor: '#fff',
                    percent: percentage_brand,
                    noPercentageSign: true
                });
            }else if(percentage_brand >=10 && percentage_brand < 80) {
                $("#all-brand").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#008000',
                    backgroundColor: '#fff',
                    percent: percentage_brand,
                    noPercentageSign: true
                });
            }else{
                $("#all-brand").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#ff0000',
                    backgroundColor: '#fff',
                    percent: percentage_brand,
                    noPercentageSign: true
                });
            }

            if(percentage_invoices < 10){
                $("#all-invoice").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#32CD32',
                    backgroundColor: '#fff',
                    percent: percentage_invoices,
                    noPercentageSign: true
                });
            }else if(percentage_invoices >=10 && percentage_invoices < 80) {
                $("#all-invoice").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#008000',
                    backgroundColor: '#fff',
                    percent: percentage_invoices,
                    noPercentageSign: true
                });
            }else{
                $("#all-invoice").circliful({
                    animationStep: 5,
                    foregroundBorderWidth: 5,
                    backgroundBorderWidth: 15,
                    foregroundColor: '#ff0000',
                    backgroundColor: '#fff',
                    percent: percentage_invoices,
                    noPercentageSign: true
                });
            }
        })

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
        var ctx = document.getElementById("containerPeriodic_total_per_chanel");
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: date,
                datasets: [{
                    label: 'Periodic Spent Report',
                    data: amount,
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
    <script>
        var ctx = document.getElementById("containerPerProduct");
        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: periodic_name,
                datasets: [{
                    label: '# of Votes',
                    data: periodic_data,
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
    <script>
        var ctx = document.getElementById('containerBudgetPacing').getContext('2d');
        var chart = new Chart(ctx, {
            // The type of chart we want to create
            type: 'line',

            // The data for our dataset
            data: {
                labels: date_bud,
                datasets: [{
                    label: "Budget Pacing",
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 0.2)',
                    data: amount_bud,
                }]
            },

            // Configuration options go here
            options: {
                scales: {
                    xAxes: [{
                        gridLines: {
                            display:false
                        }
                    }],
                    yAxes: [{
                        gridLines: {
                            display:false
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