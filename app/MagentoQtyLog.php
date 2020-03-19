<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoQtyLog extends Model
{
    //
    protected $table = 'magento_quantity_log';
     const CREATED_AT = 'inserted_date';
    const UPDATED_AT = 'modified_date';

    public function magentoProducts()
    {
    	return $this->belongsTo('App\MagentoProduct','magento_id');
    }
}
