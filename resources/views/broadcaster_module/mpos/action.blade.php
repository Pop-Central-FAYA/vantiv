@extends('layouts.ssp.layout')

@section('title')
    <title> Torch | MPO'S-Action</title>
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
                    <th>Time Picked</th>
                    <th>Adslots</th>
                    <th>Approval</th>
                    @if(Auth::user()->companies()->count() == 1)
                        <th>Action</th>
                    @endif
                    <th>Reason</th>
                    <th>Reason for Rejection</th>
                    <th>Recomendation</th>
                    @if(Auth::user()->hasPermissionTo('update.mpo_status'))
                        <th>Action</th>
                    @endif
                </tr>
                </thead>
                <tbody>

                @if($count_mpo_data_files > 0)
                    @foreach ($mpo_data_files as $mpo_file)
                        <tr id="row{{ $mpo_file->file_code }}">
                        <td>
                            <video width="150" controls><source src="{{ asset($mpo_file->file_url) }}"></video>
                        </td>
                        <td>{{ $mpo_file->time_picked }} seconds</td>
                        <td>
                            <p>{{ $mpo_file->get_adslot->get_rate_card->get_day->day }}</p>
                            <p>{{ $mpo_file->get_adslot->day_part->day_parts }}</p>
                            <p>{{ $mpo_file->get_adslot->get_rate_card->hourly_range->time_range }}</p>
                            <p>{{ $mpo_file->get_adslot->from_to_time }}</p>
                        </td>
                        @if(Auth::user()->hasPermissionTo('update.mpo_status'))
                            <td>
                                @if ($mpo_file->status === 'pending')
                                    <span class="span_state status_pending">Pending</span>
                                @elseif ($mpo_file->status === 'approved')
                                    <span class="span_state status_success">Approved</span>
                                @elseif ($mpo_file->status=== 'rejected')
                                    <span class="span_state status_danger">Rejected</span>
                                @endif
                            </td>
                        @endif
                        <td>
                            <select id="status{{ $mpo_file->file_code }}" class="jide form-control" @if($mpo_file->status == 'rejected') disabled @endif data-disappear="{{ $mpo_file->file_code }}">
                                <option value="null">Select Status</option>
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </td>
                        <td>
                            @if($mpo_file->status === 'rejected')
                                <p>{{ $mpo_file->adslot_reasons->last() ? $mpo_file->adslot_reasons->last()->rejection_reason->name : '' }}</p>
                            @endif
                        </td>
                        <input type="hidden" name="file_code" id="file_code" value="{{ $mpo_file->file_code }}">
                        @if(Auth::user()->companies()->count() == 1)
                        <td>
                            <select name="rejection_reason[]" class="reason_default rejection_reason" style="width: 200px;" id="reason{{ $mpo_file->file_code }}" multiple>
                                <option value="null">Select Reason</option>
                                @foreach($reject_reasons as $reject_reason)
                                    <option value="{{ $reject_reason->id }}">{{ $reject_reason->name }}</option>
                                @endforeach
                            </select>
                        </td>
                        @endif
                        <td>
                            <textarea name="recommendation" id="recommendations{{ $mpo_file->file_code }}" class="recommendation_default" cols="30" rows="10">
                                @if($mpo_file->status === 'rejected')
                                    {{ $mpo_file->adslot_reasons->last() ? $mpo_file->adslot_reasons->last()->recommendation : '' }}
                                @endif
                            </textarea>
                        </td>
                        <td>
                            <button @if($mpo_file->status == 'rejected') disabled style="pointer-events: none" @endif class="update_file update{{ $mpo_file->file_code }} btn btn-primary"
                                    name="status"
                                    data-broadcaster_id="{{ $mpo_file->broadcaster_id || $mpo_file->agency_broadcaster }}"
                                    data-campaign_id="{{ $mpo_file->campaign_id }}"
                                    data-file_code="{{ $mpo_file->file_code }}"
                                    data-token="{{ csrf_token() }}"
                                    data-mpo_id="{{ $mpo_data[0]['mpo_id'] }}"
                                    data-status="{{ $mpo_file->status }}"
                            >
                                Update
                            </button>
                        </td>
                    </tr>
                    @endforeach
                @endif
                </tbody>
            </table>

            <p><br></p>
        {{ $mpo_data_files->links('pagination.general') }}
            <p><br></p>
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

            $('#flash-file-message').hide();

            $('.reason_default').prop('disabled', true);

            $('.recommendation_default').prop('disabled', true);

            $("body").delegate('.jide', 'change', function (e) {
                $(".load").css({
                    opacity : 0.2
                });
                var url = $(this).data('disappear');
                var file_status = $(this).val()
                if (file_status === 'rejected') {
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
                file_status = $("select#status"+file_code).val();
                campaign_id = $(this).data("campaign_id");
                mpo_id = $(this).data("mpo_id");
                recommendation = $("#recommendations"+file_code).val();

                if (rejection_reason === null && file_status === 'null') {
                    toastr.error("File Status and Rejection reason can't be empty");
                    $(".load").css({
                        opacity : 1
                    });
                    return;
                }

                if (file_status === 'rejected' && rejection_reason[0] === "null") {
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
                    url: 'file-status/update/'  + file_code + '/'  + campaign_id + '/' + mpo_id,
                    method: "GET",
                    data: {
                        status: file_status,
                        rejection_reason: rejection_reason,
                        campaign_id: campaign_id,
                        mpo_id : mpo_id,
                        recommendation: recommendation
                    },
                    success: function (data) {
                        if(data.status === 'approved'){
                            $(".load").css({
                                opacity : 1
                            });
                            toastr.success(data.status, 'File Status Successfully Updated');
                            location.reload();
                        }else if(data.error === 'error'){
                            $(".load").css({
                                opacity : 1
                            });
                            toastr.error('An error occurred while performing your request');
                            location.reload();
                        }
                    },
                    error: function () {
                        $(".load").css({
                            opacity : 1
                        });
                        toastr.error(data.status, 'File Status not Updated');
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

