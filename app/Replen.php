<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use DB;

use App\Products;

use Illuminate\Pagination\LengthAwarePaginator;

class Replen extends Model
{
    public static function getReplenRequestRecords($perPage = '', $params = array())
    {
        $replenPick=replenPickLocationType();
        $replenPickLocation=implode(",", $replenPick);
        $replenBulk=replenBulkLocationType();
        $replenBulkLocation=implode(",", $replenBulk);
        $object=Products::select('products.id','products.ros','products.stock_hold_days','products.title','products.product_identifier','products.main_image_internal','replens.replan_qty','replens.priority','products.sku','replens_update_trans.user_id','locations_assign.id as loc_ass_id','products.product_type','replens.warehouse_id','products.is_flammable','replens.id as replen_id','replens.cron_replan_priority','replens.cron_replan_qty','replens.replen_status');

       //$object->selectRaw('SUM((CASE WHEN locations_master.type_of_location In ('.$replenPickLocation.') THEN locations_assign.total_qty  END)) as total_in_pick');
                $object->selectRaw('GROUP_CONCAT(DISTINCT(CASE WHEN locations_master.type_of_location IN ('.$replenPickLocation.') THEN CONCAT(locations_master.location,"-",locations_assign.total_qty) END) SEPARATOR ",") as total_in_pick');


       // $object->selectRaw('SUM(DISTINCT(CASE WHEN locations_master.type_of_location In (1,3,6,7) THEN locations_assign.available_qty  END)) as total_in_pick');
        $object->selectRaw('COUNT(DISTINCT(CASE WHEN locations_master.type_of_location IN ('.$replenPickLocation.') THEN locations_master.location END)) as total_in_pick_count');
        //$object->selectRaw('COUNT((CASE WHEN locations_master.type_of_location IN  ('.$replenPickLocation.') THEN locations_assign.id END)) as total_in_pick_count');

        //$object->selectRaw('SUM((CASE WHEN locations_master.type_of_location IN ('.$replenBulkLocation.') THEN locations_assign.total_qty END)) as total_in_bulk');
         $object->selectRaw('GROUP_CONCAT(DISTINCT(CASE WHEN locations_master.type_of_location IN ('.$replenBulkLocation.') THEN CONCAT(locations_master.location,"-",locations_assign.total_qty) END) SEPARATOR ",") as total_in_bulk');

         $object->selectRaw('COUNT(DISTINCT(CASE WHEN locations_master.type_of_location IN ('.$replenBulkLocation.') THEN locations_master.location END)) as total_in_bulk_count');

        //$object->selectRaw('COUNT((CASE WHEN locations_master.type_of_location IN ('.$replenBulkLocation.') THEN locations_assign.id END)) as total_in_bulk_count');

       // $object->selectRaw('SUM(locations_assign.total_qty) as total_in_warehouse');

        $object->selectRaw('GROUP_CONCAT((CASE WHEN locations_master.type_of_location IN ('.$replenPickLocation.') THEN CONCAT(locations_master.location,"-",locations_assign.total_qty) END) SEPARATOR "<br/>") as pick_location_list');

        $object->selectRaw('GROUP_CONCAT((CASE WHEN locations_master.type_of_location IN ('.$replenBulkLocation.')  THEN CONCAT(locations_master.location,"-",locations_assign.total_qty) END) SEPARATOR "<br/>") as bulk_location_list');

        $object->selectRaw('GROUP_CONCAT(DISTINCT(CONCAT(users.first_name," ",users.last_name) ) ORDER BY replens_update_trans.id DESC SEPARATOR "<br/>") as override_by_users_list');

        $object->selectRaw('(products.ros * products.stock_hold_days) as stock_hold_qty');

        $object->selectRaw("SUM(locations_assign.allocated_qty) as allocated");

        $object->join('replens',function ($join) {
            $join->on('replens.product_id', '=', 'products.id');
            $join->where('replens.status',1);
             $join->where('replens.replan_qty','!=',0);
             $join->where('replens.priority','!=',0);
            $join->where('replens.replan_qty','!=',NULL);
        });

        $object->leftJoin('replens_update_trans', function($join) {
            $join->on('replens_update_trans.replen_id', '=', 'replens.id');
            
        });

        $object->leftJoin('users', function($join) {
            $join->on('users.id', '=', 'replens_update_trans.user_id');
            
        });

        $object->leftJoin('locations_assign', function($join) {
            $join->on('locations_assign.product_id', '=', 'products.id');
        });

       
        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

      
        if (!empty($params['search'])) 
        {
            $object->leftJoin('product_barcodes as var_product_barcodes', function ($join) {
                    $join->on('var_product_barcodes.product_id', '=', 'products.id');
                });
        }

        $object->where(function($q) use ($params) 
        {
            if (!empty($params['search'])) {
                $q->where('products.sku', $params['search']);
                $q->orWhere('products.title', 'like', "%" . $params['search'] . "%");
                $q->orWhere('products.product_identifier', $params['search']);
                $q->orwhere('var_product_barcodes.barcode', $params['search']);
            }
        });

        if (!empty($params['advance_search'])) 
        {
            $advance_search=$params['advance_search'];

            if(!empty($advance_search['priority']))
            {
                $object->where('replens.priority',$advance_search['priority']);
            }

            if(!empty($advance_search['warehouse_id']))
            {
                 $object->Join('warehouse_master', function($join) {
                    $join->on('warehouse_master.id', '=', 'locations_assign.warehouse_id');
                });
                $object->where('warehouse_master.id',$advance_search['warehouse_id']);
              //  $object->where('replens.warehouse_id',$advance_search['warehouse_id']);
            }
            
            if(!empty($advance_search['pick_aisle']))
            {
                $object->where('locations_master.aisle',$advance_search['pick_aisle']);
                $object->whereIn('locations_master.type_of_location',$replenPick);
            }
            
            if(!empty($advance_search['bulk_aisle']))
            {
                $object->where('locations_master.aisle',$advance_search['pick_aisle']);
                $object->whereIn('locations_master.type_of_location',$replenBulk);
            }
            
            if(!empty($advance_search['days']))
            {
                $daysFilter="IF(products.ros!=0,SUM(DISTINCT(CASE WHEN locations_master.type_of_location IN (1,6,7,3) THEN locations_assign.available_qty  END)) / products.ros,0)";
                $object->selectRaw("{$daysFilter} as filtterday");
                $object->having('filtterday','<',$advance_search['days']);
                $object->whereIn('locations_master.type_of_location',replenPickLocationType());

            }
            
            if (!empty($advance_search['filter_custom_tags'])) 
            {
                $staticTags=product_logic_base_tags();
                $staticTagFilter=0;               
                foreach ($advance_search['filter_custom_tags'] as $key => $value) 
                {                   
                    if(in_array($value, $staticTags))
                    {
                        $object->where(function($subQ) use ($params,$value) 
                        {                        
                            if($value=='Flammable')
                            {
                                $subQ->orwhere('products.is_flammable', 1);
                            }
                            
                            if($value=='Reduced')
                            {
                                 $subQ->orwhere('products.is_reduced', 1);
                            }
                            
                            if($value=='Do not buy again')
                            {
                                $subQ->orwhere('products.is_do_not_buy_again', 1);
                            }
                            
                            if($value=='Heavy')
                            {
                                $subQ->orwhere('products.is_heavy', 1);
                            }
                            
                            if($value=='Promotional')
                            {
                                $subQ->orwhere('products.is_promotional', 1);
                            }
                        });
                        $staticTagFilter= $staticTagFilter+1;
                    }
                }
                
                if( $staticTagFilter>0)
                {                    
                    $object->orwhereHas('tags', function($tag_q) use ($advance_search) {
                        $tag_q->whereIn('name', $advance_search['filter_custom_tags']);
                    });
                }
                else
                {
                    $object->whereHas('tags', function($tag_q) use ($advance_search) {
                        $tag_q->whereIn('name', $advance_search['filter_custom_tags']);
                    });
                }
            }
        }

        if(!empty($params))
        {
            $object->orderBy($params['order_column'],$params['order_dir']);
        }
        else
        {
            $object->orderBy('products.id','desc');
        }
        
        $object->groupBy('products.id');       
        $perPage = $params['length'];        
        $curPage = $params['page'];
        $itemQuery = clone $object;
        $itemQuery->addSelect('products.*');
        $items = $itemQuery->forPage($curPage, $perPage)->get();
        $totalResult = $object->addSelect(DB::raw('count(*) as count'))->get();        
        $totalItems = count($totalResult);      
        $paginatedItems = new LengthAwarePaginator($items->all(),$totalItems, $perPage);       
        return $paginatedItems;
    }

    

    
     public static function aisleWiseProductCount($siteId)
    {
        $replenBulk=replenBulkLocationType();
        $object=\App\Locations::select('replens.product_id','locations_master.aisle','replens.default_location','locations_master.id');

        $object->selectRaw('(COUNT(DISTINCT replens.product_id)) as count_product');
        
        $object->leftJoin('replens', function($join) {
            $join->on('replens.default_location', '=', 'locations_master.id');
            $join->whereIn('replens.priority',assignAislePriority());
           // $join->where('replens.status',1);
        });

        $object->whereIn('locations_master.type_of_location',$replenBulk);

        $object->where('locations_master.site_id',$siteId);
        $object->where('locations_master.status',1);
        
        $object->groupBy('locations_master.aisle');

        return $object->get()->toArray();
    }

    public static function getReplenData($params, $perPage = 10) 
    {
        $default='1';

        $query = Replen::select("replens.id","replens.priority","replens.replan_qty","replens.replen_status");        

        $query->selectRaw("products.title,products.sku,products.main_image_internal,products.product_identifier,products.id as product_id,products.main_image_internal_thumb");

        $query->selectRaw("locations_master.aisle,locations_master.type_of_location,locations_master.location");

        $query->selectRaw("product_supplier.supplier_sku");   

        $query->Join('locations_master', function($q) {
            $q->on('replens.default_location', 'locations_master.id');            
        });        

        $query->leftJoin('products', function($q) {
            $q->on('replens.product_id', 'products.id');
        });

        $query->leftJoin('product_supplier', function($leftJoin) {
            $leftJoin->on('replens.product_id','=','product_supplier.product_id')
            ->where('product_supplier.is_default', '=', 1);            
        });        

        $query->where('replens.status', 1);  

        $query->where('replens.priority','!=', 0);              

        if(!empty($params['selected_priority']))
        {
            $selected_priority_arr=explode(',',$params['selected_priority']);            
            $query->whereIn('replens.priority',$selected_priority_arr);
        }

        if(!empty($params['show_aisle']))
        {
            //$selected_priority_arr=explode(',',$params['show_aisle']);            
            $query->whereIn('locations_master.aisle',$params['show_aisle']);
        }

        if (isset($params['productSearch']) && !empty($params['productSearch'])) 
        {
            $searchString = trim($params['productSearch']);
            $query->where(function($q) use($searchString) {                
                $q->where('products.title', 'like', '%' . $searchString . '%');
                $q->orWhere('products.sku', 'like', '%' . $searchString . '%');
                $q->orWhere('products.product_identifier', 'like', '%' . $searchString . '%');
                $q->orWhere('product_supplier.supplier_sku', 'like', '%' . $searchString . '%');
            });
        }

        $query->groupBy('replens.id');

        if ($params['sortBy'] == "title")
        {
            $query->orderBy("products.title", $params['sortDirection']);
        }

        if ($params['sortBy'] == "priority")
        {
            $query->orderBy("replens.priority", $params['sortDirection']);
        }

        if ($params['sortBy'] == "aisle")
        {
            $query->orderBy("locations_master.aisle", $params['sortDirection']);
        }

        if ($params['sortBy'] == "location")
        {
            $query->orderBy("locations_master.location", $params['sortDirection']);
        }

        //dd($query->toSql());
        
        return $query->get();
    }

    public static function getReplenDetailData($id,$aisle_array=array()) 
    {
        $default='1';

        $query = Replen::select("replens.id","replens.priority","replens.replan_qty","replens.replen_status","replens.selected_bulk_location","replens.selected_pro_barcode","replens.warehouse_id");

        $query->selectRaw("products.title,products.sku,products.main_image_internal,products.product_identifier,products.id as product_id,products.main_image_internal_thumb");

        $query->selectRaw("locations_master.aisle,locations_master.type_of_location,locations_master.location");

        $query->Join('locations_master', function($q) {
            $q->on('replens.default_location', 'locations_master.id');            
        });        

        $query->leftJoin('products', function($q) {
            $q->on('replens.product_id', 'products.id');
        });
        
        $query->where('replens.id', $id);

        if(!empty($aisle_array))
        {
            $query->whereIn('locations_master.aisle',$aisle_array);
        }

        $query->where('replens.status', 1);

        $query->where('replens.priority','!=', 0);

        $query->groupBy('replens.id');

        $query->orderBy("locations_master.location", 'asc');        
        
        return $query->get();
    }

}