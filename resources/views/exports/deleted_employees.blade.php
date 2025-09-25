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
            // Decide employment type
            if (!is_null($employee->designation)) {
                $employmentType = 'Provisional';
            } elseif (!is_null($employee->post_assigned)) {
                $employmentType = 'Muster Roll';
            } else {
                $employmentType = $employee->employment_type; // fallback
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
