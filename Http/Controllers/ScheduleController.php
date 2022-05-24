<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\gift;
use App\schedule;

class ScheduleController extends Controller
{
    public function index(schedule $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->with('gift')->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('schedule.index', ['data' => $result]);
    }

    public function create(gift $model)
    {

        $matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('schedule.create', ['data' => $result]);
    }

    public function store(Request $request, schedule $model)
    {
         //dd($request->all());

         $validatedData = $request->validate([
            'date' => 'required|max:255',
            'prize' => 'required|max:255',
            'count' => 'required|max:255',
            'start_time' => 'required|max:255',
            'end_time' => 'required|max:255',

        ]);

        $date = date('Y-m-d', strtotime($request->date));

        $start_time = $request->start_time;
        $new_start_time = date("H:i", strtotime($start_time));

        $end_time = $request->end_time;
        $new_end_time = date("H:i", strtotime($end_time));

         $store = array(
            'date' => $date,
            'prize' => $request->prize,
            'count' => $request->count,
            'count' => $request->count,
            'start_time' => $new_start_time,
            'end_time' => $new_end_time,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

         //DB::table('scratch_form')->insert($scratch_details);

         $id = DB::table('schedule')->insert($store);

         //dd($id);
        // Session::put('insert_id', $id);

         return redirect()->route('schedule.index');


    }

    public function edit(schedule $model, gift $gift, $id)
    {   
        //dd($id);

        $gift_where = ['is_active' => '1'];

        //$result = participants::where($matchThese)->get();

        $gift = $gift->where($gift_where)->get();

       // dd($gift);


        $matchThese = ['id' => $id];

        $results = schedule::where($matchThese)->get();

        //dd($results);
        
        //$emp = Employee::find($id);
        return view('schedule.edit',['data' => $results, 'gift' => $gift]);
       
    }

    public function update(Request $request, $id, gift $model)
    {   
        //dd($request->all());
      $validatedData = $request->validate([
            'date' => 'required|max:255',
            'prize' => 'required|max:255',
            'count' => 'required|max:255',
            'start_time' => 'required|max:255',
            'end_time' => 'required|max:255',

        ]);
		
		//dd($id);

        $date = date('Y-m-d', strtotime($request->date));

        $start_time = $request->start_time;
        $new_start_time = date("H:i", strtotime($start_time));

        $end_time = $request->end_time;
        $new_end_time = date("H:i", strtotime($end_time));

       
            $result = DB::table('schedule')
                ->where('id', $id)
                ->update([
                    'date' => $date,
                    'prize' => $request->prize,
                    'count' =>  $request->count,
					'start_time' => $new_start_time,
					'end_time' => $new_end_time,
                    'updated_at' => Carbon::now()
             ]);
        
    
        //$model->update($request->all());

        return redirect()->route('schedule.index')->withStatus(__('Schedule successfully updated.'));
    }

    public function destroy(Request $request, $id)
    {
        //$delete = DB::table('employee')->where('id', $id)->delete();

        $result = DB::table('schedule')
                ->where('id', $id)
                ->update([
                    'is_active' => '0',
                    'updated_at' => Carbon::now()
             ]);
        

        return redirect()->route('schedule.index')->withStatus(__('Schedule successfully deleted.'));
    }


}
