@extends('layouts.ssp.layout')

@section('title')
    <title> Torch | Users </title>
@stop

@section('content')
    @include('partials.new-frontend.broadcaster.inventory_management.sidebar')
    <!-- main container -->
    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Users</h2>
            </div>
        </div>

        <!-- campaign details -->
        <div class="the_frame client_dets border_top_color mb4 load_this_div">

            <!-- filter -->
            <div class="filters clearfix">

                <div class="column col_4 date_filter">
                    <a href="" class="active">ALL</a>
                </div>

                <div class="column col_4">
                    <div class="header_search">

                        <input type="text" name="users" id="searchTable" class="key_search" placeholder="Search ...">

                    </div>
                </div>

                <div class="column col_4 clearfix">
                    @if(Auth::user()->hasPermissionTo('create.user'))
                        <div class="col_8 right align_right">
                            <a href="{{ route('user.invite') }}" class="btn small_btn"><span class="_plus"></span>Invite New User</a>
                        </div>
                    @endif
                </div>

            </div>
            <!-- end -->

            <table class="display user">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role(s)</th>
                    @if(Auth::user()->companies()->count() > 1)
                        <th>Company</th>
                    @endif
                    @if(Auth::user()->hasPermissionTo('update.user'))
                        <th>Edit</th>
                    @endif
                    @if(Auth::user()->hasPermissionTo('update.user'))
                        <th>Status</th>
                    @endif
                </tr>
                </thead>
            </table>

        </div>
    </div><!-- main contain -->
    @foreach($users as $user)
    <div class="modal_contain load_this_div" style="height: 250px;" id="user_modal_{{ $user['id'] }}">
        <div class="wallet_placer margin_center mb3"></div>
        <form class="resend_invite" data-user_id="{{ $user['id'] }}" id="invite_{{ $user['id'] }}">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="user_id" value="{{ $user['id'] }}">
            <div class="clearfix mb3">
                <p>Click on the send invitation button to re send an invite</p>
            </div>
            <div class="align_right">
                <button type="submit" class="btn button_create">Send Invitation Mail</button>
            </div>
        </form>
    </div>
    @endforeach
@stop

@section('scripts')
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    {{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        $(document).ready(function () {
            var can_edit = '<?php echo Auth::user()->hasPermissionTo('update.user') ?>';
            var can_toggle_status = '<?php echo Auth::user()->hasPermissionTo('update.user') ?>';
            $("body").delegate(".modal_user_click", "click", function() {
                var href = $(this).attr("href");
                $(href).modal();
                return false;
            });

            var UserList =  $('.user').DataTable({
                dom: 'Blfrtip',
                paging: true,
                serverSide: true,
                processing: true,
                aaSorting: [],
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                aLengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                oLanguage: {
                    sLengthMenu: "_MENU_"
                },
                ajax: {
                    url: '/user/data-table',
                },
                columns: getColumn(),
            });

            $('.key_search').on('keyup', function(){
                UserList.search($(this).val()).draw() ;
            });

            function getColumn() {
                var data = [
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'roles', name: 'roles'},
                ];
                if(companies > 1){
                    data.push({data: 'company', name: 'company'})
                }
                if(can_edit){
                    data.push({data: 'edit', name: 'edit'})
                }
                if(can_toggle_status){
                    data.push({data: 'status', name: 'status'})
                }
                return data;
            }

            $(".resend_invite").on('submit', function(e) {
                event.preventDefault(e);
                $('.load_this_div').css({
                    opacity : 0.2
                });
                $('a').css('pointer-events','none');
                $('.button_create').hide();
                var user_id = $(this).data('user_id');
                var formdata = $("#invite_"+user_id).serialize();
                var weHaveSuccess = false;

                $.ajax({
                    cache: false,
                    type: "POST",
                    url: '/user/resend/invitation',
                    dataType: 'json',
                    data: formdata,
                    beforeSend: function(data) {
                        // run toast showing progress
                        toastr_options = {
                            "progressBar": true,
                            // "showDuration": "300",
                            "preventDuplicates": true,
                            "tapToDismiss": false,
                            "hideDuration": "1",
                            "timeOut": "300000000"
                        };
                        msg = "Processing your request..."
                        toastr.info(msg, null, toastr_options)
                    },
                    success: function (data) {
                        toastr.clear();
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            location.href = '/user/all';
                        } else {
                            toastr.error(data.message);
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }
                        weHaveSuccess = true;
                    },
                    error : function (xhr) {
                        toastr.clear();
                        if(xhr.status === 500){
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else if(xhr.status === 503){
                            toastr.error('The request took longer than expected, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }else{
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.button_create').show();
                            return;
                        }
                    }
                })
            });

            $('body').delegate('.user_status', 'change', function() {
                var user_id = $(this).data('user_id');
                var select_value = $('#status_'+user_id).val();
                event.preventDefault();
                $('.load_this_div').css({
                    opacity : 0.2
                });
                $('a').css('pointer-events','none');
                $('.user_status').prop('disabled', 'disabled');
                $.ajax({
                    cache: false,
                    type: "GET",
                    url: '/user/status/update',
                    dataType: 'json',
                    data: {user_id : user_id, status : select_value},
                    beforeSend: function(data) {
                        // run toast showing progress
                        toastr_options = {
                            "progressBar": true,
                            // "showDuration": "300",
                            "preventDuplicates": true,
                            "tapToDismiss": false,
                            "hideDuration": "1",
                            "timeOut": "300000000"
                        };
                        msg = "Processing your request..."
                        toastr.info(msg, null, toastr_options)
                    },
                    success: function (data) {
                        toastr.clear();
                        if (data.status === 'success') {
                            toastr.success(data.message);
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.user_status').prop('disabled', false);
                            $('.user').DataTable().ajax.reload();
                        } else {
                            toastr.error(data.message);
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.user_status').prop('disabled', false);
                            return;
                        }
                        weHaveSuccess = true;
                    },
                    error : function (xhr) {
                        toastr.clear();
                        if(xhr.status === 500){
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.user_status').prop('disabled', false);
                            return;
                        }else if(xhr.status === 503){
                            toastr.error('The request took longer than expected, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.user_status').prop('disabled', false);
                            return;
                        }else{
                            toastr.error('An unknown error has occurred, please try again');
                            $('.load_this_div').css({
                                opacity: 1
                            });
                            $('a').css('pointer-events','');
                            $('.user_status').prop('disabled', false);
                            return;
                        }
                    }
                })
            })

        })
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <style>

        .dataTables_filter {
            display: none;
        }

        #DataTables_Table_0_wrapper .dt-buttons button {
            line-height: 2.5;
            color: #fff;
            cursor: pointer;
            background: #44C1C9;
            -webkit-appearance: none;
            font-family: "Roboto", sans-serif;
            font-weight: 500;
            border: 0;
            padding: 3px 20px 0;
            font-size: 14px;

            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;

            -webkit-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
            -moz-box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);
            box-shadow: 9px 10px 20px 1px rgba(0, 159, 160, 0.21);

            position: relative;
            display: inline-block;
            text-transform: uppercase;
        !important;
        }
    </style>
@stop
