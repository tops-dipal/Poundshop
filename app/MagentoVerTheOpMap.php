<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoVerTheOpMap extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['variation_theme_id','variation_theme_option_id','product_id','parent_product_id'];
    protected $table = 'magento_variation_theme_options_mapping';
}
