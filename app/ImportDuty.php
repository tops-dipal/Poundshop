<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportDuty extends Model
{
    use SoftDeletes; 
    protected $table = 'import_duty';

    public static function getAllDuty($perPage = '',$params = array()){
        
        $codesOb=self::select('import_duty.*','commodity_codes.code','countries.name');
        $codesOb->orderBy($params['order_column'],$params['order_dir']);
        $codesOb->leftJoin('countries', 'countries.id', 'import_duty.country_id');
         $codesOb->leftJoin('commodity_codes', function($join){
            $join->on('commodity_codes.id', '=', 'import_duty.commodity_code_id')
                  ->whereNull('commodity_codes.deleted_at');
        });
      
        if (!empty($params['search'])) {
           $codesOb->orWhere('import_duty.rate','like',"%".$params['search']."%");
           $codesOb->orWhere('countries.name','like',"%".$params['search']."%");
           $codesOb->orWhere('commodity_codes.code','like',"%".$params['search']."%");
        }
        //echo $codesOb->toSQL();exit;
        return $codesOb->paginate($perPage);
    }

    public function commodityCode()
    {
        return $this->belongsTo('App\CommodityCodes');
    }

    public function country()
    {
        return $this->belongsTo('App\Country');
    }
    
    
    public static function getImportDutyValue($importDuties,$item){
        
        $commodityCode=$item->products->commodity_code_id;
        $qry=$importDuties; //->wherePivot('commodity_code_id', $commodityCode);
 
        if(!empty($commodityCode)){ // If Product has Commodity Code
            $importDutyValue=$qry->wherePivot('commodity_code_id', $commodityCode)->first();
            if(!empty($importDutyValue) && @count($importDutyValue)>0){ //if country has same commodity code return it
                return $importDutyValue->pivot->rate;
            }else{ //else return 0;
                return 0;
            }
        }else{ // Else Commodity Does have Code
            $DefaultImportDuty=$qry->where('is_default',1)->first();
            if(isset($DefaultImportDuty) && @count($DefaultImportDuty) > 0){ // if default found return default
                return $DefaultImportDuty->pivot->rate;
            }else{ //return 0;
                return 0;
            }
                    
        }
    }
}
