<table>
    <thead>
    <tr>
        <th>Office Name</th>
        @foreach($skills as $skill)
            <th>{{ $skill ?: 'No Skill Mentioned' }}</th>
        @endforeach
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($offices as $office)
        <tr>
            <td>{{ $office['name'] }}</td>
            @foreach($skills as $skill)
                <td>{{ $office[$skill] }}</td>
            @endforeach
            <td>{{ $office['total'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
