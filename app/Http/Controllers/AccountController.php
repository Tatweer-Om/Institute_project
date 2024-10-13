<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index(){

        if (!Auth::check()) {
            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(5, explode(',', $user->permit_type))) {
            return view('accounts.account');
        } else {
             return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));

        }



        // } else {

        //     return redirect()->route('home');
        // }




    }

    public function show_account()
    {
        $sno=0;

        $view_account= Account::all();
        if(count($view_account)>0)
        {
            foreach($view_account as $value)
            {

                $account_name='<a href="javascript:void(0);">'.$value->account_name.'</a>';
                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_account_modal" onclick=edit("'.$value->id.'") title="Edit">
                            <i class="fas fa-pencil-alt" title="Edit"></i>
                        </a>
                    <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                        <i class="fas fa-trash" title="Edit"></i>
                    </a>';
                $add_data=get_date_only($value->created_at);
                if($value->account_type) {
                    $account_type = trans('accounts.normal_account', [], session('locale'));
                } else {
                    $account_type = trans('accounts.saving_account', [], session('locale'));
                }

                $sno++;
                $json[] = array(
                    // $sno,
                    '<strong>' . trans('messages.account_name_lang', [], session('locale')) . ':</strong> ' . $account_name . '<br>' .
                    '<strong>' . trans('messages.account_branch_lang', [], session('locale')) . ':</strong> ' . $value->account_branch . '<br>' .
                    '<strong>' . trans('messages.account_no_lang', [], session('locale')) . ':</strong> ' . $value->account_no . '<br>' .
                    '<strong>' . trans('messages.account_type_lang', [], session('locale')) . ':</strong> ' .  $account_type,

                    '<strong>' . trans('messages.opening_balance_lang', [], session('locale')) . ':</strong> ' . $value->opening_balance . '<br>' .
                    '<strong>' . trans('messages.commission_lang', [], session('locale')) . ':</strong> ' . $value->commission,

                    // $account_type,
                    '<div style="white-space: pre-line; text-align:justify; width: 120px; overflow-wrap: break-word;">' . $value->notes . '</div>',
                    '<strong>' . trans('messages.added_by_lang', [], session('locale')) . ':</strong> ' .  $value->added_by .'<br>'. '<strong>' . trans('messages.created_at_lang', [], session('locale')) . ':</strong> ' .  $add_data,
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

    public function add_account(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;



        $account = new account();

        // $account->account_id = genUuid() . time();
        $account->account_name = $request['account_name'];
        $account->account_branch = $request['account_branch'];
        $account->account_no = $request['account_no'];
        $account->opening_balance = $request['opening_balance'];
        $account->commission = $request['commission'];
        $account->account_type = $request['account_type'];
        $account->account_status = $request['account_status'];
        $account->notes = $request['notes'];
        $account->added_by = $user;
        $account->user_id =  $user_id;
        $account->save();
        return response()->json(['account_id' => $account->id]);

    }

    public function edit_account(Request $request){
        $account = new account();
        $account_id = $request->input('id');
        // Use the Eloquent where method to retrieve the account by column name
        $account_data = Account::where('id', $account_id)->first();

        if (!$account_data) {
            return response()->json(['error' => trans('messages.account_not_found_lang', [], session('locale'))], 404);
        }
        // Add more attributes as needed
        $data = [
            'account_id' => $account_data->id,
            'account_name' => $account_data->account_name,
            'account_branch' => $account_data->account_branch,
            'account_no' => $account_data->account_no,
            'opening_balance' => $account_data->opening_balance,
            'commission' => $account_data->commission,
            'account_type' => $account_data->account_type,
            'account_status' => $account_data->account_status,
            'notes' => $account_data->notes,
            // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_account(Request $request){
        $account_id = $request->input('account_id');
        $account = Account::where('id', $account_id)->first();
        if (!$account) {
            return response()->json(['error' => trans('messages.account_not_found_lang', [], session('locale'))], 404);
        }

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $account->account_name = $request['account_name'];
        $account->account_branch = $request['account_branch'];
        $account->account_no = $request['account_no'];
        $account->opening_balance = $request['opening_balance'];
        $account->commission = $request['commission'];
        $account->account_type = $request['account_type'];
        $account->account_status = $request['account_status'];
        $account->notes = $request['notes'];
        $account->updated_by = $user;
        $account->save();
        return response()->json(['success' => trans('messages.data_update_success_lang', [], session('locale'))]);
    }

    public function delete_account(Request $request){
        $account_id = $request->input('id');
        $account = Account::where('id', $account_id)->first();
        if (!$account) {
            return response()->json(['error' => trans('messages.account_not_found_lang', [], session('locale'))], 404);
        }
        $account->delete();
        return response()->json(['success' => trans('messages.delete_success_lang', [], session('locale'))]);
    }
}
