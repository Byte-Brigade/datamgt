<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>Tipe Cabang</th>
            <th>Alamat</th>
            <th>Telp</th>
            <th>Fasilitas ATM</th>
            <th>Layanan ATM</th>
            <th>NPWP</th>
            <th>NITKU</th>
            <th>Status Kepemilikan</th>
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
                <td>{{ $branch->address }}</td>
                <td>{{ $branch->telp }}</td>
                <td>{{ $branch->layanan_atm == 'Tidak Ada' ? 'Tidak Ada' : 'Ada'}}</td>
                <td>{{ $branch->layanan_atm == 'Tidak Ada' ? 'Tidak Ada' : $branch->layanan_atm}}</td>
                <td>{{ $branch->npwp }}</td>
                <td>{{ $branch->nitku }}</td>
                <td>{{ $branch->status }}</td>
                <td>{{ isset($branch->masa_sewa) ? $branch->masa_sewa.' Tahun' : '-' }}</td>
                <td>{{ $branch->expired_date }}</td>
                <td>{{ $branch->open_date }}</td>
                <td>{{ $branch->owner }}</td>
                <td>{{ $branch->status == 'Milik' ? $branch->total_biaya_sewa : '-'}}</td>
                <td>{{ $branch->status != 'Milik' ? $branch->total_biaya_sewa : '-'}}</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>-</td>
                <td>{{ $branch->izin }}</td>
                <td>{{ $branch->employees->count(). ' Orang'}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
