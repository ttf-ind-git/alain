<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\gift;
use App\prewinner;


class PrewinnerController extends Controller
{
    public function index(prewinner $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->with('gift')->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('pre_winners.index', ['data' => $result]);
    }

    public function create(gift $model)
    {

        $matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('pre_winners.create', ['data' => $result]);
    }


    public function store(Request $request, prewinner $model)
    {
    	// dd($request->receipt_number);

         $validatedData = $request->validate([
    		'receipt_no' => 'required|max:255',
            'gift' => 'required|max:255',
	    ]);

       
         $store = array(
            'receipt_no' => $request->receipt_no,
            'gift_id' => $request->gift,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

         //DB::table('scratch_form')->insert($scratch_details);

         $id = DB::table('prewinner')->insert($store);

         //dd($id);
        // Session::put('insert_id', $id);

        return redirect()->route('prewinner.index')->withStatus(__('Pre Winner successfully created.'));


    }

     public function edit(prewinner $model, $id)
    {   
        //dd($id);

        $matchThese = ['id' => $id];

        $results = prewinner::where($matchThese)->get();

        $matchThese1 = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result1 = gift::where($matchThese1)->get();

    	//dd($result1);

    	
    	//$emp = Employee::find($id);
        return view('pre_winners.edit',['datas' => $results, 'gifts' => $result1]);
       
    }


    public function update(Request $request, $id, prewinner $model)
    {	
        //dd($request->gift);
       $validatedData = $request->validate([
    		'receipt_no' => 'required|max:255',
            'gift' => 'required|max:255',
	    ]);

            $result = DB::table('prewinner')
                ->where('id', $id)
                ->update([
                    'receipt_no' => $request->receipt_no,
		            'gift_id' => $request->gift,
		            'updated_at' => Carbon::now()
             ]);

        //$model->update($request->all());

        return redirect()->route('prewinner.index')->withStatus(__('Pre Winner successfully updated.'));

    }

    public function destroy(Request $request, $id)
    {
    	//$delete = DB::table('employee')->where('id', $id)->delete();

        $result = DB::table('prewinner')
                ->where('id', $id)
                ->update([
                    'is_active' => '0',
                    'updated_at' => Carbon::now()
             ]);
        

    	return redirect()->route('prewinner.index')->withStatus(__('Pre Winner successfully deleted.'));

    }



}
