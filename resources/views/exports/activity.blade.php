<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Log Name</th>
            <th>Description</th>
            <th>Subject Type</th>
            <th>Event</th>
            <th>Subject ID</th>
            <th>Causer Name</th>
            <th>Properties</th>
            <th>Created At</th>
            <th>Updated At</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($activities as $index => $activity)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $activity->log_name }}</td>
                <td>{{ $activity->description }}</td>
                <td>{{ $activity->subject_type }}</td>
                <td>{{ $activity->event }}</td>
                <td>{{ $activity->subject_id }}</td>
                <td>{{ $activity->causer->name }}</td>
                <td>{{ $activity->properties }}</td>
                <td>{{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($activity->updated_at)->format('d/m/Y') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
