@extends('layouts.app')

@section('stylesheets')

    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" type="text/css"/>

@endsection

@section('content')

@section('title', trans('app.discount'))

<section class="content-header">
    <h1>
        Ad Management <small>Discount</small>

    </h1>
    <ol class="breadcrumb" style="font-size: 16px">

        <li><a href="#"><i class="fa fa-edit"></i> Ad Management</a> </li>
        <li><a href="index.html"><i class="fa fa-address-card"></i> Discount</a> </li>

    </ol>
</section>

<!-- Main content -->

<section class="content">
    <div class="row">
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <div class="col-md-8 Campaign" style="padding:2%"></div>
        <!-- /.col -->
        <div class="col-md-2 hidden-sm hidden-xs"></div>
        <!-- /.col -->

        <div class="row" style="padding: 5%">
            <div class="col-xs-12">

                <div class="col-md-11">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs" style="background:#eee">
                            <li class="active"><a href="#agency" data-toggle="tab">Agency</a></li>
                            <li ><a href="#brand" data-toggle="tab">Brand</a></li>
                            <li><a href="#time" data-toggle="tab">Time</a></li>
                            <li><a href="#daypart" data-toggle="tab">Day Part</a></li>
                            <li><a href="#price" data-toggle="tab">Price</a></li>
                            <li><a href="#pslot" data-toggle="tab">P.Slot</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="active tab-pane" id="agency">

                                <div class="add-disc" style="margin: auto">
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="discount_type_id" value="{{ $types[0]->id }}">
                                        <div class="row">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2">
                                                    <h4>Add a Discount</h4>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="discount_type_value" id="discount_type_value">
                                                        @foreach ($agencies as $agency)
                                                            <option value="{{ $agency['id'] }}">{{ $agency['fullname'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="percent_value" placeholder="Percent Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_start_date" placeholder="Percent Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_stop_date" placeholder="Percent Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="number" name="value" placeholder="Amount Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_start_date" placeholder="Value Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_stop_date" placeholder="Value Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="submit" value="Add" name="">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="box-body">

                                    @if(count($agency_discounts) === 0)

                                        <h4>OOPs!!!, You have agency discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped agency">
                                            <thead>
                                            <tr>
                                                <th>Agency</th>
                                                <th>Discount %</th>
                                                <th>Discount % Start Date</th>
                                                <th>Discount % Stop Date</th>
                                                <th>Discount N</th>
                                                <th>Discount N Start Date</th>
                                                <th>Discount N Stop Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($agency_discounts as $agency_discount)
                                                <tr>
                                                    <td>{{ $agency_discount->discount_type_sub_value }}</td>
                                                    <td>{{ $agency_discount->percent_value }}%</td>
                                                    <td>{{ date('Y-m-d', strtotime($agency_discount->percent_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($agency_discount->percent_stop_date)) }}</td>
                                                    <td>&#8358;{{ $agency_discount->value }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($agency_discount->value_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($agency_discount->value_start_date)) }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $agency_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $agency_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($agency_discounts as $agency_discount)

                                    <div class="modal fade" id="myModal{{ $agency_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">

                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $agency_discount->discount_type_sub_value }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $agency_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="discount_type_id" value="{{ $types[0]->id }}">
                                                    <input type="hidden" name="discount_type_value" value="{{ $agency_discount->discount_type_value }}">
                                                    <input type="hidden" name="discount_type_sub_value" value="{{ $agency_discount->discount_type_sub_value }}">
                                                    <div class="modal-body">

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Percent Value</label>
                                                                    <input type="number" name="percent_value" value="{{ $agency_discount->percent_value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Start Date</label>
                                                                    <input type="number" name="percent_start_date" value="{{ date('Y-m-d', strtotime($agency_discount->percent_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Stop Date</label>
                                                                    <input type="number" name="percent_stop_date" value="{{ date('Y-m-d', strtotime($agency_discount->percent_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Amount Value</label>
                                                                    <input type="number" name="value" value="{{ $agency_discount->value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Start Date</label>
                                                                    <input type="number" name="value_start_date" value="{{ date('Y-m-d', strtotime($agency_discount->value_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Stop Date</label>
                                                                    <input type="number" name="value_stop_date" value="{{ date('Y-m-d', strtotime($agency_discount->value_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <input type="submit" value="Update Discount" class="btn btn-primary" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach

                            </div>

                            <div class="tab-pane" id="brand">
                                <div class="add-disc">
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="discount_type_id" value="{{ $types[1]->id }}">
                                        <div class="row">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2">
                                                    <h4>Add a Discount</h4>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="discount_type_value">
                                                        @foreach ($brands as $brand)
                                                            <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="percent_value" placeholder="Percent Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_start_date" placeholder="Percent Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_stop_date" placeholder="Percent Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="number" name="value" placeholder="Amount Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_start_date" placeholder="Value Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_stop_date" placeholder="Value Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="submit" value="Add" name="">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="box-body">
                                    @if(count($brand_discounts) === 0)

                                        <h4>OOPs!!!, You have brand discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Brand</th>
                                                <th>Discount %</th>
                                                <th>Discount % Start Date</th>
                                                <th>Discount % Stop Date</th>
                                                <th>Discount N</th>
                                                <th>Discount N Start Date</th>
                                                <th>Discount N Stop Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($brand_discounts as $brand_discount)
                                                <tr>
                                                    <td>{{ $brand_discount->discount_type_value }}</td>
                                                    <td>{{ $brand_discount->percent_value }}%</td>
                                                    <td>{{ date('Y-m-d', strtotime($brand_discount->percent_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($brand_discount->percent_stop_date)) }}</td>
                                                    <td>&#8358;{{ $brand_discount->value }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($brand_discount->value_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($brand_discount->value_start_date)) }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $brand_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $brand_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($brand_discounts as $brand_discount)

                                    <div class="modal fade" id="myModal{{ $brand_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $brand_discount->discount_type_value }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $brand_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="discount_type_id" value="{{ $types[1]->id }}">
                                                    <input type="hidden" name="discount_type_value" value="{{ $brand_discount->discount_type_value }}">
                                                    <input type="hidden" name="discount_type_sub_value" value="{{ $brand_discount->discount_type_sub_value }}">

                                                    <div class="modal-body">

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Percent Value</label>
                                                                    <input type="number" name="percent_value" value="{{ $brand_discount->percent_value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Start Date</label>
                                                                    <input type="number" name="percent_start_date" value="{{ date('Y-m-d', strtotime($brand_discount->percent_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Stop Date</label>
                                                                    <input type="number" name="percent_stop_date" value="{{ date('Y-m-d', strtotime($brand_discount->percent_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Amount Value</label>
                                                                    <input type="number" name="value" value="{{ $brand_discount->value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Start Date</label>
                                                                    <input type="number" name="value_start_date" value="{{ date('Y-m-d', strtotime($brand_discount->value_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Stop Date</label>
                                                                    <input type="number" name="value_stop_date" value="{{ date('Y-m-d', strtotime($brand_discount->value_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <input type="submit" value="Update Discount" class="btn btn-primary" />
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>

                            <div class="tab-pane" id="time">

                                <div class="add-disc">
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="discount_type_id" value="{{ $types[2]->id }}">
                                        <div class="row">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2">
                                                    <h4>Add a Discount</h4>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="discount_type_value">
                                                        <option>Select Time</option>
                                                        @foreach ($hourly_ranges as $hourly_range)
                                                            <option value="{{ $hourly_range->id }}">{{ $hourly_range->time_range }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="percent_value" placeholder="Percent Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_start_date" placeholder="Percent Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_stop_date" placeholder="Percent Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="number" name="value" placeholder="Amount Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_start_date" placeholder="Value Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_stop_date" placeholder="Value Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="submit" value="Add" name="">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="box-body">
                                    @if(count($time_discounts) === 0)

                                        <h4>OOPs!!!, You have time discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Time</th>
                                                <th>Discount %</th>
                                                <th>Discount % Start Date</th>
                                                <th>Discount % Stop Date</th>
                                                <th>Discount N</th>
                                                <th>Value tart Date</th>
                                                <th>Value Stop Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach( $time_discounts as $time_discount)
                                                <tr>
                                                    <td>{{ $time_discount->discount_type_sub_value }}</td>
                                                    <td>{{ $time_discount->percent_value }}%</td>
                                                    <td>{{ date('Y-m-d', strtotime($time_discount->percent_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($time_discount->percent_stop_date)) }}</td>
                                                    <td>&#8358;{{ $time_discount->value }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($time_discount->value_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($time_discount->value_start_date)) }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $time_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $time_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </div>

                                @foreach ($time_discounts as $time_discount)

                                    <div class="modal fade" id="myModal{{ $time_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $time_discount->discount_type_sub_value }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $time_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="discount_type_id" value="{{ $types[2]->id }}">
                                                    <input type="hidden" name="discount_type_value" value="{{ $time_discount->discount_type_value }}">
                                                    <input type="hidden" name="discount_type_sub_value" value="{{ $time_discount->discount_type_sub_value }}">

                                                    <div class="modal-body">

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Percent Value</label>
                                                                    <input type="number" name="percent_value" value="{{ $time_discount->percent_value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Start Date</label>
                                                                    <input type="number" name="percent_start_date" value="{{ date('Y-m-d', strtotime($time_discount->percent_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Stop Date</label>
                                                                    <input type="number" name="percent_stop_date" value="{{ date('Y-m-d', strtotime($time_discount->percent_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Amount Value</label>
                                                                    <input type="number" name="value" value="{{ $time_discount->value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Start Date</label>
                                                                    <input type="number" name="value_start_date" value="{{ date('Y-m-d', strtotime($time_discount->value_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Stop Date</label>
                                                                    <input type="number" name="value_stop_date" value="{{ date('Y-m-d', strtotime($time_discount->value_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <input type="submit" value="Update Discount" class="btn btn-primary" />
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                @endforeach

                            </div>

                            <div class="tab-pane" id="daypart">

                                <div class="add-disc">

                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="discount_type_id" value="{{ $types[3]->id }}">
                                        <div class="row">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2">
                                                    <h4>Add a Discount</h4>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="discount_type_value">
                                                        <option>Select Day Part</option>
                                                        @foreach ($day_parts as $day_part)
                                                            <option value="{{ $day_part->id }}">{{ $day_part->day_parts }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="percent_value" placeholder="Percent Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_start_date" placeholder="Percent Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_stop_date" placeholder="Percent Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="number" name="value" placeholder="Amount Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_start_date" placeholder="Value Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_stop_date" placeholder="Value Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="submit" value="Add" name="">
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="box-body">

                                    @if(count($daypart_discounts) === 0)

                                        <h4>OOPs!!!, You have day-parts discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Day Part</th>
                                                <th>Discount %</th>
                                                <th>Discount % Start Date</th>
                                                <th>Discount % Stop Date</th>
                                                <th>Discount N</th>
                                                <th>Value Start Date</th>
                                                <th>Value Stop Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach( $daypart_discounts as $daypart_discount)
                                                <tr>
                                                    <td>{{ $daypart_discount->discount_type_sub_value }}</td>
                                                    <td>{{ $daypart_discount->percent_value }}%</td>
                                                    <td>{{ date('Y-m-d', strtotime($daypart_discount->percent_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($daypart_discount->percent_stop_date)) }}</td>
                                                    <td>&#8358;{{ $daypart_discount->value }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($daypart_discount->value_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($daypart_discount->value_start_date)) }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $daypart_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $daypart_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($daypart_discounts as $daypart_discount)

                                    <div class="modal fade" id="myModal{{ $daypart_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $daypart_discount->discount_type_sub_value }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $daypart_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="discount_type_id" value="{{ $types[3]->id }}">
                                                    <input type="hidden" name="discount_type_value" value="{{ $daypart_discount->discount_type_value }}">
                                                    <input type="hidden" name="discount_type_sub_value" value="{{ $daypart_discount->discount_type_sub_value }}">

                                                    <div class="modal-body">

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Percent Value</label>
                                                                    <input type="number" name="percent_value" value="{{ $daypart_discount->percent_value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Start Date</label>
                                                                    <input type="number" name="percent_start_date" value="{{ date('Y-m-d', strtotime($daypart_discount->percent_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Stop Date</label>
                                                                    <input type="number" name="percent_stop_date" value="{{ date('Y-m-d', strtotime($daypart_discount->percent_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Amount Value</label>
                                                                    <input type="number" name="value" value="{{ $daypart_discount->value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Start Date</label>
                                                                    <input type="number" name="value_start_date" value="{{ date('Y-m-d', strtotime($daypart_discount->value_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Stop Date</label>
                                                                    <input type="number" name="value_stop_date" value="{{ date('Y-m-d', strtotime($daypart_discount->value_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <input type="submit" value="Update Discount" class="btn btn-primary" />
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>

                            <div class="tab-pane" id="price">
                                <div class="add-disc">

                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="discount_type_id" value="{{ $types[4]->id }}">
                                        <div class="row">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2">
                                                    <h4>Add a Discount</h4>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="discount_type_value" placeholder="Min. Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="percent_value" placeholder="Percent Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_start_date" placeholder="Percent Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_stop_date" placeholder="Percent Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="number" name="discount_type_sub_value" placeholder="Max. Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="value" placeholder="Amount Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_start_date" placeholder="Value Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_stop_date" placeholder="Value Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="submit" value="Add" name="">
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="box-body">
                                    @if(count($price_discounts) === 0)

                                        <h4>OOPs!!!, You have price discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Price Discount</th>
                                                <th>Discount %</th>
                                                <th>Discount % Start Date</th>
                                                <th>Discount % Stop Date</th>
                                                <th>Discount N</th>
                                                <th>Value Start Date</th>
                                                <th>Value Stop Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($price_discounts as $price_discount)
                                                <tr>
                                                    <td>{{ $price_discount->discount_type_value }} - {{ $price_discount->discount_type_sub_value }}</td>
                                                    <td>{{ $price_discount->percent_value }}%</td>
                                                    <td>{{ date('Y-m-d', strtotime($price_discount->percent_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($price_discount->percent_stop_date)) }}</td>
                                                    <td>&#8358;{{ $price_discount->value }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($price_discount->value_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($price_discount->value_start_date)) }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $price_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $price_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($price_discounts as $price_discount)

                                    <div class="modal fade" id="myModal{{ $price_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        Edit Discount - (<strong>{{ $price_discount->discount_type_value }} - {{ $price_discount->discount_type_sub_value }}</strong>)
                                                    </h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $price_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="discount_type_id" value="{{ $types[4]->id }}">

                                                    <div class="modal-body">

                                                        <div class="row">

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Min. Value</label>
                                                                    <input type="number" name="discount_type_value" value="{{ $price_discount->discount_type_value }}" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Value</label>
                                                                    <input type="number" name="percent_value" value="{{ $price_discount->percent_value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Amount Value</label>
                                                                    <input type="number" name="value" value="{{ $price_discount->value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Max. Value</label>
                                                                    <input type="number" name="discount_type_sub_value" value="{{ $price_discount->discount_type_sub_value }}" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Start Date</label>
                                                                    <input type="number" name="percent_start_date" value="{{ date('Y-m-d', strtotime($price_discount->percent_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Start Date</label>
                                                                    <input type="number" name="value_start_date" value="{{ date('Y-m-d', strtotime($price_discount->value_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row">

                                                            <div class="col-md-4"></div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Stop Date</label>
                                                                    <input type="number" name="percent_stop_date" value="{{ date('Y-m-d', strtotime($price_discount->percent_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Value Stop Date</label>
                                                                    <input type="number" name="value_stop_date" value="{{ date('Y-m-d', strtotime($price_discount->value_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <input type="submit" value="Update Discount" class="btn btn-primary" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            </div>

                            <div class="tab-pane" id="pslot">

                                <div class="add-disc">

                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="discount_type_id" value="{{ $types[5]->id }}">
                                        <div class="row">
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2">
                                                    <h4>Add a Discount</h4>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="discount_type_value" placeholder="Min. Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="percent_value" placeholder="Percent Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_start_date" placeholder="Percent Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="percent_stop_date" placeholder="Percent Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="number" name="discount_type_sub_value" placeholder="Max. Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="value" placeholder="Amount Value">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_start_date" placeholder="Value Start Date" class="form-control flatpickr">
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="text" name="value_stop_date" placeholder="Value Stop Date" class="form-control flatpickr">
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 20px;">
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2"></div>
                                                <div class="col-md-2">
                                                    <input type="submit" value="Add" name="">
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>

                                <div class="box-body">

                                    @if(count($pslot_discounts) === 0)

                                        <h4>OOPs!!!, You have P. Slots discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>P. Slot Discount</th>
                                                <th>Discount %</th>
                                                <th>Discount % Start Date</th>
                                                <th>Discount % Stop Date</th>
                                                <th>Discount N</th>
                                                <th>Value Start Date</th>
                                                <th>Value Stop Date</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($pslot_discounts as $pslot_discount)
                                                <tr>
                                                    <td>{{ $pslot_discount->discount_type_value }} - {{ $pslot_discount->discount_type_sub_value }}</td>
                                                    <td>{{ $pslot_discount->percent_value }}%</td>
                                                    <td>{{ date('Y-m-d', strtotime($pslot_discount->percent_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($pslot_discount->percent_stop_date)) }}</td>
                                                    <td>&#8358;{{ $pslot_discount->value }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($pslot_discount->value_start_date)) }}</td>
                                                    <td>{{ date('Y-m-d', strtotime($pslot_discount->value_stop_date)) }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $pslot_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $pslot_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($pslot_discounts as $pslot_discount)

                                    <div class="modal fade" id="myModal{{ $pslot_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        Edit Discount - (<strong>{{ $pslot_discount->discount_type_value }} - {{ $pslot_discount->discount_type_sub_value }}</strong>)
                                                    </h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $pslot_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="discount_type_id" value="{{ $types[5]->id }}">

                                                    <div class="modal-body">

                                                        <div class="row">

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Min. Value</label>
                                                                    <input type="number" name="discount_type_value" value="{{ $pslot_discount->discount_type_value }}" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_value">Discount% Value</label>
                                                                    <input type="number" name="percent_value" value="{{ $pslot_discount->percent_value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="value">Amount Value</label>
                                                                    <input type="number" name="value" value="{{ $pslot_discount->value }}" class="form-control" />
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="discount_type_sub_value">Max. Value</label>
                                                                    <input type="number" name="discount_type_sub_value" value="{{ $pslot_discount->discount_type_sub_value }}" class="form-control">
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="percent_start_date">Discount% Start Date</label>
                                                                    <input type="text" name="percent_start_date" value="{{ date('Y-m-d', strtotime($pslot_discount->percent_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="value_start_date">Value Start Date</label>
                                                                    <input type="text" name="value_start_date" value="{{ date('Y-m-d', strtotime($pslot_discount->value_start_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                        </div>

                                                        <div class="row">

                                                            <div class="col-md-4"></div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="percent_stop_date">Discount% Stop Date</label>
                                                                    <input type="number" name="percent_stop_date" value="{{ date('Y-m-d', strtotime($pslot_discount->percent_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>

                                                            <div class="col-md-4">
                                                                <div class="form-group">
                                                                    <label for="value_stop_date">Value Stop Date</label>
                                                                    <input type="text" name="value_stop_date" value="{{ date('Y-m-d', strtotime($pslot_discount->value_stop_date)) }}" class="form-control flatpickr" />
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                        <input type="submit" value="Update Discount" class="btn btn-primary" />
                                                    </div>
                                                </form>

                                            </div>
                                        </div>
                                    </div>

                                @endforeach

                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /.col -->

            </div>
        </div>
    </div>

</section>


@stop

@section('scripts')

    <script src="https://unpkg.com/flatpickr"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>

    <script>
        function ConfirmDelete() {
            var x = confirm("Are you sure you want to delete?");
            if (x)
                return true;
            else
                return false;
        }

        $("a#a_del").click(function(){
            return ConfirmDelete();
        });

        $(document).ready(function () {

            $('#discount_type_value').change(function () {
                var discount_type_value = $("#discount_type_value option:selected" ).text();
                var c = $("#hey").value = discount_type_value;
                console.log(c+"jj");
            });



            flatpickr(".flatpickr", {
                altInput: true
            });

        });

        var DataCampaign = $('.agency').DataTable({
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: '/walkins/all-walkins/data',
                data: function (d) {
                    d.start_date = $('input[name=txtFromDate_tvc]').val();
                    d.stop_date = $('input[name=txtToDate_tvc]').val();
                }
            },
            columns: [
                {data: 'id', name: 'id'},
                {data: 'full_name', name: 'full_name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'campaign', name: 'campaign'},
                {data: 'delete', name: 'name'}
            ]
        });

    </script>

@stop