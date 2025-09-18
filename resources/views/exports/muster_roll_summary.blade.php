@php $sl = 1; @endphp

<table border="1">
    <thead>
    <tr>
        <th>Sl. No</th>
        <th>Office / Scheme</th>
        @foreach($skills as $skill)
            <th>{{ $skill ?: 'No Skill Mentioned' }}</th>
        @endforeach
        <th>Total</th>
    </tr>
    </thead>
    <tbody>
    {{-- Offices --}}
    @foreach($offices as $office)
        @if($office['name'] === 'Total (A)')
            {{-- Skip serial for Total (A) --}}
            <tr>
                <td></td>
                <td><strong>{{ $office['name'] }}</strong></td>
                @foreach($skills as $skill)
                    <td><strong>{{ $office[$skill] }}</strong></td>
                @endforeach
                <td><strong>{{ $office['total'] }}</strong></td>
            </tr>
        @else
            {{-- Normal office row with serial --}}
            <tr>
                <td>{{ $sl++ }}</td>
                <td>{{ $office['name'] }}</td>
                @foreach($skills as $skill)
                    <td>{{ $office[$skill] }}</td>
                @endforeach
                <td>{{ $office['total'] }}</td>
            </tr>
        @endif
    @endforeach

    {{-- Schemes --}}
    @foreach($schemes as $scheme)
        @if($scheme['name'] === 'Total (B)')
            {{-- Skip serial for Total (B) --}}
            <tr>
                <td></td>
                <td><strong>{{ $scheme['name'] }}</strong></td>
                @foreach($skills as $skill)
                    <td><strong>{{ $scheme[$skill] }}</strong></td>
                @endforeach
                <td><strong>{{ $scheme['total'] }}</strong></td>
            </tr>
        @else
            {{-- Normal scheme row with serial --}}
            <tr>
                <td>{{ $sl++ }}</td>
                <td>{{ $scheme['name'] }}</td>
                @foreach($skills as $skill)
                    <td>{{ $scheme[$skill] }}</td>
                @endforeach
                <td>{{ $scheme['total'] }}</td>
            </tr>
        @endif
    @endforeach

    {{-- Grand Total row (NO serial) --}}
    <tr>
        <td></td>
        <td><strong>{{ $grandTotal['name'] }}</strong></td>
        @foreach($skills as $skill)
            <td><strong>{{ $grandTotal[$skill] }}</strong></td>
        @endforeach
        <td><strong>{{ $grandTotal['total'] }}</strong></td>
    </tr>
    </tbody>
</table>
