<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Totes extends Model
{
    use SoftDeletes; 
    protected $table = 'totes_master';

    public static function getAllTotes($perPage = '',
                                $params = array()){
        
        $totesOb=self::select();
        $totesOb->orderBy($params['order_column'],$params['order_dir']);
        if (!empty($params['search'])) {
           $totesOb->where('name','like',"%".$params['search']."%");
           $totesOb->orWhere('length','like',"%".$params['search']."%");
           $totesOb->orWhere('width','like',"%".$params['search']."%");
           $totesOb->orWhere('height','like',"%".$params['search']."%");
           $totesOb->orWhere('max_volume','like',"%".$params['search']."%");
           $totesOb->orWhere('max_weight','like',"%".$params['search']."%");
           $totesOb->orWhere('quantity','like',"%".$params['search']."%");
           if(strcasecmp($params['search'],"nextday")==0 || strcasecmp($params['search'],"next day")==0 || strcasecmp($params['search'],"standard")==0 || strcasecmp($params['search'],"european")==0)
           {
           		if(strcasecmp($params['search'],"nextday")==0 || strcasecmp($params['search'],"next day")==0)
           		{
           			$category=1;
           		}
           		else if(strcasecmp($params['search'],"standard")==0)
           		{
           			$category=2;
           		}
           		else if(strcasecmp($params['search'],"european")==0)
           		{
           			$category=3;
           		}
           		$totesOb->orWhere('category','like',"%".$category."%");
           }
           
        }
        return $totesOb->paginate($perPage);
    }
}
