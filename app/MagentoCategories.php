<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MagentoCategories extends Model
{
    protected $table = 'magento_categories';

    public static function getAllCategory(){
    	return MagentoCategories::select('category_id as id','id as table_id', 'name', 'parent_id')->orderBy('name','ASC')->get();
    }
}
