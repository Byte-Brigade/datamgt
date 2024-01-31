<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Cabang</th>
            <th>Jenis Perizinan</th>
            <th>Tgl Pengesahan</th>
            <th>Tgl Masa Berlaku s/d</th>
            <th>Progress Resertifikasi</th>
        </tr>
    </thead>
    <tbody>
        @php
            $previousBranchName = null;
            $number = 0;
        @endphp
        @foreach ($disnakers as $index => $disnaker)
            @php
                $branch_name = $disnaker->branches->branch_name;

            @endphp
            <tr>
                @if ($branch_name != $previousBranchName)

                    <td>{{ $number = $number + 1}}</td>
                    <td>{{ $disnaker->branches->branch_name }}</td>
                @else
                    <td></td>
                    <td></td>
                @endif
                <td>{{ $disnaker->jenis_perizinan->name }}</td>
                <td>{{ \Carbon\Carbon::parse($disnaker->tgl_pengesahan)->format('d M Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($disnaker->tgl_masa_berlaku)->format('d M Y') }}</td>
                <td>{{ $disnaker->progress_resertifikasi }}</td>
            </tr>
            @php
                $previousBranchName = $branch_name;
            @endphp
        @endforeach
    </tbody>
</table>
