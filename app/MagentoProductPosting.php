<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Products;
use DB;

class MagentoProductPosting extends Model
{
    protected $table = 'magento_product_posting';

    protected $guarded = [];

    public function getMainImageUrlAttribute(){
        if(!empty($this->attributes['main_image_url']))
            return $this->attributes['main_image_url'];
        else
            return url('/img/no-image.jpeg');
    }

    public static function posting_valid_product($product_master_id, $store_id)
    {
            
        $object = Products::select('products.*');

        $object->leftJoin('magento_products', function($join) use ($store_id){
            $join->on('magento_products.product_id', '=', 'products.id');
            $join->where('magento_products.store_id', '=', $store_id);
            $join->where('magento_products.is_deleted_product', '=', 0);
        });

        $object->leftJoin('magento_product_posting', function ($join) use ($store_id)
        {
            $join->on('magento_product_posting.product_master_id', '=', 'products.id');
            $join->where('magento_product_posting.store_id', $store_id);
            $join->where('magento_product_posting.is_revised', 0);
        });    

        $object->where(function($q) use ($product_master_id){ 
                        $q->where('products.id', $product_master_id);
                        $q->whereNULL('magento_products.id');
                        $q->whereIn('products.product_type', ['normal', 'parent']);
                        $q->where(function($sub_query){
                            $sub_query->whereNotNull('magento_product_posting.id');
                            $sub_query->orWhere(function ($sub_query2){
                                $sub_query2->where('products.mp_image_missing', 0);
                                $sub_query2->where('products.info_missing', 0);
                            });
                        });
                    });

        return $object->first();
    }

    public static function get_posting_details($product_master_id, $store_id)
    {
        $object = self::select('magento_product_posting.*');
        
        $object->where(function($q) use ($product_master_id, $store_id){
            $q->where('product_master_id', $product_master_id);
            $q->where('store_id', $store_id);
            $q->whereIn('posting_result_status', [0, 3]);
            $q->whereIn('is_posted', [0, 1]);
            $q->where('is_revised', 0);
        });
        
        return $object->first();
    }


    public static function getToBeListedRecords($perPage = '',$params = array())
    {

        
        if(!empty($params['advance_search']))
        {

            $advance_search_data=$params['advance_search'];
            $order_by_season = $advance_search_data['sort_by_season'];
         
        }
        else
        {
            $order_by_season='0';
        }
        

        $select = array('products.id',
                         'products.title',
                        'products.main_image_marketplace',
                        'products.sku',
                        'products.single_selling_price',
                        'magento_products.id as mp_id',
                        'magento_products.product_id',
                        'magento_products.store_id',
                        'magento_product_posting.store_id',
                        'magento_product_posting.is_revised',
                        'magento_product_posting.id as mpp_id',
                        'magento_product_posting.main_image_url as magento_main_image_url',
                        'magento_product_posting.date_to_go_live',
                        'magento_products.is_deleted_product'
                    );

        if($order_by_season == '1')
        {
            $current_year = date('Y');
            $prvious_year = ($current_year == 1) ? '12' : $current_year-1;

            $select[] = DB::raw(
                    "(CASE 
                        WHEN MONTH(CURDATE()) >= MONTH(range_master.seasonal_from) THEN DATE_FORMAT(range_master.seasonal_from,'".$current_year."-%m-%d')
                        WHEN MONTH(CURDATE()) < MONTH(range_master.seasonal_from) THEN DATE_FORMAT(range_master.seasonal_from,'".$prvious_year."-%m-%d')
                        ELSE NULL
                    END) AS temp_seasonal_from"
                );

            $select[] = DB::raw(
                    "(CASE 
                        WHEN MONTH(CURDATE()) >= MONTH(range_master.seasonal_to) THEN DATE_FORMAT(range_master.seasonal_to,'".$current_year."-%m-%d')
                        WHEN MONTH(CURDATE()) < MONTH(range_master.seasonal_to) THEN DATE_FORMAT(range_master.seasonal_to,'".$prvious_year."-%m-%d')
                        ELSE NULL
                    END) AS temp_seasonal_to"
                );

            $select[] = DB::raw(
                    "(CASE 
                        WHEN CURDATE() BETWEEN range_master.seasonal_from AND range_master.seasonal_to THEN 1
                        WHEN range_master.seasonal_from IS NOT NULL AND range_master.seasonal_to IS NOT NULL THEN 2
                        ELSE 3
                    END) AS seasonal_sort"
                );
        }  

        $object = Products::select($select);  

        $object->leftJoin('magento_products', function($join) use ($params){
            $join->on('magento_products.product_id', '=', 'products.id');
            $join->where('magento_products.is_deleted_product',0);
            if(!empty($params['advance_search']))
            {
                $advance_search_data=$params['advance_search'];
                $storeData=\App\StoreMaster::find($advance_search_data['store_id']);
                if(!empty($storeData))
                {
                    $join->where('magento_products.store_id', '=', $advance_search_data['store_id']);
                }
            }
        });
        
        if($order_by_season == '1')
        {
            $object->leftJoin('range_master', function ($join){
                $join->on('products.buying_category_id', '=', 'range_master.id');
                $join->whereNotNull('products.buying_category_id');
            });
        }    

        if (!empty($params['search'])) {
            $object->leftJoin('product_barcodes', function ($join) {
                $join->on('product_barcodes.product_id', '=', 'products.id');
                });
           /*$object->orWhere('products.title','like',"%".$params['search']."%");
           $object->orWhere('products.sku','like',"%".$params['search']."%");*/
            $object->where(function($q) use ($params){
                $q->where('products.sku', $params['search']);
                $q->orWhere('products.title','like',"%".$params['search']."%");
                $q->orWhere('products.product_identifier','like',"%".$params['search']."%");
                $q->orwhere('product_barcodes.barcode', $params['search']);
             });
        }

        $object->leftJoin('magento_product_posting', function($join) use($params){
            $join->on('magento_product_posting.product_master_id', '=', 'products.id');
            $join->where('magento_product_posting.is_revised',0);
            if(!empty($params['advance_search']))
            {
                $advance_search_data=$params['advance_search'];
                $storeData=(!empty($advance_search_data['store_id'])) ? \App\StoreMaster::find($advance_search_data['store_id']) : '';
                if(!empty($storeData))
                {
                    $join->where('magento_product_posting.store_id', $advance_search_data['store_id']);
                }
            }
        });

        $object->where(function($q){ 
            $q->where('products.mp_image_missing', 0);
            $q->where('products.info_missing', 0);
            $q->whereNULL('magento_products.id');
            $q->where(function($q){ 
                $q->whereNULL('magento_product_posting.id');
                $q->orwhere('magento_product_posting.is_posted', 0);
            });
            $q->whereIn('products.product_type',['normal']);
            if (!empty($params['search'])) {
               $object->where('products.title','like',"%".$params['search']."%");
               $object->where('products.sku','like',"%".$params['search']."%");
           }
        });
        
        if($order_by_season == '1')
        {
            $object->orderBy('seasonal_sort', 'asc');

            $object->orderBy('temp_seasonal_from', 'asc');
        }    
        
        return $object->paginate($perPage);
    }

    public static function getAlreadyListingRecords($perPage = '',$params = array())
    {
            
       $object = \App\MagentoProduct::select('magento_products.main_image_url','magento_products.product_title','magento_products.sku','magento_products.quantity','magento_products.selling_price','magento_products.id','magento_products.store_id','magento_products.is_enabled','magento_products.magento_create_date')
        ->with(['magentoPriceLog' => function($query) {
             $query->where('is_selling_price_posted',0)->orderBy('id', 'desc')->get();
                
        }])
        ->with(['magentoQtyLog' => function($query) {
             $query->where('is_quantity_posted',0)->orderBy('id', 'desc')->get();
        }]);
        if(!empty($params['advance_search']))
        {
            $advance_search_data=$params['advance_search'];
            $storeData=(!empty($advance_search_data['store_id'])) ? \App\StoreMaster::find($advance_search_data['store_id']) : '';
            if(!empty($storeData))
            {
                $object->where('magento_products.store_id', '=', $advance_search_data['store_id']);
            }
        }
        if (!empty($params['search'])) {
            $object->leftJoin('product_barcodes', function ($join) {
                $join->on('product_barcodes.product_id', '=', 'magento_products.product_id')->where('magento_products.product_id','!=',0);
            });
           $object->where('magento_products.product_title','like',"%".$params['search']."%");
           $object->orWhere('magento_products.sku','like',"%".$params['search']."%");
            $object->orWhere('magento_products.upc','like',"%".$params['search']."%");
            $object->orWhere('magento_products.ean','like',"%".$params['search']."%");
            $object->orwhere('product_barcodes.barcode', $params['search']);
        }
        
        $object->where('magento_products.is_deleted_product', 0);
        $object->whereIn('magento_products.product_type', ['normal']);
        //print_r($object->get());exit;
        return $object->paginate($perPage);
    }
    public static function getInprogressRecords($perPage = '',$params = array())

    {
            
       $object = self::select('magento_product_posting.main_image_url','magento_product_posting.id','magento_product_posting.product_title','magento_product_posting.sku','magento_product_posting.quantity','magento_product_posting.selling_price','magento_product_posting.is_revised','magento_product_posting.product_master_id','magento_products.id as mp_id','magento_products.store_id','magento_product_posting.is_revised', 'magento_product_posting.magento_id', 'magento_product_posting.posting_result_status','magento_product_posting.posting_result','magento_product_posting.date_to_go_live');

        $object->leftJoin('magento_products', function($join) use ($params){
            $join->on('magento_products.sku', '=', 'magento_product_posting.sku');
            $join->where('magento_products.is_deleted_product', '=', 0);
           if(!empty($params['advance_search']))
            {
                $advance_search_data=$params['advance_search'];
                $storeData=(!empty($advance_search_data['store_id'])) ? \App\StoreMaster::find($advance_search_data['store_id']) : '';
                if(!empty($storeData))
                {
                    $join->where('magento_products.store_id', '=', $advance_search_data['store_id']);
                }
            }
        });

      //  $object = self::select('magento_product_posting.main_image_url','magento_product_posting.id','magento_product_posting.product_title','magento_product_posting.sku','magento_product_posting.quantity','magento_product_posting.selling_price','magento_product_posting.is_revised','magento_product_posting.product_master_id', 'magento_product_posting.magento_id');

      /*  if(!empty($params['advance_search']))
        {
            $advance_search_data=$params['advance_search'];
            $storeData=\App\StoreMaster::find($advance_search_data['store_id']);
            if(!empty($storeData))
            {
                $object->where('magento_product_posting.store_id', '=',  $advance_search_data['store_id']);
            }
        }*/
        
         if (!empty($params['search'])) {
               $object->leftJoin('product_barcodes', function ($join) {
                $join->on('product_barcodes.product_id', '=', 'magento_product_posting.product_master_id');
                });

               
           $object->where('magento_product_posting.product_title','like',"%".$params['search']."%");
           $object->orWhere('magento_product_posting.sku','like',"%".$params['search']."%");
           $object->orWhere('magento_product_posting.magento_product_id','like',"%".$params['search']."%");
           $object->orwhere('product_barcodes.barcode', $params['search']);
        }

        $object->where(function ($q){
            $q->whereNULL('magento_products.id');
            $q->orWhere('magento_product_posting.is_revised', 1);
        });
        
        $object->whereIn('is_posted', [1,2]);
        $object->whereIn('magento_product_posting.product_type', ['normal']);

        return $object->paginate($perPage);
    }


    public static function getPostingProductList($store_id,$limit)
    {
        $object = self::select('magento_product_posting.*','products.bulk_selling_quantity as quantity');
        $object->Join('products', 'products.id', '=', 'magento_product_posting.product_master_id');
        $object->where(function($q) use ($store_id){
            $q->where('products.is_deleted', '0');
            $q->where('magento_product_posting.is_posted', '1');
            $q->where('magento_product_posting.is_revised','0');
            $q->where('magento_product_posting.store_id',$store_id);
            $q->where('magento_product_posting.status', '1');
            $q->where('magento_product_posting.date_to_go_live', date('Y-m-d')); //get only current date
            $q->whereIn('magento_product_posting.product_type', array("normal","parent"));
        });

        $object->limit($limit);
        return $object->get();                
    }
    
    public function variation_theme_detatils(){
        return $this->belongsTo(VariationThemes::class,'variation_theme_id');
    }

    public static function getProductVariList($parent_id,$store_id)
    {
        $object = self::select('magento_product_posting.*','products.bulk_selling_quantity as quantity');
        $object->Join('products', 'products.id', '=', 'magento_product_posting.product_master_id');
        $object->where(function($q) use ($store_id,$parent_id){
            $q->where('products.parent_id', $parent_id);
            $q->where('products.is_deleted', '0');
            $q->where('magento_product_posting.status', '1');
            $q->where('magento_product_posting.is_revised','0');
            $q->where('magento_product_posting.store_id',$store_id);            
        });        

        return $object->get();
    }

    public static function getMagePostingProductList($store_id,$limit)
    {
        $object = self::select('magento_product_posting.*');
        $object->Join('magento_products', 'magento_products.id', '=', 'magento_product_posting.magento_id');        
        $object->where(function($q) use ($store_id){
            $q->where('magento_products.is_deleted_product', '0');
            $q->where('magento_product_posting.is_posted', '1');
            $q->where('magento_product_posting.is_revised','1');
            $q->where('magento_product_posting.store_id',$store_id);
            $q->where('magento_product_posting.status', '1');
            $q->whereIn('magento_product_posting.product_type', ["normal","parent"]);
        });

        $object->limit($limit);
        return $object->get();        
    }

    public static function getMagePostingProductVariList($parent_id,$store_id,$limit)
    {
        $object = self::select('magento_product_posting.*');                
        $object->where(function($q) use ($store_id,$parent_id){
            $q->where('magento_product_posting.parent_id', $parent_id);                        
            $q->where('magento_product_posting.is_revised','0');
            $q->where('magento_product_posting.store_id',$store_id);            
        });        

        return $object->get();        
    }

    public static function get_revise_details($magento_product_id, $store_id)
    {
        $object = self::select('magento_product_posting.*');
        
        $object->where(function($q) use ($magento_product_id, $store_id){
            $q->where('magento_id', $magento_product_id);
            $q->where('store_id', $store_id);
            $q->whereIn('posting_result_status', [0, 3]);
            $q->where('is_posted', 1);
            $q->where('is_revised', 1);
        });
        
        return $object->first();
    }
}
