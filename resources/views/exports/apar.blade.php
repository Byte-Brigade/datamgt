<table>
    <thead>
        <tr>
            <th rowspan="2"  class="text-center">No</th>
            <th rowspan="2"  class="text-center">Kode Cabang</th>
            <th rowspan="2"  class="text-center">Nama Cabang</th>
            <th rowspan="2"  class="text-center">Expired Date</th>

            @foreach (range(1, 11) as $num)

                    <th  class="text-center" colspan="2">{{ "APAR_".$num}}</th>

            @endforeach

            <th  class="text-center" rowspan="2">Keterangan</th>
        </tr>
        <tr>
            @foreach (range(1, 11) as $num)
                    <th  class="text-center">Titik Posisi</th>
                    <th  class="text-center">Masa Expired</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($apars as $index => $apar)

            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $apar->branches->branch_code }}</td>
                <td>{{ $apar->branches->branch_name }}</td>
                <td>{{ \Carbon\Carbon::parse($apar->expired_date)->format('d/m/Y')  }}</td>
                @foreach ($apar->detail as $apar_detail)
                    <td>{{ $apar_detail->titik_posisi}}</td>
                    <td>{{ \Carbon\Carbon::parse($apar->expired_date)->format('d/m/Y') }}</td>
                @endforeach
                <td>{{ $apar->keterangan }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
