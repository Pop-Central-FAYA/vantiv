<table>
    <!-- MEDIA PLAN DETAILS --> 
    <table>
        <tr>
            <th colspan="1" rowspan="3">
                
            </th>
        </tr>
    </table>
    <tbody>
        <tr>
            <td></td>
            <td colspan="12"></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12"></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12">CLIENT: {{ $media_plan_data->client->name}}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12">BRAND: {{ $media_plan_data->brand->name }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12">CAMPAIGN: {{ $media_plan_data->campaign_name}}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12">MEDIUM: {{ $media_type }}</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12">MARKET:</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12">DURATION: {{ $material_length }}"</td>
        </tr>
        <tr>
            <td></td>
            <td colspan="12">PERIOD:</td>
        </tr>
        <tr><td colspan="12"></td></tr>
        <tr><td colspan="12"></td></tr>
        <tr><td colspan="12"></td></tr>
    </tbody>

    <thead>
        <tr>
            <th rowspan="2"></th>
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
        <tr></tr>
        <tr></tr>

        @php 
            $network_total_spots=0; $network_total_bonus_spots=0; $network_cost_bonus_spots=0; $network_gross_value=0; $network_net_value=0; $network_net_value_after_bonus_spots=0;

            $sum_region_total_spots=0; $sum_region_total_bonus_spots=0; $sum_region_cost_bonus_spots=0; $sum_region_gross_value=0; $sum_region_net_value=0; $sum_region_net_value_after_bonus_spots=0;

            $cable_total_spots=0; $cable_total_bonus_spots=0; $cable_cost_bonus_spots=0; $cable_gross_value=0; $cable_net_value=0; $cable_net_value_after_bonus_spots=0; 
        @endphp

        @if($national_stations)
            <tr><td><b>NETWORK</b></td></tr>
            @foreach($national_stations as $station => $programs)
                <tr><td></td><td><b>{{ $station }}</b></td></tr>
                @foreach($programs as $timebelt)
                    <tr>
                        <td></td>
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
                    @php 
                        $network_total_spots += $timebelt['total_spots']; 
                        $network_total_bonus_spots += $timebelt['bonus_spots'];
                        $network_cost_bonus_spots += $timebelt['cost_bonus_spots'];
                        $network_gross_value += $timebelt['gross_value'];
                        $network_net_value += $timebelt['net_value'];
                        $network_net_value_after_bonus_spots += $timebelt['net_value_after_bonus_spots'];
                    @endphp
                @endforeach
                <tr></tr> <!-- empty row -->
            @endforeach

            <!-- DISPLAY NETWORK TOTAL -->
            <tr>
                <td></td>
                <td>TOTAL NETWORK</td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $network_total_spots }}</td>
                <td>{{ $network_total_bonus_spots }}</td>
                <td>{{ $network_cost_bonus_spots }}</td>
                <td></td>
                <td>{{ $network_net_value }}</td>
                <td>{{ $network_net_value_after_bonus_spots }}</td>
                @foreach($monthly_weeks as $month => $weeks)
                    @php $weeksCount = 1; $totalWeeks = count($weeks);  @endphp
                    @foreach ($weeks as $weeks)
                        @if ($weeksCount < $totalWeeks)
                            <td></td>
                        @elseif ($weeksCount == $totalWeeks)
                            <td></td>
                            <td></td>
                        @endif
                        @php $weeksCount++; @endphp
                    @endforeach
                @endforeach
                <td>{{ $network_total_spots }}</td>
            </tr>
            <tr></tr> <!-- empty row -->
        @endif

        @if($regional_stations)
            @foreach($regional_stations as $region => $stations)
                @php $region_total_spots=0; $region_total_bonus_spots=0; $region_cost_bonus_spots=0; $region_net_value=0; $region_gross_value=0; $region_net_value_after_bonus_spots=0; @endphp
                <tr><td>{{ strtoupper($region) }}</td></tr>
                @foreach($stations as $station => $programs)
                    <tr><td></td><td>{{ $station }}</td></tr>
                    @foreach($programs as $program => $timebelt)
                        <tr>
                            <td></td>
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
                        @php 
                            $region_total_spots += $timebelt['total_spots']; 
                            $region_total_bonus_spots += $timebelt['bonus_spots'];
                            $region_cost_bonus_spots += $timebelt['cost_bonus_spots'];
                            $region_gross_value += $timebelt['gross_value'];
                            $region_net_value += $timebelt['net_value'];
                            $region_net_value_after_bonus_spots += $timebelt['net_value_after_bonus_spots'];
                        @endphp
                    @endforeach
                    <tr></tr> <!-- empty row -->
                @endforeach

                @php 
                    $sum_region_total_spots += $region_total_spots; 
                    $sum_region_total_bonus_spots += $region_total_bonus_spots;
                    $sum_region_cost_bonus_spots += $region_cost_bonus_spots;
                    $sum_region_gross_value += $region_gross_value;
                    $sum_region_net_value += $region_net_value;
                    $sum_region_net_value_after_bonus_spots += $region_net_value_after_bonus_spots;
                @endphp
                <!-- DISPLAY Region TOTAL -->
                <tr>
                    <td></td>
                    <td>TOTAL {{ strtoupper($region) }}</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $region_total_spots }}</td>
                    <td>{{ $region_total_bonus_spots }}</td>
                    <td>{{ $region_cost_bonus_spots }}</td>
                    <td></td>
                    <td>{{ $region_net_value }}</td>
                    <td>{{ $region_net_value_after_bonus_spots }}</td>
                    @foreach($monthly_weeks as $month => $weeks)
                        @php $weeksCount = 1; $totalWeeks = count($weeks);  @endphp
                        @foreach ($weeks as $weeks)
                            @if ($weeksCount < $totalWeeks)
                                <td></td>
                            @elseif ($weeksCount == $totalWeeks)
                                <td></td>
                                <td></td>
                            @endif
                            @php $weeksCount++; @endphp
                        @endforeach
                    @endforeach
                    <td>{{ $region_total_spots }}</td>
                </tr>
                <tr></tr> <!-- empty row -->
            @endforeach
        @endif

        @if($cable_stations)
            <tr><td>CABLE</td></tr>
            @foreach($cable_stations as $station => $programs)
                <tr><td></td><td>{{ $station }}</td></tr>
                @foreach($programs as $timebelt)
                    <tr>
                        <td></td>
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
                    @php 
                        $cable_total_spots += $timebelt['total_spots']; 
                        $cable_total_bonus_spots += $timebelt['bonus_spots'];
                        $cable_cost_bonus_spots += $timebelt['cost_bonus_spots'];
                        $cable_gross_value += $timebelt['gross_value'];
                        $cable_net_value += $timebelt['net_value'];
                        $cable_net_value_after_bonus_spots += $timebelt['net_value_after_bonus_spots'];
                    @endphp
                @endforeach
                <tr></tr> <!-- empty row -->
            @endforeach
            <!-- DISPLAY Cable TOTAL -->
            <tr>
                <td></td>
                <td>TOTAL CABLE</td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $cable_total_spots }}</td>
                <td>{{ $cable_total_bonus_spots }}</td>
                <td>{{ $cable_cost_bonus_spots }}</td>
                <td></td>
                <td>{{ $cable_net_value }}</td>
                <td>{{ $cable_net_value_after_bonus_spots }}</td>
                @foreach($monthly_weeks as $month => $weeks)
                    @php $weeksCount = 1; $totalWeeks = count($weeks);  @endphp
                    @foreach ($weeks as $weeks)
                        @if ($weeksCount < $totalWeeks)
                            <td></td>
                        @elseif ($weeksCount == $totalWeeks)
                            <td></td>
                            <td></td>
                        @endif
                        @php $weeksCount++; @endphp
                    @endforeach
                @endforeach
                <td>{{ $cable_total_spots }}</td>
            </tr>
            <tr></tr> <!-- empty row -->
        @endif
        <!-- DISPLAY SUB TOTAL -->
            <tr>
                <td></td>
                <td>SUB TOTAL (NAIRA)</td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>{{ $network_total_spots + $sum_region_total_spots + $cable_total_spots }}</td>
                <td>{{ $network_total_bonus_spots + $sum_region_total_bonus_spots + $cable_total_bonus_spots }}</td>
                <td>{{ $network_cost_bonus_spots + $sum_region_cost_bonus_spots + $cable_cost_bonus_spots }}</td>
                <td>{{ $network_gross_value + $sum_region_gross_value + $cable_gross_value }}</td>
                <td>{{$network_net_value + $sum_region_net_value + $cable_net_value }}</td>
                <td>{{ $network_net_value_after_bonus_spots + $sum_region_net_value_after_bonus_spots + $cable_net_value_after_bonus_spots }}</td>
                @foreach($monthly_weeks as $month => $weeks)
                    @php $weeksCount = 1; $totalWeeks = count($weeks);  @endphp
                    @foreach ($weeks as $weeks)
                        @if ($weeksCount < $totalWeeks)
                            <td></td>
                        @elseif ($weeksCount == $totalWeeks)
                            <td></td>
                            <td></td>
                        @endif
                        @php $weeksCount++; @endphp
                    @endforeach
                @endforeach
                <td>{{ $network_total_spots + $sum_region_total_spots + $cable_total_spots }}</td>
            </tr>
        <tr></tr>
    </tbody>
</table>