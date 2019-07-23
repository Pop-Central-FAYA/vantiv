@extends('layouts.ssp.layout')

@section('title')
    <title>Torch | Edit User</title>
@stop

@section('content')
    <div class="main_contain">
    <!-- header -->
    @include('partials.new-frontend.broadcaster.header')
    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Edit User</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_this_div">

            <div class="margin_center col_7 clearfix pt4 create_fields">
                <form class="update_user" data-user_id="{{ $user->id }}" id="update_{{ $user->id }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="margin_center col_11 clearfix pt4 create_fields">

                        <div class="clearfix mb3">
                            @if($user->full_name !== null)
                                <p> {{ $user->full_name }} </p><br>
                            @endif
                            <p>{{ $user->email }}</p>
                        </div>

                        <input type="hidden" name="user_id" value="{{ $user->id }}">
                        <p class="m-b">Roles</p>
                        <div class="clearfix mb3">
                            <div class="column col_12">
                                <div class="create_check clearfix mb">
                                    <ul>
                                        @foreach($roles as $role)
                                            <li class="col_4 column m-b">
                                                <input name="roles[]" value="{{ $role['id'] }}"
                                                       @foreach($user->getRoleNames() as $checked_role)
                                                       @if($checked_role === $role['role'])
                                                       checked
                                                       @endif
                                                       @endforeach
                                                       type="checkbox" class="check_box role_user_{{ $user['id'] }}" id='{{ $role['id'] }}'>
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
                                                           @foreach($user->companies as $checked_company)
                                                           @if($checked_company->id === $company->id)
                                                           checked
                                                           @endif
                                                           @endforeach
                                                           type="checkbox" class="check_box company_user_{{ $user['id'] }}" id='{{ $company->id }}'>
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
                        <div class="column col_4">
                            <a href="{{ route('user.index') }}" class="btn uppercased _white _go_back"><span class=""></span> Back</a>
                        </div>
                        <div class="mb4 align_right pt">
                            <input type="submit" value="Update User" id="submit_user{{ $user->id }}" class="btn uppercased mb4 button_create">
                        </div>

                    </div>
                </form>
            </div>
        </div>
        <!-- main frame end -->
    </div>
@stop

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script>
        <?php echo "var companies =".Auth::user()->companies()->count().";\n"; ?>
        $(document).ready(function () {
            $(".update_user").on('submit', function(e) {
                event.preventDefault(e);
                $('.load_this_div').css({
                    opacity : 0.2
                });
                $('a').css('pointer-events','none');
                $('.button_create').hide();
                var user_id = $(this).data('user_id');
                if ($('.role_user_'+user_id).is(":checked") === false){
                    toastr.error('Please select at least a role');
                    $('.load_this_div').css({
                        opacity : 1
                    });
                    $('a').css('pointer-events','');
                    $('.button_create').show();
                    return;
                }
                if(companies > 1) {
                    if ($('.company_user_'+user_id).is(":checked") === false){
                        toastr.error('Please select at least a company');
                        $('.load_this_div').css({
                            opacity : 1
                        });
                        $('a').css('pointer-events','');
                        $('.button_create').show();
                        return;
                    }
                }
                var formdata = $("#update_"+user_id).serialize();
                var weHaveSuccess = false;

                $.ajax({
                    cache: false,
                    type: "POST",
                    url: '/user/update/'+user_id,
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
        });
    </script>
@stop

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop


