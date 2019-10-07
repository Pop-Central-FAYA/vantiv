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
            @foreach($summary_by_length['data'] as $summary)
                <tr>
                    <td></td>
                    <td style="height: 21;">{{ $summary['length'] }} Seconds</td>
                    <td style="height: 21;">{{ $summary['duration'] }} {{ ($summary['duration'] == 1 ? 'Week':'Weeks') }}</td>
                    <td style="height: 21; text-align: right;">{{ $summary['total_spots'] }}</td>
                    <td style="height: 21; text-align: right;">{{ number_format($summary['gross_total'], 2) }}</td>
                    <td style="height: 21; text-align: right;">{{ number_format($summary['net_total'], 2) }}</td>
                    <td></td>
                </tr>
            @endforeach

            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ $summary_by_length['totals']['total_spots'] }}</td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($summary_by_length['totals']['gross_total'], 2) }}</td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($summary_by_length['totals']['net_total'], 2) }}</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="font-size: 16;">VAT on Media Cost (5%)</td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right; font-weight: bold">{{ number_format($summary_by_length['totals']['vat'], 2) }}</td>
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
            @foreach($summary_by_length_station_type['data'] as $summary)
                <tr>
                    <td></td>
                    <td></td>
                    <td style="height: 21; font-weight: bold">{{ $summary['duration'] }} Seconds</td>
                    <td style="height: 21; font-weight: bold">{{ ucfirst($summary['station_type']) }}</td>
                    <td style="height: 21; font-weight: bold; text-align: right;">{{ $summary['total_spots'] }}</td>
                    <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($summary['net_total'], 2) }}</td>
                    <td></td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td style="height: 21; font-weight: bold">Total</td>
                <td></td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ $summary_by_length_station_type['totals']['total_spots'] }}</td>
                <td style="height: 21; font-weight: bold; text-align: right;">{{ number_format($summary_by_length_station_type['totals']['net_total'], 2) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>
</html>