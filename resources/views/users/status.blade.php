<select class="user_status" data-user_id="{{ $id }}" name="status" id="status_{{ $id }}">
    @foreach($statuses as $status)
        <option value="{{ $status }}"
        @if($status === $user_status)
            selected
        @endif
        >{{ $status }}</option>
    @endforeach
</select>
