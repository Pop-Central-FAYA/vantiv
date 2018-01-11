<?php
    $user_id = Auth::user()->id;
    $role = \DB::select("SELECT role_id from role_user WHERE user_id = '$user_id'");
?>


@if($role[0]->role_id === 3)
    @include('partials.broadcaster_sidebar')
@elseif($role[0]->role_id === 4)
    @include('partials.agent_sidebar')
@endif
