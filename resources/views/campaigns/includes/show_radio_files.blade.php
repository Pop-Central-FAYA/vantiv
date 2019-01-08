@if($radio_uploads)
    <div class="row">
        @foreach($radio_uploads as $radio_upload)
            <form action="{{ route('uploads.remove', ['walkins' => $radio_upload->user_id, 'id' => $radio_upload->id]) }}" method="POST">
                {{ csrf_field() }}
                <div class="clearfix mb">
                    <div class="column col_4">
                        @if($radio_upload->file_url)
                            <audio controls><source src="{{ asset($radio_upload->file_url) }}"></audio>
                        @else
                            <img src="{{ asset('new_assets/images/logo.png') }}" alt="">
                        @endif
                    </div>
                    <div class="column col_3">
                        <p><br></p>
                        <p><br></p>
                        <p>Time Picked: {{ $radio_upload->time }} Seconds</p>
                    </div>
                    <div class="column col_4">
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
