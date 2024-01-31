<table>
    <thead>
        <tr>
            <th rowspan="2" class="text-center">No</th>
            <th rowspan="2" class="text-center">Kode Cabang</th>
            <th rowspan="2" class="text-center">Nama Cabang</th>
            <th rowspan="2" class="text-center">Expired Date</th>

            @foreach (range(1, 11) as $num)
                <th class="text-center" colspan="2">{{ 'APAR_' . $num }}</th>
            @endforeach

            <th class="text-center" rowspan="2">Keterangan</th>
        </tr>
        <tr>
            @foreach (range(1, 11) as $num)
                <th class="text-center">Titik Posisi</th>
                <th class="text-center">Masa Expired</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($branches as $index => $branch)
            @if($branch->ops_apar->count() > 0)

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $branch->branch_code }}</td>
                <td>{{ $branch->branch_name }}</td>
                <td>{{ \Carbon\Carbon::parse($branch->ops_apar->max('periode'))->format('d/m/Y') }}</td>
                @foreach ($branch->ops_apar as $apar)
                    <td>{{ $apar->titik_posisi }}</td>
                    <td>{{ \Carbon\Carbon::parse($apar->expired_date)->format('d/m/Y') }}</td>
                @endforeach
                <td>{{ $branch->ops_apar->count() }} Tabung</td>
            </tr>
            @endif
        @endforeach

    </tbody>
</table>
