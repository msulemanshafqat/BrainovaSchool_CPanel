<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class MobileAppSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $data = [
            'student' => [
                'menu' => [
                    ["name" => "Subject", "slug" => "subject", "icon" => ('images/student/menu-icon/subject.png')],
                    ["name" => "Class Routine", "slug" => "class-routine", "icon" => ('images/student/menu-icon/class-routine.png')],
                    ["name" => "Exam Routine", "slug" => "exam-routine", "icon" => ('images/student/menu-icon/exam-routine.png')],
                    ["name" => "Marksheet", "slug" => "marksheet", "icon" => ('images/student/menu-icon/marksheet.png')],
                    ["name" => "Attendance", "slug" => "attendance", "icon" => ('images/student/menu-icon/attendance.png')],
                    ["name" => "Fees", "slug" => "fees", "icon" => ('images/student/menu-icon/fees.png')],
                    ["name" => "Online Exam", "slug" => "online-exam", "icon" => ('images/student/menu-icon/online-exam.png')],
                    ["name" => "Student Info", "slug" => "student-info", "icon" => ('images/student/menu-icon/student-info.png')],
                    ["name" => "Events", "slug" => "events", "icon" => ('images/student/menu-icon/class-routine.png')],
                    ["name" => "Student Homework", "slug" => "student-homework", "icon" => ('images/student/menu-icon/online-exam.png')],
                    ["name" => "Library", "slug" => "student-library", "icon" => ('images/student/menu-icon/library.png')],
                    ["name" => "Chat", "slug" => "chat", "icon" => ('images/teacher/menu-icon/chat.png')],
                ],
                'slider' => [
                    ["name" => "Empowering Every Student, Every Day", "slug" => "subject", "icon" => ('frontend/img/sliders/01.webp')],
                    ["name" => "Smart Tools for Smarter Learning.", "slug" => "subject", "icon" => ('frontend/img/sliders/02.webp')],
                    ["name" => "From Attendance to Achievement — All in One Place", "slug" => "subject", "icon" => ('frontend/img/sliders/03.webp')],
                ],
            ],
            'teacher' => [
                'menu' => [
                    ["name" => "Students", "slug" => "students", "icon" => ('images/teacher/menu-icon/student-info.png')],
                    ["name" => "Attendance", "slug" => "attendance", "icon" => ('images/teacher/menu-icon/attendance.png')],
                    ["name" => "Routine", "slug" => "routine", "icon" => ('images/teacher/menu-icon/routine.png')],
                    ["name" => "Homework", "slug" => "homework", "icon" => ('images/teacher/menu-icon/homework.png')],
                    ["name" => "Live Class", "slug" => "live-class", "icon" => ('images/teacher/menu-icon/live_class.png')],
                    ["name" => "Online Exam", "slug" => "online-exam", "icon" => ('images/teacher/menu-icon/online-exam.png')],
                    ["name" => "Report", "slug" => "report", "icon" => ('images/teacher/menu-icon/report.png')],
                    ["name" => "Chat", "slug" => "chat", "icon" => ('images/teacher/menu-icon/chat.png')],
                    ["name" => "Library", "slug" => "library", "icon" => ('images/student/menu-icon/library.png')],
                ],
                'slider' => [
                    ["name" => "Teach Better, Track Smarter.", "slug" => "subject", "icon" => ('frontend/img/sliders/01.webp')],
                    ["name" => "Your Classroom, Organized in One Tap.", "slug" => "subject", "icon" => ('frontend/img/sliders/02.webp')],
                    ["name" => "From Planning to Progress — Simplified for Teachers.", "slug" => "subject", "icon" => ('frontend/img/sliders/03.webp')],
                ],
            ],
            'guardian' => [
                'menu' => [
                    ["name" => "Child Info", "slug" => "student-info", "icon" => ('images/teacher/menu-icon/student-info.png')],
                    ["name" => "Attendance", "slug" => "attendance", "icon" => ('images/teacher/menu-icon/attendance.png')],
                    ["name" => "Marksheet", "slug" => "marksheet", "icon" => ('images/student/menu-icon/marksheet.png')],
                    ["name" => "Exam Routine", "slug" => "exam-routine", "icon" => ('images/student/menu-icon/exam-routine.png')],
                    ["name" => "Fees", "slug" => "fees", "icon" => ('images/student/menu-icon/fees.png')],
                    ["name" => "Homework", "slug" => "homework", "icon" => ('images/teacher/menu-icon/homework.png')],
                    ["name" => "Notice", "slug" => "notice", "icon" => ('images/guardian/menu-icon/Notification.png')],
                ],
                'slider' => [
                    ["name" => "Stay Connected to Your Child’s Education.", "slug" => "student-info", "icon" => ('frontend/img/sliders/01.webp')],
                    ["name" => "From Classroom to Home — Always in the Know.", "slug" => "notice", "icon" => ('frontend/img/sliders/02.webp')],
                    ["name" => "Track Progress, Attendance & More — Anytime, Anywhere.", "slug" => "attendance", "icon" => ('frontend/img/sliders/03.webp')],
                ],
            ],
        ];



        foreach ($data as $userType => $types) {
            foreach ($types as $type => $items) {
                foreach ($items as $i => $item) {
                    DB::table('mobile_app_settings')->insert([
                        'type'       => $type,
                        'user_type'  => $userType,
                        'title'      => $item['name'],
                        'slug'       => $item['slug'],
                        'icon_path'  => $item['icon'],
                        'is_active'  => true,
                        'serial'     => $i + 1,
                    ]);
                }
            }
        }
    }
}
