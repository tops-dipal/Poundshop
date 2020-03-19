<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Pallet extends Model
{
    //
    protected $table = 'pallets_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guards;
    
    /**
     * Display a listing of the Contient.
     * @author : Hitesh Tank
     * @param Int $page
     * @param String $sorting_on
     * @param String $sorting_by (Possible value ASC or DESC)
     * @return \Illuminate\Http\Response
     */
    public static function getAllPallets($perPage = '',$params = array())
    {        
        $palletOb=self::select();
        $palletOb->orderBy($params['order_column'],$params['order_dir']);
        if (!empty($params['search'])) 
        {
           $palletOb->where('name','like',"%".$params['search']."%");
           $palletOb->orWhere('length','like',"%".$params['search']."%");
             $palletOb->orWhere('width','like',"%".$params['search']."%");
              $palletOb->orWhere('height','like',"%".$params['search']."%");
               $palletOb->orWhere('max_weight','like',"%".$params['search']."%");
                $palletOb->orWhere('quantity','like',"%".$params['search']."%");
                if(strcasecmp($params['search'],"yes")==0 || strcasecmp($params['search'],"no")==0)
                {
                    if(strcasecmp($params['search'],"yes")==0)
                    {
                        $returnable=1;
                        $sellable=1;
                    }
                    else
                    {
                        $returnable=0;
                        $sellable=0;
                    }
                   
                     $palletOb->orWhere('returnable','like',"%".$returnable."%");
                     $palletOb->where('sellable','like',"%".$sellable."%");
                }
                

        }
        
        return $palletOb->paginate($perPage);
    }
}
