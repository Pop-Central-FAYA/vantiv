@extends('dsp_layouts.old_auth')

@section('page-title', trans('app.login'))

@section('content')

    <div class="form-wrap col-md-6 auth-form" id="login">
        <div style="text-align: center; margin-bottom: 25px;">
            <a href="{{ route('dashboard') }}">{!! AssetsHelper::logo() !!}</a>
        </div>

        {{--@include('partials/messages')--}}

                    <v-content>
                        <change-password
                                :user-data="{{ json_encode($user) }}" 
                                :routes="{{ json_encode($routes) }}" 
                                :permission-list="{{ json_encode($permissions) }}">
                        </change-password>
                    </v-content>

    </div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/as/login.js') !!}
@stop

