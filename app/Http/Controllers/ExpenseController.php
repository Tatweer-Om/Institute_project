<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Expense;
use Illuminate\Support\Facades\File;
use App\Models\Expensecat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class ExpenseController extends Controller
{
    public function index(){


        $view_account= Account::all();
        $view_expense= Expensecat::all();


        if (!Auth::check()) {

            return redirect()->route('login_page')->with('error', trans('messages.please_log_in', [], session('locale')));
        }

        $user = Auth::user();

        if (in_array(5, explode(',', $user->permit_type))) {

            return view ('expense.expense', compact('view_account', 'view_expense'));
        } else {


 return redirect()->route('/')->with('error', trans('messages.you_dont_have_permissions', [], session('locale')));
        }

    }

    public function show_expense()
    {
        $sno=0;

        $view_expense= Expense::all();
        if(count($view_expense)>0)
        {
            foreach($view_expense as $value)
            {

                $expense_name='<a href="javascript:void(0);">'.$value->expense_name.'</a>';
                // cat_name

                $cat_name = getColumnValue('expensecats','id',$value->category_id,'expense_category_name');
                // payment_method
                $payment_method = getColumnValue('accounts','id',$value->payment_method,'account_name');
                $modal='<a class="btn btn-outline-secondary btn-sm edit" data-bs-toggle="modal" data-bs-target="#add_expense_modal" onclick=edit("'.$value->id.'") title="Edit">
                <i class="fas fa-pencil-alt" title="Edit"></i>
                    </a>
                    <a class="btn btn-outline-secondary btn-sm edit" onclick=del("'.$value->id.'") title="Delete">
                        <i class="fas fa-trash" title="Edit"></i>
                    </a>';
                if(!empty($value->expense_image))
                {
                    $modal.=' <a target="_blank" class="btn btn-outline-secondary btn-sm edit" href="'.url('download_expense_image').'/'.$value->expense_image.'"><i class="fas fa-download"></i>
                    </a>';
                }
                $add_data=get_date_only($value->created_at);

                $sno++;
                $json[]= array(
                            $sno,
                            $cat_name,
                            $expense_name,
                            $value->amount,
                            $payment_method,
                            $value->expense_date,
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

    public function add_expense(Request $request)
    {

        // Validate the request
        $validator = Validator::make($request->all(), [
            'expense_image' => 'nullable|file|mimes:pdf,xlsx,xls,jpeg,jpg,png,docx|max:2048',
        ]);


        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;

        $expense = new Expense();
        $expense_image = "";

        // Handle the file upload
        if ($request->hasFile('expense_image')) {
            $folderPath = public_path('custom_images/expense_images');

            // Check if the folder doesn't exist, then create it
            if (!File::isDirectory($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }

            // Create a unique filename
            $expense_image = time() . '.' . $request->file('expense_image')->extension();
            $request->file('expense_image')->move($folderPath, $expense_image);
        }

        // Save expense details
        $expense->category_id = $request['category_name'];
        $expense->expense_name = $request['expense_name'];
        $expense->payment_method = $request['payment_method'];
        $expense->amount = $request['amount'];
        $expense->expense_date = $request['expense_date'];
        $expense->notes = $request['notes'];
        $expense->expense_image = $expense_image;
        $expense->added_by = $user;
        $expense->user_id = $user_id;
        $expense->save();

        // Update the account balance
        $account_data = Account::where('id', $request['payment_method'])->first();
        $new_amount = $account_data->opening_balance - $request['amount'];
        $account_data->opening_balance = $new_amount;
        $account_data->updated_by = 'admin';
        $account_data->save();

        return response()->json(['expense_id' => $expense->id]);
    }


    public function edit_expense(Request $request){
        $expense = new Expense();
        $expense_id = $request->input('id');
        // Use the Eloquent where method to retrieve the expense by column name
        $expense_data = Expense::where('id', $expense_id)->first();

        if (!$expense_data) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.expense_not_found', [], session('locale'))], 404);
        }
        // Add more attributes as needed
        $data = [
            'expense_id' => $expense_data->id,
            'expense_name' => $expense_data->expense_name,
            'category_id' => $expense_data->category_id,
            'amount' => $expense_data->amount,
            'payment_method' => $expense_data->payment_method,
            'expense_date' => $expense_data->expense_date,
            'category_image' => $expense_data->category_image,
            'notes' => $expense_data->notes,
           // Add more attributes as needed
        ];

        return response()->json($data);
    }

    public function update_expense(Request $request){


        $user_id = Auth::id();
        $data= User::find( $user_id)->first();
        $user= $data->user_name;
        $expense_id = $request->input('expense_id');
        $expense = Expense::where('id', $expense_id)->first();
        if (!$expense) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.expense_not_found', [], session('locale'))], 404);
        }

        // plus from account
        $account_data = Account::where('id', $expense->payment_method)->first();
        $new_amount = $account_data->opening_balance + $expense->amount;
        $account_data->opening_balance = $new_amount;
        $account_data->updated_by = $user;
        $account_data->save();

        // Validate the request
        $validator = Validator::make($request->all(), [
            'expense_image' => 'nullable|file|mimes:pdf,xlsx,xls,jpeg,jpg,png,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $expense = new Expense();
        $expense_image = "";

        // Handle the file upload
        if ($request->hasFile('expense_image')) {
            $folderPath = public_path('custom_images/expense_images');

            // Check if the folder doesn't exist, then create it
            if (!File::isDirectory($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }

            // Create a unique filename
            $expense_image = time() . '.' . $request->file('expense_image')->extension();
            $request->file('expense_image')->move($folderPath, $expense_image);
        }
        $expense->category_id = $request['category_name'];
        $expense->expense_name = $request['expense_name'];
        $expense->payment_method = $request['payment_method'];
        $expense->amount = $request['amount'];
        $expense->expense_date = $request['expense_date'];
        $expense->notes = $request['notes'];
        $expense->updated_by = $user;
        $expense->save();

        // plus from account
        $account_data = Account::where('id', $request['payment_method'])->first();
        $new_amount = $account_data->opening_balance - $request['amount'];
        $account_data->opening_balance = $new_amount;
        $account_data->updated_by = $user;
        $account_data->save();


        return response()->json([
            trans('messages.success_lang', [], session('locale')) => trans('messages.expense_update_lang', [], session('locale'))
        ]);
    }

    public function delete_expense(Request $request){
        $expense_id = $request->input('id');
        $expense = Expense::where('id', $expense_id)->first();
        if (!$expense) {
            return response()->json([trans('messages.error_lang', [], session('locale')) => trans('messages.expense_not_found', [], session('locale'))], 404);
        }
        $expense->delete();
        return response()->json([
            trans('messages.success_lang', [], session('locale')) => trans('messages.expense_deleted_lang', [], session('locale'))
        ]);
    }

    // download
    public function download_expense_image($filename)
    {
        $filePath = public_path('customer_images/expense_images/' . $filename);

        // Check if file exists
        if (file_exists($filePath)) {
            return response()->download($filePath, $filename);
        }

        // File not found
        abort(404);
    }

}
