@extends('layouts.new_app')

@section('title')
    <title>Faya - Dashboard</title>
@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>All Brands </h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>Broadcaster</a></li>
                        <li><a href="#">All Brands</a></li>
                    </ul>
                </div>
            </div>
                <div class="row">
                    <div class="col-lg-4">
                        <form action="{{ route('broadcasters.brands.search') }}" method="get">
                            <div class="input-group">
                                <input type="text" class="form-control" required name="result" placeholder="Search for brands">
                                <span class="input-group-btn">
                                <button class="btn btn-default" type="submit">Go!</button>
                              </span>
                            </div><!-- /input-group -->
                        </form>
                    </div>
                </div>
                <p><br></p>
            <div class="row">
                <div class="col-12 all-brands">
                    @foreach ($brands as $brand)
                        <div class="col-6">
                            <div class="col-6">
                                <h2>{{ $brand->name }}</h2>
                                <a href="" class="edit" data-toggle="modal" data-target=".{{ $brand->id }}">Edit</a> <a href="" class="delete" data-toggle="modal" data-target=".{{ $brand->id }}delete">Delete</a>
                            </div>
                        <div class="col-6">
                            <img src="{{ $brand->image_url ? asset(decrypt($brand->image_url)) : asset('new_assets/images/logo.png') }}">
                        </div>
                    </div>
                @endforeach
            </div>
                <div class="text-center">
                    {{ $brands->links() }}
                </div>
        </div>
    </div>

    @foreach($brands as $brand)
        <div class="modal fade {{ $brand->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>Edit : {{ $brand->name }}</h4>
                    </div>
                    <form action="{{ route('brands.update', ['id' => $brand->id]) }}" method="post">
                        <div class="modal-body">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="group">
                                    <input type="text" name="brand_name" value="{{ $brand->name }}" class="form-control">
                                    <p><br></p>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Update Brand</button>
                            <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    @foreach($brands as $brand)
        <div class="modal fade {{ $brand->id }}delete" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="padding: 5%">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4>Delete : {{ $brand->name }}</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this brand?</p>
                    </div>
                    <div class="modal-footer">
                        <a class="btn btn-danger" href="{{ route('brands.delete', ['id' => $brand->id ]) }}">Yes</a>
                        <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
@stop

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop

