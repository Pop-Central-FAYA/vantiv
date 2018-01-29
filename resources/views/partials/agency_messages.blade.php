@if(Session::get('success', false))
    <?php $data = Session::get('success'); ?>
    @if (is_array($data))
        @foreach ($data as $msg)
            <div class="alert alert-success alert-notification">
                <i class="fa fa-check"></i>
                {{ $msg }}
                <button type="button" class="close" aria-hidden="true">&times;</button>
            </div>
        @endforeach
    @else
        <div class="alert alert-success alert-notification">
            <i class="fa fa-check"></i>
            {{ $data }}
            <button type="button" class="close" aria-hidden="true">&times;</button>
        </div>
    @endif
@endif

@if(Session::get('error', false))
    <?php $data = Session::get('error'); ?>
    @if (is_array($data))
        @foreach ($data as $msg)
            <div class="alert alert-danger alert-notification">
                <i class="fa fa-times"></i>
                {{ $msg }}
                <button type="button" class="close" aria-hidden="true">&times;</button>
            </div>
        @endforeach
    @else
        <div class="alert alert-danger alert-notification">
            <i class="fa fa-mark"></i>
            {{ $data }}
            <button type="button" class="close" aria-hidden="true">&times;</button>
        </div>
    @endif
@endif