<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cron extends Model
{
    protected $table = 'cron_log';    	
    public $timestamps = false;
}
