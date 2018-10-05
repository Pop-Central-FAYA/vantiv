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
                    <a href="{{ route('pending-mpos') }}" class="back_icon block_disp left"></a>
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
                    <th>Recomendation</th>
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
                            <select id="is_file_accepted{{ $file->file_code }}" class="jide form-control" @if(count($file->rejection_reasons) > 0) disabled @endif data-disappear="{{ $file->file_code }}">
                                <option value="null">Select Status</option>
                                <option value="1">Approve</option>
                                <option value="2">Reject</option>
                            </select>
                        </td>
                        <td>
                            @foreach($file->rejection_reasons as $rejection_reason)
                                <p>{{ $rejection_reason->name }}</p>
                            @endforeach
                        </td>
                        <input type="hidden" name="file_code" id="file_code" value="{{ $file->file_code }}">
                        <td>
                            <select name="rejection_reason[]" class="reason_default rejection_reason" style="width: 200px;" id="reason{{ $file->file_code }}" multiple>
                                <option value="null">Select Reason</option>
                                @foreach($reject_reasons as $reject_reason)
                                    <option value="{{ $reject_reason->id }}">{{ $reject_reason->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <textarea name="recommendation" id="recommendations{{ $file->file_code }}" class="recommendation_default" cols="30" rows="10">{{ $file->recommendation }}</textarea>
                        </td>
                        <td>
                            <button @if(count($file->rejection_reasons) > 0) disabled style="pointer-events: none" @endif class="update_file update{{ $file->file_code }} btn btn-primary"
                                    name="status"
                                    data-broadcaster_id="{{ $file->broadcaster_id || $file->agency_broadcaster }}"
                                    data-campaign_id="{{ $file->campaign_id }}"
                                    data-file_code="{{ $file->file_code }}"
                                    data-token="{{ csrf_token() }}"
                                    data-mpo_id="{{ $mpo_data[0]['mpo_id'] }}"
                                    data-is_file_accepted="{{ $file->is_file_accepted }}"
                                    data-rejection_reason="{{ $file->rejection_reasons }}"
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    {{--datatables--}}
    <script>

        $(document).ready(function( $ ) {

            $('.rejection_reason').select2({
                maximumSelectionLength: 1
            });

            $('#flash-file-message').hide()

            $('.reason_default').prop('disabled', true);

            $('.recommendation_default').prop('disabled', true);

            $("body").delegate('.jide', 'change', function (e) {
                $(".load").css({
                    opacity : 0.2
                });
                var url = $(this).data('disappear');
                var is_file_value = $(this).val()
                if (is_file_value === '2') {
                    $('#reason'+url).prop('disabled', false);
                    $('#recommendations'+url).prop('disabled', false);
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
                recommendation = $("#recommendations"+file_code).val();

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
                        mpo_id : mpo_id,
                        recommendation: recommendation
                    },
                    success: function (data) {
                        $(".load").css({
                            opacity : 1
                        });
                        toastr.success(data.is_file_accepted, 'File Status Successfully Updated');
                        location.reload();
                    },
                    error: function () {
                        $(".load").css({
                            opacity : 1
                        });
                        toastr.error(data.is_file_accepted, 'File Status not Updated');
                        location.reload();
                    }
                });

            });

        } );
    </script>
@stop

@section('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop

