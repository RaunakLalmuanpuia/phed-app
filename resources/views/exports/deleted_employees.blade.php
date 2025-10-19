<table>
    <thead>
    <tr>
        <th colspan="13" style="text-align: center; font-weight: bold; font-size: 16px;">
            Deleted Employees List
        </th>
    </tr>
    <tr>
        <th>Name</th>
        <th>Fathers/Mothers Name</th>
        <th>Date of Birth</th>
        <th>Mobile</th>
        <th>Present Address</th>
        <th>Office</th>
        <th>Employment Type</th>
        <th>Designation</th>
        <th>Post/Work Assigned</th>
        <th>Reason</th>
        <th>Seniority List</th>
        <th>Year</th>
        <th>Remarks</th>
    </tr>
    </thead>
    <tbody>
    @foreach($employees as $employee)
        @php
            // Apply the same logic as getEmployeeType()
            if (!empty($employee->designation) && !empty($employee->date_of_retirement)) {
                $employmentType = 'Work Charge';
            } elseif (!empty($employee->designation) && empty($employee->date_of_retirement)) {
                $employmentType = 'Provisional';
            } else {
                $employmentType = 'Muster Roll';
            }
        @endphp

        <tr>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->parent_name }}</td>
            <td>
                {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '' }}
            </td>
            <td>{{ $employee->mobile }}</td>
            <td>{{ $employee->address }}</td>
            <td>{{ $employee->office?->name }}</td>
            <td>{{ $employmentType }}</td>
            <td>{{ $employee->designation }}</td>
            <td>{{ $employee->post_assigned }}</td>
            <td>{{ $employee->deletionDetail?->reason }}</td>
            <td>{{ $employee->deletionDetail?->seniority_list }}</td>
            <td>{{ $employee->deletionDetail?->year }}</td>
            <td>{{ $employee->deletionDetail?->remark }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
