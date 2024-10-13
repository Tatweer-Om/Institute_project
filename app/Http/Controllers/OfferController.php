<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Offer;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OfferController extends Controller
{
    public function index(){


        $courses = Course::all();

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(11, explode(',', $user->permit_type))) {

            return view ('offer.offer', compact('courses'));

        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }

    }

    public function show_offer()
    {
        $sno=0;

        $view_offer= Offer::all();
        if(count($view_offer)>0)
        {
            foreach($view_offer as $value)
            {


                $courses = explode(',', $value->course_id);

                // Initialize an empty string for the course names
                $course_names = '';

                // Fetch the course names and concatenate them
                foreach ($courses as $course_id) {
                    $course_name = Course::where('id', $course_id)->value('course_name');
                    if ($course_name) {
                        $course_names .= $course_name . ', ';
                    }
                }

                $offer_name='<a href="offer_profile/' . $value->id . '">' . ($value->offer_name) . '</a>';

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_offer_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);



                $sno++;
                $json[] = array(
                    $sno,
                    '<span>' . trans('messages.offer_name', [], session('locale')) . ': ' . $offer_name . '</span><br>' .
                    '<span>' . trans('messages.course', [], session('locale')) . ': ' . $course_names . '</span><br>',
                    '<span>' . trans('messages.start_date', [], session('locale')) . ': ' . $value->start_date . '</span><br>' .
                    '<span>' . trans('messages.end_date', [], session('locale')) . ': ' . $value->end_date . '</span><br>' .
                    '<span>' . trans('messages.offer_discount', [], session('locale')) . ': ' . ($value->offer_discount ? $value->offer_discount . '%' : trans('messages.na', [], session('locale'))) . '</span>',
                    '<span style="text-align: justify; white-space: pre-line;">' . $value->notes . '</span>',
                    '<span>' . trans('messages.added_by', [], session('locale')) . ': ' . $value->added_by . '</span><br>' .
                    '<span>' . trans('messages.added_date', [], session('locale')) . ': ' . $add_data . '</span>',
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

    public function add_offer(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        $offer = new offer();

        $offer->offer_name = $request['offer_name'];
        $courses = $request->input('course_id');

        if (!empty($courses)) {
            $offer->course_id = implode(',', $courses);

        } else {
            $offer->course_id = '';
        }


        $offer->start_date = $request['start_date'];
        $offer->end_date = $request['end_date'];
        $offer->offer_discount = $request['offer_discount'];
        $offer->notes = $request['notes'];
        $offer->added_by = $user_name;
        $offer->user_id = $user_id;

        $offer->save();

        return response()->json(['offer_id' => $offer->id, 'status' => 1]);
    }




    public function edit_offer(Request $request)
    {
        $offer_id = $request->input('id');
        $offer_data = Offer::where('id', $offer_id)->first();

        if (!$offer_data) {
            return response()->json(['error' => trans('messages.offer_not_found', [], session('locale'))], 404);
        }

        $course_ids = !empty($offer_data->course_id) ? explode(',', $offer_data->course_id) : [];

        $select_option='';
        $courses = Course::all();

        foreach( $courses as $course){
            $select='';
            if (in_array($course->id, $course_ids)){
            $select="selected";

            }

            $select_option.='  <option '.$select.' value="'.$course->id.'">'.$course->course_name.'</option>';

        }

        $data = [
            'offer_id' => $offer_data->id,
            'offer_name' => $offer_data->offer_name,
            'course_id' => $select_option,
            'start_date' => $offer_data->start_date,
            'end_date' => $offer_data->end_date,
            'offer_discount' => $offer_data->offer_discount,
            'notes' => $offer_data->notes,
        ];

        return response()->json($data);
    }


    public function update_offer(Request $request)
    {
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        // Find the offer by ID
        $offer_id = $request->input('offer_id');
        $offer = Offer::where('id', $offer_id)->first();
        if (!$offer) {
            return response()->json(['error' => trans('messages.offer_not_found', [], session('locale'))], 404);
        }

        $offer->offer_name = $request['offer_name'];
        $offer->start_date = $request['start_date'];
        $offer->course_id =  implode(',', $request->input('course_id'));
        $offer->end_date = $request['end_date'];
        $offer->offer_discount = $request['offer_discount'];
        $offer->notes = $request['notes'];
        $offer->updated_by = $user_name;
        $offer->save();

        return response()->json(['offer_id' => $offer->id, 'status' => 1]);
    }


    public function delete_offer(Request $request){
        $offer_id = $request->input('id');
        $offer = Offer::where('id', $offer_id)->first();
        if (!$offer) {
            return response()->json(['error' => trans('messages.offer_not_found', [], session('locale'))], 404);
        }
        $offer->delete();
        return response()->json([
            'success' => trans('messages.offer_deleted_lang', [], session('locale'))
        ]);


    }

    public function offer_profile($id){

        $offer= Offer::where('id', $id)->first();

        return view ('offer.offer_profile', compact('offer'));


    }

    // public function offer_profile_data(Request $request)
    // {

    //     $offer = Offer::find($request->offer_id);
    //     if (!$offer) {
    //         return response()->json(['message' => 'offer not found'], 404);
    //     }
    //     $bookings = Booking::with([
    //         'bills',
    //         'payments',
    //         'dress.brand',
    //         'dress.category',
    //         'dress.color',
    //         'dress.size'
    //     ])->where('offer_id', $offer->id)->get();

    //     $upcoming_bookings = Booking::with([
    //         'bills',
    //         'payments',
    //         'dress.brand',
    //         'dress.category',
    //         'dress.color',
    //         'dress.size'
    //     ])
    //     ->where('offer_id', $offer->id)
    //     ->where('rent_date', '>', Carbon::now())
    //     ->get();

    //     $upcoming_bookings_count= $upcoming_bookings->count();
    //     $total_bookings= $bookings->count();
    //     $total_amount = 0;
    //     $total_panelty=0;
    //     foreach ($bookings as $booking) {

    //         foreach ($booking->payments as $payment) {
    //             $total_amount += $payment->paid_amount;
    //         }
    //     }

    //     foreach ($bookings as $booking) {
    //         foreach ($booking->bills as $payment) {
    //             echo $payment->total_panelty;
    //             $total_panelty += $payment->total_penalty;
    //         }
    //     }


    //     $currentBookings = Booking::with([
    //         'bills',
    //         'payments',
    //         'dress.brand',
    //         'dress.category',
    //         'dress.color',
    //         'dress.size'
    //     ])
    //     ->where('offer_id', $offer->id)
    //     ->whereDate('rent_date', '<=', Carbon::now())
    //     ->whereDate('return_date', '>=', Carbon::now())
    //     ->get();

    //     return response()->json([
    //         'bookings' => $bookings,
    //         'up_bookings'=> $upcoming_bookings,
    //         'total_amount'=>$total_amount,
    //         'total_bookings'=>$total_bookings,
    //         'upcoming_bookings_count'=>$upcoming_bookings_count,
    //         'total_panelty'=>$total_panelty,
    //         'current_bookings'=>$currentBookings


    //     ]);
    // }


public function get_course($id)
{
    $course = Course::find($id);
    return response()->json(['price' => $course ? $course->course_price : null]);
}


}
