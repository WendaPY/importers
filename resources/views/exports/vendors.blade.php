<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Phone</th>
        <th>Comments</th>
        <th>Products</th>
        <th>Latitude</th>
        <th>Longitude</th>
    </tr>
    </thead>
    <tbody>
        @foreach($vendors as $vendor)
            <tr>
                <td>
                    {{ $vendor->id }}
                </td>
                <td>
                    {{ $vendor->name }}
                </td>
                <td>
                    {{ $vendor->phone }}
                </td>
                <td>
                    {{ $vendor->comments }}
                </td>
                <td>
                    {{ $vendor->products->pluck('name')->implode(' / ') }}
                </td>
                <td>
                    {{ $vendor->lat }}
                </td>
                <td>
                    {{ $vendor->lon }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
