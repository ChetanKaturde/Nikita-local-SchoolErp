<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Academic\Attendance;
use App\Models\Academic\Timetable;
use App\Models\Academic\Division;
use App\Models\Result\Examination;
use App\Models\Result\StudentMark;
use App\Models\Library\Book;
use App\Models\Library\BookIssue;
use Carbon\Carbon;

class StudentTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        echo "\n=== Seeding Test Data for Ayaan Gupta (Student ID: 208) ===\n";

        $studentId = 208;
        $divisionId = 20;
        $teacherId = 3; // John Teacher

        // 1. ATTENDANCE DATA
        echo "\n1. Adding Attendance Records...\n";
        $attendanceDates = [
            ['date' => '2026-03-01', 'status' => 'present'],
            ['date' => '2026-03-02', 'status' => 'present'],
            ['date' => '2026-03-03', 'status' => 'absent'],
            ['date' => '2026-03-04', 'status' => 'present'],
            ['date' => '2026-03-05', 'status' => 'present'],
            ['date' => '2026-03-08', 'status' => 'present'],
            ['date' => '2026-03-09', 'status' => 'late'],
            ['date' => '2026-03-10', 'status' => 'present'],
            ['date' => '2026-03-11', 'status' => 'present'],
            ['date' => '2026-03-12', 'status' => 'absent'],
            ['date' => '2026-03-15', 'status' => 'present'],
            ['date' => '2026-03-16', 'status' => 'present'],
            ['date' => '2026-03-17', 'status' => 'present'],
            ['date' => '2026-03-18', 'status' => 'present'],
            ['date' => '2026-03-19', 'status' => 'present'],
            ['date' => '2026-03-22', 'status' => 'present'],
            ['date' => '2026-03-23', 'status' => 'present'],
        ];

        foreach ($attendanceDates as $data) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'division_id' => $divisionId,
                    'date' => $data['date'],
                ],
                [
                    'status' => $data['status'],
                    'marked_by' => $teacherId,
                    'check_in_time' => '09:00:00',
                    'remarks' => $data['status'] === 'absent' ? 'Sick leave' : null,
                ]
            );
        }
        echo "   ✓ Added " . count($attendanceDates) . " attendance records\n";

        // 2. TIMETABLE DATA
        echo "\n2. Adding Timetable Entries...\n";
        $timetableEntries = [
            ['day' => 'monday', 'start' => '09:00:00', 'end' => '09:50:00', 'subject_id' => 1, 'room' => 'A-101'],
            ['day' => 'monday', 'start' => '10:00:00', 'end' => '10:50:00', 'subject_id' => 2, 'room' => 'A-101'],
            ['day' => 'tuesday', 'start' => '09:00:00', 'end' => '09:50:00', 'subject_id' => 3, 'room' => 'B-102'],
            ['day' => 'tuesday', 'start' => '11:00:00', 'end' => '11:50:00', 'subject_id' => 4, 'room' => 'LAB-1'],
            ['day' => 'wednesday', 'start' => '09:00:00', 'end' => '09:50:00', 'subject_id' => 5, 'room' => 'A-101'],
            ['day' => 'wednesday', 'start' => '10:00:00', 'end' => '10:50:00', 'subject_id' => 1, 'room' => 'A-101'],
            ['day' => 'thursday', 'start' => '09:00:00', 'end' => '09:50:00', 'subject_id' => 2, 'room' => 'B-102'],
            ['day' => 'thursday', 'start' => '11:00:00', 'end' => '11:50:00', 'subject_id' => 6, 'room' => 'LAB-2'],
            ['day' => 'friday', 'start' => '09:00:00', 'end' => '09:50:00', 'subject_id' => 3, 'room' => 'A-101'],
            ['day' => 'friday', 'start' => '10:00:00', 'end' => '10:50:00', 'subject_id' => 4, 'room' => 'LAB-1'],
        ];

        foreach ($timetableEntries as $entry) {
            Timetable::create([
                'division_id' => $divisionId,
                'subject_id' => $entry['subject_id'],
                'teacher_id' => $teacherId,
                'day_of_week' => $entry['day'],
                'start_time' => $entry['start'],
                'end_time' => $entry['end'],
                'room_number' => $entry['room'],
                'academic_year_id' => 1,
                'is_active' => true,
                'status' => 'active',
            ]);
        }
        echo "   ✓ Added " . count($timetableEntries) . " timetable entries\n";

        // 3. EXAMINATION & MARKS DATA
        echo "\n3. Adding Examination and Marks Records...\n";
        
        // Create examinations
        $exams = [
            ['name' => 'Mid Term Examination', 'code' => 'MID2026', 'start_date' => '2026-02-15', 'end_date' => '2026-02-28'],
            ['name' => 'Final Examination', 'code' => 'FINAL2026', 'start_date' => '2026-03-10', 'end_date' => '2026-03-25'],
        ];

        foreach ($exams as $examData) {
            $exam = Examination::firstOrCreate(
                ['code' => $examData['code']],
                [
                    'name' => $examData['name'],
                    'start_date' => $examData['start_date'],
                    'end_date' => $examData['end_date'],
                    'type' => 'Term Exam',
                    'academic_year' => '2025-2026',
                    'status' => 'completed',
                ]
            );

            // Add marks for subjects
            $subjects = [1, 2, 3, 4, 5, 6];
            foreach ($subjects as $subjectId) {
                $marksObtained = rand(65, 95);
                $maxMarks = 100;
                $grade = $marksObtained >= 90 ? 'O' : ($marksObtained >= 80 ? 'A+' : ($marksObtained >= 70 ? 'A' : ($marksObtained >= 60 ? 'B+' : 'B')));
                
                StudentMark::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'examination_id' => $exam->id,
                        'subject_id' => $subjectId,
                    ],
                    [
                        'marks_obtained' => $marksObtained,
                        'max_marks' => $maxMarks,
                        'grade' => $grade,
                        'result' => 'pass',
                        'is_approved' => true,
                    ]
                );
            }
        }
        echo "   ✓ Added marks for 2 examinations × 6 subjects\n";

        // 4. LIBRARY BOOK DATA
        echo "\n4. Adding Library Book Records...\n";
        
        // Create some books if they don't exist
        $booksData = [
            ['title' => 'Advanced Mathematics', 'author' => 'R.S. Agarwal', 'isbn' => '978-8121901017', 'category' => 'Mathematics'],
            ['title' => 'Physics Fundamentals', 'author' => 'H.C. Verma', 'isbn' => '978-8177091403', 'category' => 'Physics'],
            ['title' => 'Organic Chemistry', 'author' => 'O.P. Tandon', 'isbn' => '978-9350181010', 'category' => 'Chemistry'],
            ['title' => 'English Literature', 'author' => 'William Shakespeare', 'isbn' => '978-0140439106', 'category' => 'English'],
        ];

        foreach ($booksData as $bookData) {
            $book = Book::firstOrCreate(
                ['isbn' => $bookData['isbn']],
                [
                    'title' => $bookData['title'],
                    'author' => $bookData['author'],
                    'category' => $bookData['category'],
                    'total_copies' => 5,
                    'available_copies' => 4,
                    'is_active' => true,
                ]
            );

            // Issue book to student
            BookIssue::create([
                'book_id' => $book->id,
                'student_id' => $studentId,
                'issue_date' => Carbon::now()->subDays(15),
                'due_date' => Carbon::now()->addDays(15),
                'status' => 'issued',
                'remarks' => 'Active issue',
            ]);
        }
        echo "   ✓ Issued " . count($booksData) . " books to student\n";

        echo "\n=== Test Data Seeding Complete ===\n";
        echo "Student: Ayaan Gupta (ID: 208)\n";
        echo "Division ID: 20\n";
        echo "Data Added:\n";
        echo "  - Attendance: 17 records\n";
        echo "  - Timetable: 10 entries\n";
        echo "  - Examinations: 2 (with marks for 6 subjects each)\n";
        echo "  - Library Books: 4 books issued\n";
        echo "=====================================\n\n";
    }
}
