@extends('agency_layouts.app')
@section('title')
    <title>Agency | Campaign-Lists</title>
@stop
@section('content')
    <section class="content-header">
        <h1>
            All Campaigns

        </h1>
        <ol class="breadcrumb" style="font-size: 16px">

            <li><a href="#"><i class="fa fa-th"></i> Agency</a> </li>
            <li><a href="index.html"><i class="fa fa-address-card"></i> All Campaign</a> </li>

        </ol>
    </section>

    <!-- Main content -->

    <section class="content">
        <div class="row">
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <div class="col-md-8 Campaign" style="padding:2%">

            </div>
            <!-- /.col -->
            <div class="col-md-2 hidden-sm hidden-xs"></div>
            <!-- /.col -->

            <div class="row" style="padding: 5%">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">All Agency Campaigns</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">
                            <table id="agency_campaign_all" class="table table-bordered table-striped agency_campaign_all">
                                <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Name</th>
                                    <th>Brand</th>
                                    <th>Product</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Amount</th>
                                    <th>MPO</th>
                                    <th>Invoice</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!--Mpo Modal -->
    @if(count($mpo) != 0)
        @foreach($mpo as $mpos)
            <div class="modal fade mpoModal{{ $mpos->campaign_id }}"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">MPO Modal</h4>
                        </div>
                        <div class="modal-body">
                            ...

                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Ad Blocks</th>
                                        <th>Duration</th>
                                        <th>Media</th>
                                        <th>Price</th>
                                        <th>Approval</th>
                                    </tr>
                                    </thead>
                                    <tr>
                                        <?php $n = 1; ?>
                                        @foreach($mpo as $mpoos)
                                            <?php
                                            $camp = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from campaigns where id='$mpoos->campaign_id'");
                                            $br = $camp[0]->brand;
                                            $brand = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT name from brands where id = '$br'");
                                            $pay_mpo = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$mpoos->campaign_id'");
                                            $file_mpo = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from files where campaign_id = '$mpoos->campaign_id'");
                                            ?>
                                            <tr>
                                                <td>{{ $n }}</td>
                                                <td>{{ $camp[0]->name }}</td>
                                                <td>{{ $brand[0]->name }}</td>
                                                @foreach($file_mpo as $files_mpo)
                                                    <?php
                                                        $adslots = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslots WHERE id = '$files_mpo->adslot'");
                                                    ?>
                                                    <tr>
                                                        <td>{{ $adslots[0]->from_to_time }}</td>
                                                        <td>{{ $adslots[0]->time_in_seconds }}</td>
                                                        <td><video width="200" controls><source src="{{ asset($files_mpo->file_url) }}"></video></td>
                                                        <td>&#8358;{{ number_format($adslots[0]->price, 2) }}</td>
                                                        <td>
                                                            <select class="form-control" name="" id="">
                                                                <option>Approved</option>
                                                                <option>Disapproved</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tr>
                                            <?php $n++; ?>
                                        @endforeach
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
                                <th>S/N</th>
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
                                            $ads = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslots where id = '$file->adslot'");
                                            $pay = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from payments where campaign_id = '$inv->campaign_id'");
                                        ?>
                                        <tr>
                                            <td>{{ $m }}</td>
                                            <td><video width="200" controls><source src="{{ asset($file->file_url) }}"></video></td>
                                            <td>{{ count($file->adslot) }}</td>
                                            <td>{{ $ads[0]->time_in_seconds }}</td>
                                            <td>80%</td>
                                            <td>&#8358;{{ number_format($ads[0]->price, 2) }}</td>
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
                    {data: 'mpo', name: 'mpo'},
                    {data: 'invoice', name: 'invoice'}
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