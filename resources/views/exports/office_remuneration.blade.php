<table border="1" style="border-collapse: collapse; width: 100%;">
    <tr>
        <th colspan="8" style="text-align:center; font-weight:bold; padding: 8px;">
            REMUNERATION OF PROVISIONAL EMPLOYEES FOR THE MONTH OF {{ \Carbon\Carbon::now()->format('F Y') }}
        </th>
    </tr>
    <tr>
        <th colspan="8" style="text-align:left; font-weight:bold; padding: 8px;">
            Name of Division : {{ $office->name }}
        </th>
    </tr>
    <tr>
        <th style="text-align:center; font-weight:bold;">Sl.No</th>
        <th style="text-align:center; font-weight:bold;">Name</th>
        <th style="text-align:center; font-weight:bold;">Designation</th>
        <th style="text-align:center; font-weight:bold;">Remuneration</th>
        <th style="text-align:center; font-weight:bold;">Medical Allowance (4%)</th>
        <th style="text-align:center; font-weight:bold;">Total (4+5)</th>
        <th style="text-align:center; font-weight:bold;">Monthly Remuneration</th>
        <th style="text-align:center; font-weight:bold;">Date of next Increment</th>
    </tr>
    <tr>
        <th style="text-align:center; font-weight:bold;">1</th>
        <th style="text-align:center; font-weight:bold;">2</th>
        <th style="text-align:center; font-weight:bold;">3</th>
        <th style="text-align:center; font-weight:bold;">4</th>
        <th style="text-align:center; font-weight:bold;">5</th>
        <th style="text-align:center; font-weight:bold;">6</th>
        <th style="text-align:center; font-weight:bold;">7</th>
        <th style="text-align:center; font-weight:bold;">8</th>
    </tr>
    @foreach($employees as $emp)
        <tr>
            <td style="text-align:center;">{{ $emp['sl_no'] }}</td>
            <td style="text-align:left;">{{ $emp['name'] }}</td>
            <td style="text-align:left;">{{ $emp['designation'] }}</td>
            <td style="text-align:right;">{{ number_format($emp['remuneration'], 0, '.', ',') }}</td>
            <td style="text-align:right;">{{ number_format($emp['medical_allowance'], 0, '.', ',') }}</td>
            <td style="text-align:right;">{{ number_format($emp['total'], 0, '.', ',') }}</td>
            <td style="text-align:right;">{{ number_format($emp['monthly_rem'], 2, '.', ',') }}</td>
            <td>
                {{ $emp['next_increment'] ? \Carbon\Carbon::parse($emp['next_increment'])->format('d-m-Y') : '' }}
            </td>

        </tr>
    @endforeach
</table>
