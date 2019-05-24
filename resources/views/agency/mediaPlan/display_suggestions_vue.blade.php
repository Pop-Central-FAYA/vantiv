@extends('layouts.faya_app')

@section('title')
    <title>FAYA | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain" id="load_this">
        <!-- header -->
        @if(Session::get('broadcaster_id'))
            @include('partials.new-frontend.broadcaster.header')
            @include('partials.new-frontend.broadcaster.campaign_management.sidebar')
        @else
            @include('partials.new-frontend.agency.header')
        @endif
        <!-- subheader -->
        <div class="sub_header clearfix mb pt">
            <div class="column col_6">
                <h2 class="sub_header">Stations & Programs</h2>
            </div>
        </div>
    <div>

    <div class="container-fluid">
        <!-- SUGGESTION FILTER & SUGGESTION TABLE & SUGGESTION GRAPH -->
        <div class="row justify-content-center mb-4">
            <media-plan-suggestions :plan-id="{{ json_encode($mediaPlanId) }}" :selected-filters="{{ json_encode($selectedFilters) }}" :filter-values="{{ json_encode($filterValues) }}" :suggestions="{{ $fayaFound['stations'] }}" :graph-days="{{ json_encode($fayaFound['days']) }}" :graph-details="{{ $fayaFound['total_graph'] }}"></media-plan-suggestions>
        </div>
        <!-- SELECTED SUGGESTIONS -->
        <div class="row justify-content-center my-5">
            <media-plan-suggestion-selected :plan-id="{{ json_encode($mediaPlanId) }}" :selected-time-belts="{{ json_encode($fayaFound['selected']) }}"></media-plan-suggestion-selected>
        </div>
        <!-- NAVIGATION & ACTION BUTTONS -->
        <div class="row justify-content-center my-5">
            <media-plan-footer-nav :plan-id="{{ json_encode($mediaPlanId) }}" :plan-status="{{ json_encode($mediaPlanStatus) }}" :prev-route="{{ json_encode(url('/')) }}" :next-route="{{ json_encode(route('agency.media_plan.create',['id'=>$mediaPlanId])) }}"></media-plan-suggestion-selected>
        </div>
    </div>

    <div class="the_frame client_dets mb1">

<!-- end -->
    </div>

  </div><!-- be -->

  <div>

    </div> <!-- be -->

    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://unpkg.com/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
@stop

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
@stop