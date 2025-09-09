<table border="1" style="border-collapse: collapse; width: 100%;">
    <tr>
        <th colspan="7" style="text-align:center; font-weight:bold; padding: 8px;  vertical-align:middle; ">
            <u>
                REMUNERATION OF PROVISIONAL EMPLOYEES FOR THE MONTH OF<br>
                {{ \Carbon\Carbon::now()->format('F Y') }}
            </u>
        </th>
    </tr>

    <tr>
        <th style="text-align:center;">Sl. No.</th>
        <th style="text-align:center;">Name of Division</th>
        <th style="text-align:center;">No. of Employee</th>
        <th style="text-align:center;">Wages for 1 Month</th>
        <th style="text-align:center;">Wages for 3 Months</th>
        <th style="text-align:center;">Wages for 6 Months</th>
        <th style="text-align:center;">Wages for 1 Year</th>
    </tr>

    @foreach($rows as $row)
        <tr>
            <td style="text-align:center;">{{ $row['sl_no'] }}</td>
            <td style="text-align:left;">{{ $row['name'] }}</td>
            <td style="text-align:center;">{{ $row['employee_cnt'] }}</td>
            <td style="text-align:right;">{{ number_format($row['one_month'], 2, '.', ',') }}</td>
            <td style="text-align:right;">{{ number_format($row['three_months'], 2, '.', ',') }}</td>
            <td style="text-align:right;">{{ number_format($row['six_months'], 2, '.', ',') }}</td>
            <td style="text-align:right;">{{ number_format($row['one_year'], 2, '.', ',') }}</td>
        </tr>
    @endforeach

    <tr>
        <td colspan="2" style="text-align:center; font-weight:bold;">TOTAL</td>
        <td style="text-align:center; font-weight:bold;">{{ $totals['employee_cnt'] }}</td>
        <td style="text-align:right; font-weight:bold;">{{ number_format($totals['one_month'], 2, '.', ',') }}</td>
        <td style="text-align:right; font-weight:bold;">{{ number_format($totals['three_months'], 2, '.', ',') }}</td>
        <td style="text-align:right; font-weight:bold;">{{ number_format($totals['six_months'], 2, '.', ',') }}</td>
        <td style="text-align:right; font-weight:bold;">{{ number_format($totals['one_year'], 2, '.', ',') }}</td>
    </tr>
</table>
