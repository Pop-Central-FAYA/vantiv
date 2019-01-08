@if($tv_uploads)
    <div class="row">
        @foreach($tv_uploads as $tv_upload)
            <form action="{{ route('uploads.remove', ['walkins' => $tv_upload->user_id, 'id' => $tv_upload->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="clearfix mb">
                    <div class="column col_6">
                        @if($tv_upload->file_url)
                            <video width="150" height="150" controls><source src="{{ asset($tv_upload->file_url) }}"></video>
                        @else
                            <img src="{{ asset('new_assets/images/logo.png') }}" alt="">
                        @endif
                    </div>
                    <div class="column col_3">
                        <p><br></p>
                        <p><br></p>
                        <p>Time Picked: {{ $tv_upload->time }} Seconds</p>
                    </div>
                    <div class="column col_3">
                        <p><br></p>
                        <p><br></p>
                        <button type="submit" class="btn small_btn">delete</button>
                    </div>
                </div>
                <p></p>

            </form>
        @endforeach
    </div>
@endif
