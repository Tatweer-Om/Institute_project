<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Expense;
use App\Models\Setting;
use App\Models\Teacher;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Constraint\Count;

class ReportController extends Controller
{


    public function income_report(Request $request) {
        // Set start and end dates based on the request, or use today's date as default
        $sdate = !empty($request['date_from']) ? $request['date_from'] : date('Y-m-d');
        $edate = !empty($request['to_date']) ? $request['to_date'] : date('Y-m-d');

        // Retrieve the first setting (general information)
        $about = Setting::first();

        // Get total expenses within the date range
        $total_expense = Expense::whereDate('created_at', '>=', $sdate)
                                ->whereDate('created_at', '<=', $edate)
                                ->sum('amount'); // Sum the 'amount' column

        // Get total income (discounted prices) and student count within the date range
        $enrolments = Enrollment::whereDate('created_at', '>=', $sdate)
                                ->whereDate('created_at', '<=', $edate);

        $total_income = $enrolments->sum('discounted_price'); // Sum the 'discounted_price' column
        $total_student = $enrolments->distinct('student_id')->count('student_id'); // Count unique students

        // Calculate profit as income minus expense
        $profit = $total_income - $total_expense;

        // Define report name
        $report_name = 'Income Report';


        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(6, explode(',', $user->permit_type))) {

            return view('reports.income_report', compact('sdate', 'edate', 'about', 'report_name', 'total_expense', 'total_income', 'total_student', 'profit'));
        } else {

             return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));

        }
        // Pass data to the view
    }


    public function course_income_report(Request $request) {

        // Set start and end dates based on the request, or use today's date as default
        $sdate = !empty($request['date_from']) ? $request['date_from'] : date('Y-m-d');
        $edate = !empty($request['to_date']) ? $request['to_date'] : date('Y-m-d');

        $course_id = $request['course_id'];

        $course= Course::where('id', $course_id )->first();

        // Retrieve the first setting (general information)
        $about = Setting::first();

        // Query for enrollments for the specified course
        $enrolments = Enrollment::where('course_id', $course_id);

        // If the provided start and end dates are not the current date, apply the date filter
        if ($sdate !== date('Y-m-d') || $edate !== date('Y-m-d')) {
            $enrolments = $enrolments->whereDate('created_at', '>=', $sdate)
                                     ->whereDate('created_at', '<=', $edate);
        }

        // Calculate the total income and count distinct students
        $total_income = $enrolments->sum('discounted_price');
        $total_student = $enrolments->distinct('student_id')->count('student_id');

        // Get all courses for the selection in the view
        $courses = Course::all();

        // Define the report name
        $report_name = 'Course Income Report';

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(6, explode(',', $user->permit_type))) {

            return view('reports.course_icome_report', compact('sdate', 'courses', 'course',  'edate', 'about', 'report_name', 'total_income', 'total_student'));
        } else {

             return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));

        }

        // Pass data to the view
    }


    public function all_courses_income(Request $request) {

        // Set start and end dates based on the request, or use today's date as default
        $sdate = !empty($request['date_from']) ? $request['date_from'] : date('Y-m-d');
        $edate = !empty($request['to_date']) ? $request['to_date'] : date('Y-m-d');

        // Retrieve the first setting (general information)
        $about = Setting::first();

        // Retrieve all courses
        $courses = Course::all();

        // Initialize an array to store course-wise income and student count
        $courses_data = [];

        // Iterate through each course
        foreach ($courses as $course) {
            // Get enrollments for the current course
            $enrolments = Enrollment::where('course_id', $course->id);

            // Retrieve the teacher for the current course
            $teacher = Teacher::where('id', $course->teacher_id)->first();

            // Apply date filters if the provided start and end dates are not the current date
            if ($sdate !== date('Y-m-d') || $edate !== date('Y-m-d')) {
                $enrolments = $enrolments->whereDate('created_at', '>=', $sdate)
                                         ->whereDate('created_at', '<=', $edate);
            }

            // Calculate total income for the current course
            $total_income = $enrolments->sum('discounted_price');

            // Calculate the number of distinct students for the current course
            $total_students = $enrolments->distinct('student_id')->count('student_id');

            // Store the course data, ensure teacher information exists before accessing
            $courses_data[] = [
                'course_name' => $course->course_name, // Assuming 'name' is the course name column
                'total_income' => $total_income,
                'total_students' => $total_students,
                'start_date' => $course->start_date,
                'end_date' => $course->end_date,
                'teacher' => $teacher ? $teacher->full_name : 'No Teacher Assigned',
            ];
        }

        // Define the report name
        $report_name = 'All Courses Income Report';


        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(6, explode(',', $user->permit_type))) {

            return view('reports.all_courses_income', compact('sdate', 'edate', 'about', 'report_name', 'courses_data'));
        } else {

             return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));

        }

        // Pass data to the view
    }










}
