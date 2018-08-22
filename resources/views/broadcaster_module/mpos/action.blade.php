@extends('layouts.faya_app')

@section('title')
    <title> FAYA | MPO'S-Action</title>
@stop

@section('content')

    <div class="main_contain">
        <!-- heaser -->
    @include('partials.new-frontend.broadcaster.header')

    @include('partials.new-frontend.broadcaster.campaign_management.sidebar')

    <!-- subheader -->

        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">MPO Details</h2>
            </div>
        </div>

        <div class="the_frame client_dets mb4">
            <div class="the_frame clearfix mb ">
                <div class="border_bottom clearfix client_name">
                    <a href="{{ route('all-mpos') }}" class="back_icon block_disp left"></a>
                    <div class="left">
                        <h2 class='sub_header'>MPO - {{ $mpo_data[0]['name'] }}</h2>
                        <p class="small_faint"></p>
                        <p><b>Campaign Name:</b> {{ $mpo_data[0]['name'] }}</p>
                        <p><b>Brand Name:</b> {{ $mpo_data[0]['brand'] }}</p>
                        <p><b>Product Name:</b> {{ $mpo_data[0]['product'] }}</p>
                        <p><b>Channel:</b> {{ $mpo_data[0]['channel'] }}</p>
                    </div>
                </div>
            </div>

            <table id="example1" class="load display">
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
                @foreach ($mpo_data[0]['files'] as $file)
                    <tr id="row{{ $file->file_code }}">
                        <td>
                            <video width="150" controls><source src="{{ asset(decrypt($file->file_url)) }}"></video>
                        </td>
                        <td>{{ $file->time_picked }} seconds</td>
                        <td>
                            @if ($file->is_file_accepted === 0)
                                <span class="span_state status_pending">Pending</span>
                            @elseif ($file->is_file_accepted === 1)
                                <span class="span_state status_success">Approved</span>
                            @elseif ($file->is_file_accepted === 2)
                                <span class="span_state status_danger">Rejected</span>
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
                                    data-mpo_id="{{ $mpo_data[0]['mpo_id'] }}"
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
            <!-- end -->
        </div>

    </div>

@stop

@section('scripts')
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    {{--<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>--}}
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    {{--datatables--}}
    <script>

        $(document).ready(function( $ ) {


            $('#flash-file-message').hide()

            $('.reason_default').prop('disabled', true);

            $("body").delegate('.jide', 'change', function (e) {
                $(".load").css({
                    opacity : 0.2
                });
                var url = $(this).data('disappear');
                var is_file_value = $(this).val()
                if (is_file_value === '2') {
                    $('#reason'+url).prop('disabled', false);
                    $(".load").css({
                        opacity : 1
                    });
                } else {
                    $('#reason'+url).prop('disabled', true);
                    $(".load").css({
                        opacity : 1
                    });
                }
            })

            $("body").delegate('.update_file', 'click', function () {
                $(".load").css({
                    opacity : 0.2
                });
                file_code = $(this).data("file_code");

                csrf = $(this).data("token");
                rejection_reason = $("select#reason"+file_code).val();
                is_file_accepted = $("select#is_file_accepted"+file_code).val();
                campaign_id = $(this).data("campaign_id");
                mpo_id = $(this).data("mpo_id");

                if (rejection_reason === 'null' && is_file_accepted === 'null') {
                    toastr.error("File Status and Rejection reason can't be empty");
                    $(".load").css({
                        opacity : 1
                    });
                    return;
                }

                if (is_file_accepted === '2' && rejection_reason === 'null') {
                    toastr.error("Please choose a reason for rejecting this file");
                    $(".load").css({
                        opacity : 1
                    });
                    return;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });

                $.ajax({
                    url: 'approve/' + is_file_accepted + '/' + file_code + '/' + rejection_reason + '/' + campaign_id + '/' + mpo_id,
                    method: "GET",
                    data: {
                        is_file_accepted: is_file_accepted,
                        rejection_reason: rejection_reason,
                        campaign_id: campaign_id,
                        mpo_id : mpo_id
                    },
                    success: function (data) {
                        $(".load").css({
                            opacity : 1
                        });
                        toastr.success(data.is_file_accepted, 'File Status Successfully Updated');
                        $(".load").load(location.href + " .load");
                    },
                    error: function () {
                        $(".load").css({
                            opacity : 1
                        });
                        toastr.error(data.is_file_accepted, 'File Status not Updated');
                        $(".load").load(location.href + " .load");
                        alert('not sent to server');
                    }
                });

            });

        } );
    </script>
@stop

