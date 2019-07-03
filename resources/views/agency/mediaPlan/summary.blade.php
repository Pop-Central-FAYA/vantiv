@extends('dsp_layouts.faya_app')

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
                <h2 class="sub_header">Summary</h2>
            </div>
        </div>

        <!-- main frame -->
        <div class="the_frame clearfix mb border_top_color load_stuff">

            <div class="margin_center col_10 clearfix create_fields">

                <div class="the_stats the_frame clearfix mb4 mt4">
                    <table class="display dashboard_campaigns">
                        <tbody>
                           <tr>
                            <td><span class="mr-2"><b>Client Name:</b></span> {{ $media_plan->client->company_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Product Name:</b></span> {{ $media_plan->product_name }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Flight Date:</b></span> {{ date('d-M-Y',strtotime($media_plan->start_date)).' to '.date('d-M-Y',strtotime($media_plan->end_date)) }}</td>
                           </tr>
                           <tr>
                            <td><span class="mr-2"><b>Status:</b></span> {{ $media_plan->status }}</td>
                           </tr>
                        </tbody>
                    </table>
                </div>

                <div class="the_frame client_dets mb4">
                    <!-- Suggestions table -->
                    <table class="display dashboard_campaigns">
                        <thead>
                        <tr>
                            <th>Medium</th>
                            <th>Material Duration</th>
                            <th>Number of Spots/units</th>
                            <th>Gross Media Cost</th>
                            <th>Net Media Cost</th>
                            <th>Savings</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php $sum_total_spots=0; $sum_gross_value=0; $sum_net_value=0; $sum_savings=0; @endphp
                            @foreach($summary as $summary)
                                <tr>
                                    <td>{{ $summary->medium }}</td>
                                    <td>{{ implode($summary->material_durations, '", ') }}</td>
                                    <td>{{ $summary->total_spots }}</td>
                                    <td>{{ number_format($summary->gross_value, 2) }}</td>
                                    <td>{{ number_format($summary->net_value, 2) }}</td>
                                    <td>{{ number_format($summary->savings, 2) }}</td>
                                    @php
                                        $sum_total_spots += $summary->total_spots;
                                        $sum_gross_value += $summary->gross_value;
                                        $sum_net_value += $summary->net_value;
                                        $sum_savings += $summary->savings;
                                    @endphp
                                </tr>
                            @endforeach
                            <tr>
                                <td>Total</td>
                                <td></td>
                                <td>{{ $sum_total_spots }}</td>
                                <td>{{ number_format($sum_gross_value, 2) }}</td>
                                <td>{{ number_format($sum_net_value, 2) }}</td>
                                <td>{{ number_format($sum_savings, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end -->
                </div>

                @if(Auth::user()->hasRole('admin'))
                    <!-- <div class="mb3">
                        <a href="{{ route('agency.media_plan.approve', ['id'=>$media_plan->id]) }}" class="btn block_disp uppercased align_center mb3"><i class="material-icons" style="margin-right: 10px">check</i>Approve Plan</a>

                        <a href="{{ route('agency.media_plan.decline', ['id'=>$media_plan->id]) }}" class="btn block_disp uppercased align_center bg_red"><i class="material-icons mt2" style="margin-right: 10px;">clear</i>Decline Plan</a>

                        <a href="{{ route('agency.media_plan.export', ['id'=>$media_plan->id]) }}" class="btn block_disp uppercased align_center"><span class="_plus"></span>Export Plan</a>
                    </div> -->
                @endif
            </div>
        </div>
        <!-- main frame end -->
        <!-- <div class="center" style="text-align: center;">
            <a href="#" onclick="goBack()" class="btn uppercased back_btn w10">
                <i class="material-icons left" style="margin-top: 5px">keyboard_backspace</i>
                <span>Back</span>
            </a>
        </div> -->

        <div class="container-fluid my-5">
            <div class="row">
                <div class="col-md-4 p-0">
                    <a id="back_btn" href="{{ route('agency.media_plan.create', ['id'=>$media_plan->id]) }}" class="btn small_btn"><i class="media-plan material-icons">navigate_before</i> Back</a>
                </div>
                <div class="col-md-8 p-0 text-right">
                    @if(Auth::user()->hasRole('admin') || Auth::user()->hasRole('finance'))
                        <a href="{{ route('agency.media_plan.approve', ['id'=>$media_plan->id]) }}" class="media-plan btn block_disp uppercased mr-1 {{ ($media_plan->status == 'Approved' || $media_plan->status == 'Declined') ? 'disabled-action-btn' : ''}}"><i class="media-plan material-icons">check</i>Approve Plan</a>
                        <a href="{{ route('agency.media_plan.decline', ['id'=>$media_plan->id]) }}" class="media-plan btn block_disp uppercased bg_red mr-1 {{ ($media_plan->status == 'Approved' || $media_plan->status == 'Declined') ? 'disabled-action-btn' : ''}}"><i class="media-plan material-icons">clear</i>Decline Plan</a>
                    @endif
                    <a href="{{ route('agency.media_plan.export', ['id'=>$media_plan->id]) }}" class="btn block_disp uppercased"><i class="media-plan material-icons">file_download</i>Export Plan</a>
                    @if ($media_plan->status == 'Approved')
                        <media-plan-create-campaign :id="{{ json_encode($media_plan->id) }}"></media-plan-create-campaign>
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
