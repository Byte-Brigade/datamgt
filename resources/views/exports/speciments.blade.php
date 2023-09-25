<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Cabang</th>
            <th>Tanggal Spesimen</th>
            <th>Hasil Konfirmasi Cabang</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($speciments as $index => $speciment)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $speciment->branches->branch_name }}</td>

                <td>{{ $speciment->tgl_speciment }}</td>
                <td>{{ $speciment->hasil_konfirmasi_cabang }}</td>
                <td>{{ $speciment->keterangan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
