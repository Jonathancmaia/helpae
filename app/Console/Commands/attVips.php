<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

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

        foreach($users as $user){
            if ($user->isVip > 0){
                $user->isVip = $user->isVip - 1;
            }
            $user->save();
        }
    }
}
