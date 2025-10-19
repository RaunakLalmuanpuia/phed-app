<table>
    <thead>
    <tr>
        <th>SL</th>
        <th>Name of Posts</th>
        <th>Group</th>
        @foreach($offices as $office)
            <th>{{ $office }}</th>
        @endforeach
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $row)
        <tr>
            <td>{{ $row['sl'] }}</td>
            <td>{{ $row['name_of_post'] }}</td>
            <td>{{ $row['group'] }}</td>
            @foreach($offices as $office)
                <td>{{ $row[$office] }}</td>
            @endforeach
            <td>{{ $row['total'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
