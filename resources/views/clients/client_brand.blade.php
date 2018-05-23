@extends('layouts.new_app')

@section('title')
    <title>Agency | All Client's Brands</title>
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
                    <h1>All Client's Brands</h1>
                    <ul>
                        <li><a href="{{ route('clients.list') }}"><i class="fa fa-edit"></i>All Clients</a></li>
                    </ul>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Brands</th>
                                <th>No. of Campaigns</th>
                                <th>Brand Logo</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($brands as $brand)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ ucfirst($brand['brand']) }}</td>
                                    <td>{{ $brand['campaigns'] }}</td>
                                    <td><img src="{{ $brand['image_url'] ? asset(decrypt($brand['image_url'])) : '' }}" class="img-circle img-responsive" alt=""></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}

@stop