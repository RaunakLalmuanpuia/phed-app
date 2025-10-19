<table>
    <thead>
    <tr>
        <th colspan="11" style="text-align: center; font-weight: bold; font-size: 16px;">
            Work Charge Employees - {{ $office->name }} ,{{ $office->type }}
        </th>
    </tr>
    <tr>
        <th>Name</th>
        <th>Fathers/Mothers Name</th>
        <th>Date of Birth</th>
        <th>Mobile</th>
        <th>Present Address</th>
        <th>Designation</th>
        <th>Date of Retirement</th>
        <th>Educational Qln</th>
        <th>Technical Qln</th>
        <th>Date of Joining</th>
        <th>Name of Office/Workplace</th>
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
            <td>
                {{ $employee->date_of_retirement ? \Carbon\Carbon::parse($employee->date_of_retirement)->format('d-m-Y') : '' }}
            </td>

            <td>{{ $employee->educational_qln }}</td>
            <td>{{ $employee->technical_qln }}</td>
            <td>
                {{ $employee->date_of_engagement ? \Carbon\Carbon::parse($employee->date_of_engagement)->format('d-m-Y') : '' }}
            </td>
            <td>{{ $employee->name_of_workplace }}</td>

        </tr>
    @endforeach
    </tbody>
</table>
