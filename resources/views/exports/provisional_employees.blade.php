<table>
    <thead>
    <tr>
        <th colspan="15" style="text-align: center; font-weight: bold; font-size: 16px;">
            Provisional Employees - {{ $office->name }} ,{{ $office->type }}
        </th>
    </tr>
    <tr>
        <th>Name</th>
        <th>Fathers/Mothers Name</th>
        <th>Date of Birth</th>
        <th>Mobile</th>
        <th>Present Address</th>
        <th>Designation</th>
        <th>Engagement Card No</th>
        <th>Educational Qln</th>
        <th>Technical Qln</th>
        <th>Name of Office/Workplace</th>
        <th>Date of Initial Engagement</th>
        <th>Remuneration</th>
        <th>Medical All.</th>
        <th>Total</th>
        <th>Monthly Remuneration</th>
        <th>Next Increment Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($employees as $employee)
        <tr>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->parent_name }}</td>
            <td>
                {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '' }}
            </td>
            <td>{{ $employee->mobile }}</td>
            <td>{{ $employee->address }}</td>
            <td>{{ $employee->designation }}</td>
            <td>{{ $employee->engagement_card_no }}</td>
            <td>{{ $employee->educational_qln }}</td>
            <td>{{ $employee->technical_qln }}</td>
            <td>{{ $employee->name_of_workplace }}</td>
            <td>
                {{ $employee->date_of_engagement ? \Carbon\Carbon::parse($employee->date_of_engagement)->format('d-m-Y') : '' }}
            </td>
            <td>{{ optional($employee->remunerationDetail)->remuneration }}</td>
            <td>{{ optional($employee->remunerationDetail)->medical_amount }}</td>
            <td>{{ optional($employee->remunerationDetail)->total }}</td>
            <td>{{ optional($employee->remunerationDetail)->round_total }}</td>

            <td>
                {{ optional($employee->remunerationDetail)->next_increment_date ? \Carbon\Carbon::parse(optional($employee->remunerationDetail)->next_increment_date)->format('d-m-Y') : '' }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
