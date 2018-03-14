@extends('layouts.new_app')

@section('title')
    <title>Advertiser | Campaign-Lists</title>
@stop

@section('content')
    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Campaign</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>All Campaign</a></li>
                        <li><a href="#">Campaign List</a></li>
                    </ul>
                </div>
                <div class="col-12 Campaign-List">
                    <table class="agency_campaign_all table">
                        <thead class="thead-dark">
                        <th>No</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Product</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>MPO</th>
                        <th>Invoice</th>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!--Mpo Modal -->
    @if(count($mpos) != 0)
        @foreach($mpos as $mpo)
            <div class="modal fade mpoModal{{ $mpo['campaign_id'] }}"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel"></h4>
                        </div>
                        <div class="modal-body">

                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <p><h3>Name: {{ $mpo['campaign_name'] }}</h3></p><br>
                                    <p><h3>Brand: {{ $mpo['brand'] }}</h3></p><br>

                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>Ad Blocks</th>
                                                <th>Duration</th>
                                                <th>Media</th>
                                                <th>Price</th>
                                                <th>Approval</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($mpo['adslot'] as $adslot)
                                                    <?php
                                                        $camp_id = $mpo['campaign_id'];
                                                        $file = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from files where adslot = '$adslot->id' AND campaign_id = '$camp_id'");
                                                        $adslot_id = $file[0]->adslot;
                                                        $pay = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$adslot_id'");
                                                        if(!$pay){
                                                            $pay = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$adslot_id'");
                                                        }
                                                        if($file[0]->is_file_accepted === 0){
                                                            $approval = "Not Approved";
                                                        }elseif($file[0]->is_file_accepted === 1){
                                                            $approval = "Approved";
                                                        }else{
                                                            $approval = "Rejected";
                                                        }
                                                    ?>
                                                    <tr>
                                                        <td>{{ $adslot->from_to_time }}</td>
                                                        <td>{{ $file[0]->time_picked }}</td>
                                                        <td><video width="200" controls><source src="{{ asset(decrypt($file[0]->file_url)) }}"></video></td>
                                                        @if($file[0]->time_picked === "60")
                                                            <td>&#8358;{{ number_format($pay[0]->price_60, 2) }}</td>
                                                        @elseif($file[0]->time_picked === "45")
                                                            <td>&#8358;{{ number_format($pay[0]->price_45, 2) }}</td>
                                                        @elseif($file[0]->time_picked === "30")
                                                            <td>&#8358;{{ number_format($pay[0]->price_30, 2) }}</td>
                                                        @else
                                                            <td>&#8358;{{ number_format($pay[0]->price_15, 2) }}</td>
                                                        @endif
                                                        <td>{{ $approval }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tr>
                                                <td><b><h3>Discount: {{ $mpo['discount'] }}</h3></b></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td><h3>Total : &#8358;{{ number_format($mpo['total'], 2) }}</h3></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <br>

                                </div>
                            </div>


                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Done</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Invoice -->
    @foreach($invoice as $inv)
    <div class="modal fade invoiceModal{{ $inv->campaign_id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Campaign Invoice -</h4>
                </div>
                <div class="modal-body">

                    ......

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Media File</th>
                                <th>Adslot ID</th>
                                <th>Playtime</th>
                                <th>Complaince</th>
                                <th>Cost</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $m = 1; ?>
                                @foreach($files as $file)
                                    @if($file->campaign_id === $inv->campaign_id)
                                        <?php
                                            $ads_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$file->adslot'");
                                            if(!$ads_price){
                                                $ads_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$file->adslot'");
                                            }
                                            $pay = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$inv->campaign_id'");
                                        ?>
                                        <tr>
                                            <td><video width="200" controls><source src="{{ asset(decrypt($file->file_url)) }}"></video></td>

                                            <td>{{ count((array) $file->adslot) }}</td>
                                            <td>{{ $file->time_picked }} Seconds</td>
                                            <td>80%</td>
                                            @if($file->time_picked === "60")
                                                <td>&#8358;{{ number_format($ads_price[0]->price_60, 2) }}</td>
                                            @elseif($file->time_picked === "45")
                                                <td>&#8358;{{ number_format($ads_price[0]->price_45, 2) }}</td>
                                            @elseif($file->time_picked === "30")
                                                <td>&#8358;{{ number_format($ads_price[0]->price_30, 2) }}</td>
                                            @else
                                                <td>&#8358;{{ number_format($ads_price[0]->price_15, 2) }}</td>
                                            @endif
                                        </tr>
                                    @endif
                                    <?php $m++; ?>
                                @endforeach
                            <tr>
                                <td><b><h3>Total:</h3></b></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><h3>&#8358;{{ number_format($pay[0]->amount, 2) }}</h3></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
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
        $(document).ready(function () {
            var Datefilter =  $('.agency_campaign_all').DataTable({
                paging: true,
                serverSide: true,
                processing: true,
                ajax: {
                    url: '/advertiser/campaigns/all-campaign/data',
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
                    {data: 'status', name: 'status'},
                    {data: 'mpo', name: 'mpo'},
                    {data: 'invoices', name: 'invoices'}
                ]
            });
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
@stop