<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReminderEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $enrollment;
    public $course;

    /**
     * Create a new message instance.
     */
    public function __construct($student, $enrollment, $course)
    {
        $this->student = $student;
        $this->enrollment = $enrollment;
        $this->course = $course;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Course Reminder')
                    ->view('email.email')
                    ->with([
                        'studentName' => $this->student->student_name,
                        'courseName' => $this->course->course_name,
                        'startDate' => $this->course->start_date,
                    ]);
    }
}
