<html>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <table style="border: 10 solid #000000;">
        <!-- MEDIA PLAN DETAILS --> 
        <tbody class="header" style="border: 10 solid #000000;">
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="height: 21; font-weight: bold;">Campaign</td>
                <td style="height: 21; font-weight: bold">Duration</td>
                <td style="height: 21; font-weight: bold">Total Spots</td>
                <td style="height: 21; font-weight: bold">Gross Total</td>
                <td style="height: 21; font-weight: bold">Net Total</td>
                <td></td>
            </tr>
            @php
                $summary_by_length_total_spots = 0;
                $summary_by_length_gross_total = 0.0;
                $summary_by_length_net_total = 0.0;
            @endphp
            @foreach($summary_by_length as $summary)
                <tr>
                    <td></td>
                    <td style="height: 21;">{{ $summary['length'] }} Seconds</td>
                    <td style="height: 21;">{{ $summary['duration'] }} Weeks</td>
                    <td style="height: 21; text-align: right;">{{ $summary['total_spots'] }}</td>
                    <td style="height: 21; text-align: right;">{{ number_format($summary['gross_total'], 2) }}</td>
                    <td style="height: 21; text-align: right;">{{ number_format($summary['net_total'], 2) }}</td>
                    <td></td>
                </tr>
                @php
                    $summary_by_length_total_spots += (INT) $summary['total_spots'];
                    $summary_by_length_gross_total += $summary['gross_total'];
                    $summary_by_length_net_total += $summary['net_total'];
                @endphp
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ $summary_by_length_total_spots }}</td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($summary_by_length_gross_total, 2) }}</td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($summary_by_length_net_total, 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-size: 16;">VAT on Media Cost (5%)</td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right; font-weight: bold">{{ number_format($summary_by_length_net_total * 0.05, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

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
                <td></td>
                <td style="height: 21; font-weight: bold">Campaign</td>
                <td></td>
                <td style="height: 21; font-weight: bold;">Total Spots</td>
                <td style="height: 21; font-weight: bold">Net Total</td>
                <td></td>
            </tr>
            @php
                $total_spots = 0;
                $net_total = 0.0;
            @endphp
            @foreach($summary_by_length_station_type as $length => $summary)
                @foreach($summary as $station_type => $details)
                    @if ($details['total_spots'])
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="height: 21; font-weight: bold">{{ $length }} Seconds</td>
                            <td style="height: 21; font-weight: bold">{{ $station_type }}</td>
                            <td style="height: 21; font-weight: bold; text-align: right;">{{ $details['total_spots'] }}</td>
                            <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($details['net_total'], 2) }}</td>
                            <td></td>
                        </tr>
                        @php
                            $total_spots += (INT) $details['total_spots'];
                            $net_total += $details['net_total'];
                        @endphp
                    @endif
                @endforeach
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td style="height: 21; font-weight: bold">Total</td>
                <td></td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ $total_spots }}</td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($net_total, 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</html>