<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\TaxRates;
use Illuminate\Database\Eloquent\SoftDeletes;
class CommodityCodes extends Model
{
    use SoftDeletes; 
    protected $table = 'commodity_codes';
  
    /**
     * 
     * @return type
     */
    public function taxRates(){
        return $this->hasMany(TaxRates::class,'commodity_code_id');
    }

    public static function getAllCodes($perPage = '',$params = array()){
        
        $codesOb=self::select();
        $codesOb->orderBy($params['order_column'],$params['order_dir']);
        if (!empty($params['search'])) {
           $codesOb->where('code','like',"%".$params['search']."%");
           if(strcasecmp($params['search'],"yes")==0 || strcasecmp($params['search'],"no")==0)
           {
           		if(strcasecmp($params['search'],"yes")==0)
           		{
           			$is_default=1;
           		}
           		else
           		{
           			$is_default=0;
           		}
           		
           		$codesOb->orWhere('is_default','like',"%".$is_default."%");
           }
          
        }
        return $codesOb->paginate($perPage);
    }

    public function importduty()
    {
        return $this->hasOne('App\ImportDuty','commodity_code_id');
    }
    
    public function importDuties()
    {
        return $this->hasMany('App\ImportDuty','commodity_code_id');
    }
     public function delete()
    {
        // delete all related photos 
        $this->importDuties()->delete();
        // as suggested by Dirk in comment,
        // it's an uglier alternative, but faster
        // Photo::where("user_id", $this->id)->delete()

        // delete the user
        return parent::delete();
    }
}
