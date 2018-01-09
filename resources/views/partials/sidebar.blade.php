@if (Auth::user()->hasRole('Broadcaster'))
    @include('partials.broadcaster_sidebar')
@elseif (Auth::user()->hasRole('Agent'))
    @include('partials.agent_sidebar')
@endif