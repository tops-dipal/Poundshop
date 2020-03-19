<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Products;
use App\ProductImage;
use Illuminate\Support\Facades\View;
use App\Http\Requests\Api\Common\CreateRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Imagine;
use Intervention\Image\ImageManagerStatic as Image;
use App\ProductBarcode;
use App\ProductSupplier;
use App\ProductLocation;
use Batch;
use DB;
use App\Tags;
use App\ProductTags;
use Gumlet\ImageResize;
use App\Range;
use Lang;

class ProductsController extends Controller {

    function __construct(Request $request) {
        $this->middleware('permission:product-list', ['only' => ['index']]);

        $this->middleware('permission:product-create', ['only' => ['saveBuyingRange']]);

        $this->middleware('permission:product-edit', ['only' => ['saveStockFile', 'saveSuppliers', 'saveBarcodes', 'saveImages', 'saveVariations', 'upload_product_img', 'addSupplier', 'saveImages', 'upload_product_img']]);

        $this->middleware('permission:product-delete', ['only' => ['destroy', 'productSupplierDestroy', 'productBarcodeDestroy', 'deleteImage']]);

        $route = $request->route();

        if (!empty($route)) {
            $action_array = explode('@', $route->getActionName());

            $function_name = !empty($action_array[1]) ? $action_array[1] : '';

            if (!empty($function_name)) {
                if ($function_name == 'saveBuyingRange') {
// CreateRequest::$roles_array = [
//                     'buying_category_id' => 'required',
//                   ];
                }

                if ($function_name == 'saveStockFile') {
                    CreateRequest::$roles_array = [
                        'id'                      => 'required',
                        'product_identifier'      => 'required',
                        'product_identifier_type' => 'required',
                        'product_type'            => 'required',
                        'sku'                     => 'required',
                        'title'                   => 'required',
                        // 'brand' => 'required',
                        'single_selling_price' => 'required',
                        // 'long_description' => 'required',
                    ];
                }

                if ($function_name == 'addSupplier') {
                    CreateRequest::$roles_array = [
                        'product_id'  => 'required',
                        'supplier_id' => 'required',
                    ];
                }

                if ($function_name == 'saveSuppliers') {
                    CreateRequest::$roles_array = [
                        'id' => 'required',
                    ];
                }

                if ($function_name == 'saveBarcodes') {
                    $unique_except_id = "";

                    if (!empty($request->barcode_id)) {
                        $unique_except_id = ',' . $request->barcode_id;
                    }

                    CreateRequest::$roles_array = [
                        'id'      => 'required',
                        'barcode' => 'required|unique:product_barcodes,barcode' . $unique_except_id,
                    ];
                }


                if ($function_name == 'saveVariations') {
                    CreateRequest::$roles_array = [
                        'id'        => 'required',
                        'var_sku.0' => 'required',
                        'var_sku.*' => 'required',
                            // 'var_barcode.*' => 'required',
                    ];

                    CreateRequest::$message_array = [
                        'var_sku.0.required' => 'Please add variation product.'
                    ];
                }
            }
        }
    }

    /**
     * Display a listing of the resource.
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    public
            function index(CreateRequest $request) {
        try {
            $adv_search_array = array();

            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }

            $columns = [
                0 => 'products.id',
                2 => 'products.title',
                3 => 'products.sku',
                7 => 'products.product_identifier',
                8 => 'products.last_cost_price',
                9 => 'products.single_selling_price',
            ];


            $params = array(
                'order_column'   => $columns[$request->order[0]['column']],
                'order_dir'      => $request->order[0]['dir'],
                'search'         => (!empty($request->search['value']) || $request->search['value'] != NULL) ? $request->search['value'] : '',
                'advance_search' => $adv_search_array,
            );

            $result = Products::getAllListingRecords($request->length, $params);


            $data = [];

// listing data
            if (!empty($result)) {
                $data = $result->getCollection()->transform(function ($result) use ($data, $request) {
                    $tags = $result->tags->pluck('name')->toArray();

                    foreach (product_logic_base_tags() as $db_tag_field => $tag_caption) {
                        $db_tag_field = 'is_' . $db_tag_field;

                        if ($result->$db_tag_field == 1) {
                            $tags[] = $tag_caption;
                        }
                    }

                    $tempArray   = array();
                    $tempArray[] = View::make('product.list-checkbox', ['object' => $result])->render();
                    $tempArray[] = View::make('product.listing-image', ['object' => $result])->render();

                    $title = "";


                    if (!empty($result->title)) {
                        if ($request->user()->can('product-edit')) {
                            $edit_url = url("product/form/$result->id?active_tab=stock-file");

                            $title = '<a href="' . $edit_url . '">' . ucwords($result->title) . '</a>';
                        }
                        else {
                            $title = ucwords($result->title);
                        }
                    }

                    $tempArray[]      = $title;
                    $tempArray[]      = !empty($result->sku) ? $result->sku : '-';
                    $tempArray[]      = "-";
                    $tempArray[]      = "-";
                    $tempArray[]      = "-";
                    $tempArray[]      = !empty($result->product_identifier) ? $result->product_identifier : '-';
                    $tempArray[]      = !empty($result->last_cost_price) ? $result->last_cost_price : '0.00';
                    $tempArray[]      = !empty($result->single_selling_price) ? $result->single_selling_price : '-';
                    $tempArray[]      = !empty($tags) ? implode(', ', $tags) : '-';
                    $tempArray[]      = "-";
                    $tempArray[]      = ($result->is_listed_on_magento == 1) ? Lang::get('messages.inventory.magento_enabled') : Lang::get('messages.inventory.magento_disabled');
                    $viewActionButton = View::make('product.action-buttons', ['object' => $result]);
                    $tempArray[]      = $viewActionButton->render();
                    return $tempArray;
                });
            }

            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $result->total(), // Total number of records
                "recordsFiltered" => $result->total(),
                "data"            => $data // Total data array
            ];

            return response()->json($jsonData);
        }
        catch (Exception $ex) {

        }
    }

    /**
     * Store a newly created resource in storage.
     * @author : Shubham Dayma
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public
            function saveBuyingRange(CreateRequest $request) {
        if (!empty($request)) {
            $data = [];

            $resp_msg = "";

            $db_array['id'] = !empty($request->id) ? $request->id : "";

            $range_details = Range::find($request->buying_category_id);

            if (!empty($range_details->id)) {
                $db_array['buying_category_id'] = $range_details->id;

                $db_array['is_seasonal'] = ($range_details->seasonal_status == 1) ? 1 : 0;

                $db_array['seasonal_from_date'] = $db_array['is_seasonal'] == 1 ? date('0000-m-d', strtotime($range_details->seasonal_from)) : NULL;

                $db_array['seasonal_to_date'] = $db_array['is_seasonal'] == 1 ? date('0000-m-d', strtotime($range_details->seasonal_to)) : NULL;
            }
            else {
                $db_array['buying_category_id'] = NULL;

                $db_array['is_seasonal'] = 0;

                $db_array['seasonal_from_date'] = NULL;

                $db_array['seasonal_to_date'] = NULL;
            }

            if (!empty($db_array['id'])) {
// setInfoMissingFlag
                $info_missing_config['product_id'] = $db_array['id'];

                $db_array['info_missing'] = Products::setInfoMissingFlag($info_missing_config);

                $db_array['modified_by'] = $request->user()->id;

                Products::where('id', $db_array['id'])->update($db_array);

                $data['id'] = $db_array['id'];

                $resp_msg = 'Product updated successfully';
            }
            else {
                $db_array['product_type'] = NULL;

                $db_array['sku'] = $request->sku;

                $db_array['info_missing'] = '1';

                $db_array['mp_image_missing'] = '1';

                $db_array['created_by'] = $request->user()->id;

                $data['id'] = Products::create($db_array)->id;

                $data['record_created'] = true;

                $resp_msg = 'Product inserted successfully';
            }


            if (!empty($resp_msg)) {

                return $this->sendResponse($resp_msg, 200, $data);
            }
            else {
                return $this->sendValidation(array('Unable to save product, please try again'), 422);
            }
        }
        else {
            return $this->sendValidation(array('Unable to save product, please try again'), 422);
        }
    }

    public
            function saveStockFile(CreateRequest $request) {
        if (!empty($request->input())) {
            $data = [];

            $check_tags = [];

            $db_product_insert = [];

            $logical_tags = product_logic_base_tags();

            $product_exist = Products::find($request->id);

            if (empty($product_exist)) {
                return $this->sendValidation(array('No prodcut found with this id'), 422);
            }

            $db_array['id']                      = !empty($request->id) ? $request->id : "";
            $db_array['product_identifier_type'] = $request->product_identifier_type;
            $db_array['product_identifier']      = $request->product_identifier;
            if (!empty($request->product_type)) {
                $db_array['product_type'] = $request->product_type;
            }

            $db_array['title']                 = $request->title;
            $db_array['short_title']           = $request->short_title;
            $db_array['sku']                   = $request->sku;
            $db_array['country_of_origin']     = $request->country_of_origin;
            $db_array['commodity_code_id']     = $request->commodity_code_id;
            $db_array['is_essential']          = $request->is_essential;
            $db_array['on_hold']               = $request->on_hold;
            $db_array['brand']                 = $request->brand;
            $db_array['threshold_quantity']    = $request->threshold_quantity;
            $db_array['single_selling_price']  = $request->single_selling_price;
            $db_array['vat_type']              = $request->vat_type;
            $db_array['bulk_selling_price']    = $request->bulk_selling_price;
            $db_array['bulk_selling_quantity'] = $request->bulk_selling_quantity;
            $db_array['recom_retail_price']    = $request->recom_retail_price;
            $db_array['comment']               = $request->comment;
            $db_array['long_description']      = $request->long_description;
            $db_array['short_description']     = $request->short_description;
            $db_array['product_length']        = $request->product_length;
            $db_array['product_width']         = $request->product_width;
            $db_array['product_height']        = $request->product_height;
            $db_array['product_weight']        = $request->product_weight;
            $db_array['modified_by']           = $request->user()->id;

// setInfoMissingFlag
            $info_missing_config['product_detail_array'] = $db_array;

            $db_array['info_missing'] = Products::setInfoMissingFlag($info_missing_config);

            foreach ($logical_tags as $logical_key => $logical_tag) {
                $db_array['is_' . $logical_key] = 0;
            }

            $posted_tags = !empty($request->tags) ? $request->tags : array();

            if (!empty($posted_tags)) {
                $flipped_logical_tags = array_flip($logical_tags);
                foreach ($posted_tags as $tag_key => $tag) {
                    if (!in_array($tag, $flipped_logical_tags)) {
                        $check_tags[] = $tag;
                    }
                    else {
                        $db_array['is_' . $tag] = 1;
                        unset($posted_tags[$tag_key]);
                    }
                }
            }

            $tag_details = Tags::whereIn('name', $check_tags)->pluck('id', 'name')->toArray();

            foreach ($posted_tags as $post_tag) {
                if (empty($tag_details[$post_tag])) {
                    $tag_details[$post_tag] = Tags::create(array('name' => $post_tag))->id;
                }
            }

            $pivotData = array_fill(0, count($tag_details), [
                'created_by' => $request->user()->id,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $syncData = array_combine($tag_details, $pivotData);

            $product_exist->tags()->sync($syncData);


// dd($tag_details);
// $exist_product_tags = $product_exist->tags()->pluck('name', 'tags.id')->toArray();
// if(!empty($posted_tags))
// {
//     $tag_details = Tags::whereIn('name', $posted_tags)->pluck('name', 'id')->toArray();
//     foreach($posted_tags as $post_tag)
//     {
//         if(empty($exist_product_tags[$post_tag]))
//         {
//             $db_product_tags['product_id'] = $db_array['id'];
//             $db_product_tags['created_by'] = $request->user()->id;
//             if(!empty($tag_details[$post_tag]))
//             {
//                 $db_product_tags['tag_id'] = $tag_details[$post_tag];
//             }
//             else
//             {
//                 $db_product_tags['tag_id'] = Tags::create(array('name' => $post_tag))->id;
//             }
//             $db_product_insert[] = $db_product_tags;
//         }
//         else
//         {
//             unset($exist_product_tags[$post_tag]);
//         }
//     }
// }
// if(!empty($exist_product_tags))
// {
//     ProductTags::whereIn('tag_id', $exist_product_tags)->delete();
// }
// if(!empty($db_product_insert))
// {
//     ProductTags::insert($db_product_insert);
// }

            if (Products::where('id', $db_array['id'])->update($db_array)) {
                $data['id'] = $db_array['id'];

                return $this->sendResponse('Product updated successfully', 200, $data);
            }
            else {
                return $this->sendValidation(array('Unable to save product, please try again'), 422);
            }
        }
        else {
            return $this->sendValidation(array('Unable to save product, please try again'), 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @author : Shubham Dayma
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function destroy(Request $request, $variation_delete = array()) {
        try {
// check id and delete record(s)
            if (!empty($request->id) || !empty($variation_delete)) {
                $delete_images = [];

                if (!empty($variation_delete)) {
                    $delete_ids = $variation_delete;
                }
                else {
                    $delete_ids = $request->id;
                }

                $variations = Products::whereIn('parent_id', $delete_ids)->pluck('id')->toArray();

                if (!empty($variations)) {
                    $delete_ids = array_merge($delete_ids, $variations);
                }

                $main_images = Products::select('main_image_internal', 'main_image_marketplace')->whereIn('id', $delete_ids)->get();

                if (!empty($main_images)) {
                    foreach ($main_images as $main_image) {
                        if (!empty($main_image->main_image_internal)) {
                            $delete_images[] = $main_image->main_image_internal;
                        }

                        if (!empty($main_image->main_image_marketplace)) {
                            $delete_images[] = $main_image->main_image_marketplace;
                        }
                    }
                }

                $other_images = ProductImage::whereIn('product_id', $delete_ids)->pluck('image')->toArray();

                if (!empty($other_images)) {
                    $delete_images = array_merge($delete_images, $other_images);
                }

                DB::beginTransaction();

                $product_delete_array = array(
                    'is_deleted' => '1',
                    'deleted_at' => date('Y-m-d H:i:s'),
                );

                Products::whereIn('id', $delete_ids)->update($product_delete_array);

                ProductTags::whereIn('product_id', $delete_ids)->delete();

                ProductBarcode::whereIn('product_id', $delete_ids)->delete();

                ProductSupplier::whereIn('product_id', $delete_ids)->delete();

                ProductImage::whereIn('product_id', $delete_ids)->delete();

                ProductLocation::whereIn('product_id', $delete_ids)->delete();

                DB::commit();

                if (!empty($delete_images)) {
                    foreach ($delete_images as $delete_image) {
                        Storage::delete($delete_image);
                    }
                }

                return $this->sendResponse('Record(s) has been deleted successfully', 200);
            }
            else {
                return $this->sendValidation(array('No record(s) found for delete, please try again'), 422);
            }
        }
        catch (Exception $ex) {
            DB::rollBack();
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function saveSuppliers(CreateRequest $request) {
        if (!empty($request->id) && $request->product_supplier_id) {
            $update_array = array();

            $product_id = $request->id;

            foreach ($request->product_supplier_id as $key => $id) {
                $db_array['id']                 = $id;
                $db_array['supplier_sku']       = $request->supplier_sku[$id];
                $db_array['price_per_case']     = $request->price_per_case[$id];
                $db_array['quantity']           = $request->quantity[$id];
                $db_array['quantity_per_case']  = $request->quantity_per_case[$id];
                $db_array['min_order_quantity'] = $request->min_order_quantity[$id];
                $db_array['note']               = $request->note[$id];
                $db_array['is_default']         = ($request->default == $db_array['id']) ? 1 : 0;
                $db_array['modified_by']        = $request->user()->id;

                $update_array[] = $db_array;
            }

            if (!empty($update_array)) {
                $obj = new ProductSupplier;

                $result = Batch::update($obj, $update_array, 'id');

// setInfoMissingFlag
                $info_missing_config['product_id'] = $product_id;

                Products::setInfoMissingFlag($info_missing_config, TRUE);

                return $this->sendResponse('Data saved successfully', 200);
            }
        }
        else {
            if (empty($request->id)) {
                return $this->sendValidation(array('Request Id not found'), 422);
            }

            if (empty($request->product_supplier_id)) {
                return $this->sendValidation(array('Please select supplier'), 422);
            }
        }
    }

    public
            function addSupplier(CreateRequest $request) {
        $product_exist = Products::find($request->product_id);

        if (!empty($product_exist)) {
            $supplier_exist = ProductSupplier::where('product_id', $request->product_id)->pluck('supplier_id')->toArray();

            if (!in_array($request->supplier_id, $supplier_exist)) {
                if (empty($supplier_exist)) {
                    $db_array['is_default'] = 1;
                }
                else {
                    $db_array['is_default'] = 0;
                }

                $db_array['supplier_id'] = $request->supplier_id;

                $db_array['product_id'] = $request->product_id;

                $db_array['created_by'] = $request->user()->id;

                if (ProductSupplier::create($db_array)) {
                    return $this->sendResponse('Supplier assigned successfully', 200);
                }
                else {
                    return $this->sendValidation(array('Unable to assign supplier, please try again'), 422);
                }
            }
            else {
                return $this->sendValidation(array('Supplier is already assign to this product'), 422);
            }
        }
        else {
            return $this->sendValidation(array('No product found with that id'), 422);
        }
    }

    function productSupplierDestroy(Request $request) {
// check id and delete record(s)
        if (!empty($request->id)) {
            if (ProductSupplier::whereIn('id', $request->id)->delete()) {
                return $this->sendResponse('Supplier(s) has been deleted successfully', 200);
            }
            else {
                return $this->sendValidation(array('Supplier(s) did not deleted, please try again'), 422);
            }
        }
        else {
            return $this->sendValidation(array('No supplier found for delete, please try again'), 422);
        }
    }

    function saveBarcodes(CreateRequest $request) {
        $product_exist = Products::find($request->id);

        if (!empty($product_exist)) {
            $db_array['id'] = $request->barcode_id;

            $db_array['product_id'] = $request->id;

            $db_array['barcode_type'] = $request->barcode_type;

            $db_array['barcode'] = $request->barcode;

            $db_array['case_quantity'] = $request->case_quantity;

            if ($db_array['barcode_type'] == '1') {
                $db_array['case_quantity'] = 1;
            }

            if (empty($db_array['id'])) {
                $db_array['created_by'] = $request->user()->id;

                ProductBarcode::create($db_array);

                $resp_msg = 'Barcode updated successfully';
            }
            else {
                $db_array['modified_by'] = $request->user()->id;

                ProductBarcode::where('id', $db_array['id'])->update($db_array);

                $resp_msg = 'Barcode added successfully';
            }

// setInfoMissingFlag
            $info_missing_config['product_object'] = $product_exist;

            $db_array['info_missing'] = Products::setInfoMissingFlag($info_missing_config, TRUE);

            if (!empty($resp_msg)) {
                return $this->sendResponse($resp_msg, 200, array('reload' => true));
            }
            else {
                return $this->sendValidation(array('Something went wrong, please try again'), 422);
            }
        }
        else {
            return $this->sendValidation(array('No product found with that id'), 422);
        }
    }

    /**
     * @author  Hitesh Tank
     * @updatedAt 28th Nov
     * @param \App\Http\Requests\Api\PO\SearchRequest $request
     * @return \Illuminate\Http\Response
     */
    public
            function searchProducts(\App\Http\Requests\Api\Inventory\SearchRequest $request) {
        try {
            $products = Products::searchProduct($request->input('search-keyword'));

            if ($products) {
                foreach ($products as $product) {
                    $product->main_image_internal;
                    $product->commodity = $product->commmodity;
                }
                if (!empty($products) && @count($products) > 0) {

                    $products = makeNulltoBlank($products->toArray());
                    return $this->sendResponse('products listed', 200, $products['data']);
                }
                else {
                    return $this->sendError('Products not found', 200);
                }
            }
            else {
                return $this->sendError('Opps..! Choose invalid Supplier', 422);
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    function productBarcodeDestroy(Request $request) {
// check id and delete record(s)
        if (!empty($request->id)) {
            if (ProductBarcode::whereIn('id', $request->id)->delete()) {
                return $this->sendResponse('Barcode(s) has been deleted successfully', 200);
            }
            else {
                return $this->sendValidation(array('Barcode(s) did not deleted, please try again'), 422);
            }
        }
        else {
            return $this->sendValidation(array('No barcode found for delete, please try again'), 422);
        }
    }

    public
            function saveImages(Request $request) {
        try {

            $files = $request->file('images');

            $remove_time_images = (isset($request->remove_time_images) ? $request->remove_time_images : array());

            $storeImagearray = array();

            $i             = 0;
            $mainImage     = 0;
            $magentoImg    = 0;
            $request_photo = 0;
            $product       = Products::find($request->id);
            if (isset($request->is_request_new_photo)) {
                if ($product->update(['is_request_new_photo' => $request->is_request_new_photo])) {
                    $request_photo = 1;
                }
                else {
                    $request_photo = 0;
                }
            }
            if ($request->hasFile('main_image_internal')) {

                if (!empty($product)) {
                    $updateArr = array();

                    $file         = $request->file('main_image_internal');
                    $uploadedFile = $file;
                    $extension    = strtolower($file->getClientOriginalExtension());
                    $folder       = "product-images/" . $request->id;
                    if (!Storage::exists($folder)) {
                        Storage::makeDirectory($folder, 0777, true);
                    }
                    if ($extension == "mp4") {
                        $filename                               = time() . 'internalVideo.' . $extension;
                        $name                                   = md5($filename . time()) . '.' . $uploadedFile->getClientOriginalExtension();
                        $path                                   = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
                        $updateArr['main_image_internal']       = $path;
                        $updateArr['main_image_internal_thumb'] = $path;
                    }
                    else {
                        $name = time() . 'internalImage.' . $extension;
                        $path = Storage::putFileAs(($folder), $uploadedFile, $name);
                    }

                    if (!empty($path) && $extension != "mp4") {

                        $folder = "product-images/" . $request->id . '/thumbnail/';
                        if (!Storage::exists($folder)) {
                            Storage::makeDirectory($folder, 0777, true);
                        }

                        $thumbName1 = explode('/', $path);

                        $thumbName = $thumbName1[2];

                        $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . "product-images/" . $request->id . '/' . $thumbName;

                        $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $folder . $thumbName;
                        /* $image  = new ImageResize($originalPath);
                          $image->resize(100, 100, true);
                          $image->save($thumbPath); */
                        Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                            $constraint->upsize();
                            $constraint->aspectRatio();
                        })->save($thumbPath, 100);
                        $updateArr['main_image_internal']       = $path;
                        $updateArr['main_image_internal_thumb'] = $folder . $thumbName;

                        if (isset($product->main_image_internal) && !empty($product->main_image_internal)) {
                            Storage::delete($product->main_image_internal);

                            $thumbName = explode('/', $product->main_image_internal)[2];

                            Storage::delete($folder . '/thumbnail/' . $thumbName);
                        }
                    }


                    if ($product->update($updateArr)) {
                        $mainImage = 1;
                    }
                }
            }
            if ($request->hasFile('main_image_marketplace')) {

                if (!empty($product)) {
                    $updateArr    = array();
                    $file         = $request->file('main_image_marketplace');
                    $uploadedFile = $file;
                    $extension    = strtolower($file->getClientOriginalExtension());
                    $folder       = "product-images/" . $request->id;
                    if (!Storage::exists($folder)) {
                        Storage::makeDirectory($folder, 0777, true);
                    }
                    if ($extension == "mp4") {
                        $filename                                  = $uploadedFile->getClientOriginalName();
                        $name                                      = time() . 'marketplace.' . $uploadedFile->getClientOriginalExtension();
                        $path                                      = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
                        $updateArr['main_image_marketplace']       = $path;
                        $updateArr['main_image_marketplace_thumb'] = $path;
                    }
                    else {
                        $name = time() . 'marketplaceImage.' . $uploadedFile->getClientOriginalExtension();
                        $path = Storage::putFileAs(($folder), $uploadedFile, $name);
                    }
                    if (!empty($path) && $extension != 'mp4') {

                        $folder = "product-images/" . $request->id . '/thumbnail/';
                        if (!Storage::exists($folder)) {
                            Storage::makeDirectory($folder, 0777, true);
                        }

                        $thumbName1 = explode('/', $path);

                        $thumbName = $thumbName1[2];

                        $originalPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . "product-images/" . $request->id . '/' . $thumbName;

                        $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $folder . $thumbName;
                        /* $image     = new ImageResize($originalPath);
                          $image->resize(100, 100, true);

                          $image->save($thumbPath); */
                        Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                            $constraint->upsize();
                            $constraint->aspectRatio();
                        })->save($thumbPath, 100);

                        $updateArr['main_image_marketplace']       = $path;
                        $updateArr['main_image_marketplace_thumb'] = $folder . $thumbName;
                        if (isset($product->main_image_marketplace) && !empty($product->main_image_marketplace)) {
                            Storage::delete($product->main_image_marketplace);
                            $thumbName = explode('/', $product->main_image_marketplace)[2];

                            Storage::delete($folder . '/thumbnail/' . $thumbName);
                        }
                    }
                    $updateArr['mp_image_missing'] = 0;

                    if ($product->update($updateArr)) {
                        $updateInfoMissing                  = array();
                        $infoMissingArray['product_object'] = $product;
                        $updateInfoMissing['info_missing']  = Products::setInfoMissingFlag($infoMissingArray);
                        $product->update($updateInfoMissing);
                        $magentoImg                         = 1;
                    }
                }
            }
            if ($request->hasFile('images')) {

                if (isset($request->updateIds)) {

                    $successCount = 0;
                    foreach ($request->updateIds as $key => $value) {
                        if (isset($request->images[$key])) {
                            $productImage = ProductImage::find($value);
                            $extension    = strtolower($request->images[$key]->getClientOriginalExtension());
                            $folder       = "product-images/" . $request->id;
                            if (!Storage::exists($folder)) {
                                Storage::makeDirectory($folder, 0777, true);
                            }
                            $uploadedFile = $request->images[$key];
                            if ($extension == "mp4") {
                                $filename = $uploadedFile->getClientOriginalName();
                                $name     = md5($filename . time()) . '.' . $uploadedFile->getClientOriginalExtension();
                                $path     = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
                            }
                            else {
                                $path = Storage::putFile(($folder), $uploadedFile);
                            }

                            $updateImageArr                = array();
                            $updateImageArr['image']       = $path;
                            $updateImageArr['image_thumb'] = $path;

                            if ($productImage->update($updateImageArr)) {
                                $successCount++;
                            }
                        }
                    }
                    foreach ($request->images as $key => $value) {

                        if (!array_key_exists($key, $request->updateIds)) {
                            $storeImagearray               = array();
                            $storeImagearray['product_id'] = $request->id;
                            $storeImagearray['image_type'] = 2;

                            $storeImagearray['modified_by'] = $request->user_id;
                            $uploadedFile                   = $request->images[$key];
                            $extension                      = strtolower($uploadedFile->getClientOriginalExtension());
                            $folder                         = "product-images/" . $request->id;
                            if (!Storage::exists($folder)) {
                                Storage::makeDirectory($folder, 0777, true);
                            }
                            if ($extension == "mp4") {
                                $filename = $uploadedFile->getClientOriginalName();
                                $name     = md5($filename . time()) . '.' . $uploadedFile->getClientOriginalExtension();
                                $path     = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
                            }
                            else {
                                $path = Storage::putFile(($folder), $uploadedFile);
                            }

                            $storeImagearray['image'] = $path;
                            if (ProductImage::create($storeImagearray)) {
                                $successCount++;
                            }
                        }
                    }

                    if ($successCount > 0) {
                        return $this->sendResponse(trans('messages.api_responses.other_image_edit_success'), 200);
                    }
                }
                else {
                    foreach ($files as $file) {

                        if (!in_array($file->getClientOriginalName(), $remove_time_images)) {
                            $storeImagearray[$i]['product_id']  = $request->id;
                            $storeImagearray[$i]['image_type']  = 2;
                            $storeImagearray[$i]['created_by']  = $request->user_id;
                            $storeImagearray[$i]['modified_by'] = $request->user_id;
                            $uploadedFile                       = $file;
                            $folder                             = "product-images/" . $request->id;
                            if (!Storage::exists($folder)) {
                                Storage::makeDirectory($folder, 0777, true);
                            }
                            $extension = strtolower($file->getClientOriginalExtension());
                            if ($extension == "mp4") {
                                $filename                           = $uploadedFile->getClientOriginalName();
                                $name                               = time() . 'Video' . '.' . $uploadedFile->getClientOriginalExtension();
                                $path                               = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
                                $storeImagearray[$i]['image']       = $path;
                                $storeImagearray[$i]['image_thumb'] = $path;
                            }
                            else {
                                $name = time() . $i . 'Image.' . $file->getClientOriginalExtension();
                                $path = Storage::putFileAs(($folder), $uploadedFile, $name);
                            }
                            if (!empty($path) && $extension != "mp4") {

                                $folder = "product-images/" . $request->id . '/thumbnail/';
                                if (!Storage::exists($folder)) {
                                    Storage::makeDirectory($folder, 0777, true);
                                }

                                $thumbName1 = explode('/', $path);

                                $thumbName = $thumbName1[2];

                                $thumbPath = Storage::disk('public')->getDriver()->getAdapter()->getPathPrefix() . $folder . $thumbName;
                                /* $image   = new ImageResize($originalPath);
                                  $image->resize(100, 100, true);
                                  $image->save($thumbPath); */
                                Image::make($uploadedFile)->resize(100, null, function ($constraint) {
                                    $constraint->upsize();
                                    $constraint->aspectRatio();
                                })->save($thumbPath, 100);
                                $storeImagearray[$i]['image']       = $path;
                                $storeImagearray[$i]['image_thumb'] = $folder . $thumbName;
                            }



                            $i++;
                        }
                    }
                    if (count($storeImagearray) > 0) {
                        if (ProductImage::insert($storeImagearray)) {
                            return $this->sendResponse(trans('messages.api_responses.product_image_add_success'), 200, array('reload' => true));
                        }
                    }
                }
            }
            else {
                if ($files != "") {

                    foreach ($request->images as $key => $file) {
                        if (!in_array($file->getClientOriginalName(), $remove_time_images)) {
                            $storeImagearray[$i]['product_id']  = $request->id;
                            $storeImagearray[$i]['image_type']  = 2;
                            $storeImagearray[$i]['created_by']  = $request->user_id;
                            $storeImagearray[$i]['modified_by'] = $request->user_id;
                            $uploadedFile                       = $request->images[$key];


                            $extension = strtolower($uploadedFile->getClientOriginalExtension());
                            $filename  = $uploadedFile->getClientOriginalName();


                            $folder = "product-images/" . $request->id;
                            if (!Storage::exists($folder)) {
                                Storage::makeDirectory($folder, 0777, true);
                            }
                            if ($extension == "mp4") {
                                $filename = $uploadedFile->getClientOriginalName();
                                $name     = md5($filename . time()) . '.' . $uploadedFile->getClientOriginalExtension();
                                $path     = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
                            }
                            else {
                                $path = Storage::putFile(($folder), $uploadedFile);
                            }


                            $storeImagearray[$i]['image'] = $path;
                            $i++;
                        }
                    }
                    if (count($storeImagearray) > 0) {
                        if (ProductImage::insert($storeImagearray)) {
                            return $this->sendResponse(trans('messages.api_responses.product_image_add_success'), 200, array('reload' => true));
                        }
                    }
                }
                else {
                    if ($mainImage == 1 && $magentoImg == 1) {
                        return $this->sendResponse(trans('messages.api_responses.product_image_add_success'), 200, array('reload' => true));
                    }
                    else if ($mainImage == 1 && $magentoImg == 0) {
                        return $this->sendResponse(trans('messages.api_responses.product_internalimage_add_success'), 200, array('reload' => true));
                    }
                    else if ($mainImage == 0 && $magentoImg == 1) {
                        return $this->sendResponse(trans('messages.api_responses.mp_image_add_success'), 200, array('reload' => true));
                    }
                    else {
                        if ($request_photo = 1) {
                            return $this->sendResponse(trans('messages.api_responses.pi_request_new'), 200, array('reload' => true));
                        }
                        else {
                            return $this->sendResponse(trans('messages.api_responses.product_image_add_success'), 200, array('reload' => true));
                        }
                    }
                }
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    function deleteImage(Request $request) {
        if ($request) {

            try {
                if ($request->remove_image_type == "other") {
                    $productImage = ProductImage::find($request->removeId);
                    if (isset($productImage->image) && !empty($productImage->image)) {
                        Storage::delete($productImage->image);
                        $thumbName = explode('/', $productImage->image)[2];
                        Storage::delete('product-images/' . $request->removeId . '/thumbnail/' . $thumbName);
                    }
                    if ($productImage->delete()) {
                        return $this->sendResponse(trans('messages.api_responses.product_image_delete_success'), 200);
                    }
                }
                else {
                    if ($request->remove_image_type == "main_image_internal") {
                        $product = Products::find($request->removeId);
                        if (isset($product->main_image_internal) && !empty($product->main_image_internal)) {
                            Storage::delete($product->main_image_internal);
                            $folder    = "product-images/" . $request->removeId;
                            $thumbName = explode('/', $product->main_image_internal)[2];

                            Storage::delete($folder . '/thumbnail/' . $thumbName);
                        }
                        $updateArr                              = array();
                        $updateArr['main_image_internal']       = NULL;
                        $updateArr['main_image_internal_thumb'] = NULL;


                        $message = trans('messages.api_responses.ii_delete_success');
                    }
                    else {
                        $product   = Products::find($request->removeId);
                        $updateArr = array();

                        if (isset($product->main_image_marketplace) && !empty($product->main_image_marketplace) && Products::getActualValOfMagentoImage($product->main_image_marketplace) == '1') {

                            Storage::delete($product->main_image_marketplace);
                            $folder    = "product-images/" . $request->removeId;
                            $thumbName = explode('/', $product->main_image_marketplace)[2];

                            Storage::delete($folder . '/thumbnail/' . $thumbName);
                            $updateArr['main_image_marketplace']       = NULL;
                            $updateArr['main_image_marketplace_thumb'] = NULL;
                        }
                        else {
                            $updateArr['main_image_marketplace_url'] = NULL;
                        }

                        if (is_null($product->main_image_marketplace_url) || Products::getActualValOfMagentoImage($product->main_image_marketplace) == '0') {
                            $mp_image_missing = 1;
                        }
                        else {
                            $mp_image_missing = 0;
                        }

                        $updateArr['mp_image_missing'] = $mp_image_missing;
                        $message                       = trans('messages.api_responses.mi_delete_success');
                    }
                    if (count($updateArr) > 0) {
                        if ($product->update($updateArr)) {
                            $updateInfoMissing                  = array();
                            $infoMissingArray['product_object'] = $product;
                            $updateInfoMissing['info_missing']  = Products::setInfoMissingFlag($infoMissingArray);
                            $product->update($updateInfoMissing);

                            return $this->sendResponse($message, 200);
                        }
                        else {
                            return $this->sendError(trans('messages.common.something_wrong'), 422);
                        }
                    }
                    else {
                        return $this->sendError(trans('messages.common.something_wrong'), 422);
                    }
                }
            }
            catch (Exception $ex) {
                return $this->sendError($ex->getMessage(), 400);
            }
        }
        else {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    function getSku() {
        if ($sku = get_sku()) {
            return $this->sendResponse('SKU generated successfully', 200, array('sku' => $sku));
        }
        else {
            return $this->sendValidation(array('Something went wrong..'), 422);
        }
    }

    function saveVariations(CreateRequest $request) {
        try {
            if (!empty($request->id)) {
                $product_exist = Products::find($request->id);

                if (!empty($product_exist) && $product_exist->product_type != 'parent') {
                    return $this->sendValidation(array('Product not found or product is not variation product'), 422);
                }

                $barcode_exist = array();

                $variation_details = array();

                if (!empty($request->var_id)) {
                    $db_variation_details = Products::select('id', 'main_image_internal')->whereIn('id', $request->var_id)->get();
                }

                if (!empty($db_variation_details)) {
                    foreach ($db_variation_details as $db_variation_detail) {
                        $variation_details[$db_variation_detail->id] = $db_variation_detail->getOriginal('main_image_internal');
                    }
                }


                if (!empty($request->var_barcode)) {
                    $exclude_ids = !empty($request->var_id) ? $request->var_id : array();

                    $exclude_ids[] = $request->id;

                    if (!empty($request->var_remove_product_id)) {
                        $exclude_ids = array_merge($exclude_ids, $request->var_remove_product_id);
                    }

                    $barcode_query = ProductBarcode::whereIn('barcode', $request->var_barcode);

                    if (!empty($exclude_ids)) {
                        $barcode_query = $barcode_query->whereNotIn('product_id', $exclude_ids);
                    }
                    $barcode_exist = $barcode_query->pluck('id')->toArray();
                }

                if (!empty($barcode_exist)) {
                    return $this->sendValidation(array('Variation product barcode should be unique'), 422);
                }

                $db_update = [];

                $db_barcode_update = [];

                $db_barcode_insert = [];

                $product_ids = [];


                $product_exist_array = object_to_array($product_exist->getOriginal());

                $parent_product_id = $product_exist_array['id'];

                $var_remove_product_ids = !empty($request->var_remove_product_id) ? $request->var_remove_product_id : array();

                unset($product_exist_array['id']);

                unset($product_exist_array['sku']);

                unset($product_exist_array['main_image_internal']);

                DB::beginTransaction();

                foreach ($request->var_sku as $key => $v_sku) {
                    if (!empty($request->var_size[$key]) || !empty($request->var_color[$key])) {
                        $db_post = array();

                        $db_post = $product_exist_array;

                        $db_post['parent_id'] = $parent_product_id;

                        $db_post['product_type'] = 'variation';

                        $db_post['variation_theme_id'] = $request->variation_theme;

                        $db_post['sku'] = $request->var_sku[$key];

                        $db_post['variation_theme_value1'] = !empty($request->var_size[$key]) ? $request->var_size[$key] : NULL;

                        $db_post['variation_theme_value2'] = !empty($request->var_color[$key]) ? $request->var_color[$key] : NULL;

                        $db_post['all_variants_place_one_location'] = !empty($request->all_variants_place_one_location) ? 1 : 0;

                        if (!empty($request->var_title[$key])) {
                            $db_post['title'] = $request->var_title[$key];
                        }

                        $db_post['created_by'] = $request->user()->id;

                        if (!empty($request->var_id[$key]) && !in_array($request->var_id[$key], $var_remove_product_ids)) {
                            $varProductImage = $variation_details[$request->var_id[$key]];

                            if (!empty($request->var_remove_product_image)) {
                                if (in_array($varProductImage, $request->var_remove_product_image)) {
                                    $db_post['main_image_internal'] = NULL;
                                }
                            }

                            if (!empty($request->var_img[$key])) {
                                if (!empty($varProductImage)) {
                                    Storage::delete($varProductImage);
                                }

                                $db_post['main_image_internal'] = $this->upload_product_img($request->var_img[$key], $parent_product_id);
                            }

                            $db_post['id'] = $request->var_id[$key];

                            $db_update[] = $db_post;

                            $product_ids[$key] = $db_post['id'];
                        }
                        else {
                            if (!empty($request->var_img[$key])) {
                                $db_post['main_image_internal'] = $this->upload_product_img($request->var_img[$key], $parent_product_id);
                            }

                            $product_ids[$key] = Products::create($db_post)->id;
                        }
                    }
                }


                if (!empty($request->var_barcode)) {
                    foreach ($request->var_barcode as $barcode_id_key => $barcode_id) {
                        $db_barcode_post = [];

                        $db_barcode_post['id'] = !empty($request->var_barcode_id[$barcode_id_key]) ? $request->var_barcode_id[$barcode_id_key] : NULL;

                        $db_barcode_post['barcode'] = !empty($request->var_barcode[$barcode_id_key]) ? $request->var_barcode[$barcode_id_key] : NULL;

                        if (!empty($db_barcode_post['id'])) {
                            $db_barcode_update[] = $db_barcode_post;
                        }
                        else {
                            if (!empty($db_barcode_post['barcode']) && !empty($product_ids[$barcode_id_key])) {
                                $db_barcode_post['product_id'] = $product_ids[$barcode_id_key];

                                $db_barcode_post['barcode_type'] = 1;

                                $db_barcode_insert[] = $db_barcode_post;
                            }
                        }
                    }
                }

                if (!empty($request->var_remove_product_image)) {
                    foreach ($request->var_remove_product_image as $del_image) {
                        Storage::delete($del_image);
                    }
                }

                if (!empty($db_update)) {
                    $obj = new Products;

                    $result = Batch::update($obj, $db_update, 'id');
                }

                if (!empty($request->var_remove_product_id)) {
                    $this->destroy($request, $request->var_remove_product_id);
                }

                $product_exist->variation_theme_id = $request->variation_theme;

                $product_exist->all_variants_place_one_location = !empty($request->all_variants_place_one_location) ? 1 : 0;

                $product_exist->save();

                // Barcodes
                if (!empty($db_barcode_update)) {
                    $bar_obj = new ProductBarcode;

                    $result = Batch::update($bar_obj, $db_barcode_update, 'id');
                }

                if (!empty($db_barcode_insert)) {
                    ProductBarcode::insert($db_barcode_insert);
                }

                DB::commit();

                return $this->sendResponse('Variation product(s) updated successfully', 200, array('reload' => true));
            }
        }
        catch (Exception $ex) {

            DB::rollBack();

            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function upload_product_img($img, $id) {

        $path = "";

        // images
        if (!empty($img)) {
            $extension = strtolower($img->getClientOriginalExtension());

            $folder = "product-images/" . $id;

            if (!Storage::exists($folder)) {
                Storage::makeDirectory($folder, 0777, true);
            }

            $uploadedFile = $img;

            if ($extension == "mp4") {
                $filename = $uploadedFile->getClientOriginalName();

                $name = md5($filename . time()) . '.' . $uploadedFile->getClientOriginalExtension();

                $path = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
            }
            else {
                $path = Storage::putFile(($folder), $uploadedFile);
            }
        }

        return $path;
    }

}
