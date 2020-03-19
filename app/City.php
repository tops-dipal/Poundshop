<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name', 'state_id'
    ];
    
     /**
     * @author : Hitesh Tank
     * @return type
     */
    public function getAttributeName(){
        return (isset($this->attributes['name']) && !empty($this->attributes['name']) ) ? $this->attributes['name'] : '';
    }
}
