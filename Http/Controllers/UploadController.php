<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Scratch;
use App\Upload;
use App\prewinner;
use App\gift;
use Carbon\Carbon;
use Mail;
use Image;


class UploadController extends Controller
{
    public function index()
    {

        $id = Session::get('insert_id');

        $lan = Session::get('language');


        if ($id == "") {

            return view('errors.error_419');
        }

        if ($lan == 'eng') {
            return view('scratch.upload');
        }

        if ($lan == 'arb') {
            return view('scratch.arabic.upload');
        }
    }

    public function store(Request $request, prewinner $prewinner)
    {
        //$model->create($request->all());

        $lan = Session::get('language');

        //dd($lan);

        $id = Session::get('insert_id');
        //dd($request->all());

        $user_email = Session::get('user_email');

        $user_phone = Session::get('user_phone');
        //dd($user_phone);

        $receipt = Session::get('receipt');

        $first_name = Session::get('first_name');

        $last_name = Session::get('last_name');

        //dd($receipt);

        if ($id == "") {
            return view('errors.error_419');
        }

        $validatedData = $request->validate([
            //'scratch_form_id' => 'unique:documents',
            'receipt' => 'required',
            'product' => 'required',

        ]);

        // dd(time());

        if ($request->hasfile('receipt')) {
            $receipt_photo = $request->receipt;

            $destinationPath = 'receipt';

            //$receipt_filename = $receipt_photo->getClientOriginalName();

            //$receipt_photo->move($destinationPath, $receipt_filename);

            $image = $request->file('receipt');

            $receipt_filename = time() . '.' . $receipt_photo->getClientOriginalName();

            //dd($receipt_filename);

            $img = Image::make($image->getRealPath())->orientate();
            $img->resize(1080, 800, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $receipt_filename, 100);

            //Image::make($image->getRealPath())->resize(468, 249)->save('public/img/products'.$filename);

        }

        if ($request->hasfile('product')) {
            $product_photo = $request->product;

            $destinationPath = 'product';


            // $product_photo->move($destinationPath, $product_filename);

            $image = $request->file('product');

            $product_filename = time() . '.' . $product_photo->getClientOriginalName();

            $img = Image::make($image->getRealPath())->orientate();
            $img->resize(1080, 800, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath . '/' . $product_filename, 100);
        }

        // $values = array(
        //     'scratch_form_id' =>  $id,
        //     'receipt_photo' => $receipt_filename,
        //     'product_photo' => $product_filename,
        //     'is_valid' => '0',
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now()
        // );

        DB::table('scratch_form')
            ->where('id', $id)
            ->update([
                'receipt_photo' => $receipt_filename,
                'product_photo' => $product_filename,
                'updated_at' => Carbon::now()
            ]);

        // $ignore_duplicate = DB::table('documents')
        //     ->where('scratch_form_id', $id)
        //     ->get();

        // //dd($ignore_duplicate);

        // if($ignore_duplicate->isNotEmpty())
        // {
        //     return view('errors.error_419');
        // }

        // if($id == "")
        // {
        //     return view('errors.error_419');
        // }

        //$document_id = DB::table('documents')->insertGetId($values);

        //dd($document_id);

        $ip_address = $request->ip();

        $date = date('Y-m-d');

        $time = date('H:i:s');

        $prize = 0;


        $remaining = DB::table('total_winner')

            ->where('is_active', '1')

            ->get();

        $valid_winners = DB::table('scratch_form')
            ->where('result', 'winner')
            ->where('is_active', '1')
            ->count();

        // dd($valid_winners);  

        $total_winners = $remaining[0]->total_winners;

        //dd($total_winners);

        $perday = DB::table('perday_rule')

            ->where('is_active', '1')

            ->get();

        //dd($perday[0]->count);

        // dd($time);

        // $timeslot =  DB::select('SELECT * FROM timeslot WHERE is_active = "1" AND (start_time < end_time AND NOW() BETWEEN start_time AND end_time) OR (end_time < start_time AND NOW() < start_time AND NOW() < end_time) OR (end_time < start_time AND NOW() > start_time) LIMIT 1 ');


        $timeslot = DB::table('timeslot')

            ->where(DB::raw('start_time'), '<=',  $time)

            ->Where(DB::raw('end_time'), '>=', $time)

            ->where('is_active',  '1')

            ->limit(1)

            ->get();


        $hour_check = DB::table('scratch_form')
            ->where('result', 'winner')
            ->where('is_active', '1')
            ->whereDate('date', date('Y-m-d'))
            ->whereNotNull('price')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get();


        $check_count = DB::table('scratch_form')
            ->where('date', $date)
            ->where('result',  'winner')
            ->where('is_active',  '1')
            ->count();

        $schedule_count = DB::table('schedule')
            ->where('date', $date)
            ->where('is_active',  '1')
            ->get();

        $sum = array();

        $balance_prize = array();
        $pending_prize = array();

        //dd($schedule_count);

        foreach ($schedule_count as $count) {

            //dd($count->id);

            $check_balance = DB::table('scratch_form')
                ->where('date', $date)
                ->where('price', $count->prize)
                ->where('schedule_id', $count->id)
                ->where('result',  'winner')
                ->where('is_active',  '1')
                ->count();

            //dd($check_balance .'-'. $count->count); 

            if ($check_balance == $count->count) {
                //dd($check_balance);
                $balance_prize[] = $count->id;
            }

            if ($check_balance !== $count->count) {
                //dd($check_balance);
                $pending_prize[] = $count->id;
                break;
            }
        }

        //dd($balance_prize);
        //dd($pending_prize);


        foreach ($schedule_count as $count) {


            $sum[] = $count->count;
        }

        $array_sum = array_sum($sum);

        //dd($array_sum);

        // if($timeslot->isNotEmpty())
        // {
        //  $time1 = strtotime($time);
        //  $time2 = strtotime($timeslot[0]->end_time);
        //  $difference = round(abs($time2 - $time1) / 3600,2); 

        //  $remaining_win = ($array_sum - $check_count);

        //  //dd($remaining_win);
        //  $remaining_minutes = 0;

        //  if($remaining_win >0)
        //  {
        //      $remaining_minutes = $difference / $remaining_win * 60 ;    
        //  }

        //      // dd($remaining_minutes);
        // }


        //  if($hour_check->isNotEmpty() ) {

        //     $minutes = (strtotime($time) - strtotime($hour_check[0]->time) ) / 60;

        //  } 

        //  if($hour_check->isEmpty() && $timeslot->isNotEmpty() ){

        //   $default_minute = round(abs(strtotime($timeslot[0]->start_time) - strtotime($timeslot[0]->end_time) ) / 3600,2);

        //   $minutes = $default_minute / $array_sum * 60 ;


        //  }

        // dd($minutes);


        if ($date >= $remaining[0]->start_date && $date <= $remaining[0]->end_date && $valid_winners < $total_winners) {

            // if ($date >= $remaining[0]->start_date && $date <= $remaining[0]->end_date && $valid_winners < $total_winners && $perday_count < $perday[0]->count && $timeslot[0]->per_hour > $perhour_count ) 
            // {
            //dd('w');

            $matchThese = ['is_active' => '1', 'is_attempt' => '0', 'receipt_no' => $receipt];

            $prewinner = $prewinner->with('gift')->where($matchThese)->get();


            if ($prewinner->isNotEmpty()) {
                //dd($remaining);

                $prize = $prewinner[0]->gift[0]->id;

                $value = rand(10, 100);

                $type = $prewinner[0]->gift[0]->name;

                $status = "winner";


                DB::table('gift_details')
                    ->where('id', $prewinner[0]->gift[0]->id)
                    ->decrement('remaining_gifts', 1);

                // DB::table('total_winner')
                // ->decrement('remaining_winners',1);


                //DB::table('result')->insert($result);

                $update_form = DB::table('scratch_form')
                    ->where('id', $id)
                    ->update([
                        'status' => 'winner',
                        'date' => $date,
                        'time' => $time,
                        'result' => 'winner',
                        'price' => $prize,
                        'value' => $value,
                        'is_active' => '1',
                        'updated_at' => Carbon::now(),
                        'reason' => 'pre_winner',
                    ]);

                DB::table('prewinner')
                    ->where('receipt_no', $receipt)
                    ->update([
                        'is_attempt' => '1',
                        'updated_at' => Carbon::now()
                    ]);

                //session()->forget('insert_id');  

                Session::put('type', $type);

                Session::put('winner', '1');


                $data = array('type' => $type, 'value' => $value);


                if ($update_form) {
                    $bcc = ['ajithrockers007@gmail.com', 'hemkumar7631@gmail.com'];

                    Mail::send('scratch.mail', $data, function ($message) use ($user_email, $bcc) {
                        $message->to($user_email);
                        $message->subject('Al Ain - Scan & Win Promotion - We are reviewing your entry');
                        // $message->bcc($bcc);
                        $message->from('alainwaterwinner@thethoughtfactory.ae', 'Alain Water');
                    });
                    session()->forget('insert_id');
                    \Log::info('mail sended to-' . $user_email);
                    return redirect()->route('result.index')->with(['status' => $prize, 'type' => $type]);
                }
            }


            $randomGift = collect([]);

            //Allocate previous day missing gifts
            if (date('H:i:s') < '23:59:59') {

                //dd(0);

                $previous_date = date('Y-m-d', strtotime("-1 days"));

                $previous_day_records = DB::table('schedule')
                    ->where('date', $previous_date)
                    ->where('is_active',  '1')
                    ->get();

                //dd($previous_day_records);

                foreach ($previous_day_records as $previous) {

                    $check_previous = DB::table('scratch_form')
                        //->where('date', $previous_date)
                        ->where('schedule_id', $previous->id)
                        ->where('price', $previous->prize)
                        ->where('result',  'winner')
                        ->where('is_active',  '1')
                        ->count();

                    //dd($check_previous);

                    if ($previous->count !== $check_previous) {
                        $randomGift = DB::table('gift_details')

                            ->select('gift_details.*', 'schedule.count as prize_count', 'schedule.start_time as s_s_time', 'schedule.end_time as s_e_time', 'schedule.id as schedule_id', 'schedule.date as s_date')

                            ->leftJoin('schedule', 'schedule.prize', '=', 'gift_details.id')

                            ->where('schedule.date', $previous_date)

                            ->where('schedule.is_active', 1)

                            ->where('schedule.id', $previous->id)

                            ->where('gift_details.id', $previous->prize)

                            ->where('gift_details.is_active', '1')

                            ->where('gift_details.remaining_gifts', '>=', 1)

                            // ->whereNotIn('gift_details.id', $balance_prize)

                            ->inRandomOrder()

                            ->limit(1)

                            ->get();

                        //dd($randomGift);

                        if ($randomGift->isNotEmpty()) {
                            break;
                        }
                    }
                }
            }


            if ($timeslot->isNotEmpty() &&  $schedule_count->isNotEmpty()) {
                //dd('enter');

                // Allocate today missing gifts
                if ($randomGift->isEmpty()) {

                    $past_count = DB::table('schedule')
                        ->where('date', $date)
                        ->where('is_active',  '1')
                        ->where(DB::raw('start_time'), '<',  $time)
                        ->Where(DB::raw('end_time'), '<', $time)
                        ->get();

                    //dd($past_count);

                    $sum = array();
                    $randomGift = collect([]);

                    foreach ($past_count as $p_count) {

                        $check_balance = DB::table('scratch_form')
                            ->where('date', $date)
                            ->where('schedule_id', $p_count->id)
                            ->where('price', $p_count->prize)
                            ->where('result',  'winner')
                            ->where(DB::raw('time'), '<',  $time)
                            ->where('is_active',  '1')
                            ->count();

                        //dd($check_balance);

                        if ($p_count->count !== $check_balance) {
                            //dd($p_count->count.' - '.$check_balance);

                            $randomGift = DB::table('gift_details')

                                ->select('gift_details.*', 'schedule.count as prize_count', 'schedule.start_time as s_s_time', 'schedule.end_time as s_e_time', 'schedule.id as schedule_id', 'schedule.date as s_date')

                                ->leftJoin('schedule', 'schedule.prize', '=', 'gift_details.id')

                                ->where('schedule.date', $date)

                                ->where('schedule.is_active', 1)

                                ->where('schedule.id', $p_count->id)

                                ->where('gift_details.id', $p_count->prize)

                                ->where('gift_details.is_active', '1')

                                ->where('gift_details.remaining_gifts', '>=', 1)

                                // ->whereNotIn('gift_details.id', $balance_prize)

                                ->where(DB::raw('schedule.start_time'), '<',  $time)

                                ->Where(DB::raw('schedule.end_time'), '<', $time)

                                ->inRandomOrder()

                                ->limit(1)

                                ->get();

                            //dd($randomGift);

                            if ($randomGift->isNotEmpty()) {
                                break;
                            }
                        }

                        $sum[] = $check_balance;
                    }

                    //dd($sum);

                }

                if ($randomGift->isNotEmpty()) {
                    //dd(1);
                    //dd($randomGift);

                }

                //dd($balance_prize);

                //Allocate current time gifts
                if ($randomGift->isEmpty()) {
                    //dd(2);
                    $randomGift = DB::table('gift_details')

                        ->select('gift_details.*', 'schedule.count as prize_count', 'schedule.start_time as s_s_time', 'schedule.end_time as s_e_time', 'schedule.id as schedule_id', 'schedule.date as s_date')

                        ->leftJoin('schedule', 'schedule.prize', '=', 'gift_details.id')

                        ->where('schedule.date', $date)

                        ->where('schedule.is_active', 1)

                        ->where('gift_details.is_active', '1')

                        ->where('gift_details.remaining_gifts', '>=', 1)

                        ->whereNotIn('schedule.id', $balance_prize)

                        ->whereIn('schedule.id', $pending_prize)

                        ->where(DB::raw('schedule.start_time'), '<=',  $time)

                        ->Where(DB::raw('schedule.end_time'), '>=', $time)

                        ->inRandomOrder()

                        ->limit(1)

                        ->get();
                }

                //dd($randomGift); 


                if ($randomGift->isNotEmpty()) {

                    //dd($randomGift);


                    $winning_count = DB::table('scratch_form')
                        ->where('scratch_form.first_name', $first_name)
                        ->where('scratch_form.last_name', $last_name)
                        ->where('scratch_form.result', 'winner')
                        ->where('scratch_form.is_active', '1')
                        ->orWhere('scratch_form.phone', $user_phone)
                        ->where('scratch_form.result', 'winner')
                        ->where('scratch_form.is_active', '1')
                        // ->where('scratch_form.receipt_number', $receipt)
                        // ->where('scratch_form.result','winner')
                        // ->where('scratch_form.is_active','1')
                        ->count();


                    //dd($winning_count);

                    $max_win = DB::table('maximum_win')
                        ->where('is_active',  '1')
                        ->get();

                    //dd($max_win[0]->count);

                    $check_count1 = DB::table('scratch_form')
                        ->where('date', $date)
                        ->where('schedule_id', $randomGift[0]->schedule_id)
                        ->where('price', $randomGift[0]->id)
                        ->where('result',  'winner')
                        ->where('is_active',  '1')
                        ->count();

                    //dd($check_count1.' '.$randomGift[0]->prize_count);

                    $prize = $randomGift[0]->id;
                    $value = rand(10, 100);
                    $type = $randomGift[0]->name;
                    //dd($type);    

                    if ($winning_count < $max_win[0]->count && $check_count1 < $randomGift[0]->prize_count) {
                        //dd('entering');

                        if ($randomGift[0]->remaining_gifts >= 1) {
                            $random = rand(0, 1);

                            $condition_check = DB::table('scratch_form')
                                ->where('result', 'winner')
                                ->where('is_active', '1')
                                ->whereDate('date', date('Y-m-d'))
                                ->whereNotNull('price')
                                ->orderBy('id', 'DESC')
                                ->limit(1)
                                ->get();

                            //dd($condition_check);

                            if ($condition_check->isEmpty()) {

                                $status = "winner";
                                $result = "winner";


                                $winning_count = DB::table('scratch_form')
                                    ->where('scratch_form.first_name', $first_name)
                                    ->where('scratch_form.last_name', $last_name)
                                    ->where('scratch_form.result', 'winner')
                                    ->where('scratch_form.is_active', '1')
                                    ->orWhere('scratch_form.phone', $user_phone)
                                    ->where('scratch_form.result', 'winner')
                                    ->where('scratch_form.is_active', '1')
                                    // ->where('scratch_form.receipt_number', $receipt)
                                    // ->where('scratch_form.result','winner')
                                    // ->where('scratch_form.is_active','1')
                                    ->count();

                                //dd($winning_count);

                                //Check again participant already win or not
                                if ($winning_count < $max_win[0]->count) {
                                    Session::put('winner', '1');

                                    DB::table('gift_details')
                                        ->where('id', $randomGift[0]->id)
                                        ->decrement('remaining_gifts', 1);

                                    $update_form = DB::table('scratch_form')
                                        ->where('id', $id)
                                        ->update([
                                            'status' => 'winner',
                                            'date' => $date,
                                            'time' => $time,
                                            'result' => 'winner',
                                            'price' => $prize,
                                            'value' => $value,
                                            'schedule_id' => $randomGift[0]->schedule_id,
                                            'is_active' => '1',
                                            'updated_at' => Carbon::now(),
                                            'reason' => 'first_winner',

                                        ]);




                                    $data = array('type' => $type, 'value' => $value);

                                    if ($update_form) {
                                        Session::put('type', $type);

                                        $bcc = ['ajithrockers007@gmail.com', 'hemkumar7631@gmail.com'];

                                        Mail::send('scratch.mail', $data, function ($message) use ($user_email, $bcc) {
                                            $message->to($user_email);
                                            $message->subject('Al Ain - Scan & Win Promotion - We are reviewing your entry');
                                            $message->bcc($bcc);
                                            $message->from('alainwaterwinner@thethoughtfactory.ae', 'Alain Water');
                                        });
                                        session()->forget('insert_id');
                                        \Log::info('mail sended to-' . $user_email);

                                        return redirect()->route('result.index')->with(['status' => $prize, 'type' => $type]);
                                    }
                                } else {

                                    $status = "lost";
                                    $result = "lost";
                                    $prize = 0;
                                    $value = 0;


                                    DB::table('scratch_form')
                                        ->where('id', $id)
                                        ->update([
                                            'status' => 'lost',
                                            'date' => $date,
                                            'time' => $time,
                                            'result' => "lost",
                                            'value' => $value,
                                            'is_active' => '1',
                                            'updated_at' => Carbon::now(),
                                            'reason' => 'already won 1',
                                        ]);

                                    return redirect()->route('result.index')->with(['status' => $prize]);
                                }
                            }

                            if ($condition_check->isNotEmpty()) {

                                $loss_count = DB::table('scratch_form')

                                    //->where( DB::raw('time'), '<=',  $condition_check[0]->time )

                                    ->whereTime('time', '>', $condition_check[0]->time)

                                    ->whereDate('date', date('Y-m-d'))

                                    ->where('is_active',  '1')

                                    // ->where('result','lo')


                                    ->count();

                                //dd($loss_count);

                                if ($loss_count >= 0) {

                                    // dd("enter");

                                    $status = "winner";
                                    $result = "winner";

                                    $winning_count = DB::table('scratch_form')
                                        ->where('scratch_form.first_name', $first_name)
                                        ->where('scratch_form.last_name', $last_name)
                                        ->where('scratch_form.result', 'winner')
                                        ->where('scratch_form.is_active', '1')
                                        ->orWhere('scratch_form.phone', $user_phone)
                                        ->where('scratch_form.result', 'winner')
                                        ->where('scratch_form.is_active', '1')
                                        // ->where('scratch_form.receipt_number', $receipt)
                                        // ->where('scratch_form.result','winner')
                                        // ->where('scratch_form.is_active','1')
                                        ->count();

                                    //Check again participant already win or not
                                    if ($winning_count < $max_win[0]->count) {

                                        Session::put('winner', '1');

                                        DB::table('gift_details')
                                            ->where('id', $randomGift[0]->id)
                                            ->decrement('remaining_gifts', 1);

                                        $update_form = DB::table('scratch_form')
                                            ->where('id', $id)
                                            ->update([
                                                'status' => 'winner',
                                                'date' => $date,
                                                'time' => $time,
                                                'result' => 'winner',
                                                'price' => $prize,
                                                'value' => $value,
                                                'schedule_id' => $randomGift[0]->schedule_id,
                                                'is_active' => '1',
                                                'updated_at' => Carbon::now(),
                                                'reason' => 'next_winner',
                                            ]);

                                        $data = array('type' => $type, 'value' => $value);

                                        if ($update_form) {
                                            Session::put('type', $type);

                                            $bcc = ['ajithrockers007@gmail.com', 'hemkumar7631@gmail.com'];


                                            Mail::send('scratch.mail', $data, function ($message) use ($user_email, $bcc) {
                                                $message->to($user_email);
                                                $message->subject('Al Ain - Scan & Win Promotion - We are reviewing your entry');
                                                $message->bcc($bcc);
                                                $message->from('alainwaterwinner@thethoughtfactory.ae', 'Alain Water');
                                            });
                                            session()->forget('insert_id');
                                            \Log::info('mail sended to-' . $user_email);

                                            return redirect()->route('result.index')->with(['status' => $prize, 'type' => $type]);
                                        }
                                    } else {

                                        $status = "lost";
                                        $result = "lost";
                                        $prize = 0;
                                        $value = 0;


                                        DB::table('scratch_form')
                                            ->where('id', $id)
                                            ->update([
                                                'status' => 'lost',
                                                'date' => $date,
                                                'time' => $time,
                                                'result' => "lost",
                                                'value' => $value,
                                                'is_active' => '1',
                                                'updated_at' => Carbon::now(),
                                                'reason' => 'already won 2',
                                            ]);

                                        return redirect()->route('result.index')->with(['status' => $prize]);
                                    }
                                }

                                /* if($loss_count < 2)
                                {
                                    //dd('loss');

                                    $status = "lost";
                                    $prize = 0;
                                    $value = 0;

                                    $result = array(
                                        'scratch_form_id' =>  $id,
                                        'document_id' => $document_id,
                                        'date' => $date,
                                        'time' => $time,
                                        'ip_address' => $ip_address,
                                        'result' => "lost",
                                        'value' => $value,
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now()
                                    );
                                }*/
                            }
                        }
                    }


                    //Already won
                    if ($winning_count >= $max_win[0]->count || $check_count1 >= $randomGift[0]->prize_count) {
                        //dd('already won');

                        $status = "lost";
                        $result = "lost";
                        $prize = 0;
                        $value = 0;


                        DB::table('scratch_form')
                            ->where('id', $id)
                            ->update([
                                'status' => 'lost',
                                'date' => $date,
                                'time' => $time,
                                'result' => "lost",
                                'value' => $value,
                                'is_active' => '1',
                                'updated_at' => Carbon::now(),
                                'reason' => 'already_won',
                            ]);

                        return redirect()->route('result.index')->with(['status' => $prize]);
                    }
                }

                //Schedule gift completed
                if ($randomGift->isEmpty()) {
                    //dd('loss');

                    $prize = 0;
                    $value = 0;

                    DB::table('scratch_form')
                        ->where('id', $id)
                        ->update([
                            'status' => 'lost',
                            'date' => $date,
                            'time' => $time,
                            'result' => "lost",
                            'value' => $value,
                            'is_active' => '1',
                            'updated_at' => Carbon::now(),
                            'reason' => 'schedule completed',
                        ]);

                    return redirect()->route('result.index')->with(['status' => $prize]);
                }
            }

            //Schedule not exist
            if ($timeslot->isEmpty() ||  $schedule_count->isEmpty()) {
                //dd('loss');

                $prize = 0;
                $value = 0;


                DB::table('scratch_form')
                    ->where('id', $id)
                    ->update([
                        'status' => 'lost',
                        'date' => $date,
                        'time' => $time,
                        'result' => "lost",
                        'value' => $value,
                        'is_active' => '1',
                        'updated_at' => Carbon::now(),
                        'reason' => 'schedule not exist',
                    ]);

                return redirect()->route('result.index')->with(['status' => $prize]);
            }
        }

        //winners greater than total winners
        if ($valid_winners >= $total_winners) {
            //dd('loss');
            $prize = 0;
            $value = 0;

            DB::table('scratch_form')
                ->where('id', $id)
                ->update([
                    'status' => 'lost',
                    'date' => $date,
                    'time' => $time,
                    'result' => "lost",
                    'value' => $value,
                    'is_active' => '1',
                    'updated_at' => Carbon::now(),
                    'reason' => 'winners gt total winners',
                ]);

            return redirect()->route('result.index')->with(['status' => $prize]);
        }

        //Campain date completed
        if ($date >= $remaining[0]->end_date || $date < $remaining[0]->start_date) {

            //dd("loss..");

            $prize = 0;
            $value = 0;


            DB::table('scratch_form')
                ->where('id', $id)
                ->update([
                    'status' => 'lost',
                    'date' => $date,
                    'time' => $time,
                    'result' => "lost",
                    'value' => $value,
                    'is_active' => '1',
                    'updated_at' => Carbon::now(),
                    'reason' => 'campaign completed',
                ]);

            return redirect()->route('result.index')->with(['status' => $prize]);
        }
    }
}
