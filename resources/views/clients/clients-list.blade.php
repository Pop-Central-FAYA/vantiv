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
                        <li><a href="#">Client List</a></li>
                    </ul>
                </div>

                @if(count($clients) === 0)
                    <p><h3>Sorry you don't have a client yet...</h3></p>
                @else
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
                                    <li>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <a href="{{ route('client_brands', ['id' => $client['client_id']]) }}" class="btn btn-success">View Brands </a>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="{{ route('agency_campaign.step1', ['id' => $client['agency_client_id']]) }}" style="background-color: #00c4ca; color: white;" class="btn btn-default">Create Campaign</a>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-default" data-toggle="modal" data-target="#brand{{ $client['client_id'] }}">Add Brand</button>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-5">
                                <img style="height: 400px;" class="img-responsive" src="{{ $client['image_url'] ? asset($client['image_url']) : '' }}" alt="">
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    <div class="text-center">
        {{ $clients->links() }}
    </div>

    @foreach($clients as $client)
    <div class="modal fade" id="brand{{ $client['client_id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">
                        Add a new brand for <strong>{{ $client['name'] }}</strong>
                    </h4>
                </div>

                <form action="{{ route('agency.brand.store') }}" method="post" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="modal-body">
                        <div class="input-group{{ $errors->has('brand_name') ? ' has-error' : '' }}">
                            <label for="brand">Brand Name</label>
                            <input type="text" class="form-control" name="brand_name" value=""  placeholder="Brand Name">

                            @if($errors->has('brand_name'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('brand_name') }}</span>
                                </strong>
                            @endif
                        </div>
                        <div class="input-group">
                            <input type="hidden" name="clients" value="{{ $client['agency_client_id'] }}">
                        </div>
                        <div class="input-group{{ $errors->has('brand_logo') ? ' has-error' : '' }}">
                            <label for="brand_logo">Brand Logo</label>
                            <input type="file" class="form-control" name="brand_logo" value=""  placeholder="">

                            @if($errors->has('brand_logo'))
                                <strong>
                                    <span class="help-block">{{ $errors->first('brand_logo') }}</span>
                                </strong>
                            @endif
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Add Brand</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    @endforeach

@stop

@section('scripts')

    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! HTML::script('assets/js/as/profile.js') !!}

@stop