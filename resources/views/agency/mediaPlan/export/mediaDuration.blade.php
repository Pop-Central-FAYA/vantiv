<table>
    <thead>
        <tr></tr>
        <tr>
            <td>CLIENT:</td>
            <td>{{ $media_plan_data->client->company_name}}</td>
        </tr>
        <tr>
            <td>BRAND:</td>
            <td></td>
        </tr>
        <tr>
            <td>CAMPAIGN:</td>
            <td>{{ $media_plan_data->campaign_name}}</td>
        </tr>
        <tr>
            <td>MEDIUM:</td>
            <td>{{ $media_type }}</td>
        </tr>
        <tr>
            <td>MARKET:</td>
            <td></td>
        </tr>
        <tr>
            <td>DURATION:</td>
            <td>{{ $material_length }}"</td>
        </tr>
        <tr>
            <td>PERIOD:</td>
            <td></td>
        </tr>
        <tr></tr>
        <tr></tr>
        <tr></tr>

        <tr>
            <th rowspan="2">STATION</th>
            <th colspan="7">DAYS OF THE WEEK</th>
            <th rowspan="2">GROSS UNIT RATE</th>
            <th rowspan="2">VOL.DISC %</th>
            <th rowspan="2">VALUE LESS VOL.DISC</th>
            <th rowspan="2">AGENCY COMM</th>
            <th rowspan="2">NET UNIT RATE</th>
            <th rowspan="2">TOTAL SPOTS</th>
            <th rowspan="2">BONUS SPOTS</th>
            <th rowspan="2">COST OF BONUS SPOTS</th>
            <th rowspan="2">GROSS VALUE</th>
            <th rowspan="2">NET VALUE</th>
            <th rowspan="2">NET VALUE AFTER BONUS SPOTS</th>
            @foreach($monthly_weeks as $month => $weeks)
                <th colspan="{{ count($weeks) + 1}}">{{ $month }}</th>
            @endforeach
            <th rowspan="2">TOTAL SPOTS</th>
        </tr>
        <tr>
            <th>M</th>
            <th>TU</th>
            <th>W</th>
            <th>TH</th>
            <th>F</th>
            <th>SA</th>
            <th>SU</th>
            
            @foreach($monthly_weeks as $month => $weeks)
                @php $weeksCount = 1; $totalWeeks = count($weeks);  @endphp
                @foreach ($weeks as $weeks)
                    @if ($weeksCount < $totalWeeks)
                        <th>WK{{ $weeksCount }}</th>
                    @elseif ($weeksCount == $totalWeeks)
                        <th>WK{{ $weeksCount }}</th>
                        <th>Monthly Total</th>
                    @endif
                    @php $weeksCount++; @endphp
                @endforeach
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $station => $programs)
            <tr><td>{{ $station }}</td></tr>
            @foreach($programs as $timebelt)
                <tr>
                    <td>{{ $timebelt['program'] }}</td>
                    @foreach($timebelt['week_days'] as $key => $value)
                        @if($value == 1)
                            <td>{{ $key }}</td>
                        @else
                            <td></td>
                        @endif
                    @endforeach
                    <td>{{ $timebelt['gross_unit_rate'] }}</td>
                    <td>{{ $timebelt['volume_discount'] }}</td>
                    <td>{{ $timebelt['value_less'] }}</td>
                    <td>{{ $timebelt['agency_commission'] }}</td>
                    <td>{{ $timebelt['net_unit_rate'] }}</td>
                    <td>{{ $timebelt['total_spots'] }}</td>
                    <td>{{ $timebelt['bonus_spots'] }}</td>
                    <td>{{ $timebelt['cost_bonus_spots'] }}</td>
                    <td>{{ $timebelt['gross_value'] }}</td>
                    <td>{{ $timebelt['net_value'] }}</td>
                    <td>{{ $timebelt['net_value_after_bonus_spots'] }}</td>
                    @foreach($timebelt['month_weeks'] as $month => $weeks)
                        @php $weeksCount = 1; $totalWeeks = count($weeks); $totalSlots = 0;  @endphp
                        @foreach ($weeks as $key => $week)
                            @if ($weeksCount < $totalWeeks)
                                <th>{{ $week->slot }}</th>
                            @elseif ($weeksCount == $totalWeeks)
                                <th>{{ $week->slot }}</th>
                                <th>{{ $totalSlots}}</th>
                            @endif
                            @php $weeksCount++; $totalSlots += $week->slot; @endphp
                        @endforeach
                    @endforeach
                    <td>{{ $timebelt['total_spots'] }}</td>
                </tr>
            @endforeach
            <tr></tr> <!-- empty row -->
        @endforeach
        <tr></tr>
    </tbody>
</table>