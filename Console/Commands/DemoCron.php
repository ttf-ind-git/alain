<?php
   
namespace App\Console\Commands;
   
use Illuminate\Console\Command;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
   
class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");
     
        /*
           Write your database logic we bellow:
           Item::create(['name'=>'hello new']);
        */

        $psw = 'secret';

        $length = 5;    
        $email = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'),1,$length);

        User::create([
            'name' => 'Ajith',
            'email' => $email.'@gmail.com',
        	'role_id'=> '2',
            'password' => Hash::make($psw),
        ]);
      
        $this->info('Demo:Cron Cummand Run successfully!');
    }
}