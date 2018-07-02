@extends('layouts.faya_app')

@section('title')
    <title> FAYA | Clients </title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Clients</h2>
            </div>

            <div class="column col_6 align_right">
                <a href="#new_client" class="btn modal_click">New Client</a>
            </div>
        </div>

        <!-- main stats -->
        <div class="the_stats clearfix mb4">
            <div class="active column col_4">
                <span class="small_faint uppercased">Total Clients</span>
                <h3>{{ count($clients) }}</h3>
            </div>

            <div class="column col_4">
                <span class="small_faint uppercased">Active Clients</span>
                <h3>{{ count($clients) }}</h3>
            </div>

            <div class="column col_4">
                <span class="small_faint uppercased">Inactive Clients</span>
                <h3>{{ count($clients) }}</h3>
            </div>
        </div>


        <div class="similar_table pt3">
            <!-- table header -->
            <div class="_table_header clearfix m-b">
                <span class="small_faint block_disp padd column col_4">Basic Info</span>
                <span class="small_faint block_disp column col_1">Brands</span>
                <span class="block_disp column col_2 color_trans">.</span>
                <span class="small_faint block_disp column col_2">Active Campaigns</span>
                <span class="small_faint block_disp column col_2">Inactive Campaigns</span>
                <span class="block_disp column col_1 color_trans">.</span>
            </div>

            <!-- table item -->
            @foreach($clients as $client)
            <div class="_table_item the_frame clearfix">
                <div class="padd column col_4">
                    <span class="client_ava"><img src="{{ $client['image_url'] ? asset(decrypt($client['image_url'])) : '' }}"></span>
                    <p>{{ $client['name'] }}</p>
                    <span class="small_faint">Added {{ date('M j, Y h:ia', strtotime($client['created_at'])) }}</span>
                </div>
                <div class="column col_1">{{ $client['count_brands'] }}</div>
                <div class="column col_2">&#8358; {{ number_format($client['total'],2) }}</div>
                <div class="column col_2">{{ $client['active_campaign'] }}</div>
                <div class="column col_2">{{ $client['inactive_campaign'] }}</div>
                <div class="column col_1">
                    <span class="more_icon"></span>
                </div>
                <a href="{{ route('client.show', ['id' => $client['client_id']]) }}">details</a>
            </div>
            @endforeach
            <!-- table item end -->
        </div>

    </div>

    <!-- new client modal -->

    <div class="modal_contain" id="new_client">
        <h2 class="sub_header mb4">New Client</h2>
        <form action="{{ route('clients.create') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <div class="clearfix">
                <div class="input_wrap column col_7{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                    <label class="small_faint">Client Brand</label>
                    <input type="text" name="brand_name" value=""  placeholder="e.g Coca Cola">
                    @if($errors->has('brand_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('brand_name') }}</span>
                        </strong>
                    @endif
                </div>
                <input type="hidden" name="broadcaster_id" value="{{ null }}">
                <input type="hidden" name="client_type_id" value="2">
                <input type="hidden" name="agency_id" value="">
                <div class='column col_5 file_select align_center pt3{{ $errors->has('image_url') ? ' has-error' : '' }}'>
                    <input type="file" id="file" name="image_url" />
                    <span class="small_faint block_disp mb3">Add Logo</span>
                    @if($errors->has('image_url'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('image_url') }}</span>
                        </strong>
                    @endif
                </div>
            </div>

            <div class="input_wrap{{ $errors->has('email') ? ' has-error' : '' }}">
                <label class="small_faint">Email</label>
                <input type="email" name="email" placeholder="name@example.com">
                @if($errors->has('email'))
                    <strong>
                        <span class="error-block" style="color: red;">{{ $errors->first('email') }}</span>
                    </strong>
                @endif
            </div>

            <div class="input_wrap{{ $errors->has('phone') ? ' has-error' : '' }}">
                <label class="small_faint">Phone Number</label>
                <input type="text" name="phone" placeholder="234** **** ****">
                @if($errors->has('phone'))
                    <strong>
                        <span class="error-block" style="color: red;">{{ $errors->first('phone') }}</span>
                    </strong>
                @endif
            </div>

            <div class="input_wrap{{ $errors->has('address') ? ' has-error' : '' }}">
                <label class="small_faint">Address</label>
                <input type="text" name="address" placeholder="Enter Address">
                @if($errors->has('address'))
                    <strong>
                        <span class="error-block" style="color: red;">{{ $errors->first('address') }}</span>
                    </strong>
                @endif
            </div>

            <div class="input_wrap">
                <label class="small_faint">Industry</label>

                <div class="select_wrap">
                    <select name="industry" id="industry">
                        <option value="">Select Industry</option>
                        @foreach($industries as $industry)
                            <option value="{{ $industry->sector_code }}">{{ $industry->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="input_wrap">
                <label class="small_faint">Sub Industry</label>

                <div class="select_wrap">
                    <select name="sub_industry" id="sub_industry">

                    </select>
                </div>
            </div>

            <div class="clearfix mb">
                <div class="input_wrap col_6 column{{ $errors->has('first_name') ? ' has-error' : '' }}">
                    <label class="small_faint">First Name</label>
                    <input type="text" id="first_name" name="first_name" placeholder="Enter First Name">
                    @if($errors->has('first_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('first_name') }}</span>
                        </strong>
                    @endif
                </div>

                <div class="input_wrap col_6 column{{ $errors->has('last_name') ? ' has-error' : '' }}">
                    <label class="small_faint">Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Enter Last Name">
                    @if($errors->has('last_name'))
                        <strong>
                            <span class="error-block" style="color: red;">{{ $errors->first('last_name') }}</span>
                        </strong>
                    @endif
                </div>
            </div>


            <div class="align_right">
                <input type="submit" value="Create Client" class="btn uppercased update">
            </div>

        </form>
    </div>
@stop

@section('scripts')
    <script>
        $(document).ready(function () {
            // $("#state").change(function() {
            $('#industry').on('change', function(e){
                $(".modal_contain").css({
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
                            $(".modal_contain").css({
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
