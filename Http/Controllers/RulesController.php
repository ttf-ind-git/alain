<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\rules;

class RulesController extends Controller
{
    public function index(rules $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('rules.win_count.index', ['data' => $result]);
    }

     public function create(rules $model)
    {

        // $roles = DB::table('roles')
        //         ->whereIn('id', [3, 4, 5, 6, 7, 8])
        //         ->get();

        return view('rules.win_count.create');
    }

    //  public function store(Request $request, gift $model)
    // {
    // 	// dd($request->receipt_number);

    //      $validatedData = $request->validate([
    // 		'name' => 'required|max:255',
    //         'total_gifts' => 'required|numeric',

	   //  ]);

       

    //      $store = array(
    //         'name' => $request->name,
    //         'total_gifts' => $request->total_gifts,
    //         'remaining_gifts' => $request->total_gifts,
    //         'created_at' => Carbon::now(),
    //         'updated_at' => Carbon::now()
    //     );

    //      //DB::table('scratch_form')->insert($scratch_details);

    //      $id = DB::table('gift_details')->insert($store);

    //      //dd($id);
    //     // Session::put('insert_id', $id);

    //      return redirect()->route('gift.index');


    // }


    public function edit(rules $model, $id)
    {   
        //dd($id);

        $matchThese = ['id' => $id];

        $results = rules::where($matchThese)->get();

        //dd($results);
    	
    	//$emp = Employee::find($id);
        return view('rules.win_count.edit',['data' => $results]);
       
    }

    public function update(Request $request, $id, rules $model)
    {	
        //dd($id);
       $validatedData = $request->validate([
    		'allowed_count' => 'required|numeric',

	    ]);

      		
            $result = DB::table('maximum_win')
                ->where('id', $id)
                ->update([
                    'count' => $request->allowed_count,
                    'updated_at' => Carbon::now()
             ]);
        
    
        //$model->update($request->all());

        return redirect()->route('max-win.index')->withStatus(__('Rule successfully updated.'));
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
