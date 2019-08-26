
<html>  
    <table>
        <tr>
            <th colspan="1" rowspan="3">
                
            </th>
        </tr>
    </table>
    <table>
        <tr>
            <th colspan="3">The Commercial Manager UPFRONT AND PERSONAL</th>
            
            <th colspan="4">Client: {{ $mpo_details->campaign->client->company_name }} Brand: {{ $mpo_details->campaign->brand->name }} 
                Campaign: {{ $mpo_details->campaign->name }}  Date: {{ date('Y-m-d', strtotime(now())) }} 
            </th>
        </tr>
        <tr>
            <th colspan="21">
                Revised Media Contract
            </th>
        </tr>
    </table>
    <table>
        <tr>
            <th colspan="21">
                This cancels and replaces Media Contract No: ....
            </th>
        </tr>
        <tr>
            <th colspan="21">
                DATES AND TIMES OF TRANSMISSION SHOULD BE CLEARLY STATED ON COTs.
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
        <tbody>
            <tr>
                <td colspan="3">
                    Total Budget
                </td>
                <td colspan="3">
                    {{ number_format($total_budget) }}
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    VAT
                </td>
                <td colspan="3">
                    5%
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    Net Total
                </td>
                <td colspan="3">
                    {{ number_format($net_total) }}
                </td>
            </tr>
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
     
