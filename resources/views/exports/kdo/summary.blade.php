<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Cabang</th>
            <th>Jumlah KDO</th>
            <th>Sewa Perbulan</th>
            <th>Jatuh Tempo</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kdos as $index => $kdo)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $kdo->branches->branch_name }}</td>

                <td>{{ $kdo->gap_kdo_mobil->unique('nopol')->count() }}</td>
                <td>{{ number_format(
                    $kdo->gap_kdo_mobil->flatMap(function ($mobil) {
                            $mobil->biaya_sewa = collect($mobil->biaya_sewa);
                            return $mobil->biaya_sewa;
                        })->groupBy('periode')->sortKeysDesc()->first()->sum('value'),
                    0,
                    ',',
                    '.',
                ) }}
                </td>
                <td>{{ $kdo->gap_kdo_mobil()->orderBy('akhir_sewa', 'asc')->first()->akhir_sewa }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
