<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ResultController extends Controller
{

    public function index()
    {

    	$id = Session::get('insert_id');
	    //dd($id);

        $lan = Session::get('language');

        $type = Session::get('type');

        // dd($type);

        Session::put('type', $type); 

        // if($id == "")
        // {
        //     return view('errors.error_419');
        // }

        session()->forget('insert_id'); 

	     if($lan =='eng'){
           return view('scratch.scratch');
         }

        if($lan =='arb'){
            return view('scratch.arabic.scratch');
         }

         
    }


    public function status_l()
    {
        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone'); 
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');

         if($lan =='eng'){
           return view('scratch.status_l');
         }

        if($lan =='arb'){
            return view('scratch.arabic.status_l');
         }

    }

    public function status_w()
    {

        //$prize = Session::get('type');

       // dd($prize);

    	return view('scratch.status_w');
    }

    public function status_t()
    {
        return view('scratch.status_t');
    }



    public function expo()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.expo');
         }

        if($lan =='arb'){
            return view('scratch.arabic.expo');
         }

        
    }

    public function nature()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.nature');
         }

        if($lan =='arb'){
            return view('scratch.arabic.nature');
         }

        
    }

     public function balloon()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.balloon');
         }

        if($lan =='arb'){
            return view('scratch.arabic.balloon');
         }

        
    }
    
    public function car()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.car');
         }

        if($lan =='arb'){
            return view('scratch.arabic.car');
         }

        
    }
 
    public function meals()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.meals');
         }

        if($lan =='arb'){
            return view('scratch.arabic.meals');
         }

        
    }

    public function palace()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.palace');
         }

        if($lan =='arb'){
            return view('scratch.arabic.palace');
         }

        
    }

    public function zipline()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.zipline');
         }

        if($lan =='arb'){
            return view('scratch.arabic.zipline');
         }

        
    }

    public function burj_khalifa()
    {  
         $id = Session::get('insert_id');
         $winner = Session::get('winner');
        //dd($id);  
       
        // if($id == "")
        // {
        //     //return view('errors.error_419');
        // }

        if($winner == "")
        {
            return view('errors.error_419');
        }


        session()->forget('insert_id'); 
        session()->forget('user_email'); 
        session()->forget('user_phone');
        session()->forget('receipt'); 
        session()->forget('winner'); 

        $lan = Session::get('language');
        
         if($lan =='eng'){
           return view('scratch.burj_khalifa');
         }

        if($lan =='arb'){
            return view('scratch.arabic.burj_khalifa');
         }

        
    }    

  

}
