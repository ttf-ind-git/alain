<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\participants;
use App\store;


class StoreController extends Controller
{
 	public function index(store $model)
    {

    	$matchThese = ['is_active' => '1'];

    	//$result = participants::where($matchThese)->get();

    	$result = $model->where($matchThese)->get();

    	//dd($result);

        //return view('scratch.home');
        return view('store.index', ['data' => $result]);
    }

    public function create(store $model)
    {

        // $roles = DB::table('roles')
        //         ->whereIn('id', [3, 4, 5, 6, 7, 8])
        //         ->get();

        return view('store.create');
    }

    public function store(Request $request, store $model)
    {
    	// dd($request->receipt_number);

         $validatedData = $request->validate([
    		'name' => 'required|max:255',
            'emirates' => 'required|max:255',
            'address' => 'required|max:255',

	    ]);

       

         $store = array(
            'name' => $request->name,
            'emirates' => $request->location,
            'address' => $request->address,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        );

         //DB::table('scratch_form')->insert($scratch_details);

         $id = DB::table('store')->insert($store);

         //dd($id);
        // Session::put('insert_id', $id);

         return redirect()->route('store.index');


    }
   
}
