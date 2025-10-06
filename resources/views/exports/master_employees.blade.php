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
