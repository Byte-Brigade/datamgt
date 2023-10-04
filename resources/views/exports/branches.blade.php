<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>NPWP</th>
            <th>Alamat</th>
            <th>No. Telpon</th>
            <th>Fasilitas ATM</th>
            <th>Layanan ATM</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($branches as $index => $branch)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $branch->branch_code }}</td>
                <td>{{ $branch->branch_name }}</td>
                <td>{{ $branch->npwp }}</td>
                <td>{{ $branch->address }}</td>
                <td>{{ $branch->telp }}</td>
                <td>{{ is_null($branch->layanan_atm) ? 'Tidak Ada' : 'Ada' }}</td>
                <td>{{ is_null($branch->layanan_atm) ? 'Tidak Ada' : $branch->layanan_atm }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
