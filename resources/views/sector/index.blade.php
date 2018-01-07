@extends('layouts.app')

@section('content')

@section('title', trans('app.all_sectors'))
<!-- Content Header (Page header) -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        {{--Welcome {{ Auth::user()->username }}!--}}

    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> All Sectors</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>All Sectors</h3>
            </div>
            <div class="panel-body">
                @if(count($sectors) === 0)
                    <h4>OOPs!!!, You have sectors on your system, please create one</h4>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Sector Code</th>
                                <th>Time Created</th>
                                <th>Time Modified</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sectors as $sector)
                                <tr><td>{{ date('M j, Y', strtotime($sector->time_created)) }}</td>
                                    <td>{{ date('M j, Y', strtotime($sector->time_modified)) }}</td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sector->name }}</td>
                                    <td>{{ $sector->sector_code }}</td>

                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <!-- /.col (RIGHT) -->
    </div>
    <!-- /.row -->

</section>
<!-- /.content -->
@stop

@section('scripts')



@stop