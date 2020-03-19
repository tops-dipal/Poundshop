<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoVerTheme extends Model
{
	public $timestamps = false;
	protected $fillable = ['magento_attribute_id','variation_theme_name','attribute_code'];
    protected $table = 'magento_variation_theme';
}
