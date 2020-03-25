<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoProduct extends Model
{
    protected $table = 'magento_products';
    const CREATED_AT = 'inserted_date';
    const UPDATED_AT = 'modified_date';
    public function getMainImageUrlAttribute(){
        if(!empty($this->attributes['main_image_url']))
            return $this->attributes['main_image_url'];
        else
            return url('/img/no-image.jpeg');
    }

    public static function revise_valid_product($magento_id)
    {
            
        $object = Self::select('magento_products.*');

        $object->where(function($q) use ($magento_id){ 
                        $q->where('magento_products.id', $magento_id);
                        $q->whereIn('magento_products.product_type', ['normal', 'parent']);
                    });

        return $object->first();
    }

    public function get_product_list($store_id)
    {
        $product_list = Self::select('magento_quantity_log.id as log_id',
				'magento_quantity_log.quantity',
                'magento_quantity_log.magento_id',                
				'magento_products.id',
				'magento_products.sku')
        	->where('magento_products.store_id', $store_id)
        ->where(function ($query) {
               $query->Where('magento_quantity_log.is_quantity_posted', '=', '0');
        })        
	    ->Join('magento_quantity_log', 'magento_quantity_log.magento_id', '=', 'magento_products.id')
	    ->get();	    
	    return $product_list;
    }

    function get_price_product_list($store_id)
    {
        $product_list = Self::select('magento_price_log.id as log_id',
                'magento_price_log.selling_price',
                'magento_price_log.magento_id',                
                'magento_products.id',
                'magento_products.sku')
            ->where('magento_products.store_id', $store_id)
        ->where(function ($query) {
               $query->Where('magento_price_log.is_selling_price_posted', '=', '0');
        })        
        ->Join('magento_price_log', 'magento_price_log.magento_id', '=', 'magento_products.id')
        ->get();        
        return $product_list;
    }

    function get_magento_products($limit)
    {
        $product_list = Self::select('magento_products.*')
            ->selectRaw('GROUP_CONCAT(magento_variation_theme.variation_theme_name SEPARATOR "|||") as variation_theme')
            ->selectRaw('GROUP_CONCAT(magento_variation_theme_options.option_value SEPARATOR "|||") as variation_theme_value')
            ->leftJoin('magento_products as magento_products_parent','magento_products.parent_id','=','magento_products_parent.id')
            ->leftJoin('magento_variation_theme_options_mapping','magento_variation_theme_options_mapping.product_id','=','magento_products.id')
            ->leftJoin('magento_variation_theme_options','magento_variation_theme_options.id','=','magento_variation_theme_options_mapping.variation_theme_option_id')
            ->leftJoin('magento_variation_theme','magento_variation_theme.id','=','magento_variation_theme_options_mapping.variation_theme_id')
            ->where('magento_products.is_detail_processed',"1")
            ->where('magento_products.product_id','0')
            ->groupBy('magento_products.id')
            ->limit($limit)
            ->get();
        return $product_list;        
    }


    public function magentoPriceLog()
    {
        return $this->hasMany(\App\MagentoPriceLog::class, 'magento_id', 'id');
    }
    public function magentoQtyLog()
    {
        return $this->hasMany(\App\MagentoQtyLog::class, 'magento_id', 'id');
    }

    public function getMagentoVariationTheme($parentIds) 
    {
        $product_list = Self::select('magento_products.id','magento_products.parent_id')
            ->selectRaw('GROUP_CONCAT(magento_variation_theme.variation_theme_name SEPARATOR "|||") as variation_theme')            
            ->leftJoin('magento_variation_theme_options_mapping','magento_variation_theme_options_mapping.product_id','=','magento_products.id')
            ->leftJoin('magento_variation_theme','magento_variation_theme.id','=','magento_variation_theme_options_mapping.variation_theme_id')
            ->whereIn('magento_products.parent_id',$parentIds)            
            ->groupBy('magento_products.id')
            ->orderBy('magento_products.id','asc')
            ->orderBy('magento_variation_theme.variation_theme_name','asc')
            ->get();
        return $product_list; 
    }

    public function magento_product_images()
    {
        return $this->hasMany(MagentoProductImage::class,'magento_id');
    }
}
