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

                            @if (count($mpo_data) === 0)

                                <h4>OOPs!!!, You have pending mpos on your system, please create one</h4>

                            @else

                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Product</th>
                                        <th>Brand</th>
                                        <th>Campaign</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                    </tr>
                                    </thead>
                                </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php $i = 0; ?>
    @foreach ($mpo_data as $mpo)

        <div class="modal fade" id="myModal{{ $mpo['id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
                        </div>

                        <br/>

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Media</th>
                                <th>Slot Picked</th>
                                <th>Approval</th>
                                <th>Action</th>
                                <th>Reason</th>
                                <th>Reason for Rejection</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($mpo['files'] as $file)
                                <tr id="row{{ $file->file_code }}">
                                    <td>
                                        <video width="150" controls><source src="{{ asset(decrypt($file->file_url)) }}"></video>
                                    </td>
                                    <td>{{ $file->time_picked }} seconds</td>
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
                                        <select id="is_file_accepted{{ $file->file_code }}" class="jide form-control" data-disappear="{{ $file->file_code }}">
                                            <option value="null">Select Status</option>
                                            <option value="1">Approve</option>
                                            <option value="2">Reject</option>
                                        </select>
                                    </td>
                                    <td>
                                        {{ $file->rejection_reason }}
                                    </td>
                                    <input type="hidden" name="file_code" id="file_code" value="{{ $file->file_code }}">
                                    <td>
                                        <select name="rejection_reason" class="reason_default form-control" id="reason{{ $file->file_code }}">
                                            <option value="null">Select Reason</option>
                                            <option value="Inappropriate Adslot">Inappropriate Adslot</option>
                                            <option value="Inappropriate Content">Inappropriate Content</option>
                                            <option value="File does not fit in this slot">File does not fit in this slot</option>
                                        </select>
                                    </td>
                                    <td>
                                        <button class="update_file update{{ $file->file_code }} btn btn-primary"
                                                name="status"
                                                data-broadcaster_id="{{ $file->broadcaster_id || $file->agency_broadcaster }}"
                                                data-campaign_id="{{ $file->campaign_id }}"
                                                data-file_code="{{ $file->file_code }}"
                                                data-token="{{ csrf_token() }}"
                                                data-is_file_accepted="{{ $file->is_file_accepted }}"
                                                data-rejection_reason="{{ $file->rejection_reason }}"
                                        >
                                            Update
                                        </button>
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
                                <video src="{{ decrypt($file->file_url) }}" width="170" height="90" controls>
                                    <p>If you are reading this, it is because your browser does not support the HTML5 video element.</p>
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

@stop

@section('scripts')

    <script src="{{ asset('asset/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('asset/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <script src="https://unpkg.com/flatpickr"></script>

    <script>

        var Datefilter =  $('#example1').DataTable({
            paging: true,
            serverSide: true,
            processing: true,
            ajax: {
                url: '/mpos/pending_mpos_data',
            },
            columns: [
                { data: 'invoice_number', name: 'invoice_number' },
                { data: 'product', name: 'product' },
                { data: 'brand', name: 'brand' },
                { data: 'campaign_name', name: 'campaign_name' },
                { data: 'start_date', name: 'start_date' },
                { data: 'stop_date', name: 'stop_date' },
                { data: 'view', name: 'view' },
            ]
        });

        $(document).ready(function () {
            $('#flash-file-message').hide()

            $('.reason_default').prop('disabled', true);

            $("body").delegate('.jide', 'change', function (e) {
                var url = $(this).data('disappear');
                var is_file_value = $(this).val()
                if (is_file_value === '2') {
                    $('#reason'+url).prop('disabled', false);
                } else {
                    $('#reason'+url).prop('disabled', true);
                }
            })

            $("body").delegate('.update_file', 'click', function () {
                var url = $(this).data('file_code');
                file_code = $(this).data("file_code");
                csrf = $(this).data("token");
                rejection_reason = $("select#reason"+url).val();
                is_file_accepted = $("select#is_file_accepted"+url).val()

                if (rejection_reason === 'null' && is_file_accepted === 'null') {
                    toastr.error("File Status and Rejection reason can't be empty");
                    return;
                }

                if (is_file_accepted === '2' && rejection_reason === 'null') {
                    toastr.error("Please choose a reason for rejecting this file");
                    return;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });

                $.ajax({
                    url: 'approve/' + is_file_accepted + '/' + file_code + '/' + rejection_reason,
                    method: "POST",
                    data: {
                        is_file_accepted: is_file_accepted,
                        rejection_reason: rejection_reason
                    },
                    success: function (data) {
                        toastr.success(data.is_file_accepted, 'File Status Successfully Updated');
                    },
                    error: function () {
                        toastr.error(data.is_file_accepted, 'File Status not Updated');
                        alert('not sent to server');
                    }
                });

            });
        });

        flatpickr(".flatpickr", {
            altInput: true
        });

    </script>

@stop