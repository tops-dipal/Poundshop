<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cartons extends Model
{
     protected  $table = "carton_master";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guards;
    
    /**
     * Display a listing of the cartons.
     * @author : Hitesh Tank
     * @param Int $page
     * @param String $sorting_on
     * @param String $sorting_by (Possible value ASC or DESC)
     * @return \Illuminate\Http\Response
     */
    public static function getAllCartons($perPage = '',
                                $params = array()){
        
        $cartonOb=self::select();
        $cartonOb->orderBy($params['order_column'],
                              $params['order_dir']);
        if (!empty($params['search'])) {
           $cartonOb->where('name','like',"%".$params['search']."%");
            $cartonOb->orWhere('length','like',"%".$params['search']."%");
             $cartonOb->orWhere('width','like',"%".$params['search']."%");
              $cartonOb->orWhere('height','like',"%".$params['search']."%");
               $cartonOb->orWhere('max_weight','like',"%".$params['search']."%");
                $cartonOb->orWhere('quantity','like',"%".$params['search']."%");
                if(strcasecmp($params['search'],"yes")==0 || strcasecmp($params['search'],"no")==0)
                {
                    if(strcasecmp($params['search'],"yes")==0)
                    {
                        $search=1;
                    }
                    else
                    {
                        $search=0;
                    }
                   
                     $cartonOb->orWhere('recycle_carton','like',"%".$search."%");
                }
                

        }
        return $cartonOb->paginate($perPage);
    }
}
