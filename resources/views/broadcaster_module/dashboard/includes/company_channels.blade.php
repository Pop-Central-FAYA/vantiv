<div class="clearfix dashboard_pies">
    <!-- tv -->
    @foreach($user_channel_with_other_details as $user_channel_details)
        @if($user_channel_details['channel_details']->channel == 'TV')
            <div>
                <div class="pie_icon margin_center">
                    <a href="#modal_channel_tv" class="modal_click">
                        <img src="{{ asset('new_frontend/img/tv.svg') }}">
                    </a>
                </div>
                <p class="align_center">TV</p>

                <div id="tv" class="_pie_chart" style="height: 150px"></div>

                <ul>
                    <li class="pie_legend active"><span class="weight_medium">{{ $user_channel_details['campaign_status_percentage']['percentage_active'] }}%</span> Active</li>
                    <li class="pie_legend pending"><span class="weight_medium">{{ $user_channel_details['campaign_status_percentage']['percentage_pending'] }}%</span> Pending</li>
                    <li class="pie_legend finished"><span class="weight_medium">{{ $user_channel_details['campaign_status_percentage']['percentage_finished'] }}%</span> Finished</li>
                </ul>
            </div>
        @elseif($user_channel_details['channel_details']->channel == 'Radio')
            <div>
                <div class="pie_icon margin_center">
                    <a href="#modal_channel_radio" class="modal_click">
                        <img src="{{ asset('new_frontend/img/radio.svg') }}">
                    </a>
                </div>
                <p class="align_center">Radio</p>

                <div id="radio" class="_pie_chart" style="height: 150px"></div>

                <ul>
                    <li class="pie_legend active"><span class="weight_medium">{{ $user_channel_details['campaign_status_percentage']['percentage_active'] }}%</span> Active</li>
                    <li class="pie_legend pending"><span class="weight_medium">{{ $user_channel_details['campaign_status_percentage']['percentage_pending'] }}%</span> Pending</li>
                    <li class="pie_legend finished"><span class="weight_medium">{{ $user_channel_details['campaign_status_percentage']['percentage_finished'] }}%</span> Finished</li>
                </ul>
            </div>
        @endif
    @endforeach

</div>

@include('broadcaster_module.dashboard.includes.channel_modal')

