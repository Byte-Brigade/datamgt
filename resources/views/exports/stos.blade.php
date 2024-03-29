<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Cabang</th>
            <th>Tipe Cabang</th>
            <th>Depre</th>
            <th>Non-Depre</th>
            <th>Total Remarked</th>
            <th>Sudah STO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stos as $index => $sto)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $sto->branches->branch_name }}</td>
                <td>{{ $sto->branches->branch_types->type_name }}</td>
                <td>{{ $sto->gap_assets()->where('category', 'Depre')->whereHas('gap_asset_details', function ($q) use ($latestPeriode) {
                        return $q->where('periode', $latestPeriode);
                    })->count() .
                    '/' .
                    $sto->gap_assets->where('category', 'Depre')->count() }}
                </td>
                <td>{{ $sto->gap_assets()->where('category', 'Non-Depre')->whereHas('gap_asset_details', function ($q) use ($latestPeriode) {
                        return $q->where('periode', $latestPeriode);
                    })->count() .
                    '/' .
                    $sto->gap_assets->where('category', 'Non-Depre')->count() }}
                </td>
                <td>{{ $sto->gap_assets()->whereHas('gap_asset_details', function ($q) use ($latestPeriode) {
                        return $q->where('periode', $latestPeriode);
                    })->count() .
                    '/' .
                    $sto->gap_assets->count() }}
                </td>
                <td>{{ $sto->remarked ? 'Sudah' : 'Belum' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
