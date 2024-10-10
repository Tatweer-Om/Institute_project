<?php

namespace App\Http\Controllers;

use DateTime;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Offer;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrolController extends Controller
{
    public function index()
    {

        $courses = Course::all();
        $students = Student::all();

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(3, explode(',', $user->permit_type))) {

            return view('enroll.enroll', compact('courses', 'students'));
        } else {

            return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }
    }

    public function course_details($id)
    {
        $course = Course::where('id', $id)->first();

        $offers = Offer::all();
        $offer_name = '';
        $discounted_price = null; // Initialize to null

        foreach ($offers as $offer) {
            // Check if the current date is between the start_date and end_date of the offer
            $currentDate = now(); // Get the current date and time
            $startDate = \Carbon\Carbon::parse($offer->start_date);
            $endDate = \Carbon\Carbon::parse($offer->end_date);

            if ($currentDate->between($startDate, $endDate) && in_array($course->id, explode(',', $offer->course_id))) {
                $offer_name = $offer->offer_name; // Get the offer name

                if ($offer->offer_discount) {
                    $discount_amount = ($course->course_price * $offer->offer_discount) / 100;
                    $discounted_price = $course->course_price - $discount_amount;
                }
                break; // Exit the loop once the first valid offer is found
            }
        }

        $teacher = Teacher::where('id', $course->teacher_id)->value('full_name') ?? '';


        $add_data = get_date_only($course->created_at);
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

        if ($course) {
            return response()->json([
                'start_date' => $course->start_date,
                'course_price' => $course->course_price,
                'end_date' => $course->end_date,
                'start_time' => $course->start_date,
                'end_time' => $course->end_date,
                'duration_months' => $durationMonths,
                'duration_hours' => $durationHours,
                'discounted_price' => $discounted_price,
                'teacher' => $teacher,
                // Add other course details as needed
            ]);
        }

        return response()->json(['error' => 'Course not found'], 404);
    }

    public function student_details($id)
    {
        $student = Student::where('id', $id)->first();



        if ($student) {
            return response()->json([
                'full_name' => $student->full_name,
                'phone' => $student->student_number,
                'email' => $student->student_email,
                'civil_number' => $student->civil_number,
                'dob' => $student->dob,
                // Add other student details as needed
            ]);
        }

        return response()->json(['error' => 'Student not found'], 404);
    }

    public function current_offers()
    {
        // Get the current date
        $currentDate = Carbon::now();

        // Fetch offers where the current date is between start_date and end_date
        $offers = Offer::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Initialize an array to hold the offers with course names
        $offersWithCourses = $offers->map(function ($offer) {
            // Get course IDs from the offer and explode into an array
            $courseIds = explode(',', $offer->course_id);

            // Fetch the course names based on the IDs
            $courses = Course::whereIn('id', $courseIds)->pluck('course_name')->toArray();

            // Add the courses to the offer object
            $offer->courses = $courses;

            return $offer;
        });

        // Return the offers with course names as JSON
        return response()->json($offersWithCourses);
    }



    public function show_enroll(Request $request)
    {

        $sno = 0;
        $courseId = '';
        $view_course = '';

        $courseId = $request->input('course_id');

        if ($courseId) {
            $view_course = Enrollment::where('course_id', $courseId)->get();
        } else {
            $view_course = Enrollment::all();
        }


        if (count($view_course) > 0) {
            foreach ($view_course as $value) {



                $course = Course::where('id', $value->course_id)->first();

                $teacher1 = Teacher::where('id', $course->teacher_id)->first();
                $teacher = $teacher1->full_name ?? '';


                $offers = Offer::all();
                $offer_name = '';


                foreach ($offers as $offer) {

                    $offer_name = $offer->offer_name; // Get the offer name
                }

                $course_name = '<a href="course_profile/' . $value->id . '">' . ($course->course_name) . '</a>';

                $modal = '<a class="btn btn-outline-secondary btn-sm edit" onclick=edit("' . $value->id . '") title="Edit">
                        <i class="fas fa-pencil-alt" title="Edit"></i>
                      </a>
                      <a class="btn btn-outline-secondary btn-sm edit" onclick=del("' . $value->id . '") title="Delete">
                        <i class="fas fa-trash" title="Delete"></i>
                      </a>';

                $add_data = get_date_only($value->created_at);

                $student = Student::where('id', $value->student_id)->first();
                $full_name = $student->full_name ?? '';

                $sno++;
                $json[] = array(
                    $sno,

                    '<span class="student_name">' . trans('messages.student_name') . ': ' . $full_name  . '</span><br>' . // Span for student name
                        '<span class="student_number">' . trans('messages.phone_number') . ': ' . $student->student_number . '</span><br>' . // Span for student number
                        '<span class="civil_number">' . trans('messages.civil_number') . ': ' . $student->civil_number . '</span>', // Span for civil number

                    '<span class="course_name">' . trans('messages.course_name') . ': ' . $course_name . '</span><br>' . // Span for course name
                        ($offer_name ? '<span class="offer_name">' . trans('messages.offer_name') . ': ' . $offer_name . '</span><br>' : '') . // Span for offer name if not empty
                        '<span class="teacher">' . trans('messages.teacher') . ': ' . $teacher . '</span>', // Span for teacher

                    '<span class="discount">' . trans('messages.discount') . ': ' . $value->total_discount . ' %</span><br>' . // Span for discount
                        '<span class="course_price">' . trans('messages.course_price') . ': ' . $value->course_price . '</span><br>' . // Span for course price
                        '<span class="discounted_price">' . trans('messages.discounted_price') . ': ' . $value->discounted_price . '</span>', // Span for discounted price

                    '<span class="added_by">' . trans('messages.added_by') . ': ' . $value->added_by . '</span><br>' . // Span for who added it
                        '<span class="add_date">' . trans('messages.added_on') . ': ' . $add_data . '</span>', // Span for date added
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


    public function add_enroll(Request $request)
    {

        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        $id = $request['course_id'];

        $enroll_count = Enrollment::where('course_id', $id)->count();
        if ($enroll_count > 15) {
            return response()->json(['customer_id' => '', 'status' => 4]);
            exit;
        }

        $student_data = Student::where('id', $request['student_id'])->first();
        $student = $student_data->full_name ?? '';

        $existingstudent = Enrollment::where('student_id', $request['student_id'])
        ->where('course_id', $id)
        ->first();        if ($existingstudent) {

            return response()->json(['customer_id' => '', 'status' => 3]);
            exit;
        }
        $course_data = Course::where('id', $request['course_id'])->first();
        $course = $course_data->course_name;

        $course_price = $course_data->course_price;
        $offer = Offer::whereRaw("FIND_IN_SET(?, course_id)", [$request['course_id']])->first();
        $offer_discount = $offer ? $offer->offer_discount : 0;
        $new_discount = $request['discount'];
        $total_discount = $offer_discount + $new_discount;
        $discount_amount = ($course_price * $total_discount) / 100;
        $discounted_price = $course_price - $discount_amount;

        $enroll = new Enrollment();

        $enroll->course_name = $course;
        $enroll->course_id = $request['course_id'];
        $enroll->student_name = $student;
        $enroll->student_id = $request['student_id'];
        $enroll->course_price =  $course_price;
        $enroll->discounted_price = $discounted_price;
        $enroll->new_discount = $new_discount;
        $enroll->offer_discount = $offer_discount ?? 0;
        $enroll->total_discount = $total_discount;
        $enroll->offer_id = $offer->id ?? '';
        $enroll->offer_name = $offer->offer_name ?? '';
        $enroll->added_by = $user_name;
        $enroll->user_id = $user_id;
        $enroll->save();

        return response()->json(['course_id' => $enroll->id, 'status' => 1]);
    }


    public function edit_enroll(Request $request)
    {
        $enroll_id = $request->input('id');
        $enroll_data = Enrollment::where('id', $enroll_id)->first();

        if (!$enroll_data) {
            return response()->json(['error' => trans('messages.enroll_not_found', [], session('locale'))], 404);
        }

        $student = Student::where('id', $enroll_data->student_id)->first();

        $data = [
            'enroll_id' => $enroll_data->id,
            'course_id' => $enroll_data->course_id,
            'student_id' => $enroll_data->student_id,
            'new_discount' => $enroll_data->new_discount,
        ];

        return response()->json($data);
    }


    public function update_enroll(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        $enroll_id = $request->input('enroll_id');

        $student_data = Student::where('id', $request['student_id'])->first();
        $student = $student_data->full_name ?? '';

        $course_data = Course::where('id', $request['course_id'])->first();
        $course = $course_data->course_name;

        $course_price = $course_data->course_price;
        $offer = Offer::whereRaw("FIND_IN_SET(?, course_id)", [$request['course_id']])->first();
        $offer_discount = $offer ? $offer->offer_discount : 0;
        $new_discount = $request['discount'];
        $total_discount = $offer_discount + $new_discount;
        $discount_amount = ($course_price * $total_discount) / 100;
        $discounted_price = $course_price - $discount_amount;


        $enroll = Enrollment::where('id', $enroll_id)->first();
        $enroll->course_name = $course;
        $enroll->course_id = $request['course_id'];
        $enroll->student_name = $student;
        $enroll->student_id = $request['student_id'];
        $enroll->course_price =  $course_price;
        $enroll->discounted_price = $discounted_price;
        $enroll->new_discount = $new_discount;
        $enroll->offer_discount = $offer_discount ?? 0;
        $enroll->total_discount = $total_discount;
        $enroll->offer_id = $offer->id ?? '';
        $enroll->offer_name = $offer->offer_name ?? '';
        $enroll->added_by = $user_name;
        $enroll->save();
        // Return a successful response with the status
        return response()->json(['enroll_id' => $enroll->id, 'status' => 5]);
    }


    public function delete_enroll(Request $request)
    {
        $enroll_id = $request->input('id');
        $enroll = Enrollment::where('id', $enroll_id)->first();
        if (!$enroll) {
            return response()->json(['error' => trans('messages.enroll_not_found', [], session('locale'))], 404);
        }
        $enroll->delete();
        return response()->json([
            'success' => trans('messages.enroll_deleted_lang', [], session('locale'))
        ]);
    }


    public function add_student2(Request $request)
    {


        $user_id = Auth::id();
        $data = User::find($user_id)->first();
        $user = $data->user_name;



        $existingstudent = Student::where('student_number', $request['student_number'])->first();
        if ($existingstudent) {

            return response()->json(['student_id' => '', 'status' => 3]);
            exit;
        }

        $full_name = $request['first_name'] . ' ' . $request['second_name'] . ' ' . $request['last_name'];

        $student = new Student();
        $student->first_name = $request['first_name'];
        $student->second_name = $request['second_name'];
        $student->last_name = $request['last_name'];

        $student->full_name = $full_name;
        $student->civil_number = $request['civil_number'];
        $student->student_number = $request['student_number'];
        $student->student_email = $request['student_email'];
        $student->dob = $request['dob'];
        $student->gender = $request['gender'];
        $student->notes = $request['notes'];
        $student->added_by = $user;
        $student->user_id =  $user_id;
        $student->save();

        $students = Student::all();

        $select_option = '';
        foreach ($students as $stud) {
            $select = '';
            if (($stud->id == $student->id)) {
                $select = "selected";
            }

            $select_option .= '  <option ' . $select . ' value="' . $student->id . '">' . $stud->full_name . '</option>';
        }


        return response()->json(['student_id' => $student->id, 'status' => 1, 'select_option' => $select_option]);
    }
}
