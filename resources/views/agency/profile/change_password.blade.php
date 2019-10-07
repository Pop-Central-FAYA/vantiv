@extends('dsp_layouts.old_auth')

@section('page-title', 'Reset Password')

@section('content')

    <div class="form-wrap col-md-6 auth-form" id="login">
        <div style="text-align: center; margin-bottom: 25px;">
            <a href="{{ route('dashboard', false) }}">{!! AssetsHelper::logo() !!}</a>
        </div>

        {{--@include('partials/messages')--}}

                    <v-content>
                        <change-password
                                :token="{{ json_encode($token) }}" 
                                :routes="{{ json_encode($routes) }}" 
                                >
                        </change-password>
                    </v-content>

    </div>

@stop

@section('scripts')
    {!! HTML::script('assets/js/as/login.js') !!}
@stop

