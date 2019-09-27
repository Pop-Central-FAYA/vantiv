<table>
    <tr>
        <th colspan="2"></th>
    </tr>
    <tr>
        <th colspan="2"></th>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th></th>
            <th colspan="6" height="17" style="text-align: center; font-size: 14; font-weight: bold;">BUDGET SUMMARY</th>
        </tr>
        <tr>
            <th></th>
            <th height="19" width="93" style="border: 2 solid #000000; text-align: left; font-size: 14;">Medium</th>
            <th style="border: 2 solid #000000; text-align: left; font-size: 14;">Material Duration</th>
            <th style="border: 2 solid #000000; text-align: center; font-size: 14;">Number of Spots/Units</th>
            <th style="border: 2 solid #000000; text-align: center; font-size: 14;">Gross Media Cost</th>
            <th style="border: 2 solid #000000; text-align: left; font-size: 14;">Net Media Cost</th>
            <th style="border: 2 solid #000000; text-align: center; font-size: 14;">Savings</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $sum_total_spots=0; $sum_gross_value=0; $sum_net_value=0; $sum_savings=0; 
        @endphp
        @foreach($summary as $summary)
            <tr>
                <td></td>
                <td height="15" style="border: 2 solid #000000; text-align: left; font-size: 11;">{{ strtoupper($summary->medium) }}</td>
                <td height="15" style="border: 2 solid #000000; text-align: left; font-size: 11;">{{ count($summary->material_durations) == 1 ? $summary->material_durations[0].'"' : implode($summary->material_durations, '", ') }}</td>
                <td height="15" style="border: 2 solid #000000; text-align: center; font-size: 11;">{{ $summary->total_spots }}</td>
                <td height="15" style="border: 2 solid #000000; text-align: center; font-size: 11;">{{ number_format($summary->gross_value, 2) }}</td>
                <td height="15" style="border: 2 solid #000000; text-align: right; font-size: 11;">{{ number_format($summary->net_value, 2) }}</td>
                <td height="15" style="border: 2 solid #000000; text-align: center; font-size: 11;">{{ number_format($summary->savings, 2) }}</td>
                @php
                    $sum_total_spots += $summary->total_spots;
                    $sum_gross_value += $summary->gross_value;
                    $sum_net_value += $summary->net_value;
                    $sum_savings += $summary->savings;
                @endphp
            </tr>
        @endforeach
        <tr>
            <td></td>
            <td style="border: 2 solid #000000; background: #D3D3D3;">Total</td>
            <td style="border: 2 solid #000000; background: #D3D3D3;"></td>
            <td style="border: 2 solid #000000; text-align: center; font-size: 11; background: #D3D3D3;">{{ $sum_total_spots }}</td>
            <td style="border: 2 solid #000000; text-align: center; font-size: 11; background: #D3D3D3;">{{ number_format($sum_gross_value, 2) }}</td>
            <td style="border: 2 solid #000000; text-align: right; font-size: 11; background: #D3D3D3;">{{ number_format($sum_net_value, 2) }}</td>
            <td style="border: 2 solid #000000; text-align: center; font-size: 11; background: #D3D3D3;">{{ number_format($sum_savings, 2) }}</td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 2 solid #000000;">Service Fee</td>
            <td style="border: 2 solid #000000;"></td>
            <td style="border: 2 solid #000000;"></td>
            <td style="border: 2 solid #000000; text-align: center; font-size: 11;">10.0%</td>
            <td style="border: 2 solid #000000; text-align: right; font-size: 11;">{{ number_format($sum_net_value * 0.1, 2) }}</td>
            <td style="border: 2 solid #000000;"></td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 2 solid #000000">VAT</td>
            <td style="border: 2 solid #000000"></td>
            <td style="border: 2 solid #000000"></td>
            <td style="border: 2 solid #000000; text-align: center; font-size: 11;">5%</td>
            <td style="border: 2 solid #000000; text-align: right; font-size: 11;">{{ number_format($sum_net_value * 0.05, 2) }}</td>
            <td style="border: 2 solid #000000"></td>
        </tr>
        <tr>
            <td></td>
            <td style="border: 2 solid #000000; background: #D3D3D3;">Total</td>
            <td style="border: 2 solid #000000; background: #D3D3D3;"></td>
            <td style="border: 2 solid #000000; background: #D3D3D3;"></td>
            <td style="border: 2 solid #000000; background: #D3D3D3;"></td>
            <td style="border: 2 solid #000000; text-align: right; font-size: 11; background: #D3D3D3; font-weight: bold;">{{ number_format(($sum_net_value + ($sum_net_value * 0.05) + ($sum_net_value * 0.1)), 2) }}</td>
            <td style="border: 2 solid #000000; background: #D3D3D3;"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>
