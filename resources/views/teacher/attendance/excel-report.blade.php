<table>
    <thead>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 16px; font-weight: bold;">
                Attendance Report - {{ $division->division_name }}
            </th>
        </tr>
        <tr>
            <th colspan="8" style="text-align: center; font-size: 12px;">
                Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} to {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </th>
        </tr>
        <tr>
            <th>S.No</th>
            <th>Date</th>
            <th>Day</th>
            <th>Student Name</th>
            <th>Roll Number</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Remarks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($attendances as $index => $attendance)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $attendance->date->format('Y-m-d') }}</td>
            <td>{{ $attendance->date->format('l') }}</td>
            <td>{{ $attendance->student->full_name ?? 'N/A' }}</td>
            <td>{{ $attendance->student->roll_number ?? 'N/A' }}</td>
            <td>{{ $attendance->timetable->subject->name ?? 'N/A' }}</td>
            <td>{{ ucfirst($attendance->status) }}</td>
            <td>{{ $attendance->remarks ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
