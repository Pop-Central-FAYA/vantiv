@extends('layouts.faya_app')

@section('title')
    <title> FAYA | Users </title>
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
        <div class="the_frame client_dets border_top_color mb4">

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

                    <div class="col_8 right align_right">
                        <a href="{{ route('user.invite') }}" class="btn small_btn"><span class="_plus"></span>Invite New User</a>
                    </div>

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
                    <th>Edit</th>
                    <th>Status</th>
                </tr>
                </thead>
            </table>

        </div>
    </div><!-- main contain -->

    @foreach($users as $user)
        <div class="modal_contain reload_content" style="width: 100%; max-width: 50%; padding: 0;" id="user_modal_{{ $user['id'] }}">
            <div class="the_frame clearfix border_top_color pt load_this_div">
                <form class="submit_form" id="update_{{ $user['id'] }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="margin_center col_11 clearfix pt4 create_fields">

                        <div class="clearfix mb3">
                            @if($user['name'] !== null)
                                <p> {{ $user['name'] }} </p><br>
                            @endif
                            <p>{{ $user['email'] }}</p>
                        </div>

                        <input type="hidden" name="user_id" value="{{ $user['id'] }}">
                        <p class="m-b">Roles</p>
                        <div class="clearfix mb3">
                            <div class="column col_12">
                                <div class="create_check clearfix mb">
                                    <ul>
                                        @foreach($roles as $role)
                                            <li class="col_4 column m-b">
                                                <input name="roles[]" value="{{ $role['id'] }}"
                                                       @foreach($user['role_name'] as $checked_role)
                                                       @if($checked_role === $role['role'])
                                                       checked
                                                       @endif
                                                       @endforeach
                                                       type="checkbox" id='{{ $role['id'] }}'>
                                                <label for="{{ $role['id'] }}">{{ $role['label'] }}</label>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->hasRole('ssp.super_admin'))
                            <p class='m-b'>Company</p>
                            <div class="clearfix mb3 ">
                                <div class="column col_12">
                                    <div class="create_check clearfix mb">
                                        <ul>
                                            @foreach($companies as $company)
                                                <li class="col_4 column m-b">
                                                    <input name="companies[]" value="{{ $company->id }}"
                                                           @foreach($user['company_id'] as $checked_company)
                                                           @if($checked_company === $company->id)
                                                           checked
                                                           @endif
                                                           @endforeach
                                                           type="checkbox" id='{{ $company->id }}'>
                                                    <label for="{{ $company->id }}">{{ $company->name }}</label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endif
                        <p><br></p>
                        <div class="mb4 align_right pt">
                            <input type="submit" value="Update User" id="submit_user{{ $user['id'] }}" class="btn uppercased mb4">
                        </div>

                    </div>
                </form>
            </div>
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
                    data.push({data: 'company', name: 'company'},{data: 'edit', name: 'edit'},{data: 'status', name: 'status'})
                }else{
                    data.push({data: 'edit', name: 'edit'},{data: 'status', name: 'status'})
                }
                return data;
            }
        })
    </script>

@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.1/css/buttons.dataTables.min.css" type="text/css"/>
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
