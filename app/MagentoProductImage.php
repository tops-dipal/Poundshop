<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoProductImage extends Model
{
	public $timestamps = false;
	protected $fillable = ['magento_id','magento_product_id','image_url','image_file','inserted_date'];
    protected $table = 'magento_product_images';
}
