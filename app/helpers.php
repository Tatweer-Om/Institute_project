<?php
use App\Models\Sms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

function get_date_only($timestamp)
{
    // Create a DateTime object from the timestamp
    $dateTime = new DateTime($timestamp);

    // Format the date as YYYY-MM-DD
    $dateOnly = $dateTime->format('Y-m-d');

    return $dateOnly;
}
function getColumnValue($table, $columnToSearch, $valueToSearch, $columnToRetrieve)
{
    $result = DB::table($table)
                ->where($columnToSearch, $valueToSearch)
                ->first();

    if ($result) {
        return $result->{$columnToRetrieve};
    }

    return 'n/a'; // or any default value you prefer
}
function get_date_time($timestamp)
{
    // Create a DateTime object from the timestamp
    $dateTime = new DateTime($timestamp);

    // Format the date as YYYY-MM-DD
    $formattedDateTime = $dateTime->format('Y-m-d h:i A');

    return $formattedDateTime;
}


function image_preview($path = null)
{
    $images = '';

    // Check if path exists and get the list of files from the directory
    $fullPath = $path; // Convert to public path

    if (is_dir($fullPath)) {
        $files = File::files($fullPath); // Fetch files using File facade

        foreach ($files as $file) {
            // Get the file extension
            $ext = pathinfo($file, PATHINFO_EXTENSION);

            // Generate the URL for the file
            $url = asset('custom_images/temp_data/' . basename($file));

            // Generate the HTML for each image
            $images .= '
                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 col-xs-12">
                    <img class="img-thumbnail mb-1" src="' . $url . '" style="max-height:60px !important;min-height:60px !important;max-width:60px;min-width:60px;">
                    <p class="text-center">
                        <a href="#" class="card-link rmv-attachment" data-file="' . $url . '">
                            <i class="fa fa-times"></i>
                        </a>
                    </p>
                </div>';
        }
    }
    return $images;
}

function get_booking_number()
{
    // order no
    $booking_data = DB::table('bookings')->orderBy('id', 'desc')->first();



    if($booking_data)
    {
        $booking_no_old = ltrim($booking_data->booking_no, '0');
    }
    else
    {
        $booking_no_old=0;
    }

    $booking_no = $booking_no_old+1;
    $booking_no = '0000000'.$booking_no;
    if(strlen($booking_no)!=8)
    {
        $len = (strlen($booking_no)-8);
        $booking_no = substr($booking_no,$len);
    }
    return $booking_no;
}

function calculateDays($rent_date, $return_date) {
    // Convert the input strings to DateTime objects
    $rentDate = new DateTime($rent_date);
    $returnDate = new DateTime($return_date);

    // Check if the return date is after the rent date
    if ($returnDate > $rentDate) {
        // Calculate the difference in days
        $interval = $rentDate->diff($returnDate);
        $daysDiff = $interval->days; // Get the difference in days

        return $daysDiff; // Return the number of days difference
    } else {
        return 1; // Return 1 if return date is not greater than rent date
    }
}


function get_sms($params)
{
    // variable
    $student_name = "";
    $student_number = "";
    $start_date = "";
    $end_date= "";
    $start_time = "";
    $end_time= "";
    $teacher_name= "";
    $course= "";
    $company= "";
    $booking_date="";
    $notes="";
    $offer="";
    $status="";


    $sms_text = Sms::where('sms_status', $params['sms_status'])->first();
    if($params['sms_status']==1)
    {

        $student_name = $params['studentName'];
        $start_date = $params['startDate'];
        $start_time = $params['start_time'];
        $end_time= $params['end_time'];
        $teacher_name= $params['teacher_name'];
        $course= $params['courseName'];
        $company= $params['company'];

    }
    // else if($params['sms_status']==2)
    // {
    //     $booking_data =  Booking::where('booking_no', $params['booking_no'])->first();
    //     $edit_customer = Customer::where('id',$booking_data->customer_id)->first();
    //     $dress_data = Dress::where('id',$booking_data->dress_id)->first();
    //     $customer_name = $edit_customer->customer_name;
    //     $customer_number = $edit_customer->customer_number;
    //     $booking_no = $params['booking_no'];
    //     $rent_date = $params['rent_date'];
    //     $return_date = $params['return_date'];
    //     $booking_date = $params['booking_date'];
    //     $dress_name = $dress_data->dress_name;
    //     $notes = $booking_data->notes;
    //     // $invoice_link = "https://myapp3.com/super_electron/receipt_bill/".$params['booking_no'];
    //     $invoice_link = route('receipt_bill', ['booking_no' => $params['booking_no']]);
    // }
    // else if($params['sms_status']==3)
    // {
    //     $booking_data =  Booking::where('booking_no', $params['booking_no'])->first();
    //     $edit_customer = Customer::where('id',$booking_data->customer_id)->first();
    //     $dress_data = Dress::where('id',$booking_data->dress_id)->first();
    //     $customer_name = $edit_customer->customer_name;
    //     $customer_number = $edit_customer->customer_number;
    //     $booking_no = $params['booking_no'];
    //     $rent_date = $params['rent_date'];
    //     $return_date = $params['return_date'];
    //     $dress_name = $dress_data->dress_name;
    //     $notes = $booking_data->notes;
    //     // $invoice_link = "https://myapp3.com/super_electron/receipt_bill/".$params['booking_no'];
    //     $invoice_link = route('receipt_bill', ['booking_no' => $params['booking_no']]);
    // }
    // else if($params['sms_status']==4)
    // {
    //     $booking_data =  Booking::where('booking_no', $params['booking_no'])->first();
    //     $edit_customer = Customer::where('id',$booking_data->customer_id)->first();
    //     $dress_data = Dress::where('id',$booking_data->dress_id)->first();
    //     $customer_name = $edit_customer->customer_name;
    //     $customer_number = $edit_customer->customer_number;
    //     $booking_no = $params['booking_no'];
    //     $rent_date = $params['rent_date'];
    //     $return_date = $params['return_date'];
    //     $total_price = $params['amount'];
    //     $dress_name = $dress_data->dress_name;
    //     $notes = $booking_data->notes;
    //     // $invoice_link = "https://myapp3.com/super_electron/receipt_bill/".$params['booking_no'];
    //     $invoice_link = route('receipt_bill', ['booking_no' => $params['booking_no']]);
    // }
    // else if($params['sms_status']==5)
    // {
    //     $booking_data =  Booking::where('booking_no', $params['booking_no'])->first();
    //     $edit_customer = Customer::where('id',$booking_data->customer_id)->first();
    //     $dress_data = Dress::where('id',$booking_data->dress_id)->first();
    //     $customer_name = $edit_customer->customer_name;
    //     $customer_number = $edit_customer->customer_number;
    //     $booking_no = $params['booking_no'];
    //     $rent_date = $params['rent_date'];
    //     $return_date = $params['return_date'];
    //     $dress_name = $dress_data->dress_name;
    //     $notes = $booking_data->notes;
    //     // $invoice_link = "https://myapp3.com/super_electron/receipt_bill/".$params['booking_no'];
    //     $invoice_link = route('receipt_bill', ['booking_no' => $params['booking_no']]);
    // }
    // else if($params['sms_status']==6)
    // {
    //     $booking_data =  Booking::where('booking_no', $params['booking_no'])->first();
    //     $edit_customer = Customer::where('id',$booking_data->customer_id)->first();
    //     $dress_data = Dress::where('id',$booking_data->dress_id)->first();
    //     $bill_data = BookingBill::where('booking_no', $params['booking_no'])->first();
    //     $customer_name = $edit_customer->customer_name;
    //     $customer_number = $edit_customer->customer_number;
    //     $booking_no = $params['booking_no'];
    //     $rent_date = $params['rent_date'];
    //     $return_date = $params['return_date'];
    //     $payment_date = $params['payment_date'];
    //     $remaining_payment = $bill_data->total_remaining;
    //     $total_price = $bill_data->grand_total;
    //     $paid_amount = $params['paid_amount'];
    //     $payment_method = $params['payment_method'];
    //     $notes = $booking_data->notes;
    //     // $invoice_link = "https://myapp3.com/super_electron/receipt_bill/".$params['booking_no'];
    //     $invoice_link = route('receipt_bill', ['booking_no' => $params['booking_no']]);
    // }



    $variables = [

        'student_name' => $student_name,
        'teacher_name' => $teacher_name,
        'course_name' => $course,
        'start_date' => $start_date,
        'end_date' => $end_date,
        'start_time' => $start_time,
        'end_time' => $end_time,
        'company' => $company,


    ];

    $string = base64_decode($sms_text->sms);
    foreach ($variables as $key => $value) {
        $string = str_replace('{' . $key . '}', $value, $string);
    }
    return $string;
}
function sms_module($contact, $sms)
{
    if (!empty($contact)) {
        $url = "http://myapp3.com/whatsapp_admin_latest/Api_pos/send_request";

        $form_data = [
            'status' => 1,
            'sender_contact' => '968' . $contact,
            'customer_id' => 'tatweeersoftweb',
            'instance_id' => '1xwaxr8k',
            'sms' => base64_encode($sms),
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $form_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Accept: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $result=json_decode($resp,true);
        // $return_status= $result['response'];

    }
}


?>
