@extends('layouts.new_app')

@section('content')

@section('title', 'Faya | Dashboard')

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
            <div class="col-12 all-brands">
                @foreach ($brand as $brands)
                    <div class="col-6">
                        <div class="col-6">
                            <h2>{{ $brands->name }}</h2>
                            <a href="" class="edit" data-toggle="modal" data-target=".{{ $brands->id }}">Edit</a> <a href="" class="delete" data-toggle="modal" data-target=".{{ $brands->id }}delete">Delete</a>
                        </div>
                        <div class="col-6"> <img src="{{ asset('new_assets/images/samsung.png') }}"></div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@foreach($brand as $brands)
    <div class="modal fade {{ $brands->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="padding: 5%">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4>Edit : {{ $brands->name }}</h4>
                </div>
                <div class="modal-body">
                    <form action="{{ route('brands.update', ['id' => $brands->id]) }}" method="post">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="group">
                                <input type="text" name="brand_name" value="{{ $brands->name }}" class="form-control">
                                <button type="submit" class="btn btn-success">Update Brand</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach($brand as $brands)
    <div class="modal fade {{ $brands->id }}delete" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="padding: 5%">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4>Delete : {{ $brands->name }}</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this brand?</p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger" href="{{ route('brands.delete', ['id' => $brands->id ]) }}">Yes</a> <button class="btn btn-primary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>
@endforeach
<!-- /.content -->
@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}


@stop

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop

