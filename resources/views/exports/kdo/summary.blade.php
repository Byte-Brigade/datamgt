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
        @php
            $number = 0;
        @endphp
        @foreach ($branches as $index => $branch)
            @if ($branch->gap_kdo->count() > 0)
                @php

                    $biaya_sewa = $branch->gap_kdo
                        ->flatMap(function ($mobil) {
                            return $mobil->biaya_sewas;
                        })
                        ->groupBy('periode')
                        ->sortKeysDesc()
                        ->first();
                @endphp
                <tr>
                    <td>{{ $number = $number + 1 }}</td>
                    <td>{{ $branch->branch_name }}</td>

                    <td>{{ $branch->gap_kdo->count() }}</td>
                    <td>{{ isset($biaya_sewa) ? $biaya_sewa->sum('value') : 0 }}
                    </td>
                    <td>{{ isset($branch->gap_kdo) ? \Carbon\Carbon::parse($branch->gap_kdo->first()->akhir_sewa)->format('d/m/Y') : '' }}
                    </td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
