<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Cabang</th>
            <th>Category</th>
            <th>Asset Number</th>
            <th>Asset Description</th>
            <th>Date In Place Service</th>
            <th>Major Category</th>
            <th>Asset Location (Lantai)</th>
            <th>Status</th>
            <th>Semester</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($assets as $index => $asset)
        @php
            $detail = $asset->gap_asset_details->where('periode', "2024-02-01")->where('semester', "Smtr 1")->first();
        @endphp
            <tr>
                <td>{{ $index = $index + 1 }}</td>
                <td>{{ $asset->branches->branch_name }}</td>
                <td>{{ $asset->category }}</td>
                <td>{{ $asset->asset_number }}</td>
                <td>{{ $asset->asset_description }}</td>
                <td>{{ \Carbon\Carbon::parse($asset->date_in_place_service)->format('d/m/Y')  }}</td>
                <td>{{ $asset->major_category }}</td>
                <td>{{ $asset->asset_location }}</td>
                <td>{{  isset($detail->status) ? $detail->status : ""}}</td>
                <td>{{  isset($detail->semester) ? $detail->semester : ""}}</td>

            </tr>
        @endforeach
    </tbody>
</table>
