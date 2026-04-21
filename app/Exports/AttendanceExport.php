<?php

namespace App\Exports;

use Illuminate\Contracts\View\View as ViewContract;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceExport implements FromView, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $attendances;
    protected $division;
    protected $startDate;
    protected $endDate;

    public function __construct($attendances, $division, $startDate, $endDate)
    {
        $this->attendances = $attendances;
        $this->division = $division;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): ViewContract
    {
        return view('teacher.attendance.excel-report', [
            'attendances' => $this->attendances,
            'division' => $this->division,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function headings(): array
    {
        return [
            'S.No',
            'Date',
            'Day',
            'Student Name',
            'Roll Number',
            'Subject',
            'Status',
            'Remarks',
        ];
    }

    public function map($attendance): array
    {
        static $i = 0;
        $i++;
        
        return [
            $i,
            $attendance->date->format('Y-m-d'),
            $attendance->date->format('l'),
            $attendance->student->full_name ?? 'N/A',
            $attendance->student->roll_number ?? 'N/A',
            $attendance->timetable->subject->name ?? 'N/A',
            ucfirst($attendance->status),
            $attendance->remarks ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Attendance Report';
    }
}
