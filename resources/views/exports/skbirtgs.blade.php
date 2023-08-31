<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Jenis Surat</th>
            <th>Nomor Surat</th>
            <th>Kantor Cabang</th>
            <th>Penerima Kuasa</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ops_skbirtgs as $index => $skbirtgs)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>Surat Kuasa BI RTGS</td>
                <td>{{ $skbirtgs->no_surat }}</td>
                <td>{{ $skbirtgs->getBranch() }}</td>
                <td> - </td>
                <td>{{ $skbirtgs->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
