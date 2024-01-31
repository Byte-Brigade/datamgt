<table>
    <thead>
        <tr>
            <th rowspan="2" class="text-center">No</th>
            <th rowspan="2" class="text-center">Kategori</th>
            <th rowspan="2" class="text-center">Target</th>
            <th colspan="4" class="text-center">Status</th>
        </tr>
        <tr>
                <th class="text-center">Done</th>
                <th class="text-center">On Progress</th>
                <th class="text-center">Not Start</th>
                <th class="text-center">Drop</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)

            <tr>
                <td>{{ $item['no'] }}</td>
                <td>{{ $item['category']}}</td>
                <td>{{ $item['target']}}</td>
                <td>{{ $item['done']}}</td>
                <td>{{ $item['on_progress']}}</td>
                <td>{{ $item['not_start']}}</td>
                <td>{{ $item['drop']}}</td>
            </tr>

        @endforeach

    </tbody>
</table>
