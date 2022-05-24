<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\gift_rule;


class GiftruleController extends Controller
{
    public function index(gift_rule $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('rules.gift_count.index', ['data' => $result]);
    }


    public function edit(gift_rule $model, $id)
    {   
        //dd($id);

        $matchThese = ['id' => $id];

        $results = gift_rule::where($matchThese)->get();

        //dd($results);
    	
    	//$emp = Employee::find($id);
        return view('rules.gift_count.edit',['data' => $results]);
       
    }

    public function update(Request $request, $id, gift_rule $model)
    {	
        //dd($id);
       $validatedData = $request->validate([
    		'max_winners' => 'required|numeric',
            'start_date' => 'required',
            'end_date' => 'required',

	    ]);


        $start_date = $request->start_date;
        $new_start_date = date("Y-m-d", strtotime($start_date));  

        $end_date = $request->end_date;
        $new_end_date = date("Y-m-d", strtotime($end_date));  
       // dd($new_end_date);
      		
            $result = DB::table('total_winner')
                ->where('id', $id)
                ->update([
                    'total_winners' => $request->max_winners,
                    // 'remaining_winners' => $request->max_winners,
                    'start_date' => $new_start_date,
                    'end_date' => $new_end_date,
                    'updated_at' => Carbon::now()
             ]);
        
    
        //$model->update($request->all());

        return redirect()->route('gift-rule.index')->withStatus(__('Rule successfully updated.'));
    }


}
