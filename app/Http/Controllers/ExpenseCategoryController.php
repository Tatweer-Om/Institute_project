<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Expensecat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function index(){

            if (!Auth::check()) {

                return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
            }

            $user = Auth::user();

            if (in_array(5, explode(',', $user->permit_type))) {

                return view ('expense.expensecat');
            } else {


            return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
            }


    }

    public function show_expense_category()
    {
        $sno=0;

        $view_expense_category= Expensecat::all();
        if(count($view_expense_category)>0)
        {
            foreach($view_expense_category as $value)
            {

                $expense_category_name='<a href="javascript:void(0);">'.$value->expense_category_name.'</a>';

                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_expense_category_modal" onclick=edit("'.$value->id.'") title="Edit">
                <i class="fas fa-pencil-alt" title="Edit"></i>
            </a>
            <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                <i class="fas fa-trash" title="Edit"></i>
            </a>';
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $expense_category_name,
                            $value->added_by,
                            $add_data,
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

    public function add_expense_category(Request $request){


        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $expense_category = new Expensecat();
        // $expense_category->expense_category_id = genUuid() . time();
        $expense_category->expense_category_name = $request['expense_category_name'];
        $expense_category->added_by = $user;
        $expense_category->user_id = $user_id;
        $expense_category->save();
        return response()->json(['expense_category_id' => $expense_category->id]);

    }

    public function edit_expense_category(Request $request){
        $expense_category = new Expensecat();
        $expense_category_id = $request->input('id');
        // Use the Eloquent where method to retrieve the expense_category by column name
        $expense_category_data = Expensecat::where('id', $expense_category_id)->first();

        if (!$expense_category_data) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.expense_category_not_found', [], session('locale'))], 404);
        }
        // Add more attributes as needed
        $data = [
            'expense_category_id' => $expense_category_data->id,
            'expense_category_name' => $expense_category_data->expense_category_name,
           // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_expense_category(Request $request){

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $expense_category_id = $request->input('expense_category_id');
        $expense_category = Expensecat::where('id', $expense_category_id)->first();
        if (!$expense_category) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.expense_category_not_found', [], session('locale'))], 404);
        }

        $expense_category->expense_category_name = $request->input('expense_category_name');
         $expense_category->updated_by = $user;
        $expense_category->save();
        return response()->json([
            trans('messages.success_lang', [], session('locale')) => trans('messages.expense_category_update_lang', [], session('locale'))
        ]);
    }

    public function delete_expense_category(Request $request){
        $expense_category_id = $request->input('id');
        $expense_category = Expensecat::where('id', $expense_category_id)->first();
        if (!$expense_category) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.expense_category_not_found', [], session('locale'))], 404);
        }
        $expense_category->delete();
        return response()->json([
            trans('messages.success_lang', [], session('locale')) => trans('messages.expense_category_deleted_lang', [], session('locale'))
        ]);
    }
}
