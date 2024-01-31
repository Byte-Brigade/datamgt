<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>No Surat</th>
            <th>Expired Date</th>
            <th>Filename</th>
            <th>Note</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($skoperasionals as $index => $skoperasional)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $skoperasional->getBranchCode() }}</td>
                <td>{{ $skoperasional->getBranchName() }}</td>
                <td>{{ $skoperasional->no_surat }}</td>
                <td>{{ $skoperasional->expiry_date }}</td>
                <td>{{ $skoperasional->file }}</td>
                <td>{{ $skoperasional->note }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
