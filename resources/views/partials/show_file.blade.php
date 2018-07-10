<?php

    if(!empty($id)){
        $user_id = $id;
    }else{
        $user_id = $walkins;
    }
    $all_files = DB::select("SELECT * from uploads where user_id = '$user_id'");

?>
@if($all_files)
    <div class="row">
        @foreach($all_files as $all_file)
            <form action="{{ route('uploads.remove', ['walkins' => $user_id, 'id' => $all_file->id]) }}" method="POST">
                {{ csrf_field() }}
                <div style="padding: 5px;">
                    @if($all_file->uploads)
                        <video width="150" height="150" controls><source src="{{ asset(decrypt($all_file->uploads)) }}"></video>
                    @else
                        <img src="{{ asset('new_assets/images/logo.png') }}" alt="">
                    @endif
                    <p>Time Picked: {{ $all_file->time }}</p><br>
                    <button type="submit" class="btn btn-danger">delete</button>
                </div>
                <p></p>

            </form>
        @endforeach
    </div>
@endif
