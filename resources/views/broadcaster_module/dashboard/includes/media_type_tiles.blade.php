<div class="clearfix dashboard_pies">
    @foreach($reports_by_media_type['campaigns'] as $media_type => $data)
        <div>
            <div class="pie_icon margin_center">
                <img src="{{ asset('new_frontend/img/tv.svg') }}">
            </div>
            <p class="align_center">{{ strtoupper($media_type) }}</p>

            <div id="pie-chart-{{$media_type}}" class="_pie_chart" style="height: 150px"></div>

            <ul id="legend-{{$media_type}}">
                <li class="pie_legend active"><span class="weight_medium">0%</span> Active</li>
                <li class="pie_legend pending"><span class="weight_medium">0%</span> Pending</li>
                <li class="pie_legend finished"><span class="weight_medium">0%</span> Finished</li>
            </ul>
        </div>            
    @endforeach

</div>