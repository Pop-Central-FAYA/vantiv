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
            </div>
            <div class="row">
                <div class="Add-brand">
                    <div class="row">
                        <div class="col-md-12 ">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12 ">
                        <div class="tvspace-box">
                            <div class="tv-space">
                                <p align="center">{{ $result }} Adslot(s)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row load_this">
                <div class="col-md-12">
                    <div id="tv-time-box" style="border:1px solid #ccc" >
                        @foreach($ratecards as $ratecard)
                            <div class="row">
                                <div class="col-md-2">
                                    <h3 class="text-center">{{ $ratecard['day'] }}</h3>
                                </div>
                                <div class="col-md-3">
                                    <h3 class="text-center">{{ $ratecard['hourly_range'] }}</h3>
                                </div>
                                <div class="col-md-7">
                                    <div class="row">
                                        @foreach($ratecard['adslot'] as $rating)
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2>
                                    @foreach($cart as $carts)
                                            @if($carts->adslot_id === $rating->id)
                                                    choosen
@endif
                                            @endforeach
                                                    " id="rate_this{{ $rating->id }}">
                                                <p align="center">{{ $rating->from_to_time }}
                                                    <br>
                                                    {{ $rating->time_difference - $rating->time_used }} Seconds Available

                                                    <?php
                                                    $percentage_used = (($rating->time_difference - $rating->time_used) / $rating->time_difference) * 100;
                                                    ?>
                                                </p> <br/>

                                                <div class="progress" style="cursor: pointer;">
                                                    <div class="progress-bar bg-success
                                            @foreach($cart as $carts)
                                                    @if($carts->adslot_id === $rating->id)
                                                            disabled
                                                    @endif
                                                    @endforeach
                                                            " data-toggle="modal" data-target=".bs-example-modal-lg{{ $rating->id }}" role="progressbar" style="width: {{ $percentage_used }}%" aria-valuenow="{{ $percentage_used }}" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>

                                                <div class="modal modal_container fade bs-example-modal-lg{{ $rating->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
                                                    <div class="modal-dialog modal-lg" role="document">
                                                        <div id="modal_con" class="modal-content" style="padding: 5%">

                                                            <form id="form_cart" action="{{ route('store.cart') }}" method="GET">
                                                                {{ csrf_field() }}
                                                                <h2 align="center">{{ $rating->from_to_time }} | {{ $rating->time_difference - $rating->time_used }} Seconds Available</h2>
                                                                <ul style="font-size: 21px; margin:0 auto; width: 80%">
                                                                    <h3 align="" style="color:#9f005d">Choose a media file</h3>
                                                                    <hr />
                                                                    <div class="row">
                                                                        <?php
                                                                        $select_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPercentages where adslot_id = '$rating->id'");
                                                                        if(!$select_price){
                                                                            $select_price = \Vanguard\Libraries\Utilities::switch_db('api')->select("SELECT * from adslotPrices where adslot_id = '$rating->id'");
                                                                        }
                                                                        ?>


                                                                        <table id="mod" class="table table-bordered table-striped">
                                                                            <thead>
                                                                            <tr>
                                                                                <th>S/N</th>
                                                                                <th>Name</th>
                                                                                <th>Duration</th>
                                                                                <th>Price</th>
                                                                                <th>Position</th>
                                                                                {{--<th>Select</th>--}}
                                                                                <th>Action</th>
                                                                            </tr>
                                                                            </thead>
                                                                            <tbody>

                                                                            <?php $j = 1; $i = 0; for($i = 0; $i < count($times); $i++){ ?>
                                                                            @if( ($rating->time_difference - $rating->time_used) >= $datas[$i]->time)
                                                                                <tr>
                                                                                    @if($datas[$i]->uploads)
                                                                                        
                                                                                        <td>{{ $j }}</td>
                                                                                        <td><div class="col-md-3"> <video width="150" controls><source src="{{ asset(decrypt($datas[$i]->uploads)) }}"></video> </div></td>
                                                                                        <input type="hidden" name="file" class="file{{ $rating->id.$datas[$i]->id }}" value="{{ $datas[$i]->uploads }}">
                                                                                        <td><div class="col-md-3"><span style="margin-left:15%"></span>{{ $datas[$i]->time }} Seconds</div></td>
                                                                                        @if($datas[$i]->time === 15)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_15 }}</div></td>
                                                                                        @elseif($datas[$i]->time === 30)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_30 }}</div></td>
                                                                                        @elseif($datas[$i]->time === 45)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_45 }}</div></td>
                                                                                        @elseif($datas[$i]->time === 60)
                                                                                            <td><div class="col-md-3">&#8358;{{ $select_price[0]->price_60 }}</div></td>
                                                                                        @endif
                                                                                        <td>
                                                                                            <select name="position" class="form-control" id="position{{ $rating->id.$datas[$i]->id }}">
                                                                                                <option value="">No Position</option>
                                                                                                @if(count($positions) > 0)
                                                                                                    @foreach($positions as $position)
                                                                                                        <option value="{{ $position->id }}"
                                                                                                            @foreach($cart as $ca)
                                                                                                                @if($ca->adslot_id === $rating->id)
                                                                                                                    @if((int)$ca->time === (int)$datas[$i]->time)
                                                                                                                        @if($ca->filePosition_id === $position->id)
                                                                                                                            selected
                                                                                                                        @endif
                                                                                                                    @endif
                                                                                                                @endif
                                                                                                            @endforeach
                                                                                                        >{{ $position->position }}</option>
                                                                                                    @endforeach
                                                                                                @endif
                                                                                            </select>
                                                                                        </td>

                                                                                        <td class="pick_button{{ $rating->id.$datas[$i]->id }}"><button id="button{{ $rating->id.$datas[$i]->id }}"
                                                                                                    @foreach($cart as $ca)
                                                                                                    @if($ca->adslot_id === $rating->id)
                                                                                                    @if((int)$ca->time === (int)$datas[$i]->time)
                                                                                                    disabled
                                                                                                    @endif
                                                                                                    @endif
                                                                                                    @endforeach
                                                                                                    type="button"
                                                                                                    data-file_slot="{{ $rating->id.$datas[$i]->id }}"
                                                                                                    @if($datas[$i]->time === 15)
                                                                                                        data-price="{{ $select_price[0]->price_15 }}"
                                                                                                    @elseif($datas[$i]->time === 30)
                                                                                                        data-price="{{ $select_price[0]->price_30 }}"
                                                                                                    @elseif($datas[$i]->time === 45)
                                                                                                        data-price="{{ $select_price[0]->price_45 }}"
                                                                                                    @elseif($datas[$i]->time === 60)
                                                                                                        data-price="{{ $select_price[0]->price_60 }}"
                                                                                                    @endif
                                                                                                        data-adslot_id="{{ $rating->id }}"
                                                                                                        data-range="{{ $rating->from_to_time }}"
                                                                                                        data-time="{{ $datas[$i]->time }}"
                                                                                                        data-walkin="{{ $walkins }}"
                                                                                                        data-file="{{ $datas[$i]->uploads }}"
                                                                                                        data-target="bs-example-modal-lg{{ $rating->id }}"
                                                                                                    class="btn btn-success btn-xs saveCart">select</button></td>

                                                                                    @endif
                                                                                </tr>
                                                                            @endif
                                                                            <?php $j++; } ?>
                                                                            </tbody>
                                                                        </table>

                                                                    </div>

                                                                    <hr/>
                                                                </ul>
                                                                {{--<button type="button" id="save_cart"  class="btn btn-large save_cart" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Save</button></a></p>--}}
                                                                <button type="button" data-dismiss="modal" class="btn btn-large" style="background: #9f005d; color:white; font-size: 20px; padding: 1% 5%; margin-top:4%; border-radius: 10px;">Close</button></a></p>
                                                            </form>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <br>
            <div class="row" id="cart_item">
                @if(count($cart) != 0)
                    <a class="btn btn-success btn-lg pull-right" href="{{ route('checkout', ['walkins' => $walkins]) }}"><i class="fa fa-shopping-cart"></i>{{ count($cart) }} Cart</a>
                @endif
            </div>
        </div>

    </div>
    <div class="row">

    </div>

    <div id="mmooddaall" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                    <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('scripts')

    <script src="{{ asset('asset/plugins/select2/select2.full.min.js') }}"></script>
    <!-- InputMask -->
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
    <script src="{{ asset('asset/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>

    <!-- bootstrap datepicker -->
    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>

    <!-- date-range-picker -->
    <script src="{{ 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js' }}"></script>
    <script src="{{ asset('asset/plugins/daterangepicker/daterangepicker.js') }}"></script>

    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('asset/plugins/iCheck/icheck.min.js') }}"></script>

    <!-- bootstrap color picker -->
    <script src="{{ asset('asset/plugins/colorpicker/bootstrap-colorpicker.min.js') }}"></script>

    <!-- bootstrap time picker -->
    <script src="{{ asset('asset/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

    <script>

        $(document).ready(function() {
            $('#step6').click(function(){
                window.location.href = "/campaign/create/1/step";
            });

            $("body").delegate(".saveCart", "click", function() {
                $(".modal_container").css({
                    opacity: 0.5
                });

                $('.saveCart').attr("disabled", false);
                var file_slot = $(this).data('file_slot');
                var price = $(this).data('price');
                var adslot_id = $(this).data('adslot_id');
                var range = $(this).data('range');
                var time = $(this).data('time');
                var walkins = $(this).data('walkin');
                var url1 = $("#form_cart").attr("action");
                var position = $("select#position"+file_slot).val();
                var file = $(this).data('file');
                var target = $(this).data('target');
                $(".saveCart").attr('disabled', true);

                $.ajax({
                    url: url1,
                    method: "GET",
                    data: {price: price, adslot_id: adslot_id, file: file, range: range, time: time, position: position, walkins: walkins, '_token':$('input[name=_token]').val()},
                    success: function(data){
                        if(data.success === "success"){
                            $(".modal_container").css({
                                opacity: 1
                            });
                            toastr.success('File picked successfully');
                            // $(".saveCart").attr('disabled', false);
                            $("#button"+file_slot).attr('disabled', true);
                            location.reload();
                            $("#rate_this"+adslot_id).addClass('choosen');
                            // $('#cart_item').load(location.href + ' #cart_item');
                        }else if(data.error === "error"){
                            $(".modal_container").css({
                                opacity: 1
                            });
                            toastr.error('You have already selected this position, please select another one');
                            $(".saveCart").attr('disabled', false);
                            // location.reload();
                        }else if(data.file_error === "file_error"){
                            $(".modal_container").css({
                                opacity: 1
                            });
                            toastr.error('This position isnt available, please select another position');
                            $(".saveCart").attr('disabled', false);
                        }else{
                            $(".modal_container").css({
                                opacity: 1
                            });
                            toastr.error('An error occurred while selecting this slot');
                            $(".saveCart").attr('disabled', false);
                            location.reload();
                        }

                    }
                })


            });
        });
    </script>


@endsection
@section('styles')
    <style>
        .choosen{
            border:2px solid #9f005d;
            background: white;
            border-radius: 10px
        }
        /*.disabled{*/
            /*cursor: not-allowed;*/
            /*pointer-events: none;*/
        /*}*/
    </style>
@stop
