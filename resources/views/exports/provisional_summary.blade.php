<table>
    <thead>
    <tr>
        <th>Office Name</th>
        @foreach($designations as $designation)
            <th>{{ $designation }}</th>
        @endforeach
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($offices as $office)
        <tr>
            <td>{{ $office['name'] }}</td>
            @foreach($designations as $designation)
                <td>{{ $office[$designation] }}</td>
            @endforeach
            <td>{{ $office['total'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
