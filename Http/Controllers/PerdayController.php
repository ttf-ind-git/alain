<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\perday;

class PerdayController extends Controller
{

	public function index(perday $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('rules.perday.index', ['data' => $result]);
    }

    public function edit(perday $model, $id)
    {   
        //dd($id);

        $matchThese = ['id' => $id];

        $results = perday::where($matchThese)->get();

        //dd($results);
    	
    	//$emp = Employee::find($id);
        return view('rules.perday.edit',['data' => $results]);
       
    }

    public function update(Request $request, $id, perday $model)
    {	
        //dd($id);
       $validatedData = $request->validate([
    		'perday_count' => 'required|numeric',

	    ]);

      		
            $result = DB::table('perday_rule')
                ->where('id', $id)
                ->update([
                    'count' => $request->perday_count,
                    'updated_at' => Carbon::now()
             ]);
        
    
        //$model->update($request->all());

        return redirect()->route('perday.index')->withStatus(__('Rule successfully updated.'));
    }

    
}
