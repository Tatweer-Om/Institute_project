<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Course;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Mail\SendReminderEmail;
use Illuminate\Support\Facades\Mail;

class CronJobController extends Controller
{
    public function getAndSendEmails()
    {
        // Fetch enrollments where the course start date is tomorrow
        $enrollments = Enrollment::whereHas('course', function($query) {
            $query->whereDate('start_date', Carbon::tomorrow());
        })->get();

        foreach ($enrollments as $enrollment) {

            $setting= Setting::first();

            $company= $setting->company_name;
            $phone= $setting->company_phone;

            // Get student details
            $student = Student::where('id', $enrollment->student_id)->first();

            $course = Course::where('id', $enrollment->course_id)->first(); // Get the course details
            $teacher= Teacher::where('id', $course->teacher_id)->first();
            $teacher_name= $teacher->full_name;
            $start_time = Carbon::parse($course->start_time)->format('g:i A'); // Converts to 12-hour format with AM/PM
            $end_time = Carbon::parse($course->end_time)->format('g:i A');

            if ($student && $course) {
                // Send email
                Mail::to($student->student_email)->send(new SendReminderEmail($student, $enrollment, $course, $company, $teacher_name));
                 $content = view('email.email', [
                    'studentName' => $student->first_name,
                    'courseName' => $course->course_name,
                    '$enrollment' => $enrollment->craeted_at,
                    'startDate' => $course->start_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'company' => $company,
                    'teacher_name'=>$teacher_name,
                    'phone'=>$phone,

                ])->render();


                $params = [
                    'studentName' => $student->first_name,
                    'courseName' => $course->course_name,
                    '$enrollment' => $enrollment->craeted_at,
                    'startDate' => $course->start_date,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'company' => $company,
                    'teacher_name'=>$teacher_name,
                    'phone'=>$phone,
                    'sms_status' => 1
                ];
                $sms = get_sms($params);
                sms_module($student->student_number, $sms);
                // Send WhatsApp message (you can use Twilio or other APIs)
            }
        }

        return response()->json(['message' => 'Notifications sent to enrolled students.']);
    }

    private function sendWhatsAppMessage($whatsappNumber, $enrollment)
    {
        $message = "Hello, you have been enrolled in the course on " . $enrollment->created_at->format('Y-m-d') . ".";

        // Replace this part with your WhatsApp API logic
        // Example with Twilio (You need to configure Twilio SDK)

        // Twilio::message($whatsappNumber, $message);

        // Alternatively, you can integrate any other WhatsApp API.
    }
}
