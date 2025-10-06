<table>
    <thead>
    <tr>
        <th colspan="13" style="text-align: center; font-weight: bold; font-size: 16px;">
            Master Employees List
        </th>
    </tr>
    <tr>
        <th>Name</th>
        <th>Fathers/Mothers Name</th>
        <th>Date of Birth</th>
        <th>Mobile</th>
        <th>Office</th>
        <th>Employment Type</th>
        <th>Designation</th>
        <th>Post/Work Assigned</th>
        <th>Is Scheme</th>
        <th>Is Deleted</th>
    </tr>
    </thead>
    <tbody>
    @foreach($employees as $employee)
        @php
            if (!empty($employee->designation) && $employee->designation !== 'null') {
                // Case 1: Has designation
                $employmentType = 'Provisional';
            } elseif (!empty($employee->post_assigned) && $employee->post_assigned !== 'null') {
                // Case 2: No designation, but has post assigned
                $employmentType = 'Muster Roll';
            } else {
                // Case 3: Fallback
                $employmentType = $employee->employment_type ;
            }
        @endphp

        <tr>
            <td>{{ $employee->name }}</td>
            <td>{{ $employee->parent_name }}</td>
            <td>
                {{ $employee->date_of_birth ? \Carbon\Carbon::parse($employee->date_of_birth)->format('d-m-Y') : '' }}
            </td>
            <td>{{ $employee->mobile }}</td>
            <td>{{ $employee->office?->name }}</td>
            <td>{{ $employmentType }}</td>
            <td>{{ $employee->designation }}</td>
            <td>{{ $employee->post_assigned }}</td>
            <td>{{ $employee->scheme_id ? 'Yes' : 'No' }}</td>
            <td> {{ $employee->employment_type === 'Deleted' ? 'Yes' : 'No' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
