<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Cabang</th>
            <th>Description</th>
            <th>Entity</th>
            <th>PIC</th>
            <th>Status Pekerjaan</th>
            <th>Dokumen Perintah Kerja</th>
            <th>Nama Vendor</th>
            <th>Tgl Scoring</th>
            <th>Scoring Vendor</th>
            <th>Schedule Scoring</th>
            <th>Type</th>
            <th>Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $number = 0;
        @endphp
        @foreach ($scoring_assessments as $index => $scoring_assessment)
            <tr>
                <td>{{ $number = $number + 1 }}</td>
                <td>{{ $scoring_assessment->branches->branch_name }}</td>
                <td>{{ $scoring_assessment->description }}</td>
                <td>{{ $scoring_assessment->entity }}</td>
                <td>{{ $scoring_assessment->pic }}</td>
                <td>{{ $scoring_assessment->dokumen_perintah_kerja }}</td>
                <td>{{ $scoring_assessment->nama_vendor }}</td>
                <td>{{ Carbon\Carbon::parse($scoring_assessment->tgl_scoring)->format('d/m/Y') }}</td>
                <td>{{ $scoring_assessment->scoring_vendor }}</td>
                <td>{{ $scoring_assessment->schedule_scoring }}</td>
                <td>{{ $scoring_assessment->type }}</td>
                <td>{{ $scoring_assessment->keterangan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
