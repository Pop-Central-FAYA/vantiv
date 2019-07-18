@extends('dsp_layouts.faya_app')

@section('title')
    <title>Vantage | Create Media Plan</title>
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
                <h2 class="sub_header">Summary</h2>
            </div>
        </div>
           <media-plan-summary  :summary-data="{{ json_encode($summary) }}" :summary-details="{{ json_encode($media_plan) }}" ></media-plan-summary>

           <div class="container-fluid my-5">
            <div class="row">
                <div class="col-md-4 p-0">
                <default-button :btn-name="{{ json_encode('Back') }}" :btn-icon="{{ json_encode('navigate_before') }}"  :btn-destination="{{ json_encode(route('agency.media_plan.create', ['id'=>$media_plan->id])) }}"></default-button>
               </div>
                <div class="col-md-8 p-0 text-right">
                    @if ($media_plan->status == 'Suggested') 
                        @if(Auth::user()->hasPermissionTo('approve.media_plan'))
                            <default-button :btn-name="{{ json_encode('Approve Plan') }}" :btn-icon="{{ json_encode('check') }}"  :btn-destination="{{ json_encode(route('agency.media_plan.approve', ['id'=>$media_plan->id])) }}"></default-button>
                            @endif
                        @if(Auth::user()->hasPermissionTo('decline.media_plan'))
                            <danger-button :btn-name="{{ json_encode('Decline Plan') }}" :btn-icon="{{ json_encode('clear') }}"  :btn-destination="{{ json_encode(route('agency.media_plan.decline', ['id'=>$media_plan->id])) }}"></danger-button>
                        @endif
                    @endif
                    @if(Auth::user()->hasPermissionTo('export.media_plan'))
                        <default-button :btn-name="{{ json_encode('Export Plan') }}" :btn-icon="{{ json_encode('file_download') }}"  :btn-destination="{{ json_encode(route('agency.media_plan.export', ['id'=>$media_plan->id])) }}"></default-button>
                    @endif
                    @if ($media_plan->status == 'Approved')
                        @if(Auth::user()->hasPermissionTo('convert.media_plan'))
                            <media-plan-create-campaign :id="{{ json_encode($media_plan->id) }}"></media-plan-create-campaign>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        
        <br><br><br><br><br><br><br>
    </div>
@stop

@section('scripts')
    <!-- App.js -->
    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
@stop
