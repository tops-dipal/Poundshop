<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\CommodityCodes;
use App\ProductBarcode;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;

class Products extends Model {

    use SoftDeletes;

    protected
            $table   = 'products';
    protected
            $guarded = [];

    public
            function getMainImageInternalAttribute() {
        if (!empty($this->attributes['main_image_internal']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_internal'];
        else
            return url('/img/no-image.jpeg');
    }

    public
            function getMainImageInternalThumbAttribute() {
        if (!empty($this->attributes['main_image_internal_thumb']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_internal_thumb'];
        else
            return url('/img/no-image.jpeg');
    }

    public
            function getMainImageMarketplaceAttribute() {
        if (!empty($this->attributes['main_image_marketplace']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_marketplace'];
        else
            return url('/img/no-image.jpeg');
    }

    public
            function getMainImageMarketplaceThumbAttribute() {
        if (!empty($this->attributes['main_image_marketplace_thumb']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_marketplace_thumb'];
        else if (!empty($this->attributes['main_image_marketplace_url']))
            return $this->attributes['main_image_marketplace_url'];
        else
            return url('/img/no-image.jpeg');
    }

    public static
            function getActualValOfMagentoImage($image = '') {
        if (!empty($image) && ($image == url('/storage/uploads/product-images/no-image.jpeg') || $image == url('/img/no-image.jpeg'))) {
            return '0';
        }
        else {
            return '1';
        }
    }

    /**
     * @author : Hitesh Tank
     * @return type (title)
     * @Desc : get Title with stripslashes data
     */
    public
            function getTitleAttribute() {
        return $this->attributes['title'] = stripslashes($this->attributes['title']);
    }

    /**
     *
     * @return type
     */
    public
            function buying_range() {
        return $this->belongsTo(Range::class, 'buying_category_id');
    }

    public
            function commodity() {
        return $this->belongsTo(CommodityCodes::class, 'commodity_code_id');
    }

    public
            function user() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public
            function variation_theme_detatils() {
        return $this->belongsTo(VariationThemes::class, 'variation_theme_id');
    }

    /**
     *
     * @return type
     */
    public
            function barCodes() {
        return $this->hasMany(ProductBarcode::class, 'product_id')->orderBy('created_at', 'desc');
    }

    public
            function suppliers() {
        return $this->hasMany(ProductSupplier::class, 'product_id')->orderBy('created_at', 'desc');
    }

    public
            function supplier() {
        return $this->hasOne(ProductSupplier::class, 'product_id')->orderBy('created_at', 'desc');
    }

    public
            function locations() {
        return $this->hasMany(ProductLocation::class, 'product_id');
    }

    public
            function variation() {
        return $this->hasMany(Products::class, 'parent_id')->where('product_type', 'variation');
    }

    public
            function productImages() {
        return $this->hasMany(ProductImage::class, 'product_id');
    }

    public
            function tags() {
        return $this->belongsToMany(Tags::class, 'product_tags', 'product_id', 'tag_id');
    }

    public
            function bookingQCChecklist() {
        return $this->hasMany('\App\BookingQcChecklist', 'product_id', 'id');
    }

    public
            function locationAssign() {
        return $this->hasMany(LocationAssign::class, 'product_id')->orderBy('created_at', 'desc');
    }

    /**
     * Supplier Listing
     * @author : Shubham Dayma
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public static
            function getAllListingRecords($perPage = '', $params = array()) {
        $select_array = array(
            'products.id',
            'products.title',
            'products.sku',
            'products.product_type',
            'products.product_identifier',
            'products.single_selling_price',
            'products.main_image_internal',
            'products.single_selling_price',
            'products.last_cost_price',
            'products.last_cost_price',
            'products.last_stock_receipt_date',
            'products.last_stock_receipt_qty',
            'products.is_listed_on_magento',
        );

        foreach (product_logic_base_tags() as $db_column => $tag_caption) {
            $select_array[] = 'products.is_' . $db_column;
        }

        $object = self::select($select_array);


        $object->where(function($q) {
            $q->whereIn('products.product_type', ['normal', 'parent']);
            $q->orWhereNull('products.product_type');
        });

// variation filter
        if (
                !empty($params['search']) &&
                (
                $params['advance_search']['search_type'] == 'all' ||
                $params['advance_search']['search_type'] == 'sku' ||
                $params['advance_search']['search_type'] == 'title' ||
                $params['advance_search']['search_type'] == 'product_barcode'
                ) ||
                !empty($params['advance_search']['filter_missing_images']) ||
                !empty($params['advance_search']['filter_flammable']) ||
                !empty($params['advance_search']['filter_reduced']) ||
                !empty($params['advance_search']['filter_do_not_buy_again']) ||
                !empty($params['advance_search']['filter_heavy']) ||
                !empty($params['advance_search']['filter_promotional']) ||
                !empty($params['advance_search']['filter_show_new_products_only'])
        ) {

            $object->leftJoin('products as var_products', function ($join) {
                $join->on('var_products.parent_id', '=', 'products.id');
                $join->whereNull('var_products.deleted_at');
                $join->where('var_products.product_type', 'variation');
            });
        }

// barcode filter
        if (
                !empty($params['search']) &&
                (
                $params['advance_search']['search_type'] == 'product_barcode' ||
                $params['advance_search']['search_type'] == 'all'
                )
        ) {
            $object->leftJoin('product_barcodes', function ($join) {
                $join->on('product_barcodes.product_id', '=', 'products.id');
            });

            $object->leftJoin('product_barcodes as var_product_barcodes', function ($join) {
                $join->on('var_product_barcodes.product_id', '=', 'var_products.id');
            });
        }

        $object->where(function($q) use ($params) {
            if (!empty($params['search']) && $params['advance_search']['search_type'] == 'all') {
                $q->where('products.sku', $params['search']);
                $q->orWhere('products.title', 'like', "%" . $params['search'] . "%");
                $q->orWhere('products.product_identifier', $params['search']);
                $q->orwhere('product_barcodes.barcode', $params['search']);

                $q->orwhere('var_products.sku', $params['search']);
                $q->orWhere('var_products.title', 'like', "%" . $params['search'] . "%");
                $q->orWhere('var_products.product_identifier', $params['search']);
                $q->orwhere('var_product_barcodes.barcode', $params['search']);
            }

            if (!empty($params['advance_search'])) {
// Barcode
                if (!empty($params['search']) && $params['advance_search']['search_type'] == 'product_barcode') {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.product_identifier', $params['search']);
                        $sub_q->orWhere('product_barcodes.barcode', $params['search']);
                        $sub_q->orWhere('var_products.product_identifier', $params['search']);
                        $sub_q->orWhere('var_product_barcodes.barcode', $params['search']);
                    });
                }

// Sku
                if (!empty($params['search']) && $params['advance_search']['search_type'] == 'sku') {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.sku', $params['search']);
                        $sub_q->orWhere('var_products.sku', $params['search']);
                    });
                }

// title
                if (!empty($params['search']) && $params['advance_search']['search_type'] == 'title') {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.title', 'like', "%" . $params['search'] . "%");
                        $sub_q->orWhere('var_products.title', 'like', "%" . $params['search'] . "%");
                    });
                }

// filter_missing_images
                if (!empty($params['advance_search']['filter_missing_images'])) {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.mp_image_missing', 1);
                        $sub_q->orWhere('var_products.mp_image_missing', 1);
                    });
                }

// filter_flammable
                if (!empty($params['advance_search']['filter_flammable'])) {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.is_flammable', 1);
                        $sub_q->orWhere('var_products.is_flammable', 1);
                    });
                }

// filter_reduced
                if (!empty($params['advance_search']['filter_reduced'])) {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.is_reduced', 1);
                        $sub_q->orWhere('var_products.is_reduced', 1);
                    });
                }


                if (!empty($params['advance_search']['filter_do_not_buy_again'])) {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.is_do_not_buy_again', 1);
                        $sub_q->orWhere('var_products.is_do_not_buy_again', 1);
                    });
                }

                if (!empty($params['advance_search']['filter_heavy'])) {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.is_heavy', 1);
                        $sub_q->orWhere('var_products.is_heavy', 1);
                    });
                }

                if (!empty($params['advance_search']['filter_promotional'])) {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.is_promotional', 1);
                        $sub_q->orWhere('var_products.is_promotional', 1);
                    });
                }

                if (!empty($params['advance_search']['filter_custom_tags'])) {
                    $q->whereHas('tags', function($tag_q) use ($params) {
                        $tag_q->whereIn('name', $params['advance_search']['filter_custom_tags']);
                    });
                }

                if (!empty($params['advance_search']['filter_show_new_products_only'])) {
                    $q->where(function($sub_q) use ($params) {
                        $sub_q->where('products.is_listed_on_magento', 0);
                        $sub_q->orWhere('var_products.is_listed_on_magento', 0);
                    });
                }

                if (!empty($params['advance_search']['filter_show_seasonal_products_only']) &&
                        !empty($params['advance_search']['filter_seasonal_from_date']) &&
                        !empty($params['advance_search']['filter_seasonal_to_date'])
                ) {

                    $from_date = str_replace('/', '-', $params['advance_search']['filter_seasonal_from_date']);

                    $from_date = date('0000' . '-m-d', strtotime($from_date));

                    $to_date = str_replace('/', '-', $params['advance_search']['filter_seasonal_to_date']);

                    $to_date = date('0000' . '-m-d', strtotime($to_date));

                    $q->where('products.is_seasonal', 1);

                    $q->where(function($q) use($from_date, $to_date) {
                        $q->whereBetween('products.seasonal_from_date', array($from_date, $to_date));
                        $q->orWhereBetween('products.seasonal_to_date', array($from_date, $to_date));
                    });
                }
            }
        });

// SEARCH FOLLOWED WITH LARAVEL eloquent (Remove group by products.id if you are using this query)
// if (!empty($params['search']) && $params['advance_search']['search_type'] == 'all')
// {
//     $q->where('products.sku', $params['search']);
//     $q->orWhere('products.title', 'like', "%" . $params['search'] . "%");
//     $q->orWhere('products.product_identifier', 'like', "%" . $params['search'] . "%");
//     $q->orwhere('product_barcodes.barcode', $params['search']);
//     $q->orWhereHas('barCodes', function($barcode_q) use ($params) {
//         $barcode_q->where('barcode', $params['search']);
//     });
// }
// Advance search
// if (!empty($params['advance_search'])) {
//     $object->where(function($q) use ($params) {
//         // Barcode
//         if (!empty($params['search']) && $params['advance_search']['search_type'] == 'product_barcode') {
//                 $q->where('product_barcodes.barcode', $params['search']);
//             });
//         }
//         // Sku
//         if (!empty($params['search']) && $params['advance_search']['search_type'] == 'sku') {
//             $q->where('products.sku', $params['search']);
//         }
//         // title
//         if (!empty($params['search']) && $params['advance_search']['search_type'] == 'title') {
//             $q->where('products.title', 'like', "%" . $params['search'] . "%");
//         }
//         // product_identifier
//         if (!empty($params['search']) && $params['advance_search']['search_type'] == 'product_identifier') {
//             $q->where('products.product_identifier', 'like', "%" . $params['search'] . "%");
//         }
//         // filter_missing_images
//         if (!empty($params['advance_search']['filter_missing_images'])) {
//             $q->where('products.mp_image_missing', 1);
//         }
//         // filter_missing_product_info
//         if (!empty($params['advance_search']['filter_missing_product_info'])) {
//             $q->where('products.info_missing', 1);
//         }
//         // filter_flammable
//         if (!empty($params['advance_search']['filter_flammable'])) {
//             $q->where('products.is_flammable', 1);
//         }
//         // filter_reduced
//         if (!empty($params['advance_search']['filter_reduced'])) {
//             $q->where('products.is_reduced', 1);
//         }
//         if (!empty($params['advance_search']['filter_do_not_buy_again'])) {
//             $q->where('products.is_do_not_buy_again', 1);
//         }
//         if (!empty($params['advance_search']['filter_heavy'])) {
//             $q->where('products.is_heavy', 1);
//         }
//         if (!empty($params['advance_search']['filter_custom_tags'])) {
//             $q->whereHas('tags', function($tag_q) use ($params) {
//                 $tag_q->whereIn('name', $params['advance_search']['filter_custom_tags']);
//             });
//         }
//         if (!empty($params['advance_search']['filter_show_new_products_only'])) {
//             $q->where('products.is_listed_on_magento', 0);
//         }
//         if (!empty($params['advance_search']['filter_show_seasonal_products_only']) &&
//                 !empty($params['advance_search']['filter_seasonal_from_date']) &&
//                 !empty($params['advance_search']['filter_seasonal_to_date'])
//         ) {
//             $from_date = str_replace('/', '-', $params['advance_search']['filter_seasonal_from_date']);
//             $from_date = date('0000' . '-m-d', strtotime($from_date));
//             $to_date = str_replace('/', '-', $params['advance_search']['filter_seasonal_to_date']);
//             $to_date = date('0000' . '-m-d', strtotime($to_date));
//             $q->where('products.is_seasonal', 1);
//             $q->where(function($q) use($from_date, $to_date) {
//                 $q->whereBetween('products.seasonal_from_date', array($from_date, $to_date));
//                 $q->orWhereBetween('products.seasonal_to_date', array($from_date, $to_date));
//             });
//         }
//     });
// }
// Variation Search
// if (!empty($params['search']) && $params['advance_search']['search_type'] == 'all'
// ) {
//     // for variation
//     $object->orWhereHas('variation', function($variation_q) use ($params) {
//         $variation_q->where('sku', $params['search']);
//         $variation_q->orWhere('title', 'like', "%" . $params['search'] . "%");
//         $variation_q->orWhereHas('barCodes', function($barcode_q) use ($params) {
//             $barcode_q->where('barcode', $params['search']);
//         });
//     });
// }
// // Variation Advance search
// if (!empty($params['advance_search']))
// {
//     if(
//         in_array($params['search'], array('title', 'product_barcode', 'sku')) ||
//         !empty($params['advance_search']['filter_missing_images']) ||
//         !empty($params['advance_search']['filter_flammable']) ||
//         !empty($params['advance_search']['filter_reduced']) ||
//         !empty($params['advance_search']['filter_do_not_buy_again']) ||
//         !empty($params['advance_search']['filter_heavy']) ||
//         !empty($params['advance_search']['filter_custom_tags'])
//     )
//     {
//         $object->orWhere(function ($variation_obj) use ($params){
//             // for variation
//             $variation_obj->whereHas('variation', function($variation_q) use ($params) {
//                 if (!empty($params['search']) && $params['advance_search']['search_type'] == 'title')
//                 {
//                     // for variation
//                     $variation_q->where('title', 'like', "%" . $params['search'] . "%");
//                 }
//                 if (!empty($params['search']) && $params['advance_search']['search_type'] == 'product_barcode')
//                 {
//                     $variation_q->whereHas('barCodes', function($barcode_q) use ($params) {
//                         $barcode_q->where('barcode', $params['search']);
//                     });
//                 }
//                 if (!empty($params['search']) && $params['advance_search']['search_type'] == 'sku')
//                 {
//                     $variation_q->where('sku', $params['search']);
//                 }
//                 if (!empty($params['advance_search']['filter_missing_images'])) {
//                     $variation_q->where('mp_image_missing', 1);
//                 }
//                 if (!empty($params['advance_search']['filter_flammable'])) {
//                     $variation_q->where('is_flammable', 1);
//                 }
//                 if (!empty($params['advance_search']['filter_reduced'])) {
//                     $variation_q->where('is_reduced', 1);
//                 }
//                 if (!empty($params['advance_search']['filter_do_not_buy_again'])) {
//                     $variation_q->where('is_do_not_buy_again', 1);
//                 }
//                 if (!empty($params['advance_search']['filter_heavy'])) {
//                     $variation_q->where('is_heavy', 1);
//                 }
//                 if (!empty($params['advance_search']['filter_custom_tags'])) {
//                     $variation_q->whereHas('tags', function($tag_q) use ($params) {
//                                         $tag_q->whereIn('name', $params['advance_search']['filter_custom_tags']);
//                                     });
//                 }
//             });
//         });
//     }
// }

        $object->groupBy('products.id');

        $object->orderBy($params['order_column'], $params['order_dir']);

        return $object->paginate($perPage);
    }

    public static
            function searchProduct($searchString) {
        $query      = self::with(['supplier', 'barCodes' => function($q) {
                                $q->orderBy('created_at', 'desc');
                            }, 'commodity' => function($q) {
                                $q->with('importDuties');
                            }])
                        ->leftJoin('product_barcodes', function($q) use ($searchString) {
                            $q->on('products.id', 'product_barcodes.product_id');
                        })->leftJoin('product_supplier', function($q) use ($searchString) {
            $q->on('products.id', 'product_supplier.product_id');
        });

        $query->select('products.*');
        $query->where(function($q) {
            $q->whereIn('products.product_type', ['parent', 'normal']);
            $q->orWhereNull('products.product_type');
        });

        $query->where(function($q) use($searchString) {
            $q->where('title', 'like', '%' . $searchString . '%');
            $q->orWhere('supplier_sku', 'like', '%' . $searchString . '%');
            $q->orWhere('sku', 'like', '%' . $searchString . '%')->orWhere('product_barcodes.barcode', 'like', '%' . $searchString . '%');
        });
        return $query->distinct()->orderBy('title')->paginate(50);
    }

    /**
     *
     * @param type $barcodes
     */
    public static
            function addNewProductAsDraft($barcode) {

        $obj              = new self;
        $obj->created_by  = Auth::user()->id;
        $obj->modified_by = Auth::user()->id;
        $obj->sku         = get_sku();
        $obj->save();
        return $obj->id;
    }

    public static
            function setInfoMissingFlag($details = array(), $runUpdateQuery = false) {
        if (!empty($details)) {
// set default to yes
            $flag = 1;

            $id = "";

            $product_details = array();

// check details in products table first
            if (!empty($details['product_id']) || !empty($details['product_object'])) {
                $product_details = !empty($details['product_object']) ? $details['product_object'] : array();
                if (!empty($details['product_id'])) {
                    $product_details = self::find($details['product_id']);
                }

                $id = !empty($product_details->id) ? $product_details->id : '';

                if (
                        (!empty($product_details->getOriginal('main_image_marketplace')) ||
                        !empty($product_details->main_image_marketplace_url)
                        ) &&
                        !empty($product_details->title) &&
                        !empty($product_details->single_selling_price) &&
                        !empty($product_details->buying_category_id)
// !empty($product_details->last_cost_price)
                ) {
                    $flag = 0;
                }
            }
            elseif (!empty($product_detail_array['id'])) {
                $id = !empty($product_detail_array['id']) ? $product_detail_array['id'] : '';

                if (
                        (!empty($product_detail_array['main_image_marketplace']) ||
                        !empty($product_detail_array['main_image_marketplace_url'])
                        ) &&
                        !empty($product_detail_array['title']) &&
                        !empty($product_detail_array['single_selling_price']) &&
                        !empty($product_detail_array['buying_category_id'])
// !empty($product_detail_array['last_cost_price'])
                ) {
                    $flag = 0;
                }
            }

// check single barcode
            if ($flag == 0 && !empty($id)) {
                $singleBarcode_exist = ProductBarcode::where(function($q) use ($id) {
                            $q->where('barcode_type', 1);
                            $q->where('product_id', $id);
                        })->pluck('id')->toArray();

                if (empty($singleBarcode_exist)) {
                    $flag = 1;
                }
            }

// check supplier sku
            if ($flag == 0 && !empty($id)) {
                $defaultSupplierDetails = ProductSupplier::select('supplier_sku')->where(function($q) use ($id) {
                            $q->where('is_default', 1);
                            $q->where('product_id', $id);
                        })->get()->first();

                if (empty($defaultSupplierDetails) || empty(@$defaultSupplierDetails->supplier_sku)) {
                    $flag = 1;
                }
            }

// update info missing flag
            if ($runUpdateQuery == true && !empty($id)) {
                if (empty($product_details)) {
                    $product_details = self::find($id);
                }

                if (!empty($product_details)) {
                    if ($product_details->info_missing != $flag) {
                        $product_details->info_missing = $flag;

                        $product_details->save();
                    }
                }
            }

            return $flag;
        }
        else {
            return 'No detail parameter found';
        }
    }

    public
            function get_replen_counter_data($product_id = '', $warehouse_id = '') {
        $self_object = self::select('products.id', 'products.ros', 'products.stock_hold_days', 'products.is_seasonal', 'products.seasonal_from_date', 'products.seasonal_to_date', 'products.is_promotional', 'products.promotion_start_at', 'products.promotion_end_at');

        $self_object->selectRaw('SUM(CASE WHEN locations_master.type_of_location = 1 OR locations_master.type_of_location = 6 OR locations_master.type_of_location = 7 OR locations_master.type_of_location = 3 THEN locations_assign.total_qty END) as total_in_pick');

        $self_object->selectRaw('SUM(CASE WHEN locations_master.type_of_location = 2 OR locations_master.type_of_location = 4 OR locations_master.type_of_location = 12 THEN locations_assign.total_qty END) as total_in_bulk');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 1 OR locations_master.type_of_location = 6 OR locations_master.type_of_location = 7 OR locations_master.type_of_location = 3 THEN locations_assign.id END ORDER BY locations_assign.location_id ASC) as all_pick_assign_id');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 1 OR locations_master.type_of_location = 6 OR locations_master.type_of_location = 7 OR locations_master.type_of_location = 3 THEN locations_master.id END ORDER BY locations_assign.location_id ASC) as all_pick_aisle_id');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 1 OR locations_master.type_of_location = 6 OR locations_master.type_of_location = 7 OR locations_master.type_of_location = 3 THEN locations_assign.total_qty END ORDER BY locations_assign.location_id ASC) as all_pick_aisle_qty');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 1 OR locations_master.type_of_location = 6 OR locations_master.type_of_location = 7 OR locations_master.type_of_location = 3 THEN locations_master.aisle END ORDER BY locations_assign.location_id ASC) as all_pick_aisle');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 1 OR locations_master.type_of_location = 6 OR locations_master.type_of_location = 7 OR locations_master.type_of_location = 3 THEN CONCAT(location_assign_trans.loc_ass_id,"||",location_assign_trans.qty,"||",location_assign_trans.best_before_date) END) as all_pick_bbd');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 2 OR locations_master.type_of_location = 4 OR locations_master.type_of_location = 12 THEN locations_assign.id END ORDER BY locations_assign.location_id ASC) as all_bulk_assign_id');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 2 OR locations_master.type_of_location = 4 OR locations_master.type_of_location = 12 THEN locations_master.id END ORDER BY locations_assign.location_id ASC) as all_bulk_aisle_id');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 2 OR locations_master.type_of_location = 4 OR locations_master.type_of_location = 12 THEN locations_assign.total_qty END ORDER BY locations_assign.location_id ASC) as all_bulk_aisle_qty');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 2 OR locations_master.type_of_location = 4 OR locations_master.type_of_location = 12 THEN locations_master.aisle END ORDER BY locations_assign.location_id ASC) as all_bulk_aisle');

        $self_object->selectRaw('GROUP_CONCAT(CASE WHEN locations_master.type_of_location = 2 OR locations_master.type_of_location = 4 OR locations_master.type_of_location = 12 THEN CONCAT(location_assign_trans.loc_ass_id,"||",location_assign_trans.qty,"||",location_assign_trans.best_before_date) END) as all_bulk_bbd');

        $self_object->selectRaw('SUM(locations_assign.total_qty) as total_in_warehouse');

        $self_object->selectRaw('SUM(locations_assign.allocated_qty) as total_reserved');

        $self_object->selectRaw('(products.ros * products.stock_hold_days) as qty_stock_hold');

        $self_object->where('products.stock_hold_days', '!=', '0');

        $self_object->where('products.ros', '!=', '0');

        $self_object->where('products.is_deleted', '=', '0');

        if (!empty($product_id)) {
            $self_object->where('products.id', $product_id);
        }

        $self_object->leftJoin('locations_assign', function($join) {
            $join->on('locations_assign.product_id', '=', 'products.id');
        });

        $self_object->leftJoin('location_assign_trans', function($join) {
            $join->on('location_assign_trans.loc_ass_id', '=', 'locations_assign.id');
        });

        $self_object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        if (!empty($warehouse_id)) {
            $self_object->where('locations_assign.warehouse_id', $warehouse_id);
            $self_object->where('locations_master.site_id', $warehouse_id);
        }

        $self_object->groupBy('products.id');

        return $self_object->get();
    }

    public static
            function getLocationQuantity($productId, $params, $perPage) {
        $selectArr = array('locations_assign.id', 'locations_assign.product_id', 'locations_assign.location_id', 'locations_master.aisle', 'locations_master.location', 'locations_master.site_id', 'locations_master.type_of_location', 'locations_master.id as loc_id', 'warehouse_master.name as site_name', 'locations_assign.total_qty', 'warehouse_master.is_default');
        $object    = \App\LocationAssign::select($selectArr);
        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        $object->Join('warehouse_master', function($join) {
            $join->on('warehouse_master.id', '=', 'locations_assign.warehouse_id');
        });
        $object->where('warehouse_master.id', $params['advance_search']['warehouse_id']);
        $object->where('locations_assign.product_id', $productId);
        $object->whereIn('locations_master.type_of_location', [1, 2, 3, 4, 6, 7, 12]);
        $object->groupBy('locations_master.id');
        $object->orderBy($params['order_column'], $params['order_dir']);
        return $object->paginate($perPage);
    }

    public static
            function getOnPoBUtNotBookedIn($productId, $params, $perPage) {
        $selectArr = array('purchase_order_master.po_number', 'supplier_master.name', 'purchase_order_master.supplier_order_number', 'purchase_order_master.id');
        $object    = \App\PurchaseOrder::select($selectArr);

        $object->leftJoin('supplier_master', function($join) {
            $join->on('supplier_master.id', '=', 'purchase_order_master.supplier_id');
        });

        $object->selectRaw('SUM(po_products.total_quantity) as total_available_qty');

        $object->leftJoin('po_products', function($join) {
            $join->on('po_products.po_id', '=', 'purchase_order_master.id');
        });

        $object->leftJoin('booking_purchase_orders', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'purchase_order_master.id');
        });

        $object->leftJoin('bookings', function($join) {
            $join->on('bookings.id', '=', 'booking_purchase_orders.booking_id');
        });



        $object->whereNULL('booking_purchase_orders.id');
        $object->where('po_products.product_id', $productId);


        $object->orderBy($params['order_column'], $params['order_dir']);
        $object->groupBy('purchase_order_master.id');
      //  dd($object->paginate($perPage));
        return $object->paginate($perPage);
    }

    public static
            function bookedInNotArraivedYet($productId, $params, $perPage) {
//$productId=57;
        $selectArr = array('bookings.booking_ref_id', 'bookings.book_date', 'bookings.slot_id', 'slots.from', 'slots.to', 'supplier_master.name as supplier_name', 'po_products.product_id', 'bookings.id', 'booking_po_products.product_id as bpp_id', 'booking_po_products.id as bpp_main_id', 'po_products.total_quantity', 'booking_po_products.difference', 'booking_purchase_orders_discrepancy.id as dis_id', 'purchase_order_master.po_number');
        $object    = \App\Booking::select($selectArr);


// $object->selectRaw('GROUP_CONCAT(DISTINCT(purchase_order_master.po_number) SEPARATOR "<br/>") as po_list');

        /* $object->selectRaw('IF(count(booking_po_products.id)=0,po_products.total_quantity,
          IF(booking_po_products.difference<0 AND COUNT(booking_purchase_orders_discrepancy.id)>0,
          IF(booking_purchase_orders_discrepancy.status NOT IN (1,6),
          (SUM(booking_purchase_orders_discrepancy.qty)-booking_po_products.difference),SUM(booking_purchase_orders_discrepancy.qty)),booking_po_products.difference))
          as total_product_qty'); */

        $object->selectRaw('IF(COUNT(booking_po_products.id)=0, po_products.total_quantity,
            IF(booking_po_products.difference<0,IF(COUNT(booking_purchase_orders_discrepancy.id)=0,(po_products.total_quantity-booking_po_products.qty_received),
            IF(booking_purchase_orders_discrepancy.discrepancy_type=1 AND booking_purchase_orders_discrepancy.status NOT IN(1,6),(SUM(IF(booking_purchase_orders_discrepancy.discrepancy_type=1 AND booking_purchase_orders_discrepancy.status NOT IN(1,6),booking_purchase_orders_discrepancy.qty,0))),po_products.total_quantity-booking_po_products.qty_received)
            ),0)) as total_product_qty');


        $object->leftJoin('booking_purchase_orders', 'booking_purchase_orders.booking_id', '=', 'bookings.id');

        $object->leftJoin('purchase_order_master', 'purchase_order_master.id', '=', 'booking_purchase_orders.po_id');




        $object->Join('slots', 'slots.id', '=', 'bookings.slot_id');
        $object->Join('supplier_master', 'supplier_master.id', '=', 'bookings.supplier_id');

        $object->leftJoin('po_products', 'po_products.po_id', '=', 'purchase_order_master.id');
        $object->leftJoin('booking_po_products', function($join) {
            $join->on('booking_po_products.booking_id', '=', 'bookings.id');
            $join->on('booking_po_products.po_id', '=', 'booking_purchase_orders.po_id');
            $join->on('booking_po_products.product_id', '=', 'po_products.product_id');
        });
        $object->leftJoin('booking_purchase_orders_discrepancy', function($join) {
            $join->on('booking_purchase_orders_discrepancy.booking_po_products_id', '=', 'booking_po_products.id');
        });

        $object->where('bookings.warehouse_id', $params['advance_search']['warehouse_id']);
        $object->where('po_products.product_id', $productId);
        $object->groupBy('bookings.id');

        $object->havingRaw('total_product_qty != ?', [0]);
        $object->orderBy($params['order_column'], $params['order_dir']);

        $perPage = $params['length'];

        $curPage     = $params['page'];
        $itemQuery   = clone $object;
        $itemQuery->addSelect('bookings.*');
        $items       = $itemQuery->forPage($curPage, $perPage)->get();
        $totalResult = $object->addSelect(DB::raw('count(*) as count'))->get();

        $totalItems = count($totalResult);

        $paginatedItems = new LengthAwarePaginator($items->all(), $totalItems, $perPage);

        return $paginatedItems;
    }

    public static
            function getWaitionPutAway($productId, $params, $perPage) {


//get records from location assign which have pickout away pallet and bulk putaway pallet
        $selectArr = array('locations_assign.id', 'locations_assign.product_id', 'locations_assign.location_id', 'locations_master.aisle', 'locations_master.location', 'locations_master.site_id', 'locations_master.type_of_location', 'locations_master.id as loc_id', 'warehouse_master.is_default');
        $object    = \App\LocationAssign::select($selectArr);

        $object->selectRaw('SUM(IF(locations_master.type_of_location=3,locations_assign.total_qty,0)) as total_pick_pallet_qty');
        $object->selectRaw('SUM(IF(locations_master.type_of_location=4,locations_assign.total_qty,0)) as total_bulk_pallet_qty');



        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        $object->Join('warehouse_master', function($join) {
            $join->on('warehouse_master.id', '=', 'locations_assign.warehouse_id');
        });

        $object->where('warehouse_master.id', $params['advance_search']['warehouse_id']);
        $object->where('locations_assign.product_id', $productId);
        $object->whereIn('locations_master.type_of_location', [3, 4]);
        $object->groupBy('locations_master.id');
        $object->orderBy($params['order_column'], $params['order_dir']);

        return $object->paginate($perPage);
    }

    public static
            function getTotalWaitingToBePutAway($productId, $warehouseId) {
        $selectArr = array('locations_assign.id', 'locations_assign.product_id', 'locations_assign.location_id', 'locations_master.aisle', 'locations_master.location', 'locations_master.site_id', 'locations_master.type_of_location', 'locations_master.id as loc_id', 'locations_assign.total_qty', 'warehouse_master.is_default');
        $object    = \App\LocationAssign::select($selectArr);

        $object->selectRaw('SUM(IF(locations_master.type_of_location=3,locations_assign.total_qty,0)) as total_pick_pallet_qty');
        $object->selectRaw('SUM(IF(locations_master.type_of_location=4,locations_assign.total_qty,0)) as total_bulk_pallet_qty');



        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
        });

        $object->Join('warehouse_master', function($join) {
            $join->on('warehouse_master.id', '=', 'locations_assign.warehouse_id');
        });
        $object->where('warehouse_master.id', $warehouseId);
        $object->where('locations_assign.product_id', $productId);
        $object->whereIn('locations_master.type_of_location', [3, 4]);
        $object->groupBy('locations_master.id');


        return $object->get()->sum('total_qty');
    }

    public static
            function getTotalOnPONotBookedIn($productId) {
        $selectArr = array('purchase_order_master.po_number', 'supplier_master.name', 'purchase_order_master.supplier_order_number', 'purchase_order_master.id', 'po_products.total_quantity');
        $object    = \App\PurchaseOrder::select($selectArr);

        $object->leftJoin('supplier_master', function($join) {
            $join->on('supplier_master.id', '=', 'purchase_order_master.supplier_id');
        });

        $object->selectRaw('SUM(po_products.total_quantity) as total_available_qty');

        $object->leftJoin('po_products', function($join) {
            $join->on('po_products.po_id', '=', 'purchase_order_master.id');
        });

        $object->leftJoin('booking_purchase_orders', function($join) {
            $join->on('booking_purchase_orders.po_id', '=', 'purchase_order_master.id');
        });

        $object->leftJoin('bookings', function($join) {
            $join->on('bookings.id', '=', 'booking_purchase_orders.booking_id');
        });
        $object->whereNULL('booking_purchase_orders.id');
        $object->where('po_products.product_id', $productId);
       
        return $object->get()->sum('total_available_qty');
    }

    public static
            function getTotallocationQty($productId, $warehouseId) {
        $selectArr = array('locations_assign.id', 'locations_assign.product_id', 'locations_assign.location_id', 'locations_master.aisle', 'locations_master.location', 'locations_master.site_id', 'locations_master.type_of_location', 'locations_master.id as loc_id', 'warehouse_master.name as site_name', 'locations_assign.total_qty', 'warehouse_master.is_default');
        $object    = \App\LocationAssign::select($selectArr);

        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
            $join->where('locations_master.status', 1);
        });


        $object->Join('warehouse_master', function($join) {
            $join->on('warehouse_master.id', '=', 'locations_assign.warehouse_id');
        });
        $object->where('warehouse_master.id', $warehouseId);
        $object->where('locations_assign.product_id', $productId);
        $object->whereIn('locations_master.type_of_location', [1, 2, 3, 4, 6, 7, 12]);
        $object->groupBy('locations_master.id');
        return $object->get()->sum('total_qty');
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     */
    public
            function getPutAwayProductDetail($params = []) {
        $query = self::select(["products.id", "products.title", "po_products.supplier_sku", "products.main_image_internal_thumb", "products.main_image_internal",
                    "booking_po_products.booking_id", "booking_po_products.barcode", "products.sku",]);

        $query->leftJoin('booking_po_products', function($q) {
            $q->on('booking_po_products.product_id', 'products.id');
        });
        $query->leftJoin('booking_po_product_case_details', function($q) {
            $q->on('booking_po_product_case_details.booking_po_product_id', 'booking_po_products.id');
        });

        $query->leftJoin('po_products', function($q) {
            $q->on('booking_po_products.po_product_id', 'po_products.id');
        });

        $query->where('products.id', $params['product_id']);
        $query->where('booking_po_products.booking_id', $params['booking_id']);
        $query->groupBy('products.id');
        return $query->first();
    }

    public
            function getPutAwayReplenProductDetail($params = []) {
        $query = self::join('locations_assign', function($q) {
                    $q->on('products.id', 'locations_assign.product_id');
                });

        $query->leftJoin('product_supplier', function($leftJoin) {
            $leftJoin->on('locations_assign.product_id', '=', 'product_supplier.product_id')
                    ->on('product_supplier.is_default', '=', DB::raw('1'));
        });
        $query->join('locations_master', function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
        });

        $query->join('location_assign_trans', function($q) {
            $q->on('locations_assign.id', 'location_assign_trans.loc_ass_id');
        });

        $query->join('product_barcodes', function($q) {
            $q->on('location_assign_trans.barcode_id', 'product_barcodes.id');
        });
        $query->select(["products.id", "products.title", "product_supplier.supplier_sku",
            "products.main_image_internal_thumb",
            "product_barcodes.barcode", "products.sku"]);

        $query->where('locations_assign.product_id', $params['product_id']);
        $query->where('locations_assign.putaway_type', 2);

        $query->groupBy('products.id');
        return $query->first();
    }

    /**
     * @author Hitesh Tank
     * @return type
     * @Desc : Return the total number of location which is associated with pick/bulk location
     */
    public
            function getLocationAssignedProduct() {
        $query = $this->join('locations_assign', function($q) {
            $q->on('products.id', 'locations_assign.product_id');
        });
        $query->join('locations_master', function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
        });
        $query->where('products.id', $this->attributes['id']);
        $query->select(['locations_assign.id', 'locations_master.location', 'type_of_location', 'aisle', 'qty_fit_in_location', 'total_qty', 'available_qty']);
        $query->whereIn('locations_master.type_of_location', [1, 2]);
        return $query->get();
    }

    public static
            function getLastScannedProductInfo($productId) {
        $object = Products::select('products.last_scanned_datetime', 'products.last_scanned_by', 'users.first_name', 'users.last_name');
        $object->Join('users', function($join) {
            $join->on('users.id', '=', 'products.last_scanned_by');
        });
        $object->where('products.id', $productId);
        return $object->first();
    }

    public static
            function labelcountsOfLocationQty($productId, $warehouseId, $typeOfCount) {

        $selectArr = array('locations_assign.id', 'locations_assign.product_id', 'locations_assign.location_id', 'locations_master.aisle', 'locations_master.location', 'locations_master.site_id', 'locations_master.type_of_location', 'locations_master.id as loc_id', 'warehouse_master.name as site_name', 'locations_assign.total_qty', 'warehouse_master.is_default');
        $object    = \App\LocationAssign::select($selectArr);

        $object->leftJoin('locations_master', function($join) {
            $join->on('locations_master.id', '=', 'locations_assign.location_id');
            $join->where('locations_master.status', 1);
        });


        $object->Join('warehouse_master', function($join) {
            $join->on('warehouse_master.id', '=', 'locations_assign.warehouse_id');
        });
        $object->where('warehouse_master.id', $warehouseId);
        $object->where('locations_assign.product_id', $productId);
        $sqlQuery = $object->groupBy('locations_master.id');

        $sq1 = $sqlQuery;
        $sq2 = $sqlQuery;
        $sq3 = $sqlQuery;
        $sq4 = $sqlQuery;
        $sq5 = $sqlQuery;

        if ($typeOfCount == 'totalInBulkLocationQty') {
            $totalInBulkLocationQty = $sq1->where('locations_master.type_of_location', 2)->get()->sum('total_qty');
            return $totalInBulkLocationQty;
        }
        else if ($typeOfCount == 'totalInPickLocationQty') {
            $totalInPickLocationQty = $sq2->where('locations_master.type_of_location', 1)->get()->sum('total_qty');
            return $totalInPickLocationQty;
        }
        else if ($typeOfCount == 'totalInReturnLocationQty') {
            $totalInReturnLocationQty = $sq3->where('locations_master.type_of_location', 10)->get()->sum('total_qty');
            return $totalInReturnLocationQty;
        }
        else if ($typeOfCount == 'totalInPickLocationCount') {
            $totalInPickLocationCount = $sq4->where('locations_master.type_of_location', 1)->get()->count();
            return $totalInPickLocationCount;
        }
        else if ($typeOfCount == 'totalInBulkLocationCount') {
            $totalInBulkLocationCount = $sq5->where('locations_master.type_of_location', 2)->get()->count();
            return $totalInBulkLocationCount;
        }
    }

    /**
     * @author Hitesh tAnk
     * @param type $assigmentId
     * @return type
     * @desc return best before date product
     */
    public
            function bestBeforeDateProducts($assigmentId) {
        return LocationAssignTrans::where('loc_ass_id', $assigmentId)->select(['best_before_date'])->first();
    }

    /**
     * @author Hitesh Tank
     * @param type $params
     * @return type
     */
//    public
//            function putAwayCaseDetail($params) {
//        $query = $this->join('booking_po_products', function($q) {
//            $q->on('products.id', 'booking_po_products.product_id');
//        });
//        $query->join('booking_po_product_case_details', function($q) {
//            $q->on('booking_po_products.id', 'booking_po_product_case_details.booking_po_product_id');
//        });
//        $query->join('booking_po_product_locations', function($q) {
//            $q->on('booking_po_product_case_details.id', 'booking_po_product_locations.case_detail_id');
//        });
//        $query->join('locations_master', function($q) {
//            $q->on('booking_po_product_locations.location_id', 'locations_master.id');
//        });
//        $query->whereRaw('booking_po_product_locations.qty != booking_po_product_locations.put_away_qty');
//        $query->where('booking_po_product_case_details.is_include_count', 1);
//        $query->where('booking_po_products.product_id', $this->attributes['id']);
//        $query->where('booking_po_products.booking_id', $params['booking_id']);
//        $query->where('locations_master.location', $params['location']);
//        $query->orderBy('booking_po_product_case_details.case_type', 'DESC');
//        $query->select(
//                ["locations_master.site_id as warehouse_id", "booking_po_product_case_details.barcode",
//                    "booking_po_product_case_details.case_type",
//                    "booking_po_product_case_details.qty_per_box",
//                    "booking_po_product_case_details.no_of_box",
//                    "booking_po_product_locations.qty as total",
//                    "booking_po_product_locations.put_away_qty",
//                    "booking_po_product_locations.best_before_date",
//                    "booking_po_products.id as booking_po_product_id",
//                    "booking_po_product_case_details.id as booking_product_case_id",
//                    "booking_po_product_locations.id as booking_po_product_location_id",
//                    "booking_po_products.booking_id",
//                    "booking_po_products.po_id",
//                    "booking_po_product_locations.best_before_date",
//                    "booking_po_products.product_id as product_id",
//                    "booking_po_product_case_details.id as booking_po_product_case_details_id",
//        ]);
//        return $query->get();
//    }

    /**
     * @author Hitesh tank
     * @param type $params
     * @return type
     */
    public
            function putAwayCaseDetail($params) {
        $query = $this->join('locations_assign', function($q) {
            $q->on('products.id', 'locations_assign.product_id');
        });

        $query->join('locations_master', function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
        });

        $query->join('location_assign_trans', function($q) {
            $q->on('location_assign_trans.loc_ass_id', 'locations_assign.id');
        });
        $query->join('product_barcodes', function($q) {
            $q->on('location_assign_trans.barcode_id', 'product_barcodes.id');
        });
        $query->where('locations_assign.product_id', $this->attributes['id']);
        $query->where('locations_assign.booking_id', $params['booking_id']);
        $query->where('locations_master.location', $params['location']);
        $query->orderBy('location_assign_trans.case_type', 'DESC');
        $query->select(["locations_assign.product_id", "locations_assign.putaway_type", "locations_master.site_id as warehouse_id", "locations_assign.booking_id", "locations_assign.po_id", "location_assign_trans.*", "product_barcodes.barcode"]);
        return $query->get();
    }

    /**
     * @author Hitesh tank
     * @param type $params
     * @return type
     */
    public
            function getPutAwayReplenCaseDetail($params) {
        $query = $this->join('locations_assign', function($q) {
            $q->on('products.id', 'locations_assign.product_id');
        });

        $query->join('locations_master', function($q) {
            $q->on('locations_assign.location_id', 'locations_master.id');
        });

        $query->join('location_assign_trans', function($q) {
            $q->on('location_assign_trans.loc_ass_id', 'locations_assign.id');
        });
        $query->join('product_barcodes', function($q) {
            $q->on('location_assign_trans.barcode_id', 'product_barcodes.id');
        });
        $query->where('locations_assign.product_id', $this->attributes['id']);
        $query->where('locations_assign.putaway_type', 2);
        $query->where('locations_master.location', $params['location']);
        $query->orderBy('location_assign_trans.case_type', 'DESC');
        $query->select(["locations_assign.putaway_type", "locations_master.site_id as warehouse_id", "locations_assign.booking_id", "locations_assign.po_id", "location_assign_trans.*", "product_barcodes.barcode"]);
        return $query->get();
    }

    /* Don't remove commented code */

    /**
     * @author Kinjal
     * @param type $params
     * @return type
     */
    public static
            function getTotalNotArrivedYetQty($productId, $warehouseId) {
        $selectArr = array('bookings.booking_ref_id', 'bookings.book_date', 'bookings.slot_id', 'slots.from', 'slots.to', 'supplier_master.name as supplier_name', 'po_products.product_id', 'bookings.id', 'booking_po_products.product_id as bpp_id', 'booking_po_products.id as bpp_main_id', 'po_products.total_quantity', 'booking_po_products.difference', 'booking_purchase_orders_discrepancy.id as dis_id', 'purchase_order_master.po_number');
        $object    = \App\Booking::select($selectArr);


        $object->selectRaw('IF(COUNT(booking_po_products.id)=0, po_products.total_quantity,
            IF(booking_po_products.difference<0,IF(COUNT(booking_purchase_orders_discrepancy.id)=0,(po_products.total_quantity-booking_po_products.qty_received),
            IF(booking_purchase_orders_discrepancy.discrepancy_type=1 AND booking_purchase_orders_discrepancy.status NOT IN(1,6),(SUM(IF(booking_purchase_orders_discrepancy.discrepancy_type=1 AND booking_purchase_orders_discrepancy.status NOT IN(1,6),booking_purchase_orders_discrepancy.qty,0))),po_products.total_quantity-booking_po_products.qty_received)
            ),0)) as total_product_qty');


        $object->leftJoin('booking_purchase_orders', 'booking_purchase_orders.booking_id', '=', 'bookings.id');

        $object->leftJoin('purchase_order_master', 'purchase_order_master.id', '=', 'booking_purchase_orders.po_id');



        $object->Join('slots', 'slots.id', '=', 'bookings.slot_id');
        $object->Join('supplier_master', 'supplier_master.id', '=', 'bookings.supplier_id');

        $object->leftJoin('po_products', 'po_products.po_id', '=', 'purchase_order_master.id');
        $object->leftJoin('booking_po_products', function($join) {
            $join->on('booking_po_products.booking_id', '=', 'bookings.id');
            $join->on('booking_po_products.po_id', '=', 'booking_purchase_orders.po_id');
            $join->on('booking_po_products.product_id', '=', 'po_products.product_id');
        });
        $object->leftJoin('booking_purchase_orders_discrepancy', function($join) {
            $join->on('booking_purchase_orders_discrepancy.booking_po_products_id', '=', 'booking_po_products.id');
        });

        $object->where('po_products.product_id', $productId);
        $object->where('bookings.warehouse_id', $warehouseId);
        $object->groupBy('bookings.id');
        $object->havingRaw('total_product_qty != ?', [0]);
        return $object->get()->sum('total_product_qty');
    }

    /* get Product detail for scanning barcode */

    public static
            function productDetailWithLocationsByScanBarcode($barcode) {
        $object = self::with(['locationAssign' => function ($query) {
                        $query->join('locations_master', 'locations_assign.location_id', '=', 'locations_master.id')->select([
                                    'locations_master.location',
                                    'locations_master.type_of_location',
                                    'locations_master.aisle', 'locations_assign.*'
                                ])->
                                selectRaw('(locations_assign.qty_fit_in_location-locations_assign.available_qty) as available_space');
                    }])->select('products.id', 'products.title', 'products.sku', 'products.product_identifier', 'products.main_image_internal_thumb');

        $object->leftJoin('product_barcodes', function($join) {
            $join->on('product_barcodes.product_id', '=', 'products.id');
        });

        $object->where(function($q) use ($barcode) {

            $q->where('products.product_identifier', $barcode);
            $q->orwhere('product_barcodes.barcode', $barcode);
        });

        return $object->first();
    }

}
