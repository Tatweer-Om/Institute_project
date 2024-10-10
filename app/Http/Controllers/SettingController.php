<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class SettingController extends Controller
{
    public function setting(){

        $setting= Setting::first();

        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(8, explode(',', $user->permit_type))) {

            return view ('setting.setting', compact('setting'));
        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }
    }

    public function add_setting(Request $request)
    {
        $user_id = Auth::id();
        $user_data = User::find($user_id);
        $user = $user_data->user_name;

        // Check if an existing record is being updated
        if ($request->setting_id) {
            // Find the existing record by id
            $setting = Setting::find($request->setting_id);
            if (!$setting) {
                return response()->json(['success' => false, 'message' => 'Setting not found'], 404);
            }
        } else {
            // Create a new record
            $setting = new Setting();
        }

        // Assign the form values to the model
        $setting->company_name = $request->company_name;
        $setting->company_email = $request->company_email;
        $setting->company_phone = $request->company_phone;
        $setting->company_cr = $request->company_cr;
        $setting->company_address = $request->company_address;
        $setting->notes = $request->notes;
        $setting->added_by = $user;
        $setting->updated_by = $user;
        $setting->user_id = $user_id;

        // Handle file upload for the logo
        if ($request->hasFile('logo')) {  // Update the name to match your form field
            $folderPath = public_path('images/logo');
            if (!File::isDirectory($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }
            $logo = time() . '.' . $request->file('logo')->extension();  // Make sure the field name matches
            $request->file('logo')->move(public_path('images/logo'), $logo);
            $setting->logo = $logo;
        }

        // Save the record (create or update)
        $setting->save();

        // Return a JSON response with the saved data
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }



    public function dress_avail(Request $request){

        if ($request->setting_id) {
            // Find the existing record by id
            $setting = Setting::find($request->setting_id);
            if (!$setting) {
                return response()->json(['success' => false, 'message' => 'Setting not found'], 404);
            }
        } else {
            // Create a new record
            $setting = new Setting();
        }

        $setting->dress_available = $request->dress_avail;

        $setting->save();
        return response()->json([
            'success' => true,
            'data' => $setting
        ]);
    }


}
