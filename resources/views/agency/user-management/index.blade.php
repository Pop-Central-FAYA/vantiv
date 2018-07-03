@extends('layouts.faya_app')

@section('title')
    <title>FAYA | User Management</title>
@stop

@section('content')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
        @include('partials.new-frontend.agency.header')

        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">User Management</h2>
            </div>

            <div class="column col_6 align_right">
                <a href="#new_user" class="btn modal_click">Invite User</a>
            </div>
        </div>



        <div class="similar_table pt3">
            <!-- table header -->
            <div class="_table_header clearfix m-b">
                <span class="weight_medium small_faint block_disp padd column col_4">User Info</span>
                <span class="weight_medium small_faint block_disp column col_2">Role</span>
                <span class="weight_medium small_faint block_disp column col_3">Email</span>
                <span class="weight_medium small_faint block_disp column col_2">Status</span>
                <span class="weight_medium color_trans block_disp column col_1">.</span>
            </div>

            <!-- table item -->
            <div class="_table_item the_frame clearfix">
                <div class="padd column col_4">
                    <span class="client_ava"><img src="{{ asset('new_frontend/img/coke.jpg') }}"></span>
                    <p>Jane Doe</p>
                    <span class="small_faint">Added 12th May, 2018</span>
                </div>
                <div class="column col_2">Admin</div>
                <div class="column col_3">jane@gmail.com</div>
                <div class="column col_2">
                    <span class="span_state status_success">Active</span>
                </div>
                <div class="column col_1">
                    <span class="more_icon"></span>
                </div>
            </div>
            <!-- table item end -->

            <!-- table item -->
            <div class="_table_item the_frame clearfix">
                <div class="padd column col_4">
                    <span class="client_ava"><img src="{{ asset('new_frontend/img/coke.jpg') }}"></span>
                    <p>Jane Doe</p>
                    <span class="small_faint">Added 12th May, 2018</span>
                </div>
                <div class="column col_2">Admin</div>
                <div class="column col_3">jane@gmail.com</div>
                <div class="column col_2">
                    <span class="span_state status_success">Active</span>
                </div>
                <div class="column col_1">
                    <span class="more_icon"></span>
                </div>
            </div>
            <!-- table item end -->

            <!-- table item -->
            <div class="_table_item the_frame clearfix">
                <div class="padd column col_4">
                    <span class="client_ava"><img src="{{ asset('new_frontend/img/coke.jpg') }}"></span>
                    <p>Jane Doe</p>
                    <span class="small_faint">Added 12th May, 2018</span>
                </div>
                <div class="column col_2">Admin</div>
                <div class="column col_3">jane@gmail.com</div>
                <div class="column col_2">
                    <span class="span_state status_success">Active</span>
                </div>
                <div class="column col_1">
                    <span class="more_icon"></span>
                </div>
            </div>
            <!-- table item end -->
        </div>

    </div>

    <!-- new client modal -->
    <div class="modal_contain" id="new_user">
        <h2 class="sub_header mb4">Invite User</h2>


        <div class="input_wrap">
            <label class="small_faint weight_medium uppercased">Full Name</label>
            <input type="text" placeholder="John Doe">
        </div>

        <div class="input_wrap">
            <label class="small_faint weight_medium uppercased">Email</label>
            <input type="text" placeholder="john@example.com">
        </div>

        <div class="input_wrap">
            <label class="small_faint weight_medium uppercased">Mobile Number</label>
            <input type="text" placeholder="08031234567">
        </div>

        <div class="input_wrap">
            <label class="small_faint weight_medium uppercased">Role</label>

            <div class="select_wrap">
                <select>
                    <option>Select Role</option>
                    <option></option>
                    <option></option>
                </select>
            </div>
        </div>

        <div class="input_wrap">
            <label class="small_faint weight_medium uppercased">Permissions</label>

            <div class="select_wrap">
                <select>
                    <option>Select Permissions</option>
                    <option></option>
                    <option></option>
                </select>
            </div>
        </div>


        <div class="align_right pt3">
            <a href="" class="padd color_initial light_font" onclick="$.modal.close();">Cancel</a>
            <input type="submit" value="Invite User" class="btn uppercased">
        </div>
    </div>
@stop