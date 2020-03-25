<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductFetch;
use App\Cron;
use Batch;
use App\Library\Magento;
use App\MagentoProduct;
use App\Vendor;
use App\MagentoProductImage;
use App\MagentoVerTheOpMap;
use App\MagentoVerTheme;
use App\MagentoVerTheOption;
use App\Country;

class MagentoProDetailUpdController extends Controller {

    public
            function __construct() {
        ini_set('max_execution_time', 0);
        $this->magento_media_path     = 'pub/media/catalog/product';
        $this->ACTION                 = "insert";
        $this->PRODUCT_LIMIT          = 4;
        $this->PRODUCT_UPDATE_LIMIT   = 10;
        $this->CUSTOM_ATTRIBUTE_ARRAY = array(
            'brand' => 'brand',
            'mpn' => 'manufacturer_part_number',
            'manufacturer' => 'manufacturer',
            'product_condition' => 'magento_item_condition',
            'product_condition_notes' => 'magento_condition_notes',
            'vendor' => 'magento_vendor_id',
            'weight' => 'product_weight',
            'ts_dimensions_length' => 'product_length',
            'ts_dimensions_height' => 'product_height',
            'ts_dimensions_width' => 'product_width',
            'upc' => 'upc',
            'ean' => 'ean',
            'isbn' => 'isbn',
        );
        //pass blank array as of now
        $this->CUSTOM_ATTRIBUTE_ARRAY = array();
        // error_reporting(E_ALL);
    }

    public
            $magento;
    public
            $page_size = 500;
    public
            $magento_pro;
    public
            $store_id;
    public
            $existing_product_list;
    public
            $image_model;
    public
            $country_array;

    public
            function index(Request $request) {
        try {
            $store_id = '1'; //default for magento
            $action   = $this->ACTION;
            if (isset($request->store_id)) {
                $store_id = $request->store_id;
            }

            if (isset($request->action)) {
                $action       = $request->action;
                $this->ACTION = $action;
            }

            $this->store_id                 = $store_id;
            $store_data                     = ProductFetch::where('id', $store_id)->get();
            $this->magento                  = new Magento;
            $this->magento->USER_NAME       = isset($store_data[0]['magento_username']) ? $store_data[0]['magento_username'] : '';
            $this->magento->PASSWORD        = isset($store_data[0]['magento_password']) ? $store_data[0]['magento_password'] : '';
            $this->magento->ENDPOINT        = isset($store_data[0]['magento_api_url']) ? $store_data[0]['magento_api_url'] : '';
            $this->magento->magento_web_url = isset($store_data[0]['magento_web_url']) ? $store_data[0]['magento_web_url'] : '';

            $this->image_pre_url = str_replace("rest/", "", $this->magento->ENDPOINT);

            if ($this->magento->USER_NAME != '' && $this->magento->PASSWORD != '' && $this->magento->ENDPOINT != '') {
                $this->STORE_ID         = $store_id;
                $this->CRON_NAME        = 'CRON_' . time();   // CRON NAME
                $this->CRON_TITLE       = 'MAGENTO_PRODUCT_DETAIL';
                $this->CRON_NAME        = $this->CRON_NAME . '_' . $this->STORE_ID;
                $country_array_data     = Country::get();
                $country_array_data_arr = $country_array_data->toArray();
                if (!empty($country_array_data_arr)) {
                    foreach ($country_array_data_arr as $row) {
                        $this->country_array[$row['sortname']] = $row['id'];
                    }
                }

                //cron start code
                $cron_id           = $this->cron_start_end_update('', $this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);
                $operation_perform = $this->invoke_product_detail_update();

                //update cron data
                $this->cron_start_end_update($cron_id, $this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);

                //code for magento
                if (!empty($operation_perform) && $operation_perform == 1) {
                    return $this->sendResponse('Products details has been stored successfully', 200);
                }
                else if (!empty($operation_perform) && $operation_perform == 2) {
                    return $this->sendResponse('All Products details has been fetched already', 200);
                }
                else {
                    return $this->sendError('Products details has not been stored successfully, please try again', 422);
                }
            }
            else {
                return $this->sendError('Magento Credetails are not set.', 422);
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function invoke_product_detail() {
        $operation_perform = 0;
        $this->magento_pro = new MagentoProduct;

        //get product type from arrayhelper
        //$product_type=magento_product_type_id_from_name('parent');
        $parentProducts = $this->magento_pro->select('id', 'magento_product_id', 'sku')->where('store_id', $this->STORE_ID)->where('is_detail_processed', 0)->where('product_type', 'parent')->limit($this->PRODUCT_LIMIT)->get();

        $updateCount          = 0;
        $parentUpdateArray    = Array();
        $varUpdateArray       = Array();
        $SKUArray             = Array();
        //$upload_path = 'uploads/inventory_images/magento/';
        $upload_path          = 'storage/uploads/magento/';
        $parentProducts_array = $parentProducts->toArray();

        if (!empty($parentProducts) && !empty($parentProducts_array)) {
            // $LastArray = end($parentProducts);
            // $LastSKU = isset($LastArray['sku'];
            foreach ($parentProducts as $key => $value) {
                $sku            = $value['sku'];
                $result         = $this->magento->send_request("V1/products/" . urlencode($sku));
                $product_detail = json_decode($result);
                if (!empty($product_detail)) {
                    $stock_item_attributes = isset($product_detail->extension_attributes->stock_item) ? $product_detail->extension_attributes->stock_item : array();
                    $custom_attributes     = isset($product_detail->custom_attributes) ? $product_detail->custom_attributes : array();
                    $media_gallery_data    = isset($product_detail->media_gallery_entries) ? $product_detail->media_gallery_entries : array();
                    $db_cust_attr          = array();
                    $desc                  = NULL;
                    $main_image            = NULL;
                    $category_ids          = NULL;
                    $tax_class_id          = NULL;
                    $meta_title            = NULL;
                    $meta_keyword          = NULL;
                    $meta_desc             = NULL;
                    $url_key               = NULL;
                    $is_feature            = NULL;
                    $country_manufacture   = NULL;
                    foreach ($custom_attributes as $cust_attr) {
                        if ($cust_attr->attribute_code == 'description') {
                            $desc = $cust_attr->value;
                        }
                        else if ($cust_attr->attribute_code == 'image') {
                            if ($cust_attr->value != '') {
                                //my code
                                $main_pic_url = $this->image_pre_url . $this->magento_media_path . $cust_attr->value;
                                $main_image   = $main_pic_url;

                                //old code
                                // $main_pic_url = $this->magento_web_url.$this->magento_media_path.$cust_attr->value;
                                // $main_file_array = explode('/', $cust_attr->value);
                                // $main_file_name = $main_file_array[count($main_file_array) - 1];
                                // $main_img_file_name = base_url().$upload_path.$main_file_name;
                                // file_put_contents($upload_path.$main_file_name, file_get_contents($main_pic_url));
                                // $main_image = $main_img_file_name;
                            }
                        }
                        else if ($cust_attr->attribute_code == 'category_ids') {
                            if (!empty($cust_attr->value)) {
                                $category_ids = implode(',', $cust_attr->value);
                            }
                        }
                        else if ($cust_attr->attribute_code == 'tax_class_id') {
                            $tax_class_id = $cust_attr->value;
                        }
                        else if ($cust_attr->attribute_code == 'meta_title') {
                            $meta_title = $cust_attr->value;
                        }
                        else if ($cust_attr->attribute_code == 'meta_keyword') {
                            $meta_keyword = $cust_attr->value;
                        }
                        else if ($cust_attr->attribute_code == 'meta_description') {
                            $meta_desc = $cust_attr->value;
                        }
                        else if ($cust_attr->attribute_code == 'url_key') {
                            $url_key = $cust_attr->value;
                        }
                        else if ($cust_attr->attribute_code == 'is_feature') {
                            $is_feature = $cust_attr->value;
                        }
                        else if ($cust_attr->attribute_code == 'country_of_manufacture') {
                            $country_manufacture = $cust_attr->value;
                            if (!empty($this->country_array) && !empty($country_manufacture) && isset($this->country_array[$country_manufacture])) {
                                $country_manufacture = $this->country_array[$country_manufacture];
                            }
                        }
                        else {
                            if (!empty($this->CUSTOM_ATTRIBUTE_ARRAY)) {
                                foreach ($this->CUSTOM_ATTRIBUTE_ARRAY as $attr_code => $db_field) {
                                    if ($cust_attr->attribute_code == $attr_code) {
                                        if ($attr_code == 'vendor') {
                                            $db_cust_attr[$db_field] = $this->set_vendor($cust_attr->value);
                                        }
                                        else {
                                            $db_cust_attr[$db_field] = $cust_attr->value;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $ParentUpdateLoopArray = Array();
                    $ParentUpdateLoopArray = Array(
                        'magento_product_id' => $value['magento_product_id'],
                        'product_title' => isset($product_detail->name) ? $product_detail->name : '',
                        'quantity' => isset($stock_item_attributes->qty) ? $stock_item_attributes->qty : '',
                        'is_in_stock' => isset($stock_item_attributes->is_in_stock) ? $stock_item_attributes->is_in_stock : '',
                        'description' => $desc,
                        'main_image_url' => $main_image,
                        'category_ids' => $category_ids,
                        'meta_title' => $meta_title,
                        'meta_keyword' => $meta_keyword,
                        'meta_description' => $meta_desc,
                        'url_key' => $url_key,
                        'is_feature' => $is_feature,
                        'tax_class' => $tax_class_id,
                        'country_of_origin' => $country_manufacture,
                        'is_detail_processed' => '1',
                        'modified_by' => '1',
                        'modified_date' => date('Y-m-d H:i:s'),
                    );

                    if (!empty($db_cust_attr)) {
                        $ParentUpdateLoopArray = $ParentUpdateLoopArray + $db_cust_attr;
                    }

                    $ProductAttributes    = isset($product_detail->extension_attributes->configurable_product_options) ? $product_detail->extension_attributes->configurable_product_options : array();
                    $VariationIds         = $product_detail->extension_attributes->configurable_product_links;
                    $ProdAttrArray        = Array();
                    $AttributeCodeArray   = Array();
                    $ParentVarThemeArray  = Array();
                    $varThemeOptionsArray = Array();
                    if (!empty($ProductAttributes)) {
                        foreach ($ProductAttributes as $pvalue) {
                            $AttrId     = $pvalue->attribute_id;
                            $attrResult = $this->magento->send_request("V1/products/attributes?searchCriteria[filterGroups][0][filters][0][field]=attribute_id&searchCriteria[filterGroups][0][filters][0][value]=$AttrId");
                            $attrData   = json_decode($attrResult);

                            if (!empty($attrData->items[0])) {
                                $attributeDetail                  = $attrData->items[0];
                                $AttributeId                      = $this->insertProductAttributes($attributeDetail);
                                $AttributeCodeArray[$AttributeId] = $attributeDetail->attribute_code;
                                $ParentVarThemeArray[]            = ($attributeDetail->default_frontend_label != '') ? $attributeDetail->default_frontend_label : $attributeDetail->attribute_code;
                                if (!empty($attributeDetail->options)) {
                                    foreach ($attributeDetail->options as $opt_value) {
                                        if ($opt_value->value != '' && $opt_value->label != '') {
                                            $varThemeOptionsArray[$attributeDetail->attribute_code][$opt_value->value] = $opt_value->label;
                                        }
                                    }
                                }
                            }
                        }
                        $ParentVarThemeArray = array_unique($ParentVarThemeArray);
                    }

                    if (!empty($ParentVarThemeArray)) {
                        sort($ParentVarThemeArray);
                        $ParentVarThemeString                     = implode('||', $ParentVarThemeArray);
                        $ParentUpdateLoopArray['variation_theme'] = $ParentVarThemeString;
                    }

                    $parentUpdateArray[] = $ParentUpdateLoopArray;

                    if (!empty($media_gallery_data)) {
                        $this->insertProductImages($media_gallery_data, $value['magento_product_id'], $value['id'], $main_image);
                    }

                    $updateCount++;

                    if (!empty($VariationIds)) {
                        //$product_type_new=magento_product_type_id_from_name('normal');

                        $variationProducts = $this->magento_pro->select('id', 'magento_product_id', 'sku')->where('store_id', $this->STORE_ID)->where('is_detail_processed', '0')->where('product_type', 'normal')->whereIn('magento_product_id', $VariationIds)->get();

                        $variationProducts_arr = $variationProducts->toArray();

                        if (!empty($variationProducts_arr) && !empty($variationProducts)) {
                            foreach ($variationProducts as $vKey => $vValue) {
                                $varSku            = $vValue['sku'];
                                $varResult         = $this->magento->send_request("V1/products/" . urlencode($varSku));
                                $VarProduct_detail = json_decode($varResult);

                                $VarCustomAttributes       = $VarProduct_detail->custom_attributes;
                                $var_stock_item_attributes = $VarProduct_detail->extension_attributes->stock_item;
                                $var_media_gallery_data    = $VarProduct_detail->media_gallery_entries;
                                $db_var_cust_attr          = array();
                                $var_product_id_type       = 'upc';
                                $var_product_id            = '';
                                $var_desc                  = NULL;
                                $var_main_image            = NULL;
                                $var_category_ids          = NULL;
                                $var_tax_class_id          = NULL;
                                $var_meta_title            = NULL;
                                $var_meta_keyword          = NULL;
                                $var_meta_desc             = NULL;
                                $var_url_key               = NULL;
                                $var_is_feature            = NULL;
                                $var_country_manufacture   = NULL;
                                $VarProdAttributes         = Array();
                                $varThemeOptionValueArray  = Array();

                                foreach ($VarCustomAttributes as $cvalue) {
                                    if (in_array($cvalue->attribute_code, $AttributeCodeArray)) {
                                        $cvalue->attr_id                                   = array_search($cvalue->attribute_code, $AttributeCodeArray);
                                        $cvalue->product_id                                = $vValue['id'];
                                        $cvalue->parent_product_id                         = $value['id'];
                                        $VarProdAttributes[]                               = $cvalue;
                                        $varThemeOptionValueArray[$cvalue->attribute_code] = $varThemeOptionsArray[$cvalue->attribute_code][$cvalue->value];
                                    }

                                    if ($cvalue->attribute_code == 'description') {
                                        $var_desc = $cvalue->value;
                                    }
                                    else if ($cvalue->attribute_code == 'image') {
                                        if ($cvalue->value != '') {
                                            //mycode
                                            $var_pic_url    = $this->image_pre_url . $this->magento_media_path . $cvalue->value;
                                            $var_main_image = $var_pic_url;
                                            //old code
                                            // $var_pic_url = $this->magento_web_url.$this->magento_media_path.$cvalue->value;
                                            // $var_file_array = explode('/', $cvalue->value);
                                            // $var_file_name = $var_file_array[count($var_file_array) - 1];
                                            // $var_img_file_name = base_url().$upload_path.$var_file_name;
                                            // file_put_contents($upload_path.$var_file_name, file_get_contents($var_pic_url));
                                            // $var_main_image = $var_img_file_name;
                                        }
                                    }
                                    else if ($cvalue->attribute_code == 'category_ids') {
                                        if (!empty($cvalue->value)) {
                                            $var_category_ids = implode(',', $cvalue->value);
                                        }
                                    }
                                    else if ($cvalue->attribute_code == 'tax_class_id') {
                                        $var_tax_class_id = $cvalue->value;
                                    }
                                    else if ($cvalue->attribute_code == 'meta_title') {
                                        $var_meta_title = $cvalue->value;
                                    }
                                    else if ($cvalue->attribute_code == 'meta_keyword') {
                                        $var_meta_keyword = $cvalue->value;
                                    }
                                    else if ($cvalue->attribute_code == 'meta_description') {
                                        $var_meta_desc = $cvalue->value;
                                    }
                                    else if ($cvalue->attribute_code == 'url_key') {
                                        $var_url_key = $cvalue->value;
                                    }
                                    else if ($cvalue->attribute_code == 'is_feature') {
                                        $var_is_feature = $cvalue->value;
                                    }
                                    else if ($cvalue->attribute_code == 'country_of_manufacture') {
                                        $var_country_manufacture = $cvalue->value;
                                        if (!empty($this->country_array) && !empty($var_country_manufacture) && isset($this->country_array[$var_country_manufacture])) {
                                            $var_country_manufacture = $this->country_array[$var_country_manufacture];
                                        }
                                    }
                                    else if ($cvalue->attribute_code == 'product_id_type') {
                                        $var_product_id_type = strtolower($cvalue->value);
                                    }
                                    else if ($cvalue->attribute_code == 'product_id') {
                                        $var_product_id = $cvalue->value;
                                    }
                                    else {
                                        if (!empty($this->CUSTOM_ATTRIBUTE_ARRAY)) {
                                            foreach ($this->CUSTOM_ATTRIBUTE_ARRAY as $attr_code => $db_field) {
                                                if ($cvalue->attribute_code == $attr_code) {
                                                    if ($attr_code == 'vendor') {
                                                        $db_var_cust_attr[$db_field] = $this->set_vendor($cvalue->value);
                                                    }
                                                    else {
                                                        $db_var_cust_attr[$db_field] = $cvalue->value;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                $varThemeOptionValueString = "";
                                if (!empty($varThemeOptionValueArray)) {
                                    ksort($varThemeOptionValueArray);
                                    $varThemeOptionValueString = implode('||', $varThemeOptionValueArray);
                                }

                                if (!empty($VarProdAttributes)) {
                                    foreach ($VarProdAttributes as $varValue) {
                                        $NotDeleteArray[] = $this->insertProductAttributeMapping($varValue);
                                    }

                                    if (isset($NotDeleteArray) && !empty($NotDeleteArray)) {
                                        MagentoVerTheOpMap::whereNotIn('id', $NotDeleteArray)->where('product_id', $vValue['id'])->delete();
                                    }
                                }

                                if (!empty($var_media_gallery_data)) {
                                    $this->insertProductImages($var_media_gallery_data, $vValue['magento_product_id'], $vValue['id'], $var_main_image);
                                }

                                $main_varUpdateArray = Array(
                                    'magento_product_id' => $vValue['magento_product_id'],
                                    'parent_id' => $value['id'],
                                    'product_type' => 'variation',
                                    'parent_sku' => $sku,
                                    'product_title' => isset($VarProduct_detail->name) ? $VarProduct_detail->name : '',
                                    'quantity' => isset($var_stock_item_attributes->qty) ? $var_stock_item_attributes->qty : '',
                                    'is_in_stock' => isset($var_stock_item_attributes->is_in_stock) ? $var_stock_item_attributes->is_in_stock : '',
                                    'description' => $var_desc,
                                    'main_image_url' => $var_main_image,
                                    'category_ids' => $var_category_ids,
                                    'meta_title' => $var_meta_title,
                                    'meta_keyword' => $var_meta_keyword,
                                    'meta_description' => $var_meta_desc,
                                    'url_key' => $var_url_key,
                                    'is_feature' => $var_is_feature,
                                    'tax_class' => $var_tax_class_id,
                                    'country_of_origin' => $var_country_manufacture,
                                    'is_detail_processed' => '1',
                                    'modified_by' => '1',
                                    'variation_theme' => $ParentVarThemeString,
                                    'variation_theme_value' => $varThemeOptionValueString,
                                    'modified_date' => date('Y-m-d H:i:s'),
                                    $var_product_id_type => $var_product_id,
                                );

                                if (!empty($db_var_cust_attr)) {
                                    $main_varUpdateArray = $main_varUpdateArray + $db_var_cust_attr;
                                }

                                $varUpdateArray[] = $main_varUpdateArray;
                                $updateCount++;
                            }
                        }
                    }
                }

                $SKUArray[] = $sku;

                if (!empty($parentUpdateArray)) {
                    Batch::update($this->magento_pro, $parentUpdateArray, 'magento_product_id');
                    $operation_perform = 1;
                }

                if (!empty($varUpdateArray)) {
                    Batch::update($this->magento_pro, $varUpdateArray, 'magento_product_id');
                    $operation_perform = 1;
                }

                $parentUpdateArray = Array();
                $varUpdateArray    = Array();
                $SKUArray          = Array();
                $updateCount       = 0;
            }
        }

        $pendingParentProducts       = $this->magento_pro->select('id', 'magento_product_id', 'sku')->where('store_id', $this->STORE_ID)->where('is_detail_processed', 0)->where('product_type', 'parent')->get();
        $pendingParentProducts_array = $pendingParentProducts->toArray();
        $normalProducts_arr          = array();

        if (empty($pendingParentProducts) || empty($pendingParentProducts_array)) {
            $updateArray        = Array();
            $product_type_new   = magento_product_type_id_from_name('normal');
            $normalProducts     = $this->magento_pro->select('id', 'magento_product_id', 'sku')->where('store_id', $this->STORE_ID)->where('is_detail_processed', 0)->where('product_type', 'normal')->limit(20)->get();
            $normalProducts_arr = $normalProducts->toArray();
            if (!empty($normalProducts)) {
                foreach ($normalProducts as $key => $value) {
                    $sku            = $value['sku'];
                    $result         = $this->magento->send_request("V1/products/" . urlencode($sku));
                    $product_detail = json_decode($result);
                    if (!empty($product_detail)) {
                        $stock_item_attributes = isset($product_detail->extension_attributes->stock_item) ? $product_detail->extension_attributes->stock_item : array();
                        $custom_attributes     = isset($product_detail->custom_attributes) ? $product_detail->custom_attributes : array();
                        $media_gallery_data    = isset($product_detail->media_gallery_entries) ? $product_detail->media_gallery_entries : array();
                        $db_update_cust_attr   = array();
                        $desc                  = NULL;
                        $main_image            = NULL;
                        $category_ids          = NULL;
                        $tax_class_id          = NULL;
                        $meta_title            = NULL;
                        $meta_keyword          = NULL;
                        $meta_desc             = NULL;
                        $url_key               = NULL;
                        $is_feature            = NULL;
                        $country_manufacture   = NULL;

                        foreach ($custom_attributes as $cust_attr) {
                            if ($cust_attr->attribute_code == 'description') {
                                $desc = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'image') {
                                if ($cust_attr->value != '') {
                                    //my code
                                    $main_pic_url = $this->image_pre_url . $this->magento_media_path . $cust_attr->value;
                                    $main_image   = $main_pic_url;

                                    //old code
                                    // $main_pic_url = $this->magento_web_url.$this->magento_media_path.$cust_attr->value;
                                    // $main_file_array = explode('/', $cust_attr->value);
                                    // $main_file_name = $main_file_array[count($main_file_array) - 1];
                                    // $main_img_file_name = base_url().$upload_path.$main_file_name;
                                    // file_put_contents($upload_path.$main_file_name, file_get_contents($main_pic_url));
                                    // $main_image = $main_img_file_name;
                                }
                            }
                            else if ($cust_attr->attribute_code == 'category_ids') {
                                if (!empty($cust_attr->value)) {
                                    $category_ids = implode(',', $cust_attr->value);
                                }
                            }
                            else if ($cust_attr->attribute_code == 'tax_class_id') {
                                $tax_class_id = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'meta_title') {
                                $meta_title = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'meta_keyword') {
                                $meta_keyword = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'meta_description') {
                                $meta_desc = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'url_key') {
                                $url_key = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'is_feature') {
                                $is_feature = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'country_of_manufacture') {
                                $country_manufacture = $cust_attr->value;
                                if (!empty($this->country_array) && !empty($country_manufacture) && isset($this->country_array[$country_manufacture])) {
                                    $country_manufacture = $this->country_array[$country_manufacture];
                                }
                            }
                            else {
                                if (!empty($this->CUSTOM_ATTRIBUTE_ARRAY)) {
                                    foreach ($this->CUSTOM_ATTRIBUTE_ARRAY as $attr_code => $db_field) {
                                        if ($cust_attr->attribute_code == $attr_code) {
                                            if ($attr_code == 'vendor') {
                                                $db_update_cust_attr[$db_field] = $this->set_vendor($cust_attr->value);
                                            }
                                            else {
                                                $db_update_cust_attr[$db_field] = $cust_attr->value;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($media_gallery_data)) {
                            $this->insertProductImages($media_gallery_data, $value['magento_product_id'], $value['id'], $main_image);
                        }

                        $main_updateArray = Array(
                            'magento_product_id' => $value['magento_product_id'],
                            'product_title' => isset($product_detail->name) ? $product_detail->name : '',
                            'quantity' => isset($stock_item_attributes->qty) ? $stock_item_attributes->qty : '',
                            'is_in_stock' => isset($stock_item_attributes->is_in_stock) ? $stock_item_attributes->is_in_stock : '',
                            'description' => $desc,
                            'main_image_url' => $main_image,
                            'category_ids' => $category_ids,
                            'meta_title' => $meta_title,
                            'meta_keyword' => $meta_keyword,
                            'meta_description' => $meta_desc,
                            'url_key' => $url_key,
                            'is_feature' => $is_feature,
                            'tax_class' => $tax_class_id,
                            'country_of_origin' => $country_manufacture,
                            'is_detail_processed' => '1',
                            'modified_by' => '1',
                            'modified_date' => date('Y-m-d H:i:s'),
                        );

                        if (!empty($db_update_cust_attr)) {
                            $main_updateArray = $main_updateArray + $db_update_cust_attr;
                        }

                        $updateArray[] = $main_updateArray;
                    }
                }

                if (!empty($updateArray)) {
                    $result            = Batch::update($this->magento_pro, $updateArray, 'magento_product_id');
                    $operation_perform = 1;
                }
            }
        }

        if (empty($parentProducts_array) && empty($normalProducts_arr)) {
            $operation_perform = 2;
        }

        return $operation_perform;
    }

    public
            function invoke_product_detail_update() {
        $operation_perform      = 0;
        $this->magento_pro      = new MagentoProduct;
        $toBeUpdateProducts     = $this->magento_pro->select('id', 'magento_product_id', 'sku', 'product_type', 'parent_id')->where('store_id', $this->STORE_ID)->where('is_detail_updated', '0')->where('is_detail_processed', '1')->whereIn('is_deleted_product', Array('0', '1'))->limit($this->PRODUCT_UPDATE_LIMIT)->get();
        $toBeUpdateProducts_arr = $toBeUpdateProducts->toArray();

        if (empty($toBeUpdateProducts) || empty($toBeUpdateProducts_arr)) {
            MagentoProduct::where('store_id', '=', $this->STORE_ID)->update(array('is_detail_updated' => '0'));

            $toBeUpdateProducts = $this->magento_pro->select('id', 'magento_product_id', 'sku', 'product_type', 'parent_id')->where('store_id', $this->STORE_ID)->where('is_detail_updated', '0')->where('is_detail_processed', '1')->whereIn('is_deleted_product', Array('0', '1'))->limit($this->PRODUCT_UPDATE_LIMIT)->get();
        }

        $ProductAttributesArray_data = MagentoVerTheme::get();
        $ProductAttributesArray_arr  = $ProductAttributesArray_data->toArray();
        $ProductAttributesArray      = array();
        $AttributeCodeArray          = array();

        if (!empty($ProductAttributesArray_data) && !empty($ProductAttributesArray_arr)) {
            foreach ($ProductAttributesArray_data as $row) {
                $ProductAttributesArray[$row->attribute_code] = $row;
            }
        }

        if (!empty($ProductAttributesArray)) {
            foreach ($ProductAttributesArray as $key => $avalue) {
                $AttributeCodeArray[$avalue['id']] = $key;
            }
        }
        $upload_path = 'uploads/inventory_images/magento/';
        if (!empty($toBeUpdateProducts)) {
            foreach ($toBeUpdateProducts as $value) {
                $sku         = $value['sku'];
                $productType = $value['product_type'];

                if ($productType == 'variation') {
                    $varResult         = $this->magento->send_request("V1/products/" . urlencode($sku));
                    $VarProduct_detail = json_decode($varResult);

                    if (!empty($VarProduct_detail) && isset($VarProduct_detail->name)) {
                        $VarCustomAttributes       = isset($VarProduct_detail->custom_attributes) ? $VarProduct_detail->custom_attributes : array();
                        $var_stock_item_attributes = isset($VarProduct_detail->extension_attributes->stock_item) ? $VarProduct_detail->extension_attributes->stock_item : array();
                        $var_media_gallery_data    = isset($VarProduct_detail->media_gallery_entries) ? $VarProduct_detail->media_gallery_entries : array();
                        $db_var_update_cust_attr   = array();
                        $var_product_id_type       = 'upc';
                        $var_product_id            = '';
                        $var_desc                  = NULL;
                        $var_main_image            = NULL;
                        $var_category_ids          = NULL;
                        $var_tax_class_id          = NULL;
                        $var_meta_title            = NULL;
                        $var_meta_keyword          = NULL;
                        $var_meta_desc             = NULL;
                        $var_url_key               = NULL;
                        $var_is_feature            = NULL;
                        $var_country_manufacture   = NULL;

                        $VarProdAttributes = Array();
                        foreach ($VarCustomAttributes as $cvalue) {
                            if (in_array($cvalue->attribute_code, $AttributeCodeArray)) {
                                $cvalue->attr_id           = array_search($cvalue->attribute_code, $AttributeCodeArray);
                                $cvalue->product_id        = $value['id'];
                                $cvalue->parent_product_id = $value['parent_id'];
                                $VarProdAttributes[]       = $cvalue;
                            }

                            if ($cvalue->attribute_code == 'description') {
                                $var_desc = $cvalue->value;
                            }
                            else if ($cvalue->attribute_code == 'image') {
                                if ($cvalue->value != '') {
                                    //mycode
                                    $var_pic_url    = $this->image_pre_url . $this->magento_media_path . $cvalue->value;
                                    $var_main_image = $var_pic_url;

                                    //old code
                                    // $var_pic_url = $this->magento_web_url.$this->magento_media_path.$cvalue->value;
                                    // $var_file_array = explode('/', $cvalue->value);
                                    // $var_file_name = $var_file_array[count($var_file_array) - 1];
                                    // $var_img_file_name = base_url().$upload_path.$var_file_name;
                                    // file_put_contents($upload_path.$var_file_name, file_get_contents($var_pic_url));
                                    // $var_main_image = $var_img_file_name;
                                }
                            }
                            else if ($cvalue->attribute_code == 'category_ids') {
                                if (!empty($cvalue->value)) {
                                    $var_category_ids = implode(',', $cvalue->value);
                                }
                            }
                            else if ($cvalue->attribute_code == 'tax_class_id') {
                                $var_tax_class_id = $cvalue->value;
                            }
                            else if ($cvalue->attribute_code == 'meta_title') {
                                $var_meta_title = $cvalue->value;
                            }
                            else if ($cvalue->attribute_code == 'meta_keyword') {
                                $var_meta_keyword = $cvalue->value;
                            }
                            else if ($cvalue->attribute_code == 'meta_description') {
                                $var_meta_desc = $cvalue->value;
                            }
                            else if ($cvalue->attribute_code == 'url_key') {
                                $var_url_key = $cvalue->value;
                            }
                            else if ($cvalue->attribute_code == 'is_feature') {
                                $var_is_feature = $cvalue->value;
                            }
                            else if ($cvalue->attribute_code == 'country_of_manufacture') {
                                $var_country_manufacture = $cvalue->value;
                                if (!empty($this->country_array) && !empty($var_country_manufacture) && isset($this->country_array[$var_country_manufacture])) {
                                    $var_country_manufacture = $this->country_array[$var_country_manufacture];
                                }
                            }
                            else if ($cvalue->attribute_code == 'product_id_type') {
                                $var_product_id_type = strtolower($cvalue->value);
                            }
                            else if ($cvalue->attribute_code == 'product_id') {
                                $var_product_id = $cvalue->value;
                            }
                            else {
                                if (!empty($this->CUSTOM_ATTRIBUTE_ARRAY)) {
                                    foreach ($this->CUSTOM_ATTRIBUTE_ARRAY as $attr_code => $db_field) {
                                        if ($cvalue->attribute_code == $attr_code) {
                                            if ($attr_code == 'vendor') {
                                                $db_update_cust_attr[$db_field] = $this->set_vendor($cust_attr->value);
                                            }
                                            else {
                                                $db_update_cust_attr[$db_field] = $cust_attr->value;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($VarProdAttributes)) {
                            foreach ($VarProdAttributes as $varValue) {
                                $NotDeleteArray[] = $this->insertProductAttributeMapping($varValue);
                            }

                            MagentoVerTheOpMap::whereNotIn('id', $NotDeleteArray)->where('product_id', $value['id'])->delete();
                        }

                        if (!empty($var_media_gallery_data)) {
                            $this->insertProductImages($var_media_gallery_data, $value['magento_product_id'], $value['id'], $var_main_image);
                        }

                        $main_updateArray = Array(
                            'magento_product_id' => $value['magento_product_id'],
                            'product_title' => isset($VarProduct_detail->name) ? $VarProduct_detail->name : '',
                            'quantity' => isset($var_stock_item_attributes->qty) ? $var_stock_item_attributes->qty : '',
                            'is_in_stock' => isset($var_stock_item_attributes->is_in_stock) ? $var_stock_item_attributes->is_in_stock : '',
                            'description' => $var_desc,
                            'main_image_url' => $var_main_image,
                            'category_ids' => $var_category_ids,
                            'meta_title' => $var_meta_title,
                            'meta_keyword' => $var_meta_keyword,
                            'meta_description' => $var_meta_desc,
                            'url_key' => $var_url_key,
                            'is_feature' => $var_is_feature,
                            'tax_class' => $var_tax_class_id,
                            'country_of_origin' => $var_country_manufacture,
                            'modified_by' => '1',
                            'modified_date' => date('Y-m-d H:i:s'),
                            'is_detail_updated' => '1',
                            $var_product_id_type => $var_product_id,
                        );

                        if (!empty($db_var_update_cust_attr)) {
                            $main_updateArray = $main_updateArray + $db_var_update_cust_attr;
                        }

                        $updateArray[] = $main_updateArray;
                    }
                }
                else {
                    $result         = $this->magento->send_request("V1/products/" . urlencode($sku));
                    $product_detail = json_decode($result);

                    if (!empty($product_detail)) {
                        $stock_item_attributes = isset($product_detail->extension_attributes->stock_item) ? $product_detail->extension_attributes->stock_item : array();
                        $custom_attributes     = isset($product_detail->custom_attributes) ? $product_detail->custom_attributes : array();
                        $media_gallery_data    = isset($product_detail->media_gallery_entries) ? $product_detail->media_gallery_entries : array();

                        $db_updateA_cust_attr = array();
                        $desc                 = NULL;
                        $main_image           = NULL;
                        $category_ids         = NULL;
                        $tax_class_id         = NULL;
                        $meta_title           = NULL;
                        $meta_keyword         = NULL;
                        $meta_desc            = NULL;
                        $url_key              = NULL;
                        $is_feature           = NULL;
                        $country_manufacture  = NULL;
                        foreach ($custom_attributes as $cust_attr) {
                            if ($cust_attr->attribute_code == 'description') {
                                $desc = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'image') {
                                if ($cust_attr->value != '') {
                                    //my code
                                    $main_pic_url = $this->image_pre_url . $this->magento_media_path . $cust_attr->value;
                                    $main_image   = $main_pic_url;

                                    //old code
                                    // $main_pic_url = $this->magento_web_url.$this->magento_media_path.$cust_attr->value;
                                    // $main_file_array = explode('/', $cust_attr->value);
                                    // $main_file_name = $main_file_array[count($main_file_array) - 1];
                                    // $main_img_file_name = base_url().$upload_path.$main_file_name;
                                    // file_put_contents($upload_path.$main_file_name, file_get_contents($main_pic_url));
                                    // $main_image = $main_img_file_name;
                                }
                            }
                            else if ($cust_attr->attribute_code == 'category_ids') {
                                if (!empty($cust_attr->value)) {
                                    $category_ids = implode(',', $cust_attr->value);
                                }
                            }
                            else if ($cust_attr->attribute_code == 'tax_class_id') {
                                $tax_class_id = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'meta_title') {
                                $meta_title = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'meta_keyword') {
                                $meta_keyword = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'meta_description') {
                                $meta_desc = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'url_key') {
                                $url_key = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'is_feature') {
                                $is_feature = $cust_attr->value;
                            }
                            else if ($cust_attr->attribute_code == 'country_of_manufacture') {
                                $country_manufacture = $cust_attr->value;
                                if (!empty($this->country_array) && !empty($country_manufacture) && isset($this->country_array[$country_manufacture])) {
                                    $country_manufacture = $this->country_array[$country_manufacture];
                                }
                            }
                            else {
                                if (!empty($this->CUSTOM_ATTRIBUTE_ARRAY)) {
                                    foreach ($this->CUSTOM_ATTRIBUTE_ARRAY as $attr_code => $db_field) {
                                        if ($cust_attr->attribute_code == $attr_code) {
                                            if ($attr_code == 'vendor') {
                                                $db_updateA_cust_attr[$db_field] = $this->set_vendor($cust_attr->value);
                                            }
                                            else {
                                                $db_updateA_cust_attr[$db_field] = $cust_attr->value;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($media_gallery_data)) {
                            $this->insertProductImages($media_gallery_data, $value['magento_product_id'], $value['id'], $main_image);
                        }

                        $main_updateArray = Array(
                            'magento_product_id' => $value['magento_product_id'],
                            'product_title' => isset($product_detail->name) ? $product_detail->name : '',
                            'quantity' => isset($stock_item_attributes->qty) ? $stock_item_attributes->qty : '',
                            'is_in_stock' => isset($stock_item_attributes->is_in_stock) ? $stock_item_attributes->is_in_stock : '',
                            'description' => $desc,
                            'main_image_url' => $main_image,
                            'category_ids' => $category_ids,
                            'meta_title' => $meta_title,
                            'meta_keyword' => $meta_keyword,
                            'meta_description' => $meta_desc,
                            'url_key' => $url_key,
                            'is_feature' => $is_feature,
                            'tax_class' => $tax_class_id,
                            'country_of_origin' => $country_manufacture,
                            'modified_by' => '1',
                            'modified_date' => date('Y-m-d H:i:s'),
                            'is_detail_updated' => '1',
                        );

                        if (!empty($db_updateA_cust_attr)) {
                            $main_updateArray = $main_updateArray + $db_updateA_cust_attr;
                        }

                        $updateArray[] = $main_updateArray;
                    }
                }
            }

            if (!empty($updateArray)) {
                Batch::update($this->magento_pro, $updateArray, 'magento_product_id');
                $operation_perform = 1;
            }
        }
        else {
            $operation_perform = 2;
        }
        return $operation_perform;
    }

    public
            function insertProductAttributes($attributeDetail) {
        $this->vari_theme = new MagentoVerTheme;
        if (!empty($attributeDetail->attribute_id)) {
            $result     = $this->vari_theme->select('id')->where('magento_attribute_id', $attributeDetail->attribute_id)->get();
            $result_arr = $result->toArray();

            $insertArray       = Array();
            $insertOptionArray = Array();
            $AttributeDbId     = '';
            if (empty($result) || empty($result_arr)) {
                $insertArray   = Array(
                    'magento_attribute_id' => $attributeDetail->attribute_id,
                    'variation_theme_name' => $attributeDetail->default_frontend_label,
                    'attribute_code' => $attributeDetail->attribute_code
                );
                $AttributeDbId = MagentoVerTheme::create($insertArray)->id;
            }
            else {
                $AttributeDbId = isset($result[0]['id']) ? $result[0]['id'] : '';
            }

            $this->mag_the_option = new MagentoVerTheOption;
            $optionResult_array   = $this->mag_the_option->select('id', 'magento_option_id')->where('magento_attribute_id', $attributeDetail->attribute_id)->get();
            $optionResult_arr     = $optionResult_array->toArray();
            $optionResult         = array();
            if (!empty($optionResult_array) && !empty($optionResult_arr)) {
                foreach ($optionResult_array as $value) {
                    $optionResult[$value->magento_option_id] = $value->id;
                }
            }

            $AttributeOptions = $attributeDetail->options;
            foreach ($AttributeOptions as $value) {
                $label = isset($value->label) ? $value->label : '';
                $valId = isset($value->value) ? $value->value : '';
                if ($label != '' && $valId != '' && !empty($label) && !empty($valId)) {
                    if (!isset($optionResult[$valId])) {
                        $insertOptionArray = Array(
                            'magento_attribute_id' => $attributeDetail->attribute_id,
                            'magento_variation_theme_id' => $AttributeDbId,
                            'magento_option_id' => $valId,
                            'option_value' => $label
                        );
                        MagentoVerTheOption::create($insertOptionArray);
                    }
                }
            }
            return $AttributeDbId;
        }
    }

    public
            function insertProductAttributeMapping($VarProdAttributes) {
        $AttributeDbId     = $VarProdAttributes->attr_id;
        $AttributeOptionId = $VarProdAttributes->value;
        $ProductId         = $VarProdAttributes->product_id;
        $ParentProductId   = $VarProdAttributes->parent_product_id;

        $this->mag_theme_option = new MagentoVerTheOption;
        $result                 = $this->mag_theme_option->where('magento_variation_theme_id', $AttributeDbId)->where('magento_option_id', $AttributeOptionId)->get();

        $result_arr        = $result->toArray();
        $InsetMappingArray = Array();
        if (!empty($result) && !empty($result_arr)) {
            $this->theme_opt_mapping = new MagentoVerTheOpMap;

            $magento_variation_theme_id = !empty($result[0]['magento_variation_theme_id']) ? $result[0]['magento_variation_theme_id'] : '';
            $id                         = !empty($result[0]['id']) ? $result[0]['id'] : '';

            $MappingResult     = $this->theme_opt_mapping->where('variation_theme_id', $magento_variation_theme_id)->where('variation_theme_option_id', $id)->where('product_id', $ProductId)->get();
            $MappingResult_arr = $MappingResult->toArray();

            if (empty($MappingResult) || empty($MappingResult_arr)) {
                $InsetMappingArray['variation_theme_id']        = $magento_variation_theme_id;
                $InsetMappingArray['variation_theme_option_id'] = $id;
                $InsetMappingArray['product_id']                = $ProductId;
                $InsetMappingArray['parent_product_id']         = $ParentProductId;
                $last_ins_id                                    = MagentoVerTheOpMap::create($InsetMappingArray)->id;
                return $last_ins_id;
            }
            else {
                return isset($MappingResult[0]['id']) ? $MappingResult[0]['id'] : '';
            }
        }
    }

    public
            function insertProductImages($imagesData, $magentoProductId, $productId, $main_image = NULL) {
        $this->image_model = new MagentoProductImage;
        $new_result        = $this->image_model->select('id', 'image_file')->where('magento_id', $productId)->get();

        $result = array();
        if (!empty($new_result)) {
            foreach ($new_result as $row) {
                $result[$row->image_file] = $row->id;
            }
        }

        $insertArray    = Array();
        $NotDeleteArray = Array();
        foreach ($imagesData as $value) {
            if ($value->media_type == 'image') {
                $file          = $value->file;
                $picURL        = $this->image_pre_url . $this->magento_media_path . $file;
                $img_file_name = $picURL;
                $file_array    = explode('/', $file);
                $file_name     = $file_array[count($file_array) - 1];
                if ($img_file_name != $main_image) {
                    if (!isset($result[$file_name])) {
                        $insertArray      = Array(
                            'magento_id' => $productId,
                            'magento_product_id' => $magentoProductId,
                            'image_url' => $img_file_name,
                            'image_file' => $file_name,
                            'inserted_date' => date('Y-m-d H:i:s'),
                        );
                        $id               = $this->image_model->create($insertArray)->id;
                        $NotDeleteArray[] = $id;
                    }
                    else {
                        $NotDeleteArray[] = $result[$file_name];
                    }
                }
            }
        }
        if (!empty($NotDeleteArray)) {
            MagentoProductImage::whereNotIn('id', $NotDeleteArray)->where('magento_id', $productId)->delete();
        }
        else {
            MagentoProductImage::where('magento_id', $productId)->delete();
        }
    }

    public
            function set_vendor($vendor_name) {
        $this->magento = new Vendor;
        $vendor_id     = NUll;
        $vendor_exist  = $this->magento->where('vendor_name', trim($vendor_name));
        if (empty($vendor_exist)) {
            $insert_vendor['vendor_name']   = $vendor_name;
            $insert_vendor['is_deleted']    = '0';
            $insert_vendor['inserted_date'] = date('Y-m-d H:i:s');
            $insert_vendor['inserted_by']   = '0';
            $insert_vendor['modified_by']   = '0';
            $vendor_id                      = $this->magento->create($insert_vendor)->id;
        }
        else {
            $vendor_exist = $vendor_exist[0];
            if ($vendor_exist['is_deleted'] == '1') {
                $vendor_update['modified_by']   = '0';
                $vendor_update['modified_date'] = date('Y-m-d H:i:s');
                $vendor_update['is_deleted']    = '0';
                Vendor::where('id', $vendor_exist['id'])->update($vendor_update);
            }

            $vendor_id = $vendor_exist['id'];
        }

        return $vendor_id;
    }

    public
            function cron_start_end_update($cron_id = NULL, $cron_type, $cron_name, $store_id) {
        try {
            if (!empty($cron_id)) {
                $cron_up           = Cron::find($cron_id);
                $cron_up->end_time = date('Y-m-d H:i:s');
                ;
                $cron_up->save();
            }
            else {
                $newc_cron  = new Cron;
                $cron_data  = array(
                    'store_id' => $store_id,
                    'cron_name' => $cron_name,
                    'cron_type' => $cron_type,
                    'start_time' => date('Y-m-d H:i:s')
                );
                //insert cron data call
                $insertedId = $newc_cron->insertGetId($cron_data);
                return $insertedId; //return cron id
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

}
