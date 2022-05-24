<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\store;
use App\gift;

class GiftController extends Controller
{
    public function index(gift $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('gifts.index', ['data' => $result]);
    }

    public function create(gift $model)
    {

        // $roles = DB::table('roles')
        //         ->whereIn('id', [3, 4, 5, 6, 7, 8])
        //         ->get();

        return view('gifts.create');
    }

     public function store(Request $request, gift $model)
    {
    	// dd($request->receipt_number);

         $validatedData = $request->validate([
    		'name' => 'required|max:255',
            'gift_type' => 'required|max:255',
            'total_gifts' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',

	    ]);

         //dd($request->start_time);

        $start_date = $request->start_date;
        $new_start_date = date("Y-m-d", strtotime($start_date));  

        $end_date = $request->end_date;
        $new_end_date = date("Y-m-d", strtotime($end_date));

        $start_time = $request->start_time;
        $new_start_time = date("H:i", strtotime($start_time));

        $end_time = $request->end_time;
        $new_end_time = date("H:i", strtotime($end_time));

         $store = array(
            'name' => $request->name,
            'Type' => $request->gift_type,
            'total_gifts' => $request->total_gifts,
            'remaining_gifts' => $request->total_gifts,
            'start_date' => $new_start_date,
            'end_date' => $new_end_date,
            'start_time' => $new_start_time,
            'end_time' => $new_end_time,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

         //DB::table('scratch_form')->insert($scratch_details);

         $id = DB::table('gift_details')->insert($store);

         //dd($id);
        // Session::put('insert_id', $id);

         return redirect()->route('gift.index');


    }


    public function edit(gift $model, $id)
    {   
        //dd($id);

        $matchThese = ['id' => $id];

        $results = gift::where($matchThese)->get();

        //dd($results);
    	
    	//$emp = Employee::find($id);
        return view('gifts.edit',['data' => $results]);
       
    }

    public function update(Request $request, $id, gift $model)
    {	
        //dd($id);
       $validatedData = $request->validate([
            'name' => 'required|max:255',
            'gift_type' => 'required|max:255',
            'total_gifts' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',

        ]);

        $start_date = $request->start_date;
        $new_start_date = date("Y-m-d", strtotime($start_date));  

        $end_date = $request->end_date;
        $new_end_date = date("Y-m-d", strtotime($end_date));

        $start_time = $request->start_time;
        $new_start_time = date("H:i", strtotime($start_time));

        $end_time = $request->end_time;
        $new_end_time = date("H:i", strtotime($end_time));  
      		
            $result = DB::table('gift_details')
                ->where('id', $id)
                ->update([
                    'name' => $request->name,
                    'Type' => $request->gift_type,
                    'total_gifts' => $request->total_gifts,
                    'start_date' => $new_start_date,
                    'end_date' => $new_end_date,
                    'start_time' => $new_start_time,
                    'end_time' => $new_end_time,
                    'updated_at' => Carbon::now()
             ]);
        
    
        //$model->update($request->all());

        return redirect()->route('gift.index')->withStatus(__('Gift successfully updated.'));
    }

    public function destroy(Request $request, $id)
    {
    	//$delete = DB::table('employee')->where('id', $id)->delete();

        $result = DB::table('gift_details')
                ->where('id', $id)
                ->update([
                    'is_active' => '0',
                    'updated_at' => Carbon::now()
             ]);
        

    	return redirect()->route('gift.index')->withStatus(__('Gift successfully deleted.'));
    }

}
