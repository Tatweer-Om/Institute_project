<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Offer;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class StudentController extends Controller
{
    public function index(){



        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();


        if (in_array(10, explode(',', $user->permit_type))) {

            return view ('student.student');

        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }

    }

    public function show_student()
    {
        $sno=0;

        $view_student= Student::all();
        if(count($view_student)>0)
        {
            foreach($view_student as $value)
            {

                $student_name='<a href="student_profile/' . $value->id . '">' . ($value->full_name) . '</a>';

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_student_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);



                $sno++;
                $json[] = array(
                    $sno,
                    '<span>اسم العميل: ' . $student_name . '</span><br>' .
                    '<span>الجنس: ' . ($value->gender == 1 ? 'ذكر' : 'أنثى') . '</span><br>' .
                    '<span>تاريخ الميلاد: ' . $value->dob . '</span><br>' .
                    '<span> Civil Number: ' . $value->civil_number . '</span>',

                    $value->student_number . '<br>' . $value->student_email,

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
        }
        else
        {
            $response = array();
            $response['sEcho'] = 0;
            $response['iTotalRecords'] = 0;
            $response['iTotalDisplayRecords'] = 0;
            $response['aaData'] = [];
            echo json_encode($response);
        }
    }

    public function add_student(Request $request){


        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

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

        return response()->json(['student_id' => $student->id , 'status' => 1]);

    }

    public function edit_student(Request $request){


        $student = new Student();
        $student_id = $request->input('id');
        $student_data = Student::where('id', $student_id)->first();

        if (!$student_data) {
            return response()->json(['error' => trans('messages.student_not_found', [], session('locale'))], 404);
        }

        $data = [
            'student_id' => $student_data->id,
            'first_name' =>   $student_data->first_name,
            'second_name' =>   $student_data->second_name,
            'last_name' =>   $student_data->last_name,
            'student_email' =>  $student_data->student_email,
            'civil_number' => $student_data->civil_number,
            'student_number' => $student_data->student_number,
            'dob' => $student_data->dob,
            'gender' => $student_data->gender,
            'notes' => $student_data->notes,


        ];

        return response()->json($data);
    }

    public function update_student(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $student_id = $request->input('student_id');
        $student = Student::where('id', $student_id)->first();
        if (!$student) {
            return response()->json(['error' => trans('messages.student_not_found', [], session('locale'))], 404);
        }


        $student->first_name = $request['first_name'];
        $student->second_name = $request['second_name'];
        $student->last_name = $request['last_name'];
        $student->civil_number = $request['civil_number'];
        $student->student_number = $request['student_number'];
        $student->student_email = $request['student_email'];
        $student->dob = $request['dob'];
        $student->gender = $request['gender'];
        $student->notes = $request['notes'];
        $student->updated_by = $user;
        $student->save();
        return response()->json(['student_id' => '', 'status' => 1]);
    }

    public function delete_student(Request $request){
        $student_id = $request->input('id');
        $student = Student::where('id', $student_id)->first();
        if (!$student) {
            return response()->json(['error' => trans('messages.student_not_found', [], session('locale'))], 404);
        }
        $student->delete();
        return response()->json([
            'success' => trans('messages.student_deleted_lang', [], session('locale'))
        ]);


    }

    public function student_profile($id){

        $student= Student::where('id', $id)->first();


        $enrol= Enrollment::where('student_id', $id)->get();

    $total_courses = $enrol->count();


    $total_price_paid = $enrol->sum('discounted_price');


    if (!Auth::check()) {

        return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
    }

    $user = Auth::user();


    if (in_array(10, explode(',', $user->permit_type))) {

        return view ('student.student_profile', compact('student', 'total_courses', 'total_price_paid'));

    } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
    }






    }


    public function show_student_courses(Request $request)
    {

        $id= $request->input('student_id');



        $sno = 0;

        $view_course = Enrollment::where('student_id', $id)->get();
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

}
