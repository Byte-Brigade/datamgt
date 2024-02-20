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
            <th>Nilai Project</th>
            <th>Tgl Selesai Pekerjaan</th>
            <th>Tgl BAST</th>
            <th>Tgl Request Scoring</th>
            <th>Tgl Scoring</th>
            <th>SLA</th>
            <th>Actual</th>
            <th>Meet The SLA</th>
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
        @foreach ($scoring-projects as $index => $scoring_project)
            <tr>
                <td>{{ $number = $number + 1 }}</td>
                <td>{{ $scoring_project->branches->branch_name }}</td>
                <td>{{ $scoring_project->description }}</td>
                <td>{{ $scoring_project->entity }}</td>
                <td>{{ $scoring_project->pic }}</td>
                <td>{{ $scoring_project->status_pekerjaan }}</td>
                <td>{{ $scoring_project->dokumen_perintah_kerja }}</td>
                <td>{{ $scoring_project->nama_vendor }}</td>
                <td>{{ $scoring_project->nilai_project}}</td>
                <td>{{ Carbon\Carbon::parse($scoring_project->tgl_selesai_pekerjaan)->format('d/m/Y') }}</td>
                <td>{{ Carbon\Carbon::parse($scoring_project->tgl_bast)->format('d/m/Y') }}</td>
                <td>{{ Carbon\Carbon::parse($scoring_project->tgl_request_scoring)->format('d/m/Y') }}</td>
                <td>{{ Carbon\Carbon::parse($scoring_project->tgl_scoring)->format('d/m/Y') }}</td>
                <td>{{ $scoring_project->sla }}</td>
                <td>{{ $scoring_project->actual }}</td>
                <td>{{ $scoring_project->meet_the_sla ? 'YES' : 'NO' }}</td>
                <td>{{ $scoring_project->scoring_vendor }}</td>
                <td>{{ $scoring_project->schedule_scoring }}</td>
                <td>{{ $scoring_project->type }}</td>
                <td>{{ $scoring_project->keterangan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
