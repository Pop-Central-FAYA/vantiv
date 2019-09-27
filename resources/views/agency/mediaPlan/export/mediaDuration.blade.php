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
                    {{  $media_type.' '.$material_length.'" ENGLISH'}}
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
                $network_total_spots=0; $network_total_bonus_spots=0; $network_cost_bonus_spots=0; $network_gross_value=0; $network_net_value=0; $network_net_value_after_bonus_spots=0;

                $sum_region_total_spots=0; $sum_region_total_bonus_spots=0; $sum_region_cost_bonus_spots=0; $sum_region_gross_value=0; $sum_region_net_value=0; $sum_region_net_value_after_bonus_spots=0;

                $cable_total_spots=0; $cable_total_bonus_spots=0; $cable_cost_bonus_spots=0; $cable_gross_value=0; $cable_net_value=0; $cable_net_value_after_bonus_spots=0; 
            @endphp

            @if(count($national_stations) > 0)
                <tr>
                    <!-- <td rowspan="{{sizeof($national_stations, 1)}}"><b>NETWORK</b></td> -->
                    <!-- <td colspan="36"><b>NETWORK</b></td> -->
                    <td><b>NETWORK</b></td>
                </tr>
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
                    <td style="color: #ffffff; background-color: #{{$brand_color}}">TOTAL NETWORK</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $network_total_spots }}</td>
                    <td>{{ $network_total_bonus_spots }}</td>
                    <td>{{ number_format($network_cost_bonus_spots, 2) }}</td>
                    <td></td>
                    <td>{{ number_format($network_net_value, 2) }}</td>
                    <td>{{ number_format($network_net_value_after_bonus_spots, 2) }}</td>
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

            @if(count($regional_stations) > 0)
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
                        <td style="color: #ffffff; background-color: #{{$brand_color}}">TOTAL {{ strtoupper($region) }}</td>
                        <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ $region_total_spots }}</td>
                        <td>{{ $region_total_bonus_spots }}</td>
                        <td>{{ number_format($region_cost_bonus_spots, 2) }}</td>
                        <td></td>
                        <td>{{ number_format($region_net_value, 2) }}</td>
                        <td>{{ number_format($region_net_value_after_bonus_spots, 2) }}</td>
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

            @if(count($cable_stations) > 0)
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
                    <td style="color: #ffffff; background-color: #{{$brand_color}}">TOTAL CABLE</td>
                    <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>{{ $cable_total_spots }}</td>
                    <td>{{ $cable_total_bonus_spots }}</td>
                    <td>{{ number_format($cable_cost_bonus_spots, 2) }}</td>
                    <td></td>
                    <td>{{ number_format($cable_net_value, 2) }}</td>
                    <td>{{ number_format($cable_net_value_after_bonus_spots, 2) }}</td>
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
                    <td>{{ number_format(($network_cost_bonus_spots + $sum_region_cost_bonus_spots + $cable_cost_bonus_spots), 2) }}</td>
                    <td>{{ number_format(($network_gross_value + $sum_region_gross_value + $cable_gross_value), 2) }}</td>
                    <td>{{ number_format(($network_net_value + $sum_region_net_value + $cable_net_value), 2) }}</td>
                    <td>{{ number_format(($network_net_value_after_bonus_spots + $sum_region_net_value_after_bonus_spots + $cable_net_value_after_bonus_spots), 2) }}</td>
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
</html>