<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\timeslot;

class TimeslotController extends Controller
{
     public function index(timeslot $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('rules.timeslot.index', ['data' => $result]);
    }

    public function create()
    {

        return view('rules.timeslot.create');
    }

    public function store(Request $request, timeslot $model)
    {
    	// dd($request->receipt_number);

         $validatedData = $request->validate([
    		'start_time' => 'required|max:255',
            'end_time' => 'required|max:255',
            'perhour' => 'required|numeric',
	    ]);

         //dd($request->start_time);

        $start_time = $request->start_time;
        $new_start_time = date("H:i", strtotime($start_time));

        $end_time = $request->end_time;
        $new_end_time = date("H:i", strtotime($end_time));

         $store = array(
            'start_time' => $new_start_time,
            'end_time' => $new_end_time,
            'per_hour' => $request->perhour,
            'is_active' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

         //DB::table('scratch_form')->insert($scratch_details);

         $id = DB::table('timeslot')->insert($store);

         //dd($id);
        // Session::put('insert_id', $id);

         return redirect()->route('timeslot.index');

    }


    public function edit(timeslot $model, $id)
    {   
        //dd($id);

        $matchThese = ['id' => $id];

        $results = timeslot::where($matchThese)->get();

       // dd($results);
    	
    	//$emp = Employee::find($id);
        return view('rules.timeslot.edit',['data' => $results]);
       
    }

    public function update(Request $request, $id, timeslot $model)
    {	
       // dd($id);
        $validatedData = $request->validate([
    		'start_time' => 'required|max:255',
            'end_time' => 'required|max:255',
            'perhour' => 'required|numeric',
	    ]);

      

        $start_time = $request->start_time;
        $new_start_time = date("H:i", strtotime($start_time));

        $end_time = $request->end_time;
        $new_end_time = date("H:i", strtotime($end_time));  
      		
            $result = DB::table('timeslot')
                ->where('id', $id)
                ->update([
                    'start_time' => $new_start_time,
		            'end_time' => $new_end_time,
		            'per_hour' => $request->perhour,
                    'updated_at' => Carbon::now()
             ]);
        

        return redirect()->route('timeslot.index')->withStatus(__('Timeslot successfully updated.'));;
    }

    public function destroy(Request $request, $id)
    {
        //$delete = DB::table('employee')->where('id', $id)->delete();

        $result = DB::table('timeslot')
                ->where('id', $id)
                ->update([
                    'is_active' => '0',
                    'updated_at' => Carbon::now()
             ]);
        

        return redirect()->route('timeslot.index')->withStatus(__('Gift successfully deleted.'));
    }


}

