<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
    use SoftDeletes; 
    protected $table = 'settings';
    protected $fillable = ['column_val'];
    
    
    public static function getData($data){
        return self::whereIn('module_name',$data)->get();
    }
}
