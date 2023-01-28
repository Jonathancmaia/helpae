<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Service;
use App\Location;
use DB;

class attVips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attVips:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove one day vip of all users.';

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
     * @return int
     */
    public function handle()
    {
        $users = User::all();

        $services = new Service;
        $locations = new Location;

        foreach($users as $user){
            
            if ($user->isVip > 0){
                $user->isVip = $user->isVip - 1;

                //if vip is ended
                if ($user->isVip == 0){

                    //if user has more than 3 annouces, turn it suspended
                    $announces = DB::table('services')->where('user_id', $user->id)->count() + DB::table('locations')->where('user_id', $user->id)->count();  

                    if ($announces > 3){

                        $allAnnounces = $services->
                        where('user_id', $user->id)->
                        where('suspended', false)->
                        union(
                            $locations->
                            where('user_id', $user->id)->
                            where('suspended', false)
                        )->latest()->get();
                        
                        $announcesCounter = 0;
                        
                        foreach($allAnnounces as $announce){

                            $announcesCounter++;

                            if ($announcesCounter >= 3){
                                $announce->suspended = true;
                                $announce->save();
                            }
                        }
                    }
                }
            }

            $user->save();
        }
    }
}
