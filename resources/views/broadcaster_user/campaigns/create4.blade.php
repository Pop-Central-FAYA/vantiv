@extends('layouts.new_app')

@section('title')
    <title>Create Campaign</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Adslots</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Create Campaign</a></li>
                        <li><a href="#">Adslots</a></li>
                    </ul>
                </div>

                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 ">
                        <a href="{{ route('broadcaster.user.campaign.store5', ['walkins' => $walkins, 'broadcaster' => $broadcaster, 'broadcaster_user' => $broadcaster_user]) }}">
                            <div class="tvspace-box">
                                <div class="tv-space">
                                    <p align="center">{{ $results  }} Adslot(s) Available</p>
                                    <p>{{ $broadcaster_brand[0]->brand }}</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>

    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>

    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>

    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var user_id = "<?php echo $walkins ?>";
            var broadcaster = "<?php echo $broadcaster ?>";
            var broadcaster_user = "<?php echo $broadcaster_user ?>";
            $('#step4').click(function(){
                window.location.href = '/broadcaster-user/campaign/create/'+user_id+'/'+broadcaster+'/'+broadcaster_user+'/step3';
            });
        });
    </script>

@stop