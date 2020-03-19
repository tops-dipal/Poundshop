<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $fillable = [
        'name','sortname','phonecode'
    ];
    protected $table = 'countries';

    public function importduty()
    {
        return $this->haseOne('App\ImportDuty');
    }
    
    public function commodityCodes()
    {
        return $this->belongsToMany(CommodityCodes::class,'import_duty','country_id','commodity_code_id')->withPivot('rate');
    }
    
    /**
     * @author : Hitesh Tank
     * @return type
     */
    public function getAttributeName(){
        return (isset($this->attributes['name']) && !empty($this->attributes['name']) ) ? $this->attributes['name'] : '';
    }
    
    
    
}
