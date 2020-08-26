<table>
    <thead>
    <tr>
        @foreach($headers as $header)
            <th>{{ $header }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
        @foreach($data as $item)
            <tr>
                @foreach ($headers as $header)
                    <td>
                        {{ $item->{$header} }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
