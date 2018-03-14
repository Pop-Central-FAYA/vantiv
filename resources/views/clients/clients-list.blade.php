@extends('layouts.new_app')

@section('title')
    <title>Agency | All Clients</title>
@stop

@section('styles')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">

@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Clients</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-edit"></i>Clients Management</a></li>
                        <li><a href="#">Create Client</a></li>
                    </ul>
                </div>

                @foreach ($clients as $client)
                    <div class="col-12 Clients-Management">
                        <div class="col-7">
                            <ul>
                                <li>Name: <span>{{ $client['name'] }}</span></li>
                                <li>No. of Campaigns <span>{{ $client['num_campaign'] }}</span></li>
                                <li>Total Expense <span>&#8358;{{ number_format($client['total'],2) }}</span></li>
                                <li>Data Created<span>{{ date('M j, Y h:ia', strtotime($client['created_at'])) }}</span></li>
                                @if($client['last_camp'] === 0)
                                    <li>Last Campaign<span></span></li>
                                @else
                                    <li>Last Campaign<span>{{ date('M j, Y', strtotime($client['last_camp'])) }}</span></li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-5">
                            <img src="{{ $client['image_url'] ? asset(decrypt($client['image_url'])) : asset('new_assets/images/logo.png') }}" alt="">
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="text-center">
        {{ $clients->links() }}
    </div>

@stop

@section('scripts')

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}

@stop