<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductTags extends Model
{
    
    protected $table = 'product_tags';
    
    protected $fillable = [
    							'id',
    							'product_id',
    							'tag_id',
    							'created_by',
    						];
}
