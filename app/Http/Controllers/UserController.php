<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function index(){

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(6, explode(',', $user->permit_type))) {

            return view ('user.user');
        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }

    }

    public function show_user()
    {

        $sno=0;

        $view_authuser= User::all();
        if(count($view_authuser)>0)
        {
            foreach($view_authuser as $value)
            {

                $user_name='<a href="javascript:void(0);">'.$value->user_name.'</a>';

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_user_modal" onclick=edit("'.$value->id.'") title="Edit">
                <i class="fas fa-pencil-alt" title="Edit"></i>
                    </a>
                    <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                        <i class="fas fa-trash" title="Edit"></i>
                    </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $user_name,
                            $value->password,
                            $value->user_phone .'<br>'.  $value->user_email,
                            $value->user_detail,
                            $value->added_by .'<br>'.    $add_data,
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

    public function add_user(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $username= $data->user_name;


        $user = new User();

        $user->user_name = $request['user_name'];
        $user->user_email = $request['user_email'];
        $user->user_phone = $request['user_phone'];
        $user->permit_type = implode(',',$request['permit_array']);
        $user->password = Hash::make($request['password']);
        $user->user_detail = $request['notes'];
        $user->added_by = $username;
        $user->user_id = $user_id;
        $user->save();
        return response()->json(['user_id' => $user->id]);

    }

    public function edit_user(Request $request){
        $user = new User();
        $user_id = $request->input('id');
        $user_data = User::where('id', $user_id)->first();

        if (!$user_data) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.user_not_found', [], session('locale'))], 404);
        }
        $permit = explode(',',$user_data->permit_type);
        $checkboxValues = [

            ['id' => 'dress', 'value' => 2, 'name' => 'messages.checkbox_course'],
            ['id' => 'enrollments', 'value' => 3, 'name'=>'messages.checkbox_enrollments'],
            ['id' => 'reports', 'value' => 4, 'name'=>'messages.checkbox_reports'],
            ['id' => 'expense', 'value' => 5, 'name'=>'messages.checkbox_expense'],
            ['id' => 'user', 'value' => 6, 'name'=>'messages.checkbox_user'],
            ['id' => 'teacher', 'value' => 7, 'name'=>'messages.teacher'],
            ['id' => 'setting', 'value' => 8, 'name'=>'messages.setting_lang'],
            ['id' => 'sms', 'value' => 9, 'name'=>'messages.sms_lang'],
            ['id' => 'student', 'value' => 10, 'name'=>'messages.student'],
            ['id' => 'offers', 'value' => 11, 'name'=>'messages.offers_lang'],


        ];
        $checked_html='<div class="col-md-1 checkbox-container me-4">
                            <div class="form-check">
                                <label class="form-check-label" for="checkbox6">'.trans('messages.all').'</label>
                                <input class="form-check-input permit_array" type="checkbox" value="1" id="checkboxAll">
                            </div>
                        </div>';

        foreach ($checkboxValues as $key => $value) {
            $checked = "";
            if (in_array($value['value'], $permit))
            {
                $checked = "checked='true'";
            }
            $checked_html.='<div class="col-md-1 checkbox-container me-4">
                                <div class="form-check">
                                    <label class="form-check-label" for="'.$value['name'].'">'.trans($value['name'], [], session('locale')).'</label>
                                    <input  class="form-check-input permit_array" type="checkbox" value="'.$value['value'].'" '.$checked.' name="permit_array[]" id="'.$value['name'].'">
                                </div>
                            </div>';
        }


        // Add more attributes as needed
        $data = [
            'user_id' => $user_data->id,
            'user_name' => $user_data->user_name,
            'user_email' => $user_data->user_email,
            'user_phone' => $user_data->user_phone,
            'permit_type' => $user_data->permit_type,
            'password' => $user_data->password,
            'user_detail' => $user_data->user_detail,
            'checked_html' => $checked_html,
            // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_user(Request $request){


        $user_id = $request->input('user_id');

        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.authuser_not_found', [], session('locale'))], 404);
        }


        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $username= $data->user_name;


        $user->user_name = $request->input('user_name');
        $user->user_email = $request['user_email'];
        $user->user_phone = $request['user_phone'];
        $user->password = $request['password'];
        if (is_array($request->input('permit_array'))) {
            $user->permit_type = implode(',', $request->input('permit_array'));
        } else {
            $user->permit_type = $request->input('permit_array'); // or handle it as needed if it's a string
        }
        $user->user_detail = $request['notes'];
        $user->updated_by = $username;
        $user->save();
        return response()->json([trans('messages.success_lang', [], session('locale')) => trans('messages.user_update_lang', [], session('locale'))]);
    }

    public function delete_user(Request $request){
        $user_id = $request->input('id');
        $user = User::where('id', $user_id)->first();
        if (!$user) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.user_not_found', [], session('locale'))], 404);
        }
        $user->delete();
        return response()->json([
            trans('messages.success_lang', [], session('locale')) => trans('messages.user_deleted_lang', [], session('locale'))
        ]);

    }



    //login logout

    public function login_page(){
        return view ('login_page.login');
    }


    public function login(Request $request)
    {


        $baseInput = $request->input('username');
        $password = $request->input('password');



        // Find the user by email or nickname
        $user = User::where(function ($query) use ($baseInput) {
            $query->where('user_email', $baseInput)
                  ->orWhere('user_name', $baseInput);
        })
        ->first();


        // If user exists and password matches, attempt login
        if ($user && Hash::check($password, $user->password)) {
            // Manually log in the user since Auth::attempt() isn't being used
            Auth::login($user);
            return response()->json(['status' => 1, 'message' => 'Login successful']);

        } else {
            return response()->json(['status' => 2, 'message' => 'Invalid credentials']);
        }
    }

    public function logout(Request $request)
    {
        $status = 2; // Default status, indicating logout failure

        if (Auth::check()) {
            Auth::logout();
            $status = 1; // Logout success status
        }

        return response()->json(['status' => $status]);
    }

}
