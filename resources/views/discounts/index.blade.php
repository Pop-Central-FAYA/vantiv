@extends('layouts.app')

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

                                <div class="add-disc">
                                    <div class="col-md-2">
                                        <h4>Add a Discount </h4>
                                    </div>
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="type" value="1">
                                        <div class="col-md-2">
                                            <select name="type_value">
                                                <option>Select Agency</option>
                                                <option>XYZ Agency</option>
                                                <option>Lex Luther Coporation</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_percent" placeholder="Discount Value Percent">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_number" placeholder="Discount Value Number">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" value="Add" name="">
                                        </div>
                                    </form>
                                </div>
                                <!-- Post -->

                                <!-- /.post -->
                                <div class="box-body">
                                    @if(count($agency_discounts) === 0)

                                        <h4>OOPs!!!, You have agency discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Agency</th>
                                                    <th>Discount %</th>
                                                    <th>Discount N</th>
                                                    <th>Action</th>
                                                </tr>
                                            <tbody>
                                            </thead>
                                                @foreach($agency_discounts as $agency_discount)
                                                <tr>
                                                    <td>{{ $agency_discount->agency }}</td>
                                                    <td>{{ $agency_discount->discount_value_percent }}%</td>
                                                    <td>&#8358;{{ $agency_discount->discount_value_number }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $agency_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $agency_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($agency_discounts as $agency_discount)

                                    <div class="modal fade" id="myModal{{ $agency_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $agency_discount->agency }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $agency_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type" value="1">
                                                    <input type="hidden" name="type_value" value="{{ $agency_discount->agency }}">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Number</label>
                                                            <input type="number" name="discount_value_number" value="{{ $agency_discount->discount_value_number }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Percent</label>
                                                            <input type="number" name="discount_value_percent" value="{{ $agency_discount->discount_value_percent }}" class="form-control" />
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
                                <!-- /.post -->
                            </div>

                            <div class="tab-pane" id="brand">
                                <div class="add-disc">
                                    <div class="col-md-2">
                                        <h4>Add a Discount </h4>
                                    </div>
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="type" value="2">
                                        <div class="col-md-2">
                                            <select name="type_value">
                                                <option>Select Brands</option>
                                                <option>Milky Wave</option>
                                                <option>Mouse</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_percent" placeholder="Discount Value Percent">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_number" placeholder="Discount Value Number">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" value="Add" name="">
                                        </div>
                                    </form>
                                </div>
                                <!-- Post -->

                                <!-- /.post -->
                                <div class="box-body">
                                    @if(count($brand_discounts) === 0)

                                        <h4>OOPs!!!, You have brand discounts on your system, please create one</h4>

                                    @else

                                        <table id="example1" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Brand</th>
                                                    <th>Discount %</th>
                                                    <th>Discount N</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($brand_discounts as $brand_discount)
                                                    <tr>
                                                        <td>{{ $brand_discount->brand }}</td>
                                                        <td>{{ $brand_discount->discount_value_percent }}%</td>
                                                        <td>&#8358;{{ $brand_discount->discount_value_number }}</td>
                                                        <td>
                                                            <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $brand_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                            <a href="{{ url('discount/' . $brand_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($brand_discounts as $brand_discount)

                                    <div class="modal fade" id="myModal{{ $brand_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $brand_discount->brand }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $brand_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type" value="2">
                                                    <input type="hidden" name="type_value" value="{{ $brand_discount->brand }}">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Percent</label>
                                                            <input type="number" name="discount_value_percent" value="{{ $brand_discount->discount_value_percent }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Number</label>
                                                            <input type="number" name="discount_value_number" value="{{ $brand_discount->discount_value_number }}" class="form-control" />
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
                                    <div class="col-md-2">
                                        <h4>Add a Discount </h4>
                                    </div>
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="type" value="3">
                                        <div class="col-md-2">
                                            <select name="type_value">
                                                <option>Select Time</option>
                                                @foreach ($hourly_ranges as $hourly_range)
                                                    <option value="{{ $hourly_range->time_range }}">{{ $hourly_range->time_range }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_percent" placeholder="Discount Value Percent">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_number" placeholder="Discount Value Number">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" value="Add" name="">
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
                                                <th>Discount N</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($time_discounts as $time_discount)
                                                <tr>
                                                    <td>{{ $time_discount->time }}</td>
                                                    <td>{{ $time_discount->discount_value_percent }}%</td>
                                                    <td>&#8358;{{ $time_discount->discount_value_number }}</td>
                                                    <td>
                                                        <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $time_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                        <a href="{{ url('discount/' . $time_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($time_discounts as $time_discount)

                                    <div class="modal fade" id="myModal{{ $time_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $time_discount->time }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $time_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type" value="3">
                                                    <input type="hidden" name="type_value" value="{{ $time_discount->time }}">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Percent</label>
                                                            <input type="number" name="discount_value_percent" value="{{ $time_discount->discount_value_percent }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Number</label>
                                                            <input type="number" name="discount_value_number" value="{{ $time_discount->discount_value_number }}" class="form-control" />
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
                                    <div class="col-md-2">
                                        <h4>Add a Discount </h4>
                                    </div>
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="type" value="4">
                                        <div class="col-md-2">
                                            <select name="type_value">
                                                <option>Select Time</option>
                                                @foreach ($day_parts as $day_part)
                                                    <option value="{{ $day_part->day_parts }}">{{ $day_part->day_parts }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_percent" placeholder="Discount Value Percent">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="discount_value_number" placeholder="Discount Value Number">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" value="Add" name="">
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
                                                    <th>Discount N</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($daypart_discounts as $daypart_discount)
                                                    <tr>
                                                        <td>{{ $daypart_discount->day_part }}</td>
                                                        <td>{{ $daypart_discount->discount_value_percent }}%</td>
                                                        <td>&#8358;{{ $daypart_discount->discount_value_number }}</td>
                                                        <td>
                                                            <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $daypart_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                            <a href="{{ url('discount/' . $daypart_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($daypart_discounts as $daypart_discount)

                                    <div class="modal fade" id="myModal{{ $daypart_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Discount - <strong>{{ $daypart_discount->day_part }}</strong></h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $time_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type" value="3">
                                                    <input type="hidden" name="type_value" value="{{ $daypart_discount->day_part }}">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Percent</label>
                                                            <input type="number" name="discount_value_percent" value="{{ $daypart_discount->discount_value_percent }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Number</label>
                                                            <input type="number" name="discount_value_number" value="{{ $daypart_discount->discount_value_number }}" class="form-control" />
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
                                    <div class="col-md-2">
                                        <h4>Add a Discount</h4>
                                    </div>
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="type" value="5">
                                        <div class="col-md-2">
                                            <input type="text" name="price_range_from" placeholder="Start">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="price_range_to" placeholder="End">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="discount_value_percent" placeholder="% Discount">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="discount_value_number" placeholder="Discount N">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" value="Add" name="">
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
                                                    <th>Min Value</th>
                                                    <th>Max Value</th>
                                                    <th>Discount %</th>
                                                    <th>Discount N</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($price_discounts as $price_discount)
                                                    <tr>
                                                        <td>{{ $price_discount->price_range_from }}</td>
                                                        <td>{{ $price_discount->price_range_to }}</td>
                                                        <td>{{ $price_discount->discount_value_percent }}%</td>
                                                        <td>&#8358;{{ $price_discount->discount_value_number }}</td>
                                                        <td>
                                                            <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $price_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                            <a href="{{ url('discount/' . $price_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>

                                    @endif
                                </div>

                                @foreach ($price_discounts as $price_discount)

                                    <div class="modal fade" id="myModal{{ $price_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        Edit Discount - (<strong>{{ $price_discount->price_range_from }} - {{ $price_discount->price_range_to }}</strong>)
                                                    </h4>
                                                </div>

                                                <form method="" class="selsec" action="{{ route('discount.update', ['discount' => $price_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type" value="5">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="discount_value">Min Value</label>
                                                            <input type="number" name="price_range_from" value="{{ $price_discount->price_range_from }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Max Value</label>
                                                            <input type="number" name="price_range_to" value="{{ $price_discount->price_range_to }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Percent</label>
                                                            <input type="number" name="discount_value_percent" value="{{ $price_discount->discount_value_percent }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Number</label>
                                                            <input type="number" name="discount_value_number" value="{{ $price_discount->discount_value_number }}" class="form-control" />
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
                                    <div class="col-md-2">
                                        <h4>Add a Discount </h4>
                                    </div>
                                    <form action="{{ route('discount.store') }}" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="type" value="6">
                                        <div class="col-md-2">
                                            <input type="text" name="price_slot_from" placeholder="Start">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" name="price_slot_to" placeholder="End">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="discount_value_percent" placeholder="% Discount">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" name="discount_value_number" placeholder="Discount N">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="submit" value="Add" name="">
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
                                                <th>Min Value</th>
                                                <th>Max Value</th>
                                                <th>Discount %</th>
                                                <th>Discount N</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($pslot_discounts as $pslot_discount)
                                                    <tr>
                                                        <td>{{ $pslot_discount->p_slot_from }}</td>
                                                        <td>{{ $pslot_discount->p_slot_to }}</td>
                                                        <td>{{ $pslot_discount->discount_value_percent }}%</td>
                                                        <td>&#8358;{{ $pslot_discount->discount_value_number }}</td>
                                                        <td>
                                                            <a href="#" style="font-size: 16px"><span class="label label-warning" data-toggle="modal" data-target="#myModal{{ $pslot_discount->id }}" style="cursor: pointer;"> Edit</span></a>
                                                            <a href="{{ url('discount/' . $pslot_discount->id . '/delete') }}" id="a_del" style="font-size: 16px"><span class="label label-danger"> <i class="fa fa-trash"></i></span></a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>

                                            </tfoot>
                                        </table>
                                    @endif
                                </div>

                                @foreach ($pslot_discounts as $pslot_discount)

                                    <div class="modal fade" id="myModal{{ $pslot_discount->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title" id="myModalLabel">
                                                        Edit Discount - (<strong>{{ $pslot_discount->p_slot_from }} - {{ $pslot_discount->p_slot_to }}</strong>)
                                                    </h4>
                                                </div>

                                                <form method="POST" class="selsec" action="{{ route('discount.update', ['discount' => $pslot_discount->id]) }}">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="type" value="6">
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="discount_value">Min Value</label>
                                                            <input type="number" name="price_slot_from" value="{{ $pslot_discount->p_slot_from }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Max Value</label>
                                                            <input type="number" name="price_slot_to" value="{{ $pslot_discount->p_slot_to }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Percent</label>
                                                            <input type="number" name="discount_value_percent" value="{{ $pslot_discount->discount_value_percent }}" class="form-control" />
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="discount_value">Discount Value Number</label>
                                                            <input type="number" name="discount_value_number" value="{{ $pslot_discount->discount_value_number }}" class="form-control" />
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
    </script>

@stop