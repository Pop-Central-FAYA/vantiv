@extends('layouts.app')

@section('stylesheets')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

@section('title', trans('app.view-clients'))

<section class="content-header">
    <h1>
        All Clients <small>All Clients</small>
    </h1>

    <ol class="breadcrumb" style="font-size: 16px">
        <li><a href="#"><i class="fa fa-edit"></i> Clients Management</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> View Clients</a> </li>
    </ol>
</section>

<!-- Main content -->

<section class="content">

    <div class="row">

        <div class="col-xs-12">

            <div class="col-md-6" style="margin-bottom: 10px;"></div>

                <div class="col-md-12">

                    <div class="col-xs-12">
                        <div class="box">

                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-striped" style="font-size: 16px">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>No. of Campaigns</th>
                                            <th>Total Expense</th>
                                            <th>Date Created</th>
                                            <th>Last Campaign</th>
                                        </tr>
                                    </thead>

                                <tbody>
                                    @foreach ($clients as $client)
                                        <tr>
                                            <td>
                                                <a href="{{ route('client.show', ['client_id' => $client['client_id']]) }}">
                                                    <p>
                                                        <img src="{{ asset($client['image_url']) }}" width="100" height="100">
                                                    </p>
                                                </a>
                                            </td>
                                            <td>{{ $client['num_campaign'] }}</td>
                                            <td>&#8358;{{ number_format($client['total'],2) }}</td>
                                            <td>{{ date('M j, Y h:ia', strtotime($client['created_at'])) }}</td>
                                            <td>{{ date('M j, Y', strtotime($client['last_camp'])) }}</td>
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