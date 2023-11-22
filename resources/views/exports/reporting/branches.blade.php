<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>Tipe Cabang</th>
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
            <th>Izin / OJK</th>
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
                <td>{{ isset($branch->masa_sewa) ? $branch->masa_sewa . ' Tahun' : '-' }}</td>
                <td>{{ $branch->open_date }}</td>
                <td>{{ $branch->expired_date }}</td>
                <td>{{ $branch->owner }}</td>
                <td>{{ $branch->status == 'Milik' ? number_format($branch->total_biaya_sewa, 0, ',', '.') : '-' }}</td>
                <td>{{ $branch->status != 'Milik' ? number_format($branch->total_biaya_sewa, 0, ',', '.') : '-' }}</td>
                <td>-</td>
                <td>{{ $branch->gap_kdo_mobil->count() }}</td>
                <td>-</td>
                <td>-</td>
                <td>{{ $branch->izin }}</td>
                <td>{{ $branch->employees->count() > 0 ? $branch->employees->count() . ' Orang' : 'Tidak Ada' }}</td>
                <td>
                    <ol>
                        @foreach ($branch->gap_disnaker as $index => $izin)
                            <li>{{ ($index+1).'. '.$izin->jenis_perizinan->name }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
