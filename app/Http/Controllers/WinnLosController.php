<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WinnLoss;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WinnLosController extends Controller
{
    public function index(){



        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(7, explode(',', $user->permit_type))) {

            return view ('winloss.winlos');

        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }

    }

    public function show_winlos()
    {
        $sno=0;

        $view_winlos= WinnLoss::all();
        if(count($view_winlos)>0)
        {
            foreach($view_winlos as $value)
            {

                $total_trade='<a href="#">' . ($value->total_trade) . '</a>';

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_winlos_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                        <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                            <i class="fas fa-trash" title="Edit"></i>
                        </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[] = array(
                    $sno,
                    $total_trade,
                    $value->win .'<br>'. $value->percentage_win,
                    $value->loss .'<br>'. $value->percentage_los,
                    $value->added_by .'<br>'. $add_data,
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

    public function add_winlos(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;



        $winlos = new WinnLoss();


        $winlos->total_trade = $request['total_trade'];
        $winlos->win = $request['win'];
        $winlos->loss = $request['loss'];
        $winlos->percentage_win = $request['percentage_win'];
        $winlos->percentage_los = $request['percentage_los'];
        $winlos->notes = $request['notes'];
        $winlos->added_by = $user_name; // Add the user name who added the winlos
        $winlos->user_id = $user_id; // Store the user ID of the admin who added the winlos
        $winlos->save();

        // Return a successful response with the winlos ID and status 1
        return response()->json(['winlos_id' => $winlos->id, 'status' => 1]);
    }




    public function edit_winlos(Request $request)
    {
        $winlos_id = $request->input('id');
        $winlos_data = WinnLoss::where('id', $winlos_id)->first();

        if (!$winlos_data) {
            return response()->json(['error' => trans('messages.winlos_not_found', [], session('locale'))], 404);
        }

        $data = [
            'winlos_id' => $winlos_data->id,
            'total_trade' => $winlos_data->total_trade,
            'win' => $winlos_data->win,
            'loss' => $winlos_data->loss,
            'percentage_win' => $winlos_data->percentage_win,
            'percentage_los' => $winlos_data->percentage_los,
            'notes' => $winlos_data->notes,
        ];

        return response()->json($data);
    }


    public function update_winlos(Request $request)
    {
        // Get the authenticated user's ID and details
        $user_id = Auth::id();
        $userData = User::find($user_id);
        $user_name = $userData->user_name;

        // Find the winlos by ID
        $winlos_id = $request->input('winlos_id');
        $winlos = WinnLoss::where('id', $winlos_id)->first();
        if (!$winlos) {
            // Return an error response if the winlos is not found
            return response()->json(['error' => trans('messages.winlos_not_found', [], session('locale'))], 404);
        }

        // Update winlos details
        $winlos->total_trade = $request['total_trade'];
        $winlos->win = $request['win'];
        $winlos->loss = $request['loss'];
        $winlos->percentage_win = $request['percentage_win'];
        $winlos->percentage_los = $request['percentage_los'];

        $winlos->notes = $request['notes'];
        $winlos->updated_by = $user_name; // Track who updated the record


        // Save the winlos record with updated data
        $winlos->save();

        // Return a successful response with the status
        return response()->json(['winlos_id' => $winlos->id, 'status' => 1]);
    }


    public function delete_winlos(Request $request){
        $winlos_id = $request->input('id');
        $winlos = WinnLoss::where('id', $winlos_id)->first();
        if (!$winlos) {
            return response()->json(['error' => trans('messages.winlos_not_found', [], session('locale'))], 404);
        }
        $winlos->delete();
        return response()->json([
            'success' => trans('messages.winlos_deleted_lang', [], session('locale'))
        ]);


    }



}
