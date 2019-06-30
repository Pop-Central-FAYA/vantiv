
<html>    
    <table>
        <tr>
            <th colspan="10"><h3>The Commercial Manager <br>UPFRONT AND PERSONAL</h3></th>
            
            <th colspan="11"><h4>Client: {{ $mpo_details->campaign->client->company_name }} <br>Brand: {{ $mpo_details->campaign->brand->name }} <br>
                Campaign: {{ $mpo_details->campaign->name }} <br> Date: {{ date('Y-m-d', strtotime(now())) }} </h4>
            </th>
        </tr>
        <tr>
            <th colspan="21">
                <p style="align : center;">Revised Media Contract</p>
            </th>
        </tr>
    </table>
    <table>
        <tr>
            <th colspan="21">
                <p>This cancels and replaces Media Contract No: ....</p>
            </th>
        </tr>
        <tr>
            <th colspan="21">
                <p>DATES AND TIMES OF TRANSMISSION SHOULD BE CLEARLY STATED ON COTs.</p>
            </th>
        </tr>
    </table>
    <table>
        <thead>
            <tr>
                <th colspan="3">Year</th>
                <th colspan="3">Media</th>
                <th colspan="3">Specifications</th>
                <th colspan="3">Spots</th>
                <th colspan="3">Vol %</th>
                <th colspan="3">Ag %</th>
                <th colspan="3">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($time_belt_summary as $summary)
            <tr>
                <td colspan="3">{{ date('Y', strtotime($mpo_details->campaign_mpo_time_belts()->first()->playout_date)) }}</td>
                <td colspan="3">{{ $mpo_details->station }}</td>
                <td colspan="3">{{ $summary['duration'].' seconds' }} <br> {{ $summary['day_part'] }}</td>
                <td colspan="3">{{ $summary['total_spot'] }}</td>
                <td colspan="3">{{ $summary['volume_percent'] }}%</td>
                <td colspan="3">15%</td>
                <td colspan="3">{{ number_format($summary['total']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <table>
        <thead>
            <tr>
                <th>Programs</th>
                <th>Positions</th>
                <th>Month</th>
                @foreach($day_numbers as $day_number)
                    <th> {{ $day_number }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($mpos as $mpo)
                <tr>
                    <td>{{ $mpo['program'] }} <br> {{ $mpo['duration'] }} Seconds <br> {{ $mpo['daypart'] }} </td>
                    <td>{{ $mpo['day_range'] }} <br> {{ $mpo['time_slot'][0] .'-'. $mpo['time_slot'][1] }}</td>
                    <td>{{ $mpo['month'] }}</td>
                        @foreach($day_numbers as $day)
                            @if(isset($mpo['exposures'][$day]))
                                <td>{{ $mpo['exposures'][$day] }}</td>
                            @else
                                <td></td>
                            @endif
                        @endforeach
                    <td><b>{{ $mpo['total_slot'] }}</b></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</html>
     
