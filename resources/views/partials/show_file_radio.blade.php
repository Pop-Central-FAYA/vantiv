<?php

if(!empty($id)){
    $user_id = $id;
}else{
    $user_id = $walkins;
}
$all_files = DB::select("SELECT * from uploads where user_id = '$user_id' AND channel = 'nzrm64hjatseog6'");

?>
@if($all_files)
    <div class="row">
        @foreach($all_files as $all_file)
            <form action="{{ route('uploads.remove', ['walkins' => $user_id, 'id' => $all_file->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="clearfix mb">
                    <div class="column col_4">
                        @if($all_file->uploads)
                            <p><br></p>
                            <audio width="100" height="100" controls><source src="{{ asset(decrypt($all_file->uploads)) }}"></audio>
                            <br>
                            <p>Time Picked: {{ $all_file->time }} Seconds</p>
                        @else
                            <img src="{{ asset('new_assets/images/logo.png') }}" alt="">
                        @endif
                    </div>

                    <div class="column col_4">
                        <p><br></p>
                        <p><br></p>
                        <p> </p>
                    </div>

                    <div class="column col_4">
                        <p><br></p>
                        <p><br></p>
                        <button type="submit" class="btn btn-danger">delete</button>
                    </div>
                </div>
                <p></p>

            </form>
        @endforeach
    </div>
@endif
