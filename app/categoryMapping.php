<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CategoryMapping extends Model
{
    //
 	protected $table = 'category_mappings';
    public static function getAllMapping($perPage = '',$params = array())
    {
    	$cat=self::select('range_master.category_name','range_master.path','magento_categories.structure','category_mappings.*','magento_categories.name');
    	$cat->leftJoin('range_master', 'range_master.id', 'category_mappings.range_id');
    	$cat->leftJoin('magento_categories', 'magento_categories.id', 'category_mappings.magento_category_id');
    	//$cat->orderBy("category_mappings.range_id",'ASC');
    	$cat->orderBy($params['order_column'],$params['order_dir']);
    	if (!empty($params['search'])) {
           $cat->orWhere('magento_categories.name','like',"%".$params['search']."%");
           $cat->orWhere('range_master.category_name','like',"%".$params['search']."%");
	   }
	    return $cat->paginate($perPage);
	}

}
