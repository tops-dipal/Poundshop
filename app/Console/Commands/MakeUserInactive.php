<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use DB;

class MakeUserInactive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inactive:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If Someone do not login in 90 days ,inactive him Automatically (For Users)';

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
       $users=User::where(DB::raw('DATEDIFF(NOW(),last_login_date)'),'>=',90)->where('status',1)->pluck('id');
        User::whereIn('id',$users)->update(['status'=>2]);
    }
}
