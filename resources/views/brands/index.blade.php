@extends('layouts.app')

@section('content')

@section('title', 'Faya | Dashboard')
<!-- Content Header (Page header) -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Welcome {{ Auth::user()->username }}!

    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> All Brands</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-xs-12">
            <div class="box">
                <div class="box-body">
                    <div class="table-responsive">
                        <table id="brands" class="table table-bordered brands">
                            <thead>
                                <th>S/N</th>
                                <th>Brands</th>
                                <th>Actions</th>
                            </thead>
                            <tbody>
                                @foreach($brand as $brands)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $brands->name }}</td>
                                        <td><a href="" class="btn btn-primary btn-xs" data-toggle="modal" data-target=".{{ $brands->id }}">Edit</a> | <a href="" class="btn btn-danger btn-xs" data-toggle="modal" data-target=".{{ $brands->id }}delete">Delete</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.row -->
</section>

@foreach($brand as $brands)
<div class="modal fade {{ $brands->id }}" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="padding: 5%">
            <h4>Edit : {{ $brands->name }}</h4>
            <hr>

            <form action="{{ route('brands.update', ['id' => $brands->id]) }}" method="post">
                {{ csrf_field() }}
                <div class="row">
                    <div class="group">
                        <input type="text" name="brand_name" value="{{ $brands->name }}" class="form-control">
                    </div>
                </div>
                <br>
                <div class="row">
                    <button type="submit" class="btn btn-success">Update Brand</button>
                </div>
            </form>

        </div>
    </div>
</div>
@endforeach

@foreach($brand as $brands)
    <div class="modal fade {{ $brands->id }}delete" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="padding: 5%">
                <h4>Delete : {{ $brands->name }}</h4>
                <hr>

                <p>Are you sure you want to delete this brand?</p>
                <br>


                <a class="btn btn-danger btn-xs" href="{{ route('brands.delete', ['id' => $brands->id ]) }}">Yes</a> <button class="btn btn-primary btn-xs" data-dismiss="modal">Cancel</button>

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

