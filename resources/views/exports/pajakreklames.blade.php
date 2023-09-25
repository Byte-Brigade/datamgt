<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
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
                <td>{{ $pajakreklame->periode_awal }}</td>
                <td>{{ $pajakreklame->periode_akhir }}</td>
                <td>{{ $pajakreklame->note }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
