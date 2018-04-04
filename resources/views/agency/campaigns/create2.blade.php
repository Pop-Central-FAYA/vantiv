@extends('layouts.new_app')
@section('title')
    <title>Agency | Create Campaigns</title>
@stop
@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Create Campaigns</h1>
                    <ul>
                        <li><a href="{{ route('dashboard') }}"><i class="fa fa-th-large"></i>Agency</a></li>
                        <li><a href="{{ route('agency.campaign.all') }}">All Campaign</a></li>
                    </ul>
                </div>
                <div class="Create-campaign">
                    <form>
                        <div class="col-12 ">
                            @if(empty($adslots))
                                <p><h1>No Adslot found for this criteria, please go back</h1></p>
                            @else
                                <h2>Choose Broadcaster</h2>
                                <hr>
                                <p><br></p>
                                @foreach($adslots as $adslot)
                                    <li>
                                        <a class="btn btn-defaul" href="{{ route('agency_campaign.step3', ['id' => $id,'broadcaster' => $adslot['broadcaster']]) }}">{{ $adslot['count_adslot'] }} Adslots available for {{ $adslot['boradcaster_brand'] }}</a>
                                    </li>
                                @endforeach
                            @endif
                        </div>

                    </form>
                    <p><br></p>
                    <div class="input-group">
                        <input type="button" style="background: #00c4ca" id="step1" class="btn btn-danger btn-lg" name="Submit" value="<< Back">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Section-->

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
        $(document).ready(function(){

            var user_id = "<?php echo $id ?>";

            $('#step1').click(function(){
                window.location.href = '/agency/campaigns/campaign/step1/'+user_id;
            });

        });
    </script>
@stop


