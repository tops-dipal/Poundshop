<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    //
    protected $table = 'locations_master';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guards;
    
    /**
     * Display a listing of the Contient.
     * @author : Mohit Trivedi
     * @param Int $page
     * @param String $sorting_on
     * @param String $sorting_by (Possible value ASC or DESC)
     * @return \Illuminate\Http\Response
     */
    public static function getAllLocations($perPage = '',$params = array())
    {        
        $palletOb=self::select();
        $palletOb->orderBy($params['order_column'],$params['order_dir']);
        $i=0;
        if (!empty($params['search'])) 
        {
           $palletOb->where(function($q) use($params){ 
               $q->where('aisle','like',"%".$params['search']."%");
               $q->orWhere('rack','like',"%".$params['search']."%");
               $q->orWhere('floor','like',"%".$params['search']."%");
               $q->orWhere('box','like',"%".$params['search']."%");
               $q->orWhere('location','like',"%".$params['search']."%");           
            });            
        }

        if(!empty($params['advance_search']))
        {
            $advance_search_data=$params['advance_search'];
            
            if(!empty($advance_search_data['fil_aisle']))
            {
                $palletOb->where('aisle',$advance_search_data['fil_aisle']);
            }

            if(!empty($advance_search_data['fil_rack']))
            {
                $palletOb->where('rack',$advance_search_data['fil_rack']);
            }

            if(!empty($advance_search_data['fil_floor']))
            {
                $palletOb->where('floor',$advance_search_data['fil_floor']);
            }

            if(!empty($advance_search_data['fil_box']))
            {
                $palletOb->where('box',$advance_search_data['fil_box']);
            }

            if(!empty($advance_search_data['fil_location']))
            {
                $palletOb->where('location',$advance_search_data['fil_location']);
            }

            if(!empty($advance_search_data['fil_site_id']))
            {
                $palletOb->where('site_id',$advance_search_data['fil_site_id']);
            }

            if(!empty($advance_search_data['fil_location_type']))
            {
                $palletOb->where('type_of_location',$advance_search_data['fil_location_type']);
            }

            if(!empty($advance_search_data['fil_status']) || $advance_search_data['fil_status']=='0')
            {
                $palletOb->where('status',$advance_search_data['fil_status']);
            }
        }

        //dd($palletOb->toSql());
        return $palletOb->paginate($perPage);
    }


    public static function getAllLocationsSelected($perPage = '',$params = array())
    {        
        $palletOb=self::select('id');
        $palletOb->orderBy($params['order_column'],$params['order_dir']);
        $i=0;
        if (!empty($params['search'])) 
        {
           $palletOb->where(function($q) use($params){ 
               $q->where('aisle','like',"%".$params['search']."%");
               $q->orWhere('rack','like',"%".$params['search']."%");
               $q->orWhere('floor','like',"%".$params['search']."%");
               $q->orWhere('box','like',"%".$params['search']."%");
               $q->orWhere('location','like',"%".$params['search']."%");           
            });            
        }

        if(!empty($params['advance_search']))
        {
            $advance_search_data=$params['advance_search'];
            
            if(!empty($advance_search_data['fil_aisle']))
            {
                $palletOb->where('aisle',$advance_search_data['fil_aisle']);
            }

            if(!empty($advance_search_data['fil_rack']))
            {
                $palletOb->where('rack',$advance_search_data['fil_rack']);
            }

            if(!empty($advance_search_data['fil_floor']))
            {
                $palletOb->where('floor',$advance_search_data['fil_floor']);
            }

            if(!empty($advance_search_data['fil_box']))
            {
                $palletOb->where('box',$advance_search_data['fil_box']);
            }

            if(!empty($advance_search_data['fil_location']))
            {
                $palletOb->where('location',$advance_search_data['fil_location']);
            }

            if(!empty($advance_search_data['fil_site_id']))
            {
                $palletOb->where('site_id',$advance_search_data['fil_site_id']);
            }

            if(!empty($advance_search_data['fil_location_type']))
            {
                $palletOb->where('type_of_location',$advance_search_data['fil_location_type']);
            }

            if(!empty($advance_search_data['fil_status']) || $advance_search_data['fil_status']=='0')
            {
                $palletOb->where('status',$advance_search_data['fil_status']);
            }
        }

        //dd($palletOb->toSql());
        return $palletOb->paginate($perPage);
    }
}
