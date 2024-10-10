<?php

namespace App\Http\Controllers;

use App\Models\Sms;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SmsController extends Controller
{
    public function index(){



        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(9, explode(',', $user->permit_type))) {

            return view('sms.sms');
        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }
    }

    public function get_sms_status(Request $request)
    {
        $sms_status = $request['sms_status'];
        $data = Sms::where('sms_status', $sms_status)->first();
        if (!empty($data)) {
            return response()->json(['status' => 1,'sms' => base64_decode($data->sms)]);
        } else {
            return response()->json(['status' => 2]);

        }
    }


    public function add_status_sms(Request $request)
    {
        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

            $add_date = date('Y-m-d');
            $sms_status = $request->input('status');
            $sms_text = $request->input('sms');
            $check_status = Sms::where('sms_status', $sms_status)->first();

            if (!empty($check_status)) {
                // product qty history

                $sms_data = Sms::where('sms_status', $sms_status)->first();
                $sms_data->sms =base64_encode($sms_text);
                $sms_data->sms_status =$sms_status;
                $sms_data->updated_by=$user;
                $sms_data->user_id = $user_id;
                $sms_data->save();
                Session::flash('success', trans('messages.message_updated_successfuly_lang', [], session('locale')));


            } else{
                $sms_data = new Sms();
                $sms_data->sms =base64_encode($sms_text);
                $sms_data->sms_status =$sms_status;
                $sms_data->added_by=$user;
                $sms_data->user_id =$user_id;
                $sms_data->save();
                Session::flash('success', trans('messages.message_added_successfuly_lang', [], session('locale')));



            }

            return redirect()->route('sms');

    }
}
