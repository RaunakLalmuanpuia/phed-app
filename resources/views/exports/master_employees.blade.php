<table>
    <thead>
    <tr>
        <th colspan="26" style="text-align: center; font-weight: bold; font-size: 16px;">
            Master Employees List
        </th>
    </tr>
    <tr>
        <th>Emp. Code</th>
        <th>Name</th>
        <th>Fathers/Mothers Name</th>
        <th>Date of Birth</th>
        <th>Mobile</th>
        <th>Office</th>
        <th>Employment Type</th>
        <th>Designation</th>
        <th>Post/Work Assigned</th>
        <th>Educational Qln</th>
        <th>Technical Qln</th>
        <th>Date of Initial Engagement</th>
        <th>Is Scheme</th>
        <th>Is Deleted</th>
        @foreach($documentTypes as $docType)
            <th>{{ $docType->name }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($employees as $employee)
        @php
            // ✅ Apply combined logic
            if (!empty($employee->designation) && !empty($employee->date_of_retirement)) {
                // Case 1: Work Charge
                $employmentType = 'Work Charge';
            } elseif (!empty($employee->designation) && empty($employee->date_of_retirement)) {
                // Case 2: Provisional
                $employmentType = 'Provisional';
            } elseif (empty($employee->designation) && !empty($employee->post_assigned)) {
                // Case 3: Muster Roll (Based on post_assigned)
                $employmentType = 'Muster Roll';
            } else {
                // Case 4: Fallback
                $employmentType = $employee->employment_type;
            }

            // ✅ Get uploaded document type IDs (keeping your second logic)
            $uploadedTypes = $employee->documents->pluck('document_type_id')->toArray();
        @endphp


        <tr>
            <td>{{ $employee->employee_code }}</td>
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
            <td>{{ $employee->educational_qln }}</td>
            <td>{{ $employee->technical_qln }}</td>
            <td>
                {{ $employee->date_of_engagement ? \Carbon\Carbon::parse($employee->date_of_engagement)->format('d-m-Y') : '' }}
            </td>
            <td>{{ $employee->scheme_id ? 'Yes' : 'No' }}</td>
            <td> {{ $employee->employment_type === 'Deleted' ? 'Yes' : 'No' }}</td>
            @foreach($documentTypes as $docType)
                <td>{{ in_array($docType->id, $uploadedTypes) ? 'Yes' : 'No' }}</td>
            @endforeach

        </tr>
    @endforeach
    </tbody>
</table>
