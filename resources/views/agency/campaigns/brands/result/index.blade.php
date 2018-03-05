@extends('layouts.new_app')
@section('title')
    <title>Agency | Search Brand-Lists</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Search Result for "{{ $result }}" </h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Agency</a></li>
                        <li><a href="#">All Brands </a></li>
                    </ul>
                </div>
                <div class="col-12 all-brands">
                    @foreach($brand as $brands)
                        <div class="col-6">
                            <div class="col-6">
                                <h2>{{ $brands->name }}</h2>
                                <a href="#" class="edit" data-toggle="modal" data-target=".{{ $brands->id }}">Edit</a> <a href="#" class="delete" data-toggle="modal" data-target=".{{ $brands->id }}delete">Delete</a> </div>
                            <div class="col-6"> <img class="img-responsive" src="{{ $brands->image_url ? asset(decrypt($brands->image_url)) : asset('new_assets/images/logo.png') }}"> </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    <div class="text-center">
        {{ $brand->links() }}
    </div>

    @foreach($brand as $brands)
        <div class="modal fade {{ $brands->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <h4>Edit : {{ $brands->name }}</h4>
                    <hr>

                    <form action="{{ route('brands.update', ['id' => $brands->id]) }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="group">
                                <input type="text" name="brand_name" value="{{ $brands->name }}" class="form-control">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <button type="submit" class="btn btn-success">Update Brand</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    @endforeach

    @foreach($brand as $brands)
        <div class="modal fade {{ $brands->id }}delete" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <h4>Delete : {{ $brands->name }}</h4>
                    <hr>

                    <p>Are you sure you want to delete this brand?</p>
                    <br>


                    <a class="btn btn-danger btn-xs" href="{{ route('agency.brands.delete', ['id' => $brands->id ]) }}">Yes</a> <button class="btn btn-primary btn-xs" data-dismiss="modal">Cancel</button>

                </div>
            </div>
        </div>
    @endforeach
    <!-- /.conte
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
