<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class References extends Model
{
    //
    protected $table = 'references';

    public static function getAllReferences($perPage = '',$params = array())
    {        
        $referenceOb=self::select();
        $referenceOb->orderBy($params['order_column'],$params['order_dir']);        
        return $referenceOb->paginate($perPage);
    }
}
