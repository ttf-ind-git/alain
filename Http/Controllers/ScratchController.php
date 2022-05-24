<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use App\Scratch;
use App\store;
use Mail;

class ScratchController extends Controller
{

    public function index(store $model)
    {
        // $data = [];
        // Mail::send('scratch.mail', $data, function($message){
        //     $message->to('ajith@ebazaar.ae');
        //     $message->subject('Al Ain - Scan & Win Promotion - We are reviewing your entry');
        //     $message->cc('iamajith26@gmail.com', 'Ajith');
        //     $message->from('alainwaterwinner@thethoughtfactory.ae','Alain Water');
        //  });

        // // check for failures
        // if (Mail::failures()) {

        //     dd('entered');
        // }


        $ip = "103.57.251.0 ";
        $details = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}"));
        // dd($details);
        if ($details->geoplugin_countryName == "United Arab Emirates" || $details->geoplugin_countryName == "India") {
            $result = DB::table('store')
                ->where('is_active', '1')
                ->get();

            //dd($store);
            Session::put('language', "eng");

            return view('scratch.home', ['data' => $result]);
        } else {
            return response('Sorry you dont have permission to access this application');
        }
    }

    public function store(Request $request, Scratch $model)
    {
        //dd($request->all());

        $validatedData = $request->validate(
            [
                'firstname' => 'required|max:255|regex:/^[\pL\s\-_]+$/u',
                'lastname' => 'required|max:255|regex:/^[\pL\s\-_]+$/u',
                'email' => 'required|email',
                //'phone' => 'required|numeric|min:9',
                //'phone' => 'required|regex:/(05)[0-9]/|not_regex:/[a-z]/|min:9',
                'phone' => 'required|regex:/^(05)[0-9]/|min:9',

                'emirates' => 'required',
                'store' => 'required|max:255',
                //'receipt_number' => 'required|max:255|unique:scratch_form',

                //  'email' => [
                //      'required',
                //      'email',
                //      Rule::unique('scratch_form')->where(function($query) {
                //        $query->where('is_active', '=', 1);
                //  })
                // ],

                //  'receipt_number' => [
                //      'required',
                //      'max:255',
                //      'regex:/^[0-9a-zA-Z\pL\-_]+$/u',
                //      Rule::unique('scratch_form')->where(function($query) {
                //        $query->where('is_active', '=', 1);
                //  })
                // ],


                'terms' => 'accepted',
                //'information' =>'accepted',

            ],
            [
                // 'email.unique'      => 'Sorry, This email is already submited.',
                //'receipt_number.unique'      => ' Receipt already submited',
                'emirates.required' => 'Please select emirate',
                'terms.accepted' => 'Terms and condition must be accept',
                //'information.accepted' =>'Terms and condition must be accept',

            ]
        );

      
        //dd($request->all());

        $ip_address = $request->ip();

        $date = date('Y-m-d');

        $time = date('H:i:s');


        //dd($ip_address);

        $random = "Usr" . $this->random_strings(20);

        //dd($random);

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE)
            $browser =  'Internet explorer';
        elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) //For Supporting IE 11
            $browser = 'Internet explorer';
        elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE)
            $browser = 'Mozilla Firefox';
        elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE)
            $browser = 'Google Chrome';
        elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== FALSE)
            $browser = "Opera Mini";
        elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE)
            $browser = "Opera";
        elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE)
            $browser = "Safari";
        else
            $browser = 'Other browser';

        //dd($browser);

        $scratch_details = array(
            'user_id' => $random,
            'first_name' => $request->firstname,
            'last_name' => $request->lastname,
            'email' => $request->email,
            'phone' => $request->phone,
            'emirates' => $request->emirates,
            'store_id' => $request->store,
            'receipt_number' => $request->receipt_number,
            'ip_address' => $ip_address,
            'device' => $request->device,
            'language' => $request->language,
            'date' => $date,
            'time' => $time,
            'browser' => $browser,
            'is_active' => '0',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

        //DB::table('scratch_form')->insert($scratch_details);

        $id = DB::table('scratch_form')->insertGetId($scratch_details);

        //dd($id);
        Session::put('insert_id', $id);

        Session::put('user_email', $request->email);

        Session::put('user_phone', $request->phone);

        Session::put('receipt', $request->receipt_number);

        Session::put('first_name', $request->firstname);

        Session::put('last_name', $request->lastname);

        Session::put('device', $request->device);

        return redirect()->route('upload.index');
    }


    function random_strings($length_of_string)
    {

        // md5 the timestamps and returns substring 
        // of specified length 
        return substr(md5(time()), 0, $length_of_string);
    }

    public function select_store(Request $request)
    {

        $id = $request->id;

        //dd($id);

        $result = DB::table('store')
            ->where('id', $id)
            ->get();

        return response()->json($result);
    }
}
