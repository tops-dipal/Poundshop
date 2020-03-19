<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Country;
use App\State;
use App\City;
class WareHouse extends Model
{
    protected $table = 'warehouse_master';
    
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
    public static function getAllWarehouses($perPage = '',$params = array())
    {        
        $warehousesOb=self::select();
        $warehousesOb->orderBy($params['order_column'],$params['order_dir']);
        if (!empty($params['search'])) 
        {
           $warehousesOb->where('name','like',"%".$params['search']."%");           
        }
        return $warehousesOb->paginate($perPage);
    }

    public function getCountry(){
        return $this->belongsTo(Country::class,'country')->withDefault();
    }
    
    public function getState(){
        return $this->belongsTo(State::class,'state')->withDefault();
    }
    
    public function getCity(){
        return $this->belongsTo(City::class,'city')->withDefault();
    }
    
    public static function getWareHouse(){
        return self::select('*')->where('status',1)->orderBy('name','ASC')->get();
    }
    /**
     * @author : Hitesh Tank
     * @return type
     */
    public function getAttributeName(){
        return (isset($this->attributes['name']) && !empty($this->attributes['name']) ) ? $this->attributes['name'] : '';
    }
    
    /**
     * @author : Hitesh Tank
     * @return type
     */
    public function getAttributeAddressLine1(){
        return (isset($this->attributes['address_line1']) && !empty($this->attributes['address_line1']) ) ? $this->attributes['address_line1'] : '';
    }
    
    /**
     * @author : Hitesh Tank
     * @return type
     */
    public function getAttributeAddressLine2(){
        return (isset($this->attributes['address_line2']) && !empty($this->attributes['address_line2']) ) ? $this->attributes['address_line2'] : '';
    }
}
