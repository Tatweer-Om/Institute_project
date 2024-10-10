<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Offer;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class CourseController extends Controller
{
    public function index()
    {

        $teachers = Teacher::all();

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(2, explode(',', $user->permit_type))) {

            return view('course.course', compact('teachers'));
        } else {

             return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));

        }
    }

    public function show_course()
    {
        $sno = 0;

        $view_course = Course::all();
        if (count($view_course) > 0) {
            foreach ($view_course as $value) {
                $teacher = Teacher::where('id', $value->teacher_id)->value('full_name');

                $offers = Offer::all();
                $offer_name = '';
                $discounted_price = null; // Initialize to null

                foreach ($offers as $offer) {
                    // Check if the current date is between the start_date and end_date of the offer
                    $currentDate = now(); // Get the current date and time
                    $startDate = \Carbon\Carbon::parse($offer->start_date);
                    $endDate = \Carbon\Carbon::parse($offer->end_date);

                    if ($currentDate->between($startDate, $endDate) && in_array($value->id, explode(',', $offer->course_id))) {
                        $offer_name = $offer->offer_name; // Get the offer name

                        if ($offer->offer_discount) {
                            $discount_amount = ($value->course_price * $offer->offer_discount) / 100;
                            $discounted_price = $value->course_price - $discount_amount;
                        }
                        break; // Exit the loop once the first valid offer is found
                    }
                }

                $course_name = '<a href="course_profile/' . $value->id . '" class="course-link" data-course-id="' . $value->id . '">' . ($value->course_name) . '</a>';


                $modal = '<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_course_modal" onclick=edit("' . $value->id . '") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                          </a>
                          <a class="btn btn-outline-secondary btn-sm edit" onclick=del("' . $value->id . '") title="Delete">
                            <i class="fas fa-trash" title="Delete"></i>
                          </a>';

                if ($offer_name) {
                    $modal .= '<button class="btn btn-outline-danger btn-sm">' . $offer_name . '</button>';
                }

                $add_data = get_date_only($value->created_at);
                $startDate = new DateTime($value->start_date);
                $endDate = new DateTime($value->end_date);
                $interval = $startDate->diff($endDate);
                $durationMonths = $interval->m + ($interval->y * 12); // Total months

                // Calculate the duration in hours
                $startTime = new DateTime($value->start_time);
                $endTime = new DateTime($value->end_time);
                $intervalTime = $startTime->diff($endTime);
                $durationHours = $intervalTime->h; // Total hours
                $durationMinutes = $intervalTime->i; // Total minutes

                $sno++;
                $json[] = array(
                    $sno,
                    '<span> Course Name: ' . $course_name . '</span><br>' .
                    '<span> Teacher Name: ' . $teacher . '</span><br>' .
                    '<span> Original Price: ' . $value->course_price . '</span><br>' .
                    ($discounted_price !== null ? '<span> Discounted Price: ' . $discounted_price . '</span>' : ''),
                    '<span> Course Duration: ' . $durationMonths . ' months</span><br>' .
                    '<span> Start Date: ' . $value->start_date . '</span><br>' .
                    '<span> End Date: ' . $value->end_date . '</span><br>' .
                    '<span> Start Time: ' . date("g:i A", strtotime($value->start_time)) . '</span><br>' .
                    '<span> End Time: ' . date("g:i A", strtotime($value->end_time)) . '</span><br>' .
                    '<span> Duration: ' . $durationHours . ' hours ' . $durationMinutes . ' minutes</span>',
                    '<span style="text-align: justify; white-space: pre-line;">' . $value->notes . '</span>',
                    '<span>أضيف بواسطة: ' . $value->added_by . '</span><br>' .
                    '<span>تاريخ الإضافة: ' . $add_data . '</span>',

                    $modal
                );
            }



            $response = array();
            $response['success'] = true;
            $response['aaData'] = $json;
            echo json_encode($response);
        } else {
            $response = array();
            $response['sEcho'] = 0;
            $response['iTotalRecords'] = 0;
            $response['iTotalDisplayRecords'] = 0;
            $response['aaData'] = [];
            echo json_encode($response);
        }
    }

    public function add_course(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        $course = new Course();

        $course->course_name = $request['course_name'];
        $course->teacher_id = $request['teacher_id'];
        $course->start_date = $request['start_date'];
        $course->end_date = $request['end_date'];
        $course->start_time = $request['start_time'];
        $course->end_time = $request['end_time'];
        $course->course_price = $request['course_price'];
        $course->notes = $request['notes'];
        $course->added_by = $user_name; // Add the user name who added the course
        $course->user_id = $user_id; // Store the user ID of the admin who added the course

        // Save the course record in the database
        $course->save();

        // Return a successful response with the course ID and status 1
        return response()->json(['course_id' => $course->id, 'status' => 1]);
    }




    public function edit_course(Request $request)
    {
        $course_id = $request->input('id');
        $course_data = Course::where('id', $course_id)->first();

        if (!$course_data) {
            return response()->json(['error' => trans('messages.course_not_found', [], session('locale'))], 404);
        }

        $data = [
            'course_id' => $course_data->id,
            'course_name' => $course_data->course_name,
            'teacher_id' => $course_data->teacher_id,
            'start_date' => $course_data->start_date,
            'end_date' => $course_data->end_date,
            'start_time' => $course_data->start_time,
            'end_time' => $course_data->end_time,
            'course_price' => $course_data->course_price,
            'notes' => $course_data->notes,
        ];

        return response()->json($data);
    }


    public function update_course(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        // Find the course by ID
        $course_id = $request->input('course_id');
        $course = Course::where('id', $course_id)->first();
        if (!$course) {
            // Return an error response if the course is not found
            return response()->json(['error' => trans('messages.course_not_found', [], session('locale'))], 404);
        }

        // Update course details

        $course->course_name = $request['course_name'];
        $course->teacher_id = $request['teacher_id'];
        $course->start_date = $request['start_date'];
        $course->end_date = $request['end_date'];
        $course->start_time = $request['start_time'];
        $course->end_time = $request['end_time'];
        $course->course_price = $request['course_price'];
        $course->notes = $request['notes'];
        $course->updated_by = $user_name; // Track who updated the record
        $course->save();

        // Return a successful response with the status
        return response()->json(['course_id' => $course->id, 'status' => 1]);
    }


    public function delete_course(Request $request)
    {
        $course_id = $request->input('id');
        $course = Course::where('id', $course_id)->first();
        if (!$course) {
            return response()->json(['error' => trans('messages.course_not_found', [], session('locale'))], 404);
        }
        $course->delete();
        return response()->json([
            'success' => trans('messages.course_deleted_lang', [], session('locale'))
        ]);
    }

    public function delete_new(Request $request)
    {
        $course_id = $request->input('id');
        $course = Enrollment::where('id', $course_id)->first();

        if (!$course) {
            return response()->json(['error' => trans('messages.course_not_found', [], session('locale'))], 404);
        }
        $course->delete();
        return response()->json([
            'success' => trans('messages.course_deleted_lang', [], session('locale'))
        ]);
    }

    public function course_profile($id)
    {

        $course = Course::where('id', $id)->first();

        $start_time=date("g:i A", strtotime($course->start_time));
        $end_time=date("g:i A", strtotime($course->end_time));


        $startDate = new DateTime($course->start_date);
        $endDate = new DateTime($course->end_date);
        $interval = $startDate->diff($endDate);
        $durationMonths = $interval->m + ($interval->y * 12); // Total months

        // Calculate the duration in hours
        $startTime = new DateTime($course->start_time);
        $endTime = new DateTime($course->end_time);
        $intervalTime = $startTime->diff($endTime);
        $durationHours = $intervalTime->h; // Total hours
        $durationMinutes = $intervalTime->i; // Total minutes

        $teacher= Teacher::where('id', $course->teacher_id)->value('full_name');

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(2, explode(',', $user->permit_type))) {

            return view('course.course_profile', compact('course', 'teacher', 'start_time', 'end_time', 'durationMonths', 'durationHours'));
        } else {

             return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));

        }

    }




public function show_enroll_student(Request $request)
{


    $sno = 0;


    $courseId = $request->input('course_id'); // Retrieve course_id from the request


    $view_course = Enrollment::where('course_id', $courseId)->get();

    if (count($view_course) > 0) {
        foreach ($view_course as $value) {



                $course= Course::where('id', $value->course_id)->first();

                $teacher = Teacher::where('id', $course->teacher_id)->value('full_name');

                $offers = Offer::all();
                $offer_name = '';


                foreach ($offers as $offer) {

                        $offer_name = $offer->offer_name; // Get the offer name
                }

            $course_name = '<a href="course_profile/' . $value->id . '">' . ($course->course_name) . '</a>';

            $modal = '
                      <a class="btn btn-outline-secondary btn-sm edit" onclick=del_new("' . $value->id . '") title="Delete">
                        <i class="fas fa-trash" title="Delete"></i>
                      </a>';

            $add_data=get_date_only($value->created_at);

            $student= Student::where('id', $value->student_id)->first();
            $sno++;
            $json[] = array(
                $sno,

                '<span class="student_name">Student Name: ' . $student->full_name . '</span><br>' . // Span for student name
                '<span class="student_number">Phone Number: ' . $student->student_number . '</span><br>' . // Span for student number
                '<span class="civil_number">Civil Number: ' . $student->civil_number . '</span>', // Span for civil number
                '<span class="course_name">Course Name: ' . $course_name . '</span><br>' . // Span for course name
                ($offer_name ? '<span class="offer_name">Offer Name: ' . $offer_name . '</span><br>' : '') . // Span for offer name if not empty
                '<span class="teacher">Teacher: ' . $teacher . '</span>', // Span for teacher

                '<span class="discount">Discount: ' . $value->total_discount . ' %</span><br>' . // Span for discount
                '<span class="course_price">Course Price: ' . $value->course_price . '</span><br>' . // Span for course price
                '<span class="discounted_price">Discounted Price: ' . $value->discounted_price . '</span>', // Span for discounted price

                '<span class="added_by">Added By: ' . $value->added_by . '</span><br>' . // Span for who added it
                '<span class="add_date">Added On: ' . $add_data . '</span>', // Span for date added
                $modal
            );

        }



        $response = array();
        $response['success'] = true;
        $response['aaData'] = $json;
        echo json_encode($response);
    } else {
        $response = array();
        $response['sEcho'] = 0;
        $response['iTotalRecords'] = 0;
        $response['iTotalDisplayRecords'] = 0;
        $response['aaData'] = [];
        echo json_encode($response);
    }
}

    public function course_profile_data(Request $request)
    {

        $course_id = $request->input('course_id');

    $enrol = Enrollment::where('course_id', $course_id)->get();

    $total_enrollments = $enrol->count();
    $total_income = $enrol->sum('discounted_price');

   // Retrieve only the first 5 offers where the course_id is in the comma-separated list of course_ids
$offers = Offer::whereRaw("FIND_IN_SET(?, course_id)", [$course_id])->limit(5)->get();


    $offers = $offers->map(function ($offer) {
        $courseIds = explode(',', $offer->course_id);

        $courses = Course::whereIn('id', $courseIds)->pluck('course_name')->toArray();

        $offer->courses = $courses ?? [];

        return $offer;
    });

    // Return the response as JSON
    return response()->json([
        'total_enrol' => $total_enrollments,
        'total_income' => $total_income,
        'offers' => $offers,
    ]);
}
}
