<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>Posisi</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal Lahir</th>
            <th>Hiring Date</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($employees as $index => $employee)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $employee->branches->branch_code }}</td>
                <td>{{ $employee->getBranch() }}</td>
                <td>{{ $employee->getPosition() }}</td>
                <td>{{ $employee->employee_id }}</td>
                <td>{{ $employee->name }}</td>
                <td>{{ $employee->email }}</td>
                <td>{{ $employee->gender }}</td>
                <td>{{ $employee->birth_date }}</td>
                <td>{{ $employee->hiring_date }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
