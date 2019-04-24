@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Invite User</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
    @if(Session::get('broadcaster_id'))
        @include('partials.new-frontend.broadcaster.header')
        @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
    @else
        @include('partials.new-frontend.agency.header')
    @endif

    <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Invite User(s)</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_7 clearfix pt4 create_fields">

                <form >
                    {{ csrf_field() }}
                    <div class="create_gauge">
                        <div class=""></div>
                    </div>
                    <div class="clearfix mb">
                        <div class="input_wrap column col_12">
                            <label class="small_faint">Email(s)</label>
                            <input style="display: inline-block;width: 100%;" type="text" name="emails" id="emails" placeholder="Enter email addresses. Add commas for multiples">
                        </div>

                    </div>

                    <div class="input_wrap">
                        <label class="small_faint">Roles</label>

                        <div class="select_wrap">
                            <select class="js-example-basic-multiple" id="roles" name="roles[]" multiple="multiple" >
                                <option value=""></option>
                                @foreach($roles as $role)
                                    <option value="{{ $role['id'] }}">
                                        {{ $role['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->companies->count() > 1)
                        <div class="input_wrap">
                        <label class="small_faint">Company</label>

                        <div class="select_wrap">
                            <select class="js-example-basic-multiple" id="companies" name="companies[]" multiple="multiple" >
                                <option value=""></option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
                    <div class="mb4 clearfix pt4 mb4">

                        <div class="column col_12 align_right">
                            <button type="submit"  class="btn uppercased ">Invite User(s) <span class=""></span></button>
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
        $(document).ready(function () {
            //select for target audience
            $('.js-example-basic-multiple').select2();
            //placeholder for target audienct
            $('#roles').select2({
                placeholder: "Please select Role(s)"
            });

            $('#companies').select2({
                placeholder: "Please select Company(s)"
            });
        });
    </script>
@stop

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop


