<table>
    <tr>
        <th colspan="1" rowspan="3">
            
        </th>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th colspan="12" height="50">BUDGET SUMMARY</th>
        </tr>
        <tr>
            <th colspan="12"></th>
        </tr>
        <tr>
            <th>Medium</th>
            <th>Material Duration</th>
            <th>Number of Spots/units</th>
            <th>Gross Media Cost</th>
            <th>Net Media Cost</th>
            <th>Savings</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $sum_total_spots=0; $sum_gross_value=0; $sum_net_value=0; $sum_savings=0; 
        @endphp
        @foreach($summary as $summary)
            <tr>
                <td>{{ $summary->medium }}</td>
                <td>{{ implode($summary->material_durations, '", ') }}</td>
                <td>{{ $summary->total_spots }}</td>
                <td>{{ number_format($summary->gross_value, 2) }}</td>
                <td>{{ number_format($summary->net_value, 2) }}</td>
                <td>{{ number_format($summary->savings, 2) }}</td>
                @php
                    $sum_total_spots += $summary->total_spots;
                    $sum_gross_value += $summary->gross_value;
                    $sum_net_value += $summary->net_value;
                    $sum_savings += $summary->savings;
                @endphp
            </tr>
        @endforeach
        <tr>
            <td>Total</td>
            <td></td>
            <td>{{ $sum_total_spots }}</td>
            <td>{{ number_format($sum_gross_value, 2) }}</td>
            <td>{{ number_format($sum_net_value, 2) }}</td>
            <td>{{ number_format($sum_savings, 2) }}</td>
        </tr>
        <tr>
            <td>Service Fee</td>
            <td></td>
            <td></td>
            <td>10.0%</td>
            <td>{{ number_format($sum_net_value * 0.1, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>VAT</td>
            <td></td>
            <td></td>
            <td>5%</td>
            <td>{{ number_format($sum_net_value * 0.05, 2) }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Total</td>
            <td></td>
            <td></td>
            <td></td>
            <td>{{ $sum_net_value + ($sum_net_value * 0.05) + ($sum_net_value * 0.1) }}</td>
            <td></td>
        </tr>
    </tbody>
</table>