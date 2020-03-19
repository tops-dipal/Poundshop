<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $fillable = [
        'name','country_id'
    ];

    protected $table = 'states';
    
     /**
     * @author : Hitesh Tank
     * @return type
     */
    public function getAttributeName(){
        return (isset($this->attributes['name']) && !empty($this->attributes['name']) ) ? $this->attributes['name'] : '';
    }
}
