<table>
    <thead>
    <tr>
        <th colspan="12" style="text-align: center; font-weight: bold; font-size: 16px;">
            Muster Roll Employees - {{ $office->name }} ,{{ $office->type }}
        </th>
    </tr>
    <tr>
        <th>Name</th>
        <th>Fathers/Mothers Name</th>
        <th>Date of Birth</th>
        <th>Mobile</th>
        <th>Post/Work Assigned</th>
        <th>Name of Office/Workplace</th>
        <th>Date of Initial Engagement</th>
        <th>Skilled Category</th>
        <th>Skilled at present</th>
        <th>Educational Qln</th>
        <th>Technical Qln</th>
        <th>Post Per Qualification</th>

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
            <td>{{ $employee->post_assigned }}</td>
            <td>{{ $employee->name_of_workplace }}</td>
            <td>
                {{ $employee->date_of_engagement ? \Carbon\Carbon::parse($employee->date_of_engagement)->format('d-m-Y') : '' }}
            </td>
            <td>{{ $employee->skill_category }}</td>
            <td>{{ $employee->skill_at_present }}</td>
            <td>{{ $employee->educational_qln }}</td>
            <td>{{ $employee->technical_qln }}</td>
            <td>{{ $employee->post_per_qualification }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
