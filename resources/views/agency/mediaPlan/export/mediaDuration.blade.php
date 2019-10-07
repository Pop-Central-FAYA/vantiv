<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <table>
        <!-- MEDIA PLAN DETAILS --> 
        <tbody class="header">
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold">CLIENT:</td>
                <td style="height: 21; font-weight: bold">{{ $media_plan_data->client->name}}</td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold">BRAND:</td>
                <td style="height: 21; font-weight: bold">{{ $media_plan_data->brand->name }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold">CAMPAIGN:</td>
                <td style="height: 21; font-weight: bold">{{ $media_plan_data->campaign_name}}</td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold">MEDIUM:</td>
                <td style="height: 21; font-weight: bold">{{ $media_type }}</td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold">MARKET:</td>
                <td style="height: 21; font-weight: bold"></td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold;">DURATION:</td>
                <td style="height: 21; font-weight: bold;">{{ $material_length }}"</td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold;">PERIOD:</td>
                <td style="height: 21; font-weight: bold;">{{ $media_plan_period }} {{ ($media_plan_period > 1) ? "weeks":"week" }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="20" style="height: 21; text-align: center; font-weight: bold">{{ ucfirst($media_plan_data->campaign_name) }}</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="20" style="height: 68; vertical-align: center; text-align: center; font-weight: bold ">
                    {{  strtoupper($media_type.' '.$material_length.'" ENGLISH') }}
                </td>
            </tr>
        </tbody>

        <thead>
            <tr>
                <th rowspan="2"></th>
                <th rowspan="2" style="text-align: center; vertical-align: center; font-weight: bold">STATION</th>
                <th colspan="7" style="height: 54; vertical-align: center; text-align: center; font-weight: bold">DAYS OF THE WEEK</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">{{$material_length.'"'}}</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">VOL.DISC</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">VALUE LESS </th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">AGENCY</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">{{$material_length.'"'}}</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">TOTAL</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">BONUS</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">COST OF BONUS</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">GROSS</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">NET</th>
                <th colspan="1" style="text-align: center; vertical-align: center; font-weight: bold">NET VALUE</th>
                @foreach($monthly_weeks as $month => $weeks)
                    <th style="text-align: center; vertical-align: center; font-weight: bold; color: #ffffff; background-color: #{{ $brand_color}}" colspan="{{ count($weeks) + 1}}">{{ strtoupper($month) }}</th>
                @endforeach
                <th colspan="1">TOTAL</th>
            </tr>
            <tr>
                <th style="font-weight: bold;">M</th>
                <th style="font-weight: bold;">TU</th>
                <th style="font-weight: bold;">W</th>
                <th style="font-weight: bold;">TH</th>
                <th style="font-weight: bold;">F</th>
                <th style="font-weight: bold;">SA</th>
                <th style="font-weight: bold;">SU</th>

                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">GROSS UNIT RATE</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">%</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">VOL.DISC</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">COMM</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">NET UNIT RATE</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">SPOTS</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">SPOTS</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">SPOTS</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">VALUE</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">VALUE</th>
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">AFTER BONUS SPOTS</th>
                
                @foreach($monthly_weeks as $month => $weeks)
                    @php $weeksCount = 1; $totalWeeks = count($weeks);  @endphp
                    @foreach ($weeks as $weeks)
                        @if ($weeksCount < $totalWeeks)
                            <th style="font-weight: bold; vertical-align: center; border-top: 2px solid #000000; color: #ffffff; background-color: #{{ $brand_color}}">WK{{ $weeksCount }}</th>
                        @elseif ($weeksCount == $totalWeeks)
                            <th style="font-weight: bold; vertical-align: center; border-top: 2px solid #000000; color: #ffffff; background-color: #{{ $brand_color}}">WK{{ $weeksCount }}</th>
                            <th style="font-weight: bold; vertical-align: center; border-top: 2px solid #000000; color: #ffffff; background-color: #{{ $brand_color}}">Monthly Total</th>
                        @endif
                        @php $weeksCount++; @endphp
                    @endforeach
                @endforeach
                <th style="font-weight: bold; height: 54; width: 30; text-align: center; vertical-align: center;">SPOTS</th>
            </tr>
        </thead>
        <tbody>
            <tr></tr>
            @php 
                $total_spots=0; $total_bonus_spots=0; $cost_bonus_spots=0; $gross_value=0; $net_value=0; $net_value_after_bonus_spots=0;
            @endphp

            @foreach($station_type_data as $station_type => $stations_programs)
                <tr>
                    <td><b>{{ strtoupper($station_type) }}</b></td>
                </tr>
                @foreach($stations_programs as $station => $programs)
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
                            <td>{{ number_format($timebelt['gross_unit_rate'], 2) }}</td>
                            <td>{{ number_format($timebelt['volume_discount'], 1) }}%</td>
                            <td>{{ number_format($timebelt['value_less'], 2) }}</td>
                            <td>{{ number_format($timebelt['agency_commission'], 1) }}%</td>
                            <td>{{ number_format($timebelt['net_unit_rate'], 2) }}</td>
                            <td>{{ $timebelt['total_spots'] }}</td>
                            <td>{{ $timebelt['bonus_spots'] }}</td>
                            <td>{{ number_format($timebelt['cost_bonus_spots'], 2) }}</td>
                            <td>{{ number_format($timebelt['gross_value'], 2) }}</td>
                            <td>{{ number_format($timebelt['net_value'], 2) }}</td>
                            <td>{{ number_format($timebelt['net_value_after_bonus_spots'], 2) }}</td>
                            @foreach($timebelt['month_weeks'] as $month => $weeks)
                                @php $weeksCount = 1; $totalWeeks = count($weeks); $totalSlots = 0;  @endphp
                                @foreach ($weeks as $key => $week)
                                    @if ($weeksCount < $totalWeeks)
                                        <th>{{ $week->slot }}</th>
                                    @elseif ($weeksCount == $totalWeeks)
                                        <th>{{ $week->slot }}</th>
                                        <th style="color: #{{$brand_color}}; ">{{ $totalSlots}}</th>
                                    @endif
                                    @php $weeksCount++; $totalSlots += $week->slot; @endphp
                                @endforeach
                            @endforeach
                            <td>{{ $timebelt['total_spots'] }}</td>
                        </tr>
                        @php 
                            $total_spots += $timebelt['total_spots']; 
                            $total_bonus_spots += $timebelt['bonus_spots'];
                            $cost_bonus_spots += $timebelt['cost_bonus_spots'];
                            $gross_value += $timebelt['gross_value'];
                            $net_value += $timebelt['net_value'];
                            $net_value_after_bonus_spots += $timebelt['net_value_after_bonus_spots'];
                        @endphp
                    @endforeach
                    <tr></tr> <!-- empty row -->
                @endforeach

                <!-- DISPLAY STATION TYPE TOTAL -->
                <tr>
                    <td></td>
                    <td style="color: #ffffff; background-color: #{{$brand_color}}">TOTAL {{strtoupper($station_type)}}</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $total_spots }}</td>
                    <td>{{ $total_bonus_spots }}</td>
                    <td>{{ number_format($cost_bonus_spots, 2) }}</td>
                    <td></td>
                    <td>{{ number_format($net_value, 2) }}</td>
                    <td>{{ number_format($net_value_after_bonus_spots, 2) }}</td>
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
                    <td>{{ $total_spots }}</td>
                </tr>
                <tr></tr> <!-- empty row -->
            @endforeach

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
                    <td>{{ $total_spots}}</td>
                    <td>{{ $total_bonus_spots }}</td>
                    <td>{{ number_format(($cost_bonus_spots), 2) }}</td>
                    <td>{{ number_format(($gross_value), 2) }}</td>
                    <td>{{ number_format(($net_value), 2) }}</td>
                    <td>{{ number_format(($net_value_after_bonus_spots), 2) }}</td>
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
                    <td>{{ $total_spots }}</td>
                </tr>
            <tr></tr>
        </tbody>
    </table>
</html>