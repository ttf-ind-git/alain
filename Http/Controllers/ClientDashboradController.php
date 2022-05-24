<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\participants;
use App\result;

class ClientDashboradController extends Controller
{
    public function index()
    {

        // $emirates_count = DB::table('scratch_form')
        //  ->select('emirates', DB::raw('count(*) as num'))
        //  ->where('is_active',1)
        //  ->groupBy('emirates')
        //  ->get();

        $emirates_count = DB::table('scratch_form')->where('scratch_form.is_active',1)
            ->select('emirates',DB::raw('count(*) as num'),DB::raw('count(*) * 100 / (select count(*) from scratch_form where is_active = 1 ) as percentage'))
            ->groupBy('emirates')
        	->orderBy('emirates')
            ->get();


        //dd($emirates_count);

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

       // dd($emirates_valid_winners);



        $total_winner = DB::table('scratch_form')
                ->where('result', 'winner')
                ->where('is_active', '1')
                ->count();

        $valid_winners = DB::table('scratch_form')
            ->where('is_valid', '1')
            ->where('result', 'winner')
            ->where('is_active', '1')
            ->count();

         //dd($valid_winners);

        $invalid_winners = DB::table('scratch_form')
            ->where('is_valid', '2')
            ->where('result', 'winner')
            ->count();

        $non_winners = DB::table('scratch_form')
            ->where('result', 'lost')
            ->where('is_active', '1')
            ->count();

        // $total_prizes = DB::table('result')
        //     ->where('result',  'winner')
        //     ->where('is_valid', '1')
        //     ->count();

        $total_participants = DB::table('scratch_form')
            ->where('is_active', '1')
            ->count();

        // $today_participants = DB::table('result')
        //     ->where('is_active', '1')
        //     ->whereDate('date', Carbon::today())
        //     ->count();

        // $today_winners = DB::table('result')
        //     ->where('is_active', '1')
        //     ->where('result', 'winner')
        //     ->whereDate('date', Carbon::today())
        //     ->count();

        // $today_valid = DB::table('result')
        //     ->leftJoin('documents', 'result.document_id', '=', 'documents.id')
        //     ->where('documents.is_valid', '1')
        //     ->whereDate('date', Carbon::today())
        //     ->where('result.result', 'winner')
        //     ->count();

         // $today_invalid = DB::table('result')
         //    ->leftJoin('documents', 'result.document_id', '=', 'documents.id')
         //    ->where('documents.is_valid', '2')
         //    ->whereDate('date', Carbon::today())
         //    ->where('result.result', 'winner')
         //    ->count();

        //dd($today_invalid);
       
        $gift_wise_count = DB::table('scratch_form')
            ->select(array('gift_details.*', DB::raw('COUNT(*) as total')))
            ->where('result', 'winner')
            //->where('is_valid', '1')
            ->where('scratch_form.is_active', '1')
            ->leftJoin('gift_details', 'gift_details.id', '=', 'scratch_form.price')
            ->groupBy('price')
            ->get();

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

        //dd($total_prizes);

        $date = Carbon::now()->subDays(10);
        //dd($date);

        $last_ten_days = DB::table('scratch_form')->where('scratch_form.is_active',1)
            ->select('scratch_form.date',DB::raw('count(*) as count'))
            //->where('scratch_form.date', '>=', DB::raw('DATE(NOW()) - INTERVAL 9 DAY'))
            ->where('scratch_form.date', '>=', $date)
            ->groupBy('scratch_form.date')
            ->get();

        //dd($last_ten_days);

        return view('pages.client_dashboard', ['data' => $emirates_count, 'total_winner' => $total_winner, 'valid_winners' =>$valid_winners, 'invalid_winners' => $invalid_winners,  'emirates_valid_winners' =>$emirates_valid_winners, 'emirates_invalid_winners' =>$emirates_invalid_winners, 'emirates_winner_count' => $emirates_winner_count, 'non_winners' => $non_winners, 'gift_wise_count' => $gift_wise_count, 'total_participants' => $total_participants, 'last_ten_days' => $last_ten_days]);
    }


    public function client_report(participants $model)
    {

    	// $matchThese = ['scratch_form.is_active' => '1'];

    	// //$result = participants::where($matchThese)->get();

    	// $result = $model->with('store')->with('documents')->where($matchThese)->with('result')->orderBy('scratch_form.created_at', 'DESC')
    	// ->select('scratch_form.*','gift_details.name')
     //    ->leftJoin('result', 'result.scratch_form_id', '=', 'scratch_form.id')
     //    ->leftJoin('gift_details', 'gift_details.id', '=', 'result.price')
     //    ->where($matchThese)
     //    ->where('result.is_valid', 1)
     //    ->orderBy('scratch_form.created_at', 'DESC')->get();

        $matchThese = ['is_active' => '1'];

        //$result = participants::where($matchThese)->get();

        $result = $model->with('store')->with('documents')->where($matchThese)->with('result')->orderBy('created_at', 'DESC')->get();

    	//dd($result); 

        return view('pages.client_report', ['data' => $result]);

	}


     public function filter_client(Request $request, participants $model, result $result)
    {  
        //dd($request->filter_ip_addr);
        //return $request->id;

        $result_status = $request->filter_result;
        $start_date = $request->start_date;

        $matchThese = ['is_active' => '1'];
        $query = $model->with('store');



        if(!empty($result_status))
        {

             $query->where('status' , $result_status);
            // dd($query1);

        }

        if(!empty($start_date))
        {
            
             $date_search_start_date = date('Y-m-d', strtotime($start_date));

             // dd($date_search_start_date);


             $query->whereDate('date', $date_search_start_date);
            // dd($query1);

        }

       
        $result= $query->where($matchThese)->orderBy('created_at', 'DESC')->get();

       // dd($result);

        return view('pages.client_report', ['data' => $result]);
       
       // return response()->json($result);

    }

    public function client_report_date(Request $request, $date, participants $model)
    {
        //dd($date);

        $matchThese = ['is_active' => '1'];

        //$result = participants::where($matchThese)->get();

        $result = $model->with('store')
        ->where($matchThese)
        ->whereDate('date', $date)
        ->orderBy('created_at', 'DESC')
        ->get();

        //dd($result); 

        return view('pages.client_report', ['data' => $result]);

    }

}
