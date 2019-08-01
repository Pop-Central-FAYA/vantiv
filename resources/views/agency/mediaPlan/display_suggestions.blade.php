@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Create Media Plan</title>
@stop

@section('content')
    <div class="main_contain">
        <!-- header -->
        @include('partials.new-frontend.agency.header')
        <!-- subheader -->
        <div class="sub_header clearfix mb">
            <div class="column col_6">
                <h2 class="sub_header">Stations & Programs</h2>
            </div>
        </div>
         <!-- SUGGESTION FILTER & SUGGESTION TABLE & SUGGESTION GRAPH -->
         <v-app>
            <v-content>
                <media-plan-suggestions :selected-suggestions="{{ json_encode($fayaFound['selected']) }}" :plan-status="{{ json_encode($mediaPlanStatus) }}" :redirect-urls="{{ json_encode($redirectUrls) }}" :plan-id="{{ json_encode($mediaPlanId) }}" :selected-filters="{{ json_encode($selectedFilters) }}" :filter-values="{{ json_encode($filterValues) }}" :suggestions="{{ $fayaFound['stations'] }}" :graph-days="{{ json_encode($fayaFound['days']) }}" :graph-details="{{ $fayaFound['total_graph'] }}"></media-plan-suggestions>
            </v-content>
        </v-app>
    <div>

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
    <script src="{{ mix('js/manifest.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ mix('js/app.js') }}"></script>
@stop