<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoVerTheOption extends Model
{
	public $timestamps = false;
	protected $fillable = ['magento_attribute_id','magento_variation_theme_id','magento_option_id','option_value'];
    protected $table = 'magento_variation_theme_options';
}
