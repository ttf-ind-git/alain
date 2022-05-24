<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\participants;
use App\result;

class ParticipantController extends Controller
{
    public function index(participants $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->with('store')->where($matchThese)->orderBy('created_at', 'DESC')->get();

        $store = DB::table('store')
                ->where('is_active', '1')
                ->get();

    	//dd($result); 

        //return view('scratch.home');
        return view('participants.index', ['data' => $result, 'store' => $store]);
    }

    public function winners(participants $model)
    {

        $matchThese = ['scratch_form.is_active' => '1'];

        //$result = participants::where($matchThese)->get();

        $result = $model->with('store')
            ->select('scratch_form.*','gift_details.name as gift_name')
            ->leftJoin('gift_details', 'gift_details.id', '=', 'scratch_form.price')
            ->where('is_valid',1)
            ->where($matchThese)
            ->orderBy('scratch_form.created_at', 'DESC')
            ->get();

       // dd($result);    

        $store = DB::table('store')
                ->where('is_active', '1')
                ->get();

        //dd($store); 

        //return view('scratch.home');
        return view('winners.index', ['data' => $result, 'store' => $store]);
    }

    public function view_documents(Request $request)
    {  
        //dd($request->id);
        //return $request->id;

        $result = DB::table('scratch_form')
                ->where('id', $request->id)
                ->get();

       return response()->json($result);

    }

    public function change_is_valid(Request $request)
    {  
        //dd($request->id);
        //return $request->id;

        $value = $request->value;
        $id = $request->id;
        $gift_id = $request->gift_id;
        //dd($id);

        if($value == 2)
        {
            $check = DB::table('scratch_form')
                ->where('id', $id)
                ->get();
             

            //dd($check[0]->is_valid);

            if($check[0]->is_valid == 1)
            {
                DB::table('gift_details')
                ->where('id', $gift_id)
                ->increment('remaining_gifts',1);

                DB::table('total_winner')
                ->increment('remaining_winners',1);   

            }

            
        }

        $result = DB::table('scratch_form')
                ->where('id', $id)
                ->update([
                    'is_valid' => $value,
                    'updated_at' => Carbon::now()
             ]);


         // DB::table('scratch_form')
         //    ->where('document_id', $id)
         //    ->update([
         //        'is_valid' => $value,
         //        'updated_at' => Carbon::now()
         // ]); 
       
        

        if($value == 1)
        {
            DB::table('gift_details')
            ->where('id', $gift_id)
            ->decrement('remaining_gifts',1);

            DB::table('total_winner')
            ->decrement('remaining_winners',1);      
        }
             

       return response()->json($result);

    }

    public function filter(Request $request, participants $model, result $result)
    {  
        //dd($request->filter_ip_addr);
        //return $request->id;

        $store = $request->filter_store;
        $ip_addr = $request->filter_ip_addr;
        $result_status = $request->filter_result;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $matchThese = ['is_active' => '1'];
        $query = $model->with('store');


        // $query = $model->with(['store', 'documents', 'result' => function($q) use($result_status) {
        //     // Query the name field in status table
        //      if(!empty($result_status))
        //      {
        //          $q->where('result' , $result_status); // '=' is optional
        //      }
           
        // }]);


        if(!empty($store))
        {

           //dd($store);
            $query->where('store_id' , $store);

        }

        if(!empty($ip_addr))
        {

           //dd($store);
            $query->where('ip_address' , $ip_addr);

        }

        if(!empty($result_status))
        {

             $query->where('status' , $result_status);
            // dd($query1);

        }

        // if(!empty($start_date))
        // {
            
        //      $date_search_start_date = date('Y-m-d', strtotime($start_date));

        //      // dd($date_search_start_date);


        //      $query->whereDate('date', $date_search_start_date);
        //     // dd($query1);

        // }

        if(!empty($end_date) && !empty($start_date) )
        {
            
             $date_search_start_date = date('Y-m-d', strtotime($start_date));
             $date_search_end_date = date('Y-m-d', strtotime($end_date));

             // dd($date_search_start_date);

             $query->whereBetween('date', [$date_search_start_date, $date_search_end_date]);

           
            // dd($query1);

        }

        $result= $query->where($matchThese)->orderBy('created_at', 'DESC')->get();

       // dd($result);

        $store = DB::table('store')
                ->where('is_active', '1')
                ->get();

        return view('participants.index', ['data' => $result, 'store' => $store]);
       
       // return response()->json($result);

    }

     public function filter_winner(Request $request, participants $model, result $result)
    {  
        //dd($request->filter_ip_addr);
        //return $request->id;

        $store = $request->filter_store;
        $ip_addr = $request->filter_ip_addr;
        $result_status = $request->filter_result;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $matchThese = ['scratch_form.is_active' => '1'];
       // $query = $model->with('store', 'documents', 'result');

         $query = $model->with('store')
            ->select('scratch_form.*','gift_details.name as gift_name')
            ->leftJoin('gift_details', 'gift_details.id', '=', 'scratch_form.price')
            ->where('is_valid',1)
            ->where($matchThese);
           


        if(!empty($store))
        {

           //dd($store);
            $query->where('store_id' , $store);

        }

        // if(!empty($ip_addr))
        // {

        //    //dd($store);
        //     $query->where('ip_address' , $ip_addr);

        // }

        // if(!empty($result_status))
        // {

        //      $query->where('status' , $result_status);
        //     // dd($query1);

        // }

        // if(!empty($start_date))
        // {
            
        //      $date_search_start_date = date('Y-m-d', strtotime($start_date));

        //      // dd($date_search_start_date);


        //      $query->whereDate('date', $date_search_start_date);
        //     // dd($query1);

        // }

        if(!empty($end_date) && !empty($start_date))
        {
            
             $date_search_start_date = date('Y-m-d', strtotime($start_date));
             $date_search_end_date = date('Y-m-d', strtotime($end_date));

             // dd($date_search_start_date);

             $query->whereBetween('scratch_form.date', [$date_search_start_date, $date_search_end_date]);

           
            // dd($query1);

        }

        $result= $query->where($matchThese)->orderBy('scratch_form.created_at', 'DESC')->get();

       // dd($result);

        $store = DB::table('store')
                ->where('is_active', '1')
                ->get();

        return view('winners.index', ['data' => $result, 'store' => $store]);
       
       // return response()->json($result);

    }


}
