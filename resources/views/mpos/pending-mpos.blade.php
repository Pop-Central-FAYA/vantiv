@extends('layouts.new_app')

@section('title')
    <title>Pending MPOs</title>
@endsection

@section('styles')

    <link rel="stylesheet" href="{{ asset('asset/plugins/datatables/dataTables.bootstrap.css') }}" />
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.3/toastr.min.css" />

@endsection

@section('content')

    <div class="main-section">
        <div class="container">
            <div class="row">
                <div class="col-12 heading-main">
                    <h1>Pending Media Purchase orders</h1>
                    <ul>
                        <li><a href="#"><i class="fa fa-th-large"></i>MPOs</a></li>
                        <li><a href="#">Pending MPOs</a></li>
                    </ul>
                </div>
            </div>

            <div class="row"></div>

            <div class="row">
                <div class="col-12">
                    <div class="box">

                        <div class="box-body">

                            @if(count($mpo_data) === 0)

                                <h4>OOPs!!!, You have pending mpos on your system, please create one</h4>

                            @else
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Brand</th>
                                        <th>Product</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($mpo_data as $mpo)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $mpo['campaign_name'] }}</td>
                                            <td>{{ $mpo['brand'] }}</td>
                                            <td>{{ $mpo['product'] }}</td>
                                            <td>{{ date('Y-m-d', strtotime($mpo['start_date'])) }}</td>
                                            <td>{{ date('Y-m-d', strtotime($mpo['stop_date'])) }}</td>
                                            <td>
                                                <a href="#" style="font-size: 16px">
                                            <span data-toggle="modal" data-target="#myModal{{ $mpo['id'] }}" style="cursor: pointer;">
                                                View
                                            </span>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @foreach ($mpo_data as $mpo)

        <div class="modal fade" id="myModal{{ $mpo['id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">
                            MPO - <strong>{{ $mpo['campaign_name'] }}</strong>
                        </h4>
                    </div>

                    <div class="modal-body">

                        <div class="row">
                            <div class="col-md-6">
                                <p><b>Campaign Name:</b> {{ $mpo['campaign_name'] }}</p>
                                <p><b>Brand Name:</b> {{ $mpo['brand'] }}</p>
                                <p><b>Product Name:</b> {{ $mpo['product'] }}</p>
                                <p><b>Channel:</b> {{ $mpo['channel'] }}</p>
                            </div>
                            <div class="col-md-6">
                                {{--<p><b><i class="fa fa-calendar-o"></i> Date Part:</b> {{ $mpo->campaign_id->day_parts }}</p>--}}
                                {{--<p><b><i class="fa fa-users"></i> Target Audience:</b> {{ $mpo->campaign_id->target_audience_id->audience }}</p>--}}
                                {{--<p><b><i class="fa fa-user"></i> Viewers age range:</b> {{ $mpo->campaign_id->minimum_age }} - {{ $mpo->campaign_id->maximum_age }}</p>--}}
                                {{--<p><b><i class="fa fa-map-marker"></i> Region:</b> {{ $mpo->campaign_id->regions }}</p>--}}
                            </div>

                        </div>

                        <br/>

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                {{--<th>Ad Blocks</th>--}}
                                {{--<th>Duration</th>--}}
                                <th>Media</th>
                                {{--<th>Price</th>--}}
                                <th>Approval</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($mpo['files'] as $file)
                                <tr>
                                    {{--<td>{{ $file->rate_id->from_to_time }}</td>--}}
                                    {{--<td>{{ $file->rate_id->time_in_seconds }}secs</td>--}}
                                    <td>
                                        <a href="#" style="font-size: 16px">
                                                                    <span data-toggle="modal"
                                                                          data-target="#myfileModal{{ $file->file_code }}"
                                                                          style="cursor: pointer;">
                                                                        View
                                                                    </span>
                                        </a>
                                    </td>
                                    {{--<td>{{ $file->rate_id->price }}</td>--}}
                                    <td>
                                        @if ($file->is_file_accepted === 0)
                                            <label class="label label-warning">Pending</label>
                                        @elseif ($file->is_file_accepted === 1)
                                            <label class="label label-success">Approved</label>
                                        @elseif ($file->is_file_accepted === 2)
                                            <label class="label label-danger">Rejected</label>
                                        @endif
                                    </td>
                                    <td>
                                        <select name="status"
                                                data-broadcaster_id="{{ $file->broadcaster_id || $file->agency_broadcaster }}"
                                                data-campaign_id="{{ $file->campaign_id }}"
                                                data-file_code="{{ $file->file_code }}"
                                                data-token="{{ csrf_token() }}"
                                                class="form-control status"
                                        >
                                            <option>Select Status</option>
                                            <option value="1">Approve</option>
                                            <option value="2">Reject</option>
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-4">
                                DISCOUNT
                                <input type="text" value="" class="form-control">
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                TOTAL
                                <input type="text" value="&#8358;{{ $mpo['amount'] }}" class="form-control" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Done</button>
                        {{--<input type="submit" value="Done" class="btn btn-primary" />--}}
                    </div>

                </div>
            </div>
        </div>


        @foreach ($mpo['files'] as $file)
            <div class="modal fade" id="myfileModal{{ $file->file_code }}" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            {{--<h4 class="modal-title" id="myModalLabel">{{ decrypt($file->file_name) }}</h4>--}}
                        </div>
                        <div class="modal-body">
                            <div style="text-align: center;">
                                {{--<iframe src="https://youtu.be/6ZfuNTqbHE8" width="100%" height="315" frameborder="0" allowfullscreen></iframe>--}}
                                {{--<video width="320" height="240" controls>--}}
                                {{--<source src="https://youtu.be/6ZfuNTqbHE8" type="video/mp4">--}}
                                {{--</video>--}}
                                <video src="{{ decrypt($file->file_url) }}" width="170" height="90" controls>
                                    <p>If you are reading this, it is because your browser does not support the HTML5
                                        video element.</p>
                                </video>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @endforeach

            </div>
            </div>
            </div>
            </div>

@stop


@section('scripts')

    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script src="https://unpkg.com/flatpickr"></script>

    <script>

        $(document).ready(function () {
            $('#flash-file-message').hide();

            $(".status").change(function () {
                is_file_accepted = $(this).val();
                broadcaster_id = $(this).data("broadcaster_id");
                campaign_id = $(this).data("campaign_id");
                file_code = $(this).data("file_code");
                csrf = $(this).data("token");

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });

                $.ajax({
                    url: 'approve/' + is_file_accepted + '/' + broadcaster_id + '/' + file_code + '/' + campaign_id,
                    method: "POST",
                    data: {is_file_accepted: is_file_accepted},
                    success: function(data){
                        toastr.success(data.is_file_accepted, 'File Status Successfully Updated');
                    },
                    error: function () {
                        toastr.error(data.is_file_accepted, 'File Status not Updated');
                        alert('not sent to server');
                    }
                });

            });
        });


        $(function () {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });

        flatpickr(".flatpickr", {
            altInput: true
        });

    </script>

@stop