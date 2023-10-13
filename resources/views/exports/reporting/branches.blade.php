<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>Tipe Cabang</th>
            <th>Status Cabang</th>
            <th>Masa Sewa</th>
            <th>Jatuh Tempo Sewa</th>
            <th>Open Date Cabang</th>
            <th>Owner/Pemilik Gedung</th>
            <th>Nilai Pembelian</th>
            <th>Nilai Sewa</th>
            <th>Tahun Pembelian</th>
            <th>Jumlah KDO</th>
            <th>Asuransi Gedung</th>
            <th>No Telp Owner</th>
            <th>Perizinan</th>
            <th>Jumlah Karyawan</th>
            <th>Izin Disnaker</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($branches as $index => $branch)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $branch->branch_code }}</td>
                <td>{{ $branch->branch_name }}</td>
                <td>{{ $branch->branch_types->type_name }}</td>
                <td>{{ $branch->status }}</td>
                <td>{{ isset($branch->masa_sewa) ? $branch->masa_sewa.' Tahun' : '-' }}</td>
                <td>{{ $branch->open_date }}</td>
                <td>{{ $branch->expired_date }}</td>
                <td>{{ $branch->owner }}</td>
                <td>{{ $branch->status == 'Milik' ? $branch->total_biaya_sewa : '-'}}</td>
                <td>{{ $branch->status != 'Milik' ? $branch->total_biaya_sewa : '-'}}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>{{ $branch->employees->count(). ' Orang'}}</td>
                <td>-</td>
            </tr>
        @endforeach
    </tbody>
</table>
