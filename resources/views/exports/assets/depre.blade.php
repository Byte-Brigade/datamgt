<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Cabang</th>
            <th>Category</th>
            <th>Asset Number</th>
            <th>Asset Description</th>
            <th>Date In Place Service</th>
            <th>Asset Cost</th>
            <th>Major Category</th>
            <th>Minor Category</th>
            <th>Depre Exp</th>
            <th>Net Book Value</th>
            <th>Asset Location (Lantai)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assets as $index => $asset)
            <tr>
                <td>{{ $index = $index + 1 }}</td>
                <td>{{ $asset->branches->branch_name }}</td>
                <td>{{ $asset->category }}</td>
                <td>{{ $asset->asset_number }}</td>
                <td>{{ $asset->asset_description }}</td>
                <td>{{ \Carbon\Carbon::parse($asset->date_in_place_service)->format('d/m/Y')  }}</td>
                <td>{{ $asset->asset_cost }}</td>
                <td>{{ $asset->major_category }}</td>
                <td>{{ $asset->minor_category }}</td>
                <td>{{ $asset->depre_exp }}</td>
                <td>{{ $asset->net_book_value }}</td>
                <td>{{ $asset->asset_location }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
