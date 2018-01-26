@extends('layouts.app')

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

@section('title', trans('app.view-client'))

<section class="content-header">
    <h1>
        All Clients <small>Clients Portfolio</small>
    </h1>

    <ol class="breadcrumb" style="font-size: 16px">
        <li><a href="#"><i class="fa fa-edit"></i> Clients Management</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> View Client</a> </li>
    </ol>
</section>

<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-xs-12">

            <div class="col-md-6" style="margin-bottom: 10px;"></div>

            <div class="col-md-12">

                <h3 align="right">Current Balance: N200,000</h3>

                <h2>{{ $user_details[0]->first_name . ' ' . $user_details[0]->last_name }}</h2>

                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-body">
                            <table id="example1" class="table table-bordered table-striped" style="font-size: 16px">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>No. of Adslots</th>
                                        <th>Total Expense</th>
                                        <th>Date Created</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($campaign as $campaigns)
                                        <tr>
                                            <td>
                                                {{ $campaigns['product'] }}
                                            </td>
                                            <td>{{ $campaigns['num_of_slot'] }}</td>
                                            <td>&#8358;{{ number_format($campaigns['payment'], 2) }}</td>
                                            <td>{{ date('M j, Y', strtotime($campaigns['date'])) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

        </div>


    </div>
</section>

@stop

@section('scripts')

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}

@stop