<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Products;
use App\ProductBarcode;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\LocationAssignTrans;

class LocationAssign extends Model {

    protected
            $table   = 'locations_assign';
    protected
            $guarded = [];

    public
            function products() {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public
            function locations() {
        return $this->belongsTo(Locations::class, 'location_id');
    }

    public
            function locationAssignTransaction() {
        return $this->hasMany(LocationAssignTrans::class, 'loc_ass_id');
    }

    public static
            function getAllProductsWithAllocatedLocation($perPage = '', $params = array()) {
        $locationAssignPick          = locationAssignPickLocationType();
        $locationAssignPickLocations = implode(",", $locationAssignPick);
        $locationAssignBulk          = locationAssignBulkLocationType();
        $locationAssignBulkLocations = implode(",", $locationAssignBulk);
        $selectArr                   = array('products.id', 'products.product_type', 'products.title', 'products.sku', 'products.stock_hold_days', 'products.product_identifier', 'main_image_internal', 'products.ros', 'locations_master.location', 'locations_assign.available_qty', 'products.is_listed_on_magento', 'locations_master.type_of_location', 'locations_master.case_pack', 'locations_master.aisle', 'locations_master.carton_id', 'location_assign_trans.qty_per_box', 'location_assign_trans.case_type');

        foreach (product_logic_base_tags() as $db_column => $tag_caption) {
            $selectArr[] = 'products.is_' . $db_column;
        }


        $boxTurnFilter = "IF(products.ros = 0,'zero','notzero')";



        $boxTurn = "IF(products.ros!=0,SUM(CASE WHEN locations_master.type_of_location IN (1,7) THEN locations_assign.qty_fit_in_location END) /products.ros,0)";

        $object = Products::with('locationAssign')->select($selectArr);

        // $object = DB::table('products')->select($selectArr);

        $object->selectRaw("(products.ros * products.stock_hold_days) as pick_stock_required");

        $object->selectRaw("SUM(CASE WHEN locations_master.type_of_location IN (" . $locationAssignPickLocations . ") THEN locations_assign.qty_fit_in_location END) as sum_of_box_val");

        $object->selectRaw('SUM(IF(locations_master.type_of_location IN (' . $locationAssignPickLocations . '),locations_assign.available_qty,0)) as total_pick');
        $object->selectRaw('SUM(IF(locations_master.type_of_location IN (' . $locationAssignBulkLocations . '),locations_assign.available_qty,0)) as total_bulk');

        $object->selectRaw('COUNT(DISTINCT(CASE WHEN locations_master.type_of_location IN (' . $locationAssignPickLocations . ') THEN locations_master.location END)) as total_in_pick_count');

        $object->selectRaw("MAX(locations_assign.qty_fit_in_location) AS qty_fit_in_location");

        $object->selectRaw('COUNT(DISTINCT(CASE WHEN locations_master.type_of_location IN (' . $locationAssignPickLocations . ') THEN locations_assign.location_id END))  as count_locations');

        $object->selectRaw("{$boxTurn} as box_turn");

        $object->selectRaw("{$boxTurnFilter} as box_turn_status");

        $stockDayColor = "IF(MAX(location_assign_trans.qty_per_box) > (products.ros * products.stock_hold_days) ,'red','blue')";

        $object->selectRaw("{$stockDayColor} as day_stock_color");

        //if (!empty($params['search'])) {
        $object->leftJoin(DB::raw('(SELECT product_id,barcode, MAX(case_quantity) AS case_quantity FROM product_barcodes where barcode_type IN (' . $locationAssignBulkLocations . ') GROUP BY product_id) as product_barcodes'), function ($join) {
            $join->on('product_barcodes.product_id', '=', 'products.id');
        });

        $warehouse_id = isset($params['advance_search']) ? $params['advance_search']["warehouse_id"] : '';
        $object->leftJoin('locations_assign', function ($join) use ($warehouse_id) {
            $join->on('locations_assign.product_id', '=', 'products.id');
            if ($warehouse_id != '') {
                $join->where('locations_assign.warehouse_id', '=', $warehouse_id);
            }
        });
        $object->leftJoin('locations_master', function ($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
            $join->where('locations_master.status', 1);
        });

        $object->leftJoin('location_assign_trans', function($join) {
            $join->on('location_assign_trans.loc_ass_id', '=', 'locations_assign.id');
            $join->whereIn('location_assign_trans.case_type', [2, 3]);
        });


        if (!empty($params['search'])) {

            $object->leftJoin('product_barcodes as var_product_barcodes', function ($join) {
                $join->on('var_product_barcodes.product_id', '=', 'products.id');
            });
        }

        $object->groupBy('products.id');
        if (!empty($params['advance_search'])) {
            $advance_search = $params['advance_search'];
            if (isset($advance_search['show_product_booked_in'])) {
                if ($advance_search['show_product_booked_in'] == '1') {
                    $object->Join('booking_po_products', function ($join) {
                        $join->on('booking_po_products.product_id', '=', 'products.id');
                    });
                    $object->Join('bookings', function ($join) {
                        $join->on('bookings.id', '=', 'booking_po_products.booking_id');
                    });
                    $object->where('bookings.book_date', '>=', date('Y-m-d'));
                }
            }
            if (isset($advance_search['product_location_not_assign'])) {
                if ($advance_search['product_location_not_assign'] == '1') {
                    $object->whereNULL('locations_assign.id');
                }
            }
            if (isset($advance_search['product_location_assign'])) {
                if ($advance_search['product_location_assign'] == '1') {
                    $object->whereNOTNULL('locations_assign.id');
                }
            }
            if (isset($advance_search['new_products'])) {
                if ($advance_search['new_products'] == '1') {
                    $object->where('products.is_listed_on_magento', 0);
                }
            }
            if (isset($advance_search['red_days_stock_holding'])) {
                if ($advance_search['red_days_stock_holding'] == '1') {
                    $object->whereRaw("{$stockDayColor} = ? ", ['red']);
                }
            }
            if (isset($advance_search['box_turn_undefined'])) {
                if ($advance_search['box_turn_undefined'] == '1') {
                    $object->havingRaw("{$boxTurn} = ? ", [0]);
                }
            }
            if (isset($advance_search['box_turn_filter'])) {
                if ($advance_search['box_turn_filter'] == '1') {
                    $object->havingRaw("{$boxTurn} >= ? ", [$advance_search['box_turn_from']]);
                    $object->havingRaw("{$boxTurn} <= ? ", [$advance_search['box_turn_to']]);
                }
            }
            if (isset($advance_search['warehouse_id'])) {
                $warehouse_id = $advance_search['warehouse_id'];
                $object->leftJoin('warehouse_master', function($join) use ($warehouse_id) {
                    $join->on('warehouse_master.id', '=', 'locations_assign.warehouse_id');
                    $join->where('warehouse_master.id', $warehouse_id);
                });

                //  $object->where('replens.warehouse_id',$advance_search['warehouse_id']);
            }

            if (isset($advance_search['assigned_aisle_filter'])) {
                if ($advance_search['assigned_aisle_filter'] == '1') {

                    $object->whereIn('locations_master.type_of_location', $locationAssignPick);

                    $object->whereRaw('locations_master.aisle = ?', [$advance_search['assigned_aisle']]);
                }
            }
        }
        $object->where(function($q) use ($params) {
            if (!empty($params['search'])) {
                $searchString = $params['search'];
                $q->where('products.sku', $searchString);
                $q->orWhere('products.title', 'like', "%" . $searchString . "%");
                $q->orWhere('products.product_identifier', $searchString);
                $q->orwhere('var_product_barcodes.barcode', $searchString);
            }
        });



        if (!empty($params)) {
            $object->orderBy($params['order_column'], $params['order_dir']);
        }
        else {
            $object->orderBy('products.id', 'desc');
        }
        //return $object->paginate($perPage);
        //  dd($object->toSql());
        /* dd($object->get());
          return $object->paginate($perPage); */


        $perPage = $params['length'];

        $curPage     = $params['page'];
        $itemQuery   = clone $object;
        $itemQuery->addSelect('products.*');
        $items       = $itemQuery->forPage($curPage, $perPage)->get();
        $totalResult = $object->addSelect(DB::raw('count(*) as count'))->get();

        $totalItems = count($totalResult);

        $paginatedItems = new LengthAwarePaginator($items->all(), $totalItems, $perPage);

        return $paginatedItems;
    }

    public static
            function getEmptyLocations($perPage = '', $params = array(), $productId) {

        $locationAssign = LocationAssign::where('product_id', $productId)->selectRaw("MAX(locations_assign.qty_fit_in_location) AS qty_fit_in_location")->selectRaw('locations_master.carton_id')->leftJoin('locations_master', 'locations_master.id', 'locations_assign.location_id')->whereIn('locations_master.type_of_location', [1, 7])->groupBy('locations_master.carton_id')->get();


        $cartQtyArr = array();
        if (count($locationAssign) > 0) {
            $casVar = "(CASE ";
            foreach ($locationAssign as $key => $value) {
                if (!is_null($value->carton_id)) {
                    $casVar .= " WHEN locations_master.carton_id = " . $value->carton_id . " THEN " . $value->qty_fit_in_location;
                }
            }
            $casVar .= " ELSE '-' END)";
        }
        else {
            $casVar = "'-'";
        }
        $allowedLocationType = locationAssignPickLocationType();


        $object = Locations::select('locations_master.*', 'locations_assign.product_id', 'locations_assign.qty_fit_in_location', 'locations_assign.location_id');
        $object->leftJoin('locations_assign', function ($join) {
            $join->on('locations_assign.location_id', '=', 'locations_master.id');
        });
        $object->leftJoin('warehouse_master', function ($join) {
            $join->on('warehouse_master.id', '=', 'locations_master.site_id');
        });


        $object->selectRaw("{$casVar} as qty_fit_loc");
        $object->where('warehouse_master.is_default', 1);
        $object->whereIn('locations_master.type_of_location', $allowedLocationType);
        $object->whereNull('locations_assign.location_id');
        if (!empty($params)) {
            $object->orderBy($params['order_column'], $params['order_dir']);
        }
        else {
            $object->orderBy('locations_master.id', 'desc');
        }
        if (!empty($params['advance_search'])) {
            $advance_search = $params['advance_search'];
            if (isset($advance_search['filter_aisle']) && $advance_search['filter_aisle'] != '') {
                $object->where('locations_master.aisle', $advance_search['filter_aisle']);
            }
        }
        $object->where(function($q) use ($params) {
            if (!empty($params['search'])) {
                $q->where('locations_master.location', $params['search']);
                $q->orWhere('locations_master.aisle', $params['search']);
            }
        });
        $object->where('locations_master.status', 1);
        return $object->paginate($perPage);
    }

    public
            function get_replen_cases_data($product_id = '', $warehouse_id = '', $location_id = '') {
        $object = self::select('locations_assign.id', 'location_assign_trans.qty_per_box', 'location_assign_trans.total_boxes', 'location_assign_trans.qty');

        $object->where('locations_assign.product_id', '=', $product_id);

        if (!empty($warehouse_id)) {
            $object->where('locations_assign.warehouse_id', '=', $warehouse_id);
        }

        $object->where('locations_assign.location_id', '=', $location_id);

        $object->leftJoin('location_assign_trans', function($join) {
            $join->on('location_assign_trans.loc_ass_id', '=', 'locations_assign.id');
        });

        //$object->groupBy('locations_assign.id');
        return $object->get();
    }

    public static
            function manageMaterialReceiptQty($data) {
        if (
                !empty($data['product_id']) && !empty($data['booking_id']) && !empty($data['po_id'])
        ) {
            $update_total_qty_loc_ids = array();

            $update_array = array();

            $location_details = !empty($data['location_details']) ? $data['location_details'] : array();

            $booking_location_not_check = !empty($data['booking_location_not_check']) ? $data['booking_location_not_check'] : array();

            $photobooth = !empty($data['booking_po_products_details']->is_photobooth) ? $data['booking_po_products_details']->is_photobooth : '';

            $photobooth_location_id = "";

            // IF PHOTOBOOTH IS YES, THAN GET CURRENT WAREHOUSE'S PHOTOBOOTH LOCATION
            if (!empty($photobooth) && $data['warehouse_id'] && !empty($location_details)) {
                $photobooth_location_details = Locations::getPhotoBoothLocation($data['warehouse_id']);

                $photobooth_location_id = $photobooth_location_details['id'];
            }

            $self_obj                    = new self();
            $where_array['product_id']   = $data['product_id'];
            $where_array['booking_id']   = $data['booking_id'];
            $where_array['po_id']        = $data['po_id'];
            $where_array['putaway_type'] = 1;

            $product_details = $self_obj->where($where_array)->get()->keyBy('location_id')->toArray();


            $find_barcode_id = [];

            $barcode_id = [];

            if (!empty($data['location_barcodes'])) {
                if (!empty($data['location_barcodes']['find_barcode_ids'])) {
                    $find_barcode_id = $data['location_barcodes']['find_barcode_ids'];
                    unset($data['location_barcodes']['find_barcode_ids']);
                }
            }

            if (!empty($find_barcode_id)) {
                $find_barcode_id = array_unique($find_barcode_id);

                $barcode_id = ProductBarcode::whereIn('barcode', $find_barcode_id)
                        ->where('product_id', $data['product_id'])
                        ->pluck('id', 'barcode')
                        ->toArray();
            }

            if (!empty($product_details)) {
                foreach ($product_details as $location_id => $product_locations) {
                    if (isset($location_details[$location_id])) {

                        $cal_qty = $location_details[$location_id];

                        if ($cal_qty > 0) {
                            // $product_update = array(
                            //     'id'          => $product_locations['id'],
                            //     'total_qty'   => $cal_qty,
                            //     'available_qty'=> $cal_qty,
                            //     'modified_by' => $data['user_id'],
                            //     'updated_at'  => system_date(),
                            // );
                            // $update_array[] = $product_update;

                            $location_assign_id = $product_locations['id'];

                            $update_total_qty_loc_ids[] = $location_assign_id;

                            // MANAGE LOCATION TRANS
                            $location_barcode = array();

                            if (!empty($data['location_barcodes'][$location_id])) {
                                $location_barcode = $data['location_barcodes'][$location_id];
                            }

                            $self_obj->manageMaterialReceiptLocationTrans($location_assign_id, $location_barcode, $barcode_id, $booking_location_not_check);

                            unset($location_details[$location_id]);
                            unset($product_details[$location_id]);
                        }
                    }
                    elseif (!empty($photobooth_location_id) && $photobooth_location_id == $location_id
                    ) {
                        unset($product_details[$location_id]);
                        $photobooth_location_id = "";
                    }
                }
            }

            if (!empty($location_details)) {
                foreach ($location_details as $location_id => $location_qty) {

                    $cal_qty = $location_qty;

                    if ($cal_qty > 0) {
                        $product_insert = array(
                            'warehouse_id'        => (int) $data['warehouse_id'],
                            'product_id'          => $data['product_id'],
                            'location_id'         => $location_id,
                            'qty_fit_in_location' => 0,
                            'putaway_type'        => 1,
                            'booking_id'          => $data['booking_id'],
                            'po_id'               => $data['po_id'],
                            'is_mannual'          => 0,
                            'total_qty'           => $cal_qty,
                            'allocated_qty'       => 0,
                            'available_qty'       => $cal_qty,
                            'created_by'          => $data['user_id'],
                            'modified_by'         => $data['user_id'],
                            'created_at'          => date('Y-m-d H:i:s'),
                        );

                        $location_assign_id = $self_obj->create($product_insert)->id;

                        // MANAGE LOCATION TRANS
                        $location_barcode = array();

                        if (!empty($data['location_barcodes'][$location_id])) {
                            $location_barcode = $data['location_barcodes'][$location_id];
                        }

                        $self_obj->manageMaterialReceiptLocationTrans($location_assign_id, $location_barcode, $barcode_id, $booking_location_not_check, true);
                    }
                }
            }

            // ADD PHOTOBOOTH LOCATION TO TRANS
            if (!empty($photobooth_location_id)) {
                $product_insert = array(
                    'warehouse_id'        => (int) $data['warehouse_id'],
                    'product_id'          => $data['product_id'],
                    'location_id'         => $photobooth_location_id,
                    'qty_fit_in_location' => 0,
                    'putaway_type'        => 1,
                    'booking_id'          => $data['booking_id'],
                    'po_id'               => $data['po_id'],
                    'is_mannual'          => 0,
                    'total_qty'           => 1,
                    'allocated_qty'       => 0,
                    'available_qty'       => 0,
                    'created_by'          => $data['user_id'],
                    'modified_by'         => $data['user_id'],
                    'created_at'          => date('Y-m-d H:i:s'),
                );

                $location_assign_id = $self_obj->create($product_insert)->id;

                $location_assign_trans_insert = array(
                    'loc_ass_id'                     => $location_assign_id,
                    'qty'                            => 1,
                    'best_before_date'               => NULL,
                    'barcode_id'                     => NULL,
                    'qty_per_box'                    => 1,
                    'total_boxes'                    => 1,
                    'case_type'                      => 1,
                    'booking_po_product_id'          => $data['booking_po_products_details']->id,
                    'booking_po_case_detail_id'      => NULL,
                    'booking_po_product_location_id' => NULL,
                );

                \App\LocationAssignTrans::create($location_assign_trans_insert);
            }

            if (!empty($update_total_qty_loc_ids)) {
                $trans_obj = \App\LocationAssignTrans::selectRaw('SUM(qty) as total_qty, loc_ass_id');

                $trans_obj->whereIn('loc_ass_id', $update_total_qty_loc_ids);

                $trans_obj->groupBy('loc_ass_id');

                $total_loc_quantities = $trans_obj->get()->toArray();

                if (!empty($total_loc_quantities)) {
                    foreach ($total_loc_quantities as $total_loc_qty) {
                        $product_update = array(
                            'id'            => $total_loc_qty['loc_ass_id'],
                            'total_qty'     => $total_loc_qty['total_qty'],
                            'available_qty' => $total_loc_qty['total_qty'],
                            'modified_by'   => $data['user_id'],
                            'updated_at'    => system_date(),
                        );

                        $update_array[] = $product_update;
                    }
                }
            }

            if (!empty($update_array)) {
                \Batch::update($self_obj, $update_array, 'id');
            }

            if (!empty($product_details)) {
                $location_assign_ids = array();

                foreach ($product_details as $location_id => $product_locations) {
                    $location_assign_ids[] = $product_locations['id'];
                }

                if (!empty($location_assign_ids)) {
                    $self_obj->whereIn('id', $location_assign_ids)->delete();
                }
            }
        }
    }

    public
            function manageMaterialReceiptLocationTrans($location_assign_id, $post_location_barcode = array(), $barcode_ids, $booking_location_not_check, $is_insert = false) {

        if (!empty($location_assign_id)) {

            $trans_obj = new \App\LocationAssignTrans();

            if (empty($post_location_barcode)) {
                $trans_obj->where('loc_ass_id', $location_assign_id)->delete();
                return true;
            }

            if (!empty($post_location_barcode)) {
                $loca_trans_records = array();

                $temp_loca_trans_records = array();

                $insert_bulk_array = array();

                $update_bulk_array = array();

                if ($is_insert === false) {

                    $trans_get_obj = \App\LocationAssignTrans::where('loc_ass_id', $location_assign_id);

                    $temp_loca_trans_records = $trans_get_obj->get()->toArray();
                }

                if (!empty($temp_loca_trans_records)) {
                    foreach ($temp_loca_trans_records as $row) {
                        $booking_po_product_location_id = $row['booking_po_product_location_id'];

                        $loca_trans_records[$booking_po_product_location_id] = $row;
                    }
                }

                foreach ($post_location_barcode as $booking_location_id => $post_details) {
                    $db_array = array();

                    $cal_qty = $post_details['qty'];

                    $qty_per_box = $post_details['qty_per_box'];

                    $total_boxes = $post_details['total_boxes'];

                    if ($post_details['case_type'] == 1) {
                        $qty_per_box = 1;
                        $total_boxes = $cal_qty;
                    }

                    $barcode_string = $post_details['barcode'];

                    $barcode_id = !empty($barcode_ids[$barcode_string]) ? $barcode_ids[$barcode_string] : '';

                    if (!empty($loca_trans_records[$booking_location_id])) {
                        $db_array['id'] = $loca_trans_records[$booking_location_id]['id'];

                        unset($loca_trans_records[$booking_location_id]);
                    }

                    $db_array['loc_ass_id'] = $location_assign_id;

                    $db_array['qty'] = $cal_qty;

                    $db_array['best_before_date'] = (!empty($post_details['best_before_date'])) ? $post_details['best_before_date'] : NULL;

                    $db_array['barcode_id'] = $barcode_id;

                    $db_array['qty_per_box'] = $qty_per_box;

                    $db_array['total_boxes'] = $total_boxes;

                    $db_array['case_type'] = $post_details['case_type'];

                    $db_array['booking_po_product_id'] = $post_details['booking_po_product_id'];

                    $db_array['booking_po_case_detail_id'] = $post_details['booking_po_case_detail_id'];

                    $db_array['booking_po_product_location_id'] = $booking_location_id;

                    if (!empty($db_array['id'])) {
                        if (empty($booking_location_not_check) || !in_array($db_array['booking_po_product_location_id'], $booking_location_not_check)) {
                            $update_bulk_array[] = $db_array;
                        }
                    }
                    else {

                        if (empty($booking_location_not_check) || !in_array($db_array['booking_po_product_location_id'], $booking_location_not_check)) {
                            $insert_bulk_array[] = $db_array;
                        }
                    }
                }

                if (!empty($insert_bulk_array)) {
                    $trans_obj->insert($insert_bulk_array);
                }

                if (!empty($update_bulk_array)) {
                    \Batch::update($trans_obj, $update_bulk_array, 'id');
                }

                if (!empty($loca_trans_records)) {
                    $remove_id = array();

                    foreach ($loca_trans_records as $booking_location_id => $loc_trans_result) {
                        $remove_id[] = $loc_trans_result['id'];
                    }

                    if (!empty($remove_id)) {
                        $trans_obj->whereIn('id', $remove_id)->delete();
                    }
                }

                return true;
            }
        }
    }

    public static
            function getRecordForStockPopUp($params) {

        $selectArr = array('location_assign_trans.qty_per_box', 'location_assign_trans.total_boxes', 'product_barcodes.barcode', 'product_barcodes.barcode_type', 'locations_assign.product_id', 'locations_master.location');



        $object = DB::table('location_assign_trans')->select($selectArr);

        $object->join('locations_assign', function($join) {
            $join->on('locations_assign.id', '=', 'location_assign_trans.loc_ass_id');
        });

        $object->join('product_barcodes', function ($join) {
            $join->on('product_barcodes.product_id', '=', 'locations_assign.product_id');
            $join->on('product_barcodes.id', '=', 'location_assign_trans.barcode_id');
        });

        $object->join('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        $object->join('products', function($join) {
            $join->on('products.id', '=', 'locations_assign.product_id');
        });

        $object->selectRaw('IF(products.ros!=0,ROUND(location_assign_trans.qty_per_box /products.ros),0) as min_day_stock_holding');
        if (!empty($params['advance_search'])) {
            $advance_search = $params['advance_search'];
            if (isset($advance_search['id'])) {

                $object->where('locations_assign.product_id', $advance_search['id']);
            }
        }

        $object->whereIn('product_barcodes.barcode_type', [2, 3]);
        //dd($params);
        if (!empty($params)) {
            $object->orderBy($params['order_column'], $params['order_dir']);
        }
        else {
            $object->orderBy('location_assign_trans.id', 'desc');
        }

        return $object->paginate();
    }

    public static
            function getCurrentStorageDetail($warehouse_id, $product_id) {
        $object = self::select('locations_assign.id', 'location_assign_trans.qty', 'locations_assign.qty_fit_in_location', 'location_assign_trans.best_before_date');

        $object->selectRaw("locations_master.location,locations_master.type_of_location");

        $object->leftJoin('location_assign_trans', function($join) {
            $join->on('location_assign_trans.loc_ass_id', '=', 'locations_assign.id');
        });

        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        $object->where('locations_assign.product_id', '=', $product_id);

        $object->where('locations_assign.warehouse_id', '=', $warehouse_id);

        $object->where('location_assign_trans.qty', '!=', NULL);

        $object->groupBy('location_assign_trans.id');

        return $object->get();
    }

    public static
            function getProductListReplen($warehouse_id, $product_id, $location_id, $barcode_id) {
        $object = self::select('location_assign_trans.id', 'location_assign_trans.qty_per_box', 'location_assign_trans.total_boxes', 'location_assign_trans.qty', 'locations_assign.qty_fit_in_location', 'location_assign_trans.best_before_date', 'location_assign_trans.case_type');

        $object->selectRaw("locations_master.location,locations_master.type_of_location");

        $object->selectRaw("product_barcodes.barcode");

        $object->leftJoin('location_assign_trans', function($join) {
            $join->on('location_assign_trans.loc_ass_id', '=', 'locations_assign.id');
        });

        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        $object->leftJoin('product_barcodes', function($join) {
            $join->on('product_barcodes.id', '=', 'location_assign_trans.barcode_id');
        });

        $object->where('locations_assign.product_id', '=', $product_id);

        $object->where('locations_assign.warehouse_id', '=', $warehouse_id);

        $object->where('locations_assign.location_id', '=', $location_id);

        if (!empty($barcode_id)) {
            $object->where('location_assign_trans.barcode_id', '=', $barcode_id);
        }

        $object->where('location_assign_trans.qty', '!=', NULL);

        $object->groupBy('location_assign_trans.id');

        return $object->get();
    }

    public static
            function getLocationWithType($location_assign_id) {
        $object = self::select('locations_assign.id', 'locations_assign.total_qty', 'locations_assign.available_qty', 'locations_master.type_of_location', 'locations_assign.total_qty', 'locations_assign.allocated_qty');
        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });
        $object->where('locations_assign.id', '=', $location_assign_id);
        return $object->get();
    }

    public
            function get_replen_bulk_data($product_id = '', $warehouse_id = '', $biggest_pick_bbd = '') {
        $object = self::select('locations_assign.id', 'location_assign_trans.qty');

        $object->where('locations_assign.product_id', '=', $product_id);

        if (!empty($warehouse_id)) {
            $object->where('locations_assign.warehouse_id', '=', $warehouse_id);
        }

        $object->where('location_assign_trans.best_before_date', '<', $biggest_pick_bbd);

        $object->whereIn('locations_master.type_of_location', array('2', '4', '12'));

        $object->leftJoin('location_assign_trans', function($join) {
            $join->on('location_assign_trans.loc_ass_id', '=', 'locations_assign.id');
        });

        $object->Join('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        $object->groupBy('location_assign_trans.id');
        return $object->get();
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     * @desc return all products for booking
     */
    public
            function getBookingPalletProducts($params) {
        $query = $this->join('locations_master', function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
        });
        $query->join('location_assign_trans', function($q) {
            $q->on('locations_assign.id', 'location_assign_trans.loc_ass_id');
        });
        $query->join('product_barcodes', function($q) {
            $q->on('location_assign_trans.barcode_id', 'product_barcodes.id');
        });
        $query->join('products', function($q) {
            $q->on('locations_assign.product_id', 'products.id');
        });
        $query->leftJoin('booking_po_products', function($q) {
            $q->on('location_assign_trans.booking_po_product_id', 'booking_po_products.id');
        });
        $query->leftJoin('po_products', function($q) {
            $q->on('booking_po_products.po_product_id', 'po_products.id');
        });

        $query->select(['products.title', 'products.sku', 'products.main_image_internal_thumb', 'products.main_image_internal', 'product_barcodes.barcode',
            'locations_assign.product_id', 'locations_assign.po_id', 'locations_assign.booking_id', 'locations_master.location', 'locations_master.id',
            'locations_assign.warehouse_id as warehouse_id', 'po_products.supplier_sku',
        ]);
        $query->selectRaw('SUM(location_assign_trans.qty) as total_pending_qty');

        $query->where('locations_assign.putaway_type', 1);

        if (isset($params['pickbulkjobs']) && !empty($params['pickbulkjobs'])) {
            $query->where('locations_master.type_of_location', $params['pickbulkjobs']);
        }
        if (isset($params['location']) && !empty($params['location'])) {
            $query->where('locations_master.location', $params['location']);
        }


        if (isset($params['productSearch']) && !empty($params['productSearch'])) {
            $searchString = trim($params['productSearch']);
            $query->where(function($q) use($searchString) {
                $q->where('product_barcodes.barcode', '=', $searchString);
                // $q->orWhere('booking_po_product_case_details.barcode', '=', $searchString);
                $q->orWhere('products.title', '=', $searchString);
                $q->orWhere('products.sku', '=', $searchString);
                $q->orWhere('po_products.supplier_sku', '=', $searchString);
            });
        }
        $query->groupBy('locations_assign.product_id');

        if ($params['sortBy'] == "title")
            $query->orderBy("products.title", $params['sortDirection']);

        if ($params['sortBy'] == "qty")
            $query->orderBy("total_pending_qty", $params['sortDirection']);

        if ($params['sortBy'] == "goods-in")
            $query->orderBy('location_assign_trans.created_at', $params['sortDirection']);
        return $query->get();
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     * @desc return all products for replen
     */
    public
            function getReplenPalletProducts($params) {
        $query = $this->join('locations_master', function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
        });

        $query->join('location_assign_trans', function($q) {
            $q->on('locations_assign.id', 'location_assign_trans.loc_ass_id');
        });

        $query->join('product_barcodes', function($q) {
            $q->on('location_assign_trans.barcode_id', 'product_barcodes.id');
        });
        $query->join('products', function($q) {
            $q->on('locations_assign.product_id', 'products.id');
        });

        $query->leftJoin('product_supplier', function($leftJoin) {
            $leftJoin->on('locations_assign.product_id', '=', 'product_supplier.product_id')
                    ->where('product_supplier.is_default', '=', 1);
        });

        $query->select(['products.title', 'products.sku', 'products.main_image_internal_thumb', 'products.main_image_internal', 'product_barcodes.barcode',
            'locations_assign.product_id', 'locations_assign.po_id', 'locations_assign.booking_id', 'locations_master.location', 'locations_master.id',
            'locations_assign.warehouse_id as warehouse_id', 'product_supplier.supplier_sku',
        ]);
        $query->selectRaw('SUM(location_assign_trans.qty) as total_pending_qty');

        $query->where('locations_assign.putaway_type', 2);
        if (isset($params['pickbulkjobs']) && !empty($params['pickbulkjobs'])) {
            $query->where('locations_master.type_of_location', $params['pickbulkjobs']);
        }
        if (isset($params['location']) && !empty($params['location'])) {
            $query->where('locations_master.location', $params['location']);
        }

        if (isset($params['productSearch']) && !empty($params['productSearch'])) {
            $searchString = trim($params['productSearch']);
            $query->where(function($q) use($searchString) {
                $q->where('product_barcodes.barcode', '=', $searchString);
                // $q->orWhere('booking_po_product_case_details.barcode', '=', $searchString);
                $q->orWhere('products.title', '=', $searchString);
                $q->orWhere('products.sku', '=', $searchString);
                $q->orWhere('product_supplier.supplier_sku', '=', $searchString);
            });
        }
        $query->groupBy('locations_assign.product_id');

        if ($params['sortBy'] == "title")
            $query->orderBy("products.title", $params['sortDirection']);

        if ($params['sortBy'] == "qty")
            $query->orderBy("total_pending_qty", $params['sortDirection']);

        if ($params['sortBy'] == "goods-in")
            $query->orderBy('location_assign_trans.created_at', $params['sortDirection']);
        return $query->get();
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     * @desc return pallet type is it for material or replen
     */
    public
            function getPalletType($params) {
        $query = self::join('locations_master', function($q) {
                    $q->on('locations_assign.location_id', 'locations_master.id');
                });
        //$query->where('location_assign.warehouse_id', $params['warehouse_id']);
        $query->where('locations_master.location', $params['location']);
        $query->whereIn('locations_master.type_of_location', [3, 4]); // pick/bulk or putaway location only
        return $query->select(['locations_assign.putaway_type'])->first();
    }

    public
            function getPalletProducts($params) {

    }

    public static
            function getAssignedPickLocations($params, $product_id) {
        $object = self::select('locations_assign.available_qty', 'locations_master.location', 'locations_master.type_of_location', 'locations_assign.qty_fit_in_location', 'locations_assign.id');
        $object->join('locations_master', function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
            $q->where('locations_master.status', 1);
            $q->whereIn('locations_master.type_of_location', locationAssignPickLocationType());
        });
        if (!empty($params)) {
            $object->orderBy($params['order_column'], $params['order_dir']);
        }
        else {
            $object->orderBy('locations_assign.id', 'desc');
        }
        $object->where('locations_assign.product_id', $product_id);
        return $object->get();
    }

    public static
            function getProductStock($product_id) {
        $pickBulkLocation = array(1, 2, 7, 12);
        $object           = self::select('locations_assign.id', 'locations_assign.total_qty');
        $object->join('locations_master', function($q) use ($pickBulkLocation) {
            $q->on('locations_assign.location_id', 'locations_master.id');
            $q->where('locations_master.status', 1);
            $q->whereIn('locations_master.type_of_location', $pickBulkLocation);
        });

        $object->where('locations_assign.product_id', $product_id);
        $object->groupBy('locations_assign.id');

        return $object->get()->sum('total_qty');
    }

}
