<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>No Izin</th>
            <th>Nilai Pajak</th>
            <th>Periode Awal</th>
            <th>Periode Akhir</th>
            <th>Note</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($pajakreklames as $index => $pajakreklame)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $pajakreklame->getBranchCode() }}</td>
                <td>{{ $pajakreklame->getBranchName() }}</td>
                <td>{{ $pajakreklame->no_izin }}</td>
                <td>{{ $pajakreklame->nilai_pajak }}</td>
                <td>{{ \Carbon\Carbon::parse($pajakreklame->periode_awal)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($pajakreklame->periode_akhir)->format('d/m/Y') }}</td>
                <td>{{ $pajakreklame->note }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
