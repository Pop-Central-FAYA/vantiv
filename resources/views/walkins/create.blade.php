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
        <li><a href="#"><i class="fa fa-dashboard"></i> Create Walkins</a></li>
    </ol>
</section>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>Create Walkins</h3>
            </div>
            <div class="panel-body">
                <form action="{{ route('walkins.store') }}" method="post">
                    {{ csrf_field() }}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" name="first_name" placeholder="Walkins First Name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" name="last_name" placeholder="Walkins Last Name" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email"> Email</label>
                                <input type="email" name="email" placeholder="Walkins Email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone_number"> Phone Number</label>
                                <input type="number" name="phone_number" placeholder="Walkins Phone Number" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-success">Add Walkins</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.col (RIGHT) -->
    </div>
    <!-- /.row -->

</section>
<!-- /.content -->
@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    <script>


        $(document).ready(function(){
            $("#txtFromDate").datepicker({
                numberOfMonths: 2,
                onSelect: function (selected) {
                    $("#txtToDate").datepicker("option", "minDate", selected)
                }
            });

            $("#txtToDate").datepicker({
                numberOfMonths: 2,
                onSelect: function(selected) {
                    $("#txtFromDate").datepicker("option","maxDate", selected)
                }
            });

            $('input[type=radio][name=premium]').change(function() {
                if (this.value == 'true') {
                    $("#premium").show();
                }
                else if (this.value == 'false') {
                    $("#premium").hide();
                }
            });

        });
    </script>

@stop

@section('style')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}">
@stop