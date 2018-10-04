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
                    <th>Rejected Media</th>
                    <th>Slot Picked</th>
                    <th>Rejection Reason</th>
                    <th>Upload New File</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($mpo_data[0]['rejected_files'] as $file)
                    <tr id="row{{ $file->file_code }}">
                        <td>
                            <video width="150" controls><source src="{{ asset(decrypt($file->file_url)) }}"></video>
                        </td>
                        <td>{{ $file->time_picked }} seconds</td>
                        <td>
                            {{ $file->rejection_reason }}
                        </td>
                        <input type="hidden" name="file_code" id="file_code" value="{{ $file->file_code }}">
                        <td>
                            <input type="file" name="upload">
                            <input type="hidden" name="f_du" id="f_du" size="5" />
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
            <audio id="audio"></audio>
            <!-- end -->
        </div>

    </div>

@stop

@section('scripts')
    {{--datatables--}}
    <script>

        //register canplaythrough event to #audio element to can get duration
        var f_duration =0;  //store duration
        document.getElementById('audio').addEventListener('canplaythrough', function(e){
            //add duration in the input field #f_du
            f_duration = Math.round(e.currentTarget.duration);
            document.getElementById('f_du').value = f_duration;
            URL.revokeObjectURL(obUrl);
        });

        //when select a file, create an ObjectURL with the file and add it in the #audio element
        var obUrl;
        document.getElementById('fup').addEventListener('change', function(e){
            var file = e.currentTarget.files[0];
            //check file extension for audio/video type
            if(file.name.match(/\.(avi|mp3|mp4|mpeg|ogg)$/i)){
                obUrl = URL.createObjectURL(file);
                document.getElementById('audio').setAttribute('src', obUrl);
            }
        });
    </script>

@stop

