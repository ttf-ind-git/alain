<?php
/*

=========================================================
* Argon Dashboard PRO - v1.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/argon-dashboard-pro-laravel
* Copyright 2018 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)

* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        // $emirates_count = DB::table('scratch_form')
        //  ->select('emirates', DB::raw('count(*) as num'))
        //  ->groupBy('emirates')
        //  ->get();

        // $emirates_count = DB::table('scratch_form')->where('is_active',1)
        //     ->select('emirates',DB::raw('count(*) as num'),DB::raw('count(*) * 100 / (select count(*) from scratch_form) as percentage'))
        //     ->groupBy('emirates')
        //     ->get();

        $emirates_count = DB::table('scratch_form')->where('scratch_form.is_active',1)
            ->select('emirates',DB::raw('count(*) as num'),DB::raw('count(*) * 100 / (select count(*) from scratch_form where is_active = 1 ) as percentage'))
            ->groupBy('emirates')
        	->orderBy('emirates')
            ->get();


        $emirates_winner_count = DB::table('scratch_form')
        	->select('emirates', DB::raw("SUM(CASE 
            WHEN status = 'winner' THEN 1 ELSE 0 END ) AS winner"))
        	->where('is_active',1)
        	//->where('status',"winner")
            ->groupBy('emirates')
            ->get();

       $emirates_valid_winners = DB::table('scratch_form')
         ->select('emirates', DB::raw("SUM(CASE 
            WHEN status = 'winner' THEN 1 ELSE 0 END ) AS winner"))
         ->where('is_active',1)
         ->where('is_valid',1)
         ->where('status',"winner")
         ->where('result',"winner")
            ->groupBy('emirates')
            ->get();

        //dd($emirates_valid_winners);

         $emirates_invalid_winners = DB::table('scratch_form')
         ->select('emirates', DB::raw("SUM(CASE 
            WHEN status = 'winner' THEN 1 ELSE 0 END ) AS winner"))
         ->where('is_active',1)
         ->where('is_valid',2)
         ->where('status',"winner")
         ->where('result',"winner")
            ->groupBy('emirates')
            ->get();

           // dd($emirates_invalid_winners);

        $total_winner = DB::table('scratch_form')
                ->where('result', 'winner')
                ->where('is_active', '1')
                ->count();

        $valid_winners = DB::table('scratch_form')
            ->where('is_valid', '1')
            ->where('result', 'winner')
            ->where('is_active', '1')
            ->count();

        $invalid_winners = DB::table('scratch_form')
            ->where('is_valid', '2')
            ->where('result', 'winner')
            ->where('is_active', '1')
            ->count();

        $non_winners = DB::table('scratch_form')
            ->where('result', 'lost')
            ->where('is_active', '1')
            ->count();

        $total_prizes = DB::table('scratch_form')
            ->where('result',  'winner')
            ->where('is_valid', '1')
            ->where('is_active', '1')
            ->count();

        $total_participants = DB::table('scratch_form')
            ->where('is_active', '1')
            ->count();

        $today_participants = DB::table('scratch_form')
            ->where('is_active', '1')
            ->whereDate('date', Carbon::today())
            ->count();

        $today_winners = DB::table('scratch_form')
            ->where('is_active', '1')
            ->where('result', 'winner')
            ->whereDate('date', Carbon::today())
            ->count();

        $today_valid = DB::table('scratch_form')
            ->where('is_valid', '1')
            ->whereDate('date', Carbon::today())
            ->where('result', 'winner')
            ->count();

         $today_invalid = DB::table('scratch_form')
            ->where('is_valid', '2')
            ->whereDate('date', Carbon::today())
            ->where('result', 'winner')
            ->count();

        //dd($today_invalid);
       
       $gift_wise_count = DB::table('scratch_form')
            ->select(array('gift_details.*', DB::raw('COUNT(*) as total')))
            ->where('result', 'winner')
            //->where('is_valid', '1')
            ->where('scratch_form.is_active', '1')
            ->leftJoin('gift_details', 'gift_details.id', '=', 'scratch_form.price')
            ->groupBy('price')
            ->get();

        //dd($gift_wise_count);

         $role_id = Auth::user()->role_id;

        if($role_id ==1){
            return view('pages.dashboard', ['data' => $emirates_count, 'emirates_valid_winners' =>$emirates_valid_winners, 'emirates_invalid_winners' =>$emirates_invalid_winners, 'total_winner' => $total_winner, 'valid_winners' =>$valid_winners, 'invalid_winners' => $invalid_winners, 'non_winners' => $non_winners, 'total_prizes' => $total_prizes, 'gift_wise_count' => $gift_wise_count, 'total_participants' => $total_participants, 'emirates_winner_count' => $emirates_winner_count, 'today_participants' => $today_participants, 'today_winner' => $today_winners, 'today_valid' => $today_valid, 'today_invalid' => $today_invalid ]);
        }

        if($role_id ==2){
             return redirect()->route('client-dashborad.index');
        }
        
    }

    public function getpiedata(Request $request)
    {  
        //dd($request->id);
        //return $request->id;

         // $emirates_count = DB::table('scratch_form')->where('is_active',1)
         //    ->select('emirates',DB::raw('count(*) as num'),DB::raw('count(*) * 100 / (select count(*) from scratch_form) as percentage'))
         //    ->groupBy('emirates')
         //    ->get();

        $emirates_count = DB::table('scratch_form')
            ->select('emirates',DB::raw('count(*) as num'),DB::raw('count(*) * 100 / (select count(*) from scratch_form where is_active = 1) as percentage'))
            ->where('is_active',1)
            ->groupBy('emirates')
        	->orderBy('emirates')
            ->get();
    
    	//dd($emirates_count);
       
    	return response()->json($emirates_count);

    }

    public function emirates_wise_valid_winners(Request $request)
    {  
        //dd($request->id);
        //return $request->id;

         $data = DB::table('scratch_form')->where('scratch_form.is_active',1)
            ->select('emirates',DB::raw('count(*) as num'),DB::raw('count(*) * 100 / (select count(*) from scratch_form) as percentage'))
            ->where('is_valid',1)
            ->where('is_active',1)
            ->groupBy('emirates')
            ->get();

       return response()->json($data);

    }

    public function emirates_wise_invalid_winners(Request $request)
    {  
        //dd($request->id);
        //return $request->id;

         $data = DB::table('scratch_form')->where('scratch_form.is_active',1)
            ->select('emirates',DB::raw('count(*) as num'),DB::raw('count(*) * 100 / (select count(*) from scratch_form) as percentage'))
            ->where('is_valid',2)
            ->groupBy('emirates')
            ->get();

       return response()->json($data);

    }

    public function get_week_data(Request $request)
    {  

         $date = \Carbon\Carbon::today()->subDays(6);   

         //dd( $date);    

          $week_records = DB::table('scratch_form')
            ->where('is_active',1)
            ->where('date','>=',$date)
            ->select('date',DB::raw('count(*) as total_participants'))
            ->groupBy('date')
            ->get();       

         //dd($week_records);


         return response()->json($week_records);

    }

     public function get_winner_retailer(Request $request)
    {  

             
         
          // $week_records = DB::table('scratch_form')
          //   ->where('is_active',1)
          //   ->where('status','winner')
          //   ->select('status',DB::raw('count(*) as total_participants'))
          //   ->groupBy('status')
          //   ->get();       

          $week_records = DB::table('scratch_form as scratch')
            ->leftJoin('store as str', 'scratch.store_id', '=', 'str.id')
            ->where('str.is_active', '1')
            ->where('scratch.status', 'winner')
            ->select('scratch.store_id','str.name',DB::raw('count(*) as winners'))
            ->groupBy('scratch.store_id')
            ->get();

         //dd($week_records);


         return response()->json($week_records);

    }

}
