@extends('layouts.app')

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

@section('title', trans('app.create-client'))

<section class="content-header">
    <h1>
        Clients Management <small>Create Client</small>
    </h1>

    <ol class="breadcrumb" style="font-size: 16px">
        <li><a href="#"><i class="fa fa-edit"></i> Clients Management</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> Create Client</a> </li>
    </ol>
</section>

<!-- Main content -->

<section class="content">
    <form action="{{ url('/clients/store') }}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-8">
                @include('clients.partials.details', ['edit' => false, 'profile' => false])
            </div>
            <div class="col-md-4">
                @include('clients.partials.auth', ['edit' => false])
            </div>
        </div>

        <div class="row">
            @include('clients.partials.other_details')
        </div>

        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                    Create Client
                </button>
            </div>
        </div>
    </form>
</section>

@stop

@section('scripts')

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}

@stop