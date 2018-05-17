@extends('layouts.new_app')
@section('title')
    <title>Advertiser | Create Brand</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create Brand</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Advertiser</a></li>
                        <li><a href="#">Create Brand</a></li>
                    </ul>
                </div>
                <div class="Add-brand changing">
                    <h2>Create Brand</h2>
                    <form action="{{ route('agency.brand.store') }}" method="post" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                                    <label for="brand_name">Brand Name</label>
                                    <input type="text" name="brand_name" required value=""  placeholder="Brand Name">
                                </div>
                                @if($errors->has('brand_name'))
                                    <strong>
                                        <span class="help-block">{{ $errors->first }}</span>
                                    </strong>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <label for="brand_logo">Brand Logo</label>
                                    <input type="file" required name="brand_logo" value=""  placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <label for="brand_name">Industry</label>
                                    <select name="industry" id="industry">
                                        <option value="">Select Industry</option>
                                        @foreach($industries as $industry)
                                            <option value="{{ $industry->sector_code }}">{{ $industry->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <label>Sub Industry</label>
                                    <select name="sub_industry" id="sub_industry">

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="Submit" class="update" name="Submit" value="Add Brand">
                                </div>
                            </div>
                        </div>
                    </form>
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
        $(document).ready(function () {
            // $("#state").change(function() {
            $('#industry').on('change', function(e){
                $(".changing").css({
                    opacity: 0.5
                });
                $('.update').attr("disabled", true);
                var industry = $("#industry").val();
                var url = '/walk-in/brand';
                $.ajax({
                    url: url,
                    method: "GET",
                    data: {industry: industry},
                    success: function(data){
                        if(data.error === 'error'){
                            $(".changing").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);
                        }else{
                            $(".changing").css({
                                opacity: 1
                            });
                            $('.update').attr("disabled", false);

                            $('#sub_industry').empty();

                            $('#sub_industry').append(' Please choose one');

                            $.each(data, function(index, title){
                                $("#sub_industry").append('' + '<option value ="'+ title.sub_sector_code + '"  > ' + title.name + '  </option>');
                            });
                        }

                    }
                });
            });
        });

    </script>
@stop



