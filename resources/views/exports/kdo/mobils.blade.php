<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Vendor</th>
            <th>Nama Cabang</th>
            <th>Nopol</th>
            <th>Awal Sewa</th>
            <th>Akhir Sewa</th>
            @foreach ($months as $index => $month)
                <th>{{ $month }}</th>
            @endforeach
            <th>Total Sewa</th>
        </tr>
    </thead>
    <tbody>
        @php
            $number = 0;
        @endphp
        @foreach ($kdo_mobils as $index => $kdo_mobil)
            <tr>
                <td>{{ $number = $number + 1 }}</td>
                <td>{{ $kdo_mobil->vendor }}</td>
                <td>{{ $kdo_mobil->branches->branch_name }}</td>
                <td>{{ $kdo_mobil->nopol }}</td>
                <td>{{ Carbon\Carbon::parse($kdo_mobil->awal_sewa)->format('d/m/Y') }}</td>
                <td>{{ Carbon\Carbon::parse($kdo_mobil->akhir_sewa)->format('d/m/Y') }}</td>
                @foreach ($months as $index => $month)
                    @php
                        $biaya_sewa = $kdo_mobil->biaya_sewas->flatMap(function ($data) {
                            return [strtolower(Carbon\Carbon::parse($data['periode'])->format('M')) => $data['value'] != 0 ? number_format($data['value'], 0, ',', '.') : '-'];
                        });
                    @endphp
                    <td>{{ isset($biaya_sewa[strtolower($month)]) ? $biaya_sewa[strtolower($month)] : '' }}</td>
                @endforeach


                <td>{{ number_format(collect($kdo_mobil->biaya_sewa)->sum('value'), 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
