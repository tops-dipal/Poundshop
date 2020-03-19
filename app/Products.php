<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\CommodityCodes;
use App\ProductBarcode;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;

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
            return url('/storage/uploads/product-images/no-image.jpeg');
    }

    public
            function getMainImageInternalThumbAttribute() {
        if (!empty($this->attributes['main_image_internal_thumb']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_internal_thumb'];
        else
            return url('/storage/uploads/product-images/no-image.jpeg');
    }

    public
            function getMainImageMarketplaceAttribute() {
        if (!empty($this->attributes['main_image_marketplace']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_marketplace'];
        else
            return url('/storage/uploads/product-images/no-image.jpeg');
    }

    public
            function getMainImageMarketplaceThumbAttribute() {
        if (!empty($this->attributes['main_image_marketplace_thumb']))
            return url('/storage/uploads') . '/' . $this->attributes['main_image_marketplace_thumb'];
        else if (!empty($this->attributes['main_image_marketplace_url']))
            return $this->attributes['main_image_marketplace_url'];
        else
            return url('/storage/uploads/product-images/no-image.jpeg');
    }

    public static
            function getActualValOfMagentoImage($image = '') {
        if (!empty($image) && $image == url('/storage/uploads/product-images/no-image.jpeg')) {
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
            'products.last_cost_price'
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
        $query->where('title', 'like', '%' . $searchString . '%');
        $query->orWhere('supplier_sku', 'like', '%' . $searchString . '%');
        $query->orWhere('sku', 'like', '%' . $searchString . '%')->orWhere('product_barcodes.barcode', 'like', '%' . $searchString . '%');
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

}
