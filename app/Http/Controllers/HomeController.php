<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $latest_courses = Course::latest()->take(10)->get();
        $latest_students = Student::latest()->take(10)->get();
        $latest_users = User::latest()->take(10)->get();
        $latest_teachers = Teacher::latest()->take(10)->get();
        $latest_enrollments = Enrollment::latest()->take(10)->get();

        $one_course = Course::latest()->take(1)->get()->value('course_name'); // Get first item instead of collection
        $one_student = Student::latest()->take(1)->get()->value('full_name');
        $one_user = User::latest()->take(1)->get()->value('user_name');
        $one_teacher = Teacher::latest()->take(1)->get()->value('full_name');
        $one_enrollment = Enrollment::latest()->take(1)->get()->value('student_name');

        $course_count = Course::count();
        $student_count = Student::count();

        $user_count = User::count();
        $teacher_count = Teacher::count();
        $enrollment_count = Enrollment::count(); // Removed redundant assignment

        if (Auth::check()) {
            return view('dashboard.index', [
                'latest_courses' => $latest_courses,
                'latest_students' => $latest_students,
                'latest_users' => $latest_users,
                'latest_teachers' => $latest_teachers,
                'latest_enrollments' => $latest_enrollments,
                'one_course' => $one_course,
                'one_student' => $one_student,
                'one_user' => $one_user,
                'one_teacher' => $one_teacher,
                'one_enrollment' => $one_enrollment,
                'course_count' => $course_count,
                'student_count' => $student_count,
                'user_count' => $user_count,
                'teacher_count' => $teacher_count,
                'enrollment_count' => $enrollment_count,
            ]);
        } else {
            return redirect()->route('login_page')->with('error', 'Logged In First');
        }
    }



    public function switchLanguage($locale)
    {
        app()->setLocale($locale);
        config(['app.locale' => $locale]);
        // You can store the chosen locale in session for persistence
        session(['locale' => $locale]);

        return redirect()->back(); // or any other redirect you want
    }
}
