<?php

namespace App\Http\Controllers;

use DateTime;
use App\Models\User;
use App\Models\Offer;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class TeacherController extends Controller
{
    public function index(){



        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(7, explode(',', $user->permit_type))) {

            return view ('teacher.teacher');

        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }

    }

    public function show_teacher()
    {
        $sno=0;

        $view_teacher= Teacher::all();
        if(count($view_teacher)>0)
        {
            foreach($view_teacher as $value)
            {

                $teacher_name='<a href="teacher_profile/' . $value->id . '">' . ($value->full_name) . '</a>';

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_teacher_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[] = array(
                    $sno,
                    '<span>اسم العميل: ' . $teacher_name . '</span><br>' .
                    '<span> Civil Number: ' . $value->civil_number . '</span>',

                    $value->teacher_number . '<br>' . $value->teacher_email,
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

    public function add_teacher(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        // Check if a teacher with the same teacher_number already exists
        $existingteacher = Teacher::where('teacher_number', $request['teacher_number'])->first();
        if ($existingteacher) {
            // Return response with status 3 if the teacher already exists
            return response()->json(['teacher_id' => '', 'status' => 3]);
        }

        // Initialize variables
        $signatureFilePath = null;
        $signatureDirectory = public_path('images/signatures'); // Define the directory

        // Create the directory if it doesn't exist
        if (!File::exists($signatureDirectory)) {
            File::makeDirectory($signatureDirectory, 0755, true); // Recursive creation
        }

        // Process the signature if provided
        $signatureData = $request->input('signature');
        if ($signatureData) {
            // Clean and decode the base64 signature data
            $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureData = str_replace(' ', '+', $signatureData);
            $signatureImage = base64_decode($signatureData);

            // Save the signature image to the signatures directory
            $fileName = 'signature_' . time() . '.png'; // Unique filename based on current time
            $filePath = $signatureDirectory . '/' . $fileName;
            file_put_contents($filePath, $signatureImage);

            // Store the relative file path to be saved in the database
            $signatureFilePath = 'images/signatures/' . $fileName;
        }

        // Create new teacher record
        $teacher = new Teacher();
        $full_name = $request['first_name'] . ' ' . $request['second_name'] . ' ' . $request['last_name'];

        $teacher->first_name = $request['first_name'];
        $teacher->second_name = $request['second_name'];
        $teacher->last_name = $request['last_name'];
        $teacher->full_name = $full_name;
        $teacher->civil_number = $request['civil_number'];
        $teacher->teacher_number = $request['teacher_number'];
        $teacher->teacher_email = $request['teacher_email'];
        $teacher->notes = $request['notes'];
        $teacher->added_by = $user_name; // Add the user name who added the teacher
        $teacher->user_id = $user_id; // Store the user ID of the admin who added the teacher
        $teacher->signature = $signatureFilePath; // Save the file path of the signature (not base64)

        // Save the teacher record in the database
        $teacher->save();

        // Return a successful response with the teacher ID and status 1
        return response()->json(['teacher_id' => $teacher->id, 'status' => 1]);
    }




    public function edit_teacher(Request $request)
    {
        $teacher_id = $request->input('id');
        $teacher_data = Teacher::where('id', $teacher_id)->first();

        if (!$teacher_data) {
            return response()->json(['error' => trans('messages.teacher_not_found', [], session('locale'))], 404);
        }

        $data = [
            'teacher_id' => $teacher_data->id,
            'first_name' => $teacher_data->first_name,
            'second_name' => $teacher_data->second_name,
            'last_name' => $teacher_data->last_name,
            'teacher_email' => $teacher_data->teacher_email,
            'civil_number' => $teacher_data->civil_number,
            'teacher_number' => $teacher_data->teacher_number,
            'notes' => $teacher_data->notes,
            'signature' => $teacher_data->signature // Add this line to include the signature
        ];

        return response()->json($data);
    }


    public function update_teacher(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        // Find the teacher by ID
        $teacher_id = $request->input('teacher_id');
        $teacher = Teacher::where('id', $teacher_id)->first();
        if (!$teacher) {
            // Return an error response if the teacher is not found
            return response()->json(['error' => trans('messages.teacher_not_found', [], session('locale'))], 404);
        }

        // Update teacher details
        $teacher->first_name = $request['first_name'];
        $teacher->second_name = $request['second_name'];
        $teacher->last_name = $request['last_name'];
        $teacher->civil_number = $request['civil_number'];
        $teacher->teacher_number = $request['teacher_number'];
        $teacher->teacher_email = $request['teacher_email'];

        $teacher->notes = $request['notes'];
        $teacher->updated_by = $user_name; // Track who updated the record

        // Handle signature update
        $signatureData = $request->input('signature');
        if ($signatureData) {
            // Directory to save signature images
            $signatureDirectory = public_path('images/signatures');

            // Create directory if it doesn't exist
            if (!File::exists($signatureDirectory)) {
                File::makeDirectory($signatureDirectory, 0755, true); // Recursive creation
            }

            // Clean and decode the base64 signature data
            $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
            $signatureData = str_replace(' ', '+', $signatureData);
            $signatureImage = base64_decode($signatureData);

            // Save the new signature image
            $fileName = 'signature_' . time() . '.png'; // Unique filename based on current time
            $filePath = $signatureDirectory . '/' . $fileName;
            file_put_contents($filePath, $signatureImage);

            // Update the signature field in the database with the new file path
            $teacher->signature = 'images/signatures/' . $fileName;
        }

        // Save the teacher record with updated data
        $teacher->save();

        // Return a successful response with the status
        return response()->json(['teacher_id' => $teacher->id, 'status' => 1]);
    }


    public function delete_teacher(Request $request){
        $teacher_id = $request->input('id');
        $teacher = Teacher::where('id', $teacher_id)->first();
        if (!$teacher) {
            return response()->json(['error' => trans('messages.teacher_not_found', [], session('locale'))], 404);
        }
        $teacher->delete();
        return response()->json([
            'success' => trans('messages.teacher_deleted_lang', [], session('locale'))
        ]);


    }

    public function teacher_profile($id){

        $teacher= Teacher::where('id', $id)->first();
        $courses= Course::where('teacher_id', $teacher->id)->get();
        $total_courses= $courses->count();
        $course_ids = $courses->pluck('id')->toArray();

        // Get the total number of unique students enrolled in these courses
        $total_students = Enrollment::whereIn('course_id', $course_ids)
            ->distinct('student_id')
            ->count('student_id');


             if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(7, explode(',', $user->permit_type))) {

            return view ('teacher.teacher_profile', compact('teacher', 'total_courses', 'total_students'));

        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }



    }


    public function show_teacher_courses(Request $request)
    {

        $id= $request->input('teacher_id');

        $sno = 0;

        $view_course = Course::where('teacher_id', $id)->get();
        if (count($view_course) > 0) {
            foreach ($view_course as $value) {
                $teacher = Teacher::where('id', $id)->value('full_name');

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


                $modal = '
                          <a class="btn btn-outline-secondary btn-sm edit" onclick=del_t("' . $value->id . '") title="Delete">
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




    public function delete_teacher_course(Request $request){
        $course_id = $request->input('id');
        if (! $course_id) {
            return response()->json(['error' => trans('messages.teacher_not_found', [], session('locale'))], 404);
        }
        $course = Course::where('id', $course_id)->first();

        $course->teacher_id=NULL;
        $course->save();
        return response()->json([
            'success' => trans('messages.teacher_deleted_lang', [], session('locale'))
        ]);


    }


}
