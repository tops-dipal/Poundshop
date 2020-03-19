<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductFetch;
use App\Cron;
use Batch;
use App\Library\Magento;
use App\MagentoProduct;
use App\MagentoProductPosting;

class MagentoProPostController extends Controller
{
    public function __construct() 
	{
		ini_set('max_execution_time', 0);
        
		$this->CUSTOM_ATTRIBUTE_ARRAY = array(
            'brand' => 'Brand',
            // 'mpn'   => 'Manufacturer Part Number',
            //'manufacturer'   => 'Manufacturer Name',
            // 'product_condition' => 'Product Condition', 
            // 'product_condition_notes' => 'Product Condition Notes', 
            // 'vendor' => 'Vendor', 
            // 'weight' => 'Weight', 
            'ts_dimensions_length' => 'Length', 
            'ts_dimensions_height' => 'Height', 
            'ts_dimensions_width' => 'Width', 
            'category_ids' => 'category Id', 
            'description' => 'Description', 
            //'tax_class_id' => 'Tax Class ID', 
            'meta_title' => 'Meta Title',
            'meta_keyword' => 'Meta Title',
            'meta_description' => 'Meta Description',
            'country_of_manufacture' => 'Country of Manufacture',
            // 'upc' => 'UPC',
            // 'isbn' => 'ISBN',
            // 'ean' => 'EAN',
        );

        $this->CUSTOM_ATTRIBUTE_DATATYPE_ARRAY = array('country_of_manufacture' => 'select',);

        $this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY = array(
            'brand' => 'brand',
            // 'mpn'   => 'manufacturer_part_number',
            //'manufacturer'   => 'manufacturer',
            // 'product_condition' => 'magento_item_condition', 
            // 'product_condition_notes' => 'magento_condition_notes', 
            // 'vendor' => 'vendor_name', 
            // 'weight' => 'weight', 
            'ts_dimensions_length' => 'magento_product_length', 
            'ts_dimensions_height' => 'magento_product_height', 
            'ts_dimensions_width' => 'magento_product_width', 
            'category_ids' => 'category', 
            'description' => 'product_description', 
            //'tax_class_id' => 'tax_class', 
            'meta_title' => 'meta_title',
            'meta_keyword' => 'meta_keyword',
            'meta_description' => 'meta_description',
            'country_of_manufacture' => 'country_of_origin',
            // 'upc' => 'magento_product_id_type',
            // 'isbn'=> 'magento_product_id_type',
            // 'ean' => 'magento_product_id_type',
        );
        $this->ATTRIBUTE_SET_GROUP_NAME = 'Attributes';

        //pass blank array
        // $this->CUSTOM_ATTRIBUTE_ARRAY=array();
        // $this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY=array();
             
	}

    public $magento;

    public $magento_pro;

    public $magento_pro_posting;

    public $store_id;
    

    public function index(Request $request)
    {
		try
		{
			$store_id='1';//default for magento
			
			if(isset($request->store_id))
			{
				$store_id=$request->store_id;
			}

			$action='';
			if(isset($request->action)) 
			{
	            $action = $request->action;
	            $this->ACTION=$action;
	        }

            $this->store_id=$store_id;
			$store_data=ProductFetch::where('id',$store_id)->get();
			$this->magento=new Magento;
			$this->magento->USER_NAME=isset($store_data[0]['magento_username'])?$store_data[0]['magento_username']:'';
			$this->magento->PASSWORD=isset($store_data[0]['magento_password'])?$store_data[0]['magento_password']:'';
			$this->magento->ENDPOINT  = isset($store_data[0]['magento_api_url'])?$store_data[0]['magento_api_url']:'';
			$this->magento->magento_web_url    = isset($store_data[0]['magento_web_url'])?$store_data[0]['magento_web_url']:'';
			$this->image_pre_url=str_replace("rest/","",$this->magento->ENDPOINT);
			$this->magento_pro_posting=new MagentoProductPosting;			
            if($this->magento->USER_NAME!='' && $this->magento->PASSWORD!='' && $this->magento->ENDPOINT!='') 
			{
				$this->STORE_ID=$store_id;
				$this->CRON_NAME = 'CRON_' . time();   // CRON NAME
	        	$this->CRON_TITLE = 'MAGENTO_PRODUCT_POSTING';
	        	$this->CRON_NAME = $this->CRON_NAME . '_' . $this->STORE_ID;	
				
                //cron start code
				$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);
                                
                $operation_perform=$this->product_post();               

				//update cron data
				$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);	

				//code for magento				
				if(!empty($operation_perform) && $operation_perform==1)
				{
					return $this->sendResponse('Products post successfully', 200);
				}
                else if(!empty($operation_perform) && $operation_perform==2)
                {
                    return $this->sendResponse('No Products Left for posting', 200);
                }
				else
				{
					return $this->sendError('Products post has not been successfully, please try again', 422);
				}
			}
			else
			{
				return $this->sendError('Magento Credetails are not set.', 422);
			}
		} 
		catch (Exception $ex) 
		{
			return $this->sendError($ex->getMessage(), 400);
		}
    } 


    public function product_post()  
    {
        $operation_perform=0;          
        $products_limit = 20;//as of now taking only 20        
        $product_attribute_id = array();
        $product_attr_options = array();
        $product_result =$this->magento_pro_posting->getPostingProductList($this->STORE_ID,$products_limit);         

        $product_result_arr=$product_result->toArray();               

        if (!empty($product_result) && !empty($product_result_arr)) 
        {
            if(!empty($this->CUSTOM_ATTRIBUTE_ARRAY))
            {
                // finding default attribute set
                $attr_set_id = '';
                $attr_set_group_id = '';
                $attribute_sets  = $this->magento->send_request("V1/products/attribute-sets/sets/list?searchCriteria=0");                
                $attribute_sets_array = json_decode($attribute_sets);                
                $attr_set_id = $attribute_sets_array->items[0]->attribute_set_id;               
                // Listing Groups in attribute set 
                $searchCondition = "searchCriteria[filter_groups][0][filters][0][field]=attribute_set_id&" ;
                $searchCondition = $searchCondition .  "searchCriteria[filter_groups][0][filters][0][value]=$attr_set_id&" ;
                $searchCondition = $searchCondition ."searchCriteria[filter_groups][0][filters][0][condition_type]=eq";                
                $attr_set_group     = $this->magento->send_request("V1/products/attribute-sets/groups/list?".$searchCondition);
                
                $attr_set_group_array = json_decode($attr_set_group);               

                if(!empty($attr_set_group_array->items))
                {
                    foreach ($attr_set_group_array->items as $attr_grps)
                    {
                        if($attr_grps->attribute_group_name == $this->ATTRIBUTE_SET_GROUP_NAME)
                        {
                            $attr_set_group_id = $attr_grps->attribute_group_id;
                        }   
                    }    
                } 
                $creat_custom_attribure=array();
                $custom_attribute_array=array();
                //each of custom attribute array 
                foreach ($this->CUSTOM_ATTRIBUTE_ARRAY as $cust_code => $cust_label)
                {
                    $product_attr = $this->magento->send_request("V1/products/attributes/$cust_code");
                    $product_attr = json_decode($product_attr);                    

                    if(!isset($product_attr->attribute_code))
                    {
                        $creat_custom_attribure[$cust_code] = $cust_label;
                    }
                    else
                    {
                        $custom_attribute_array[$cust_code] = $cust_label;
                        $custom_attribute_detail[$cust_code] = $product_attr;
                        if(!empty($attr_set_group_id) && !empty($attr_set_id))
                        {    
                            $post_data_attr_set = array (
								'attributeSetId' => $attr_set_id,
								'attributeGroupId' => $attr_set_group_id,
								'attributeCode' => $cust_code,
								'sortOrder' => 0,
                            );

                            $product_attr_set = $this->magento->send_request("V1/products/attribute-sets/attributes",$post_data_attr_set,'POST');
                        }
                    }    
                } 
                
                if(isset($creat_custom_attribure) && !empty($creat_custom_attribure))
                {                    
                    foreach ($creat_custom_attribure as $cust_code => $cust_label)
                    {
                        $option = array();
                        $post_data=array ('attribute' => 
                                            array (
                                                'isWysiwygEnabled' => true,
                                                'isHtmlAllowedOnFront' => true,
                                                'usedForSortBy' => true,
                                                'isFilterable' => true,
                                                'isFilterableInSearch' => true,
                                                'position' => 0,
                                                'applyTo' => 
                                                array(),
                                                'isSearchable' => 'No',
                                                'isVisibleInAdvancedSearch' => 'No',
                                                'isComparable' => 'No',
                                                'isUsedForPromoRules' => 'No',
                                                'isVisibleOnFront' => 'No',
                                                'usedInProductListing' => 'No',
                                                'isVisible' => true,
                                                'scope' => 'store',
                                                'extensionAttributes' => 
                                                array (
                                                ),
                                                'attributeId' => 0,
                                                'attributeCode' => trim($cust_code),
                                                'frontendInput' => isset($this->CUSTOM_ATTRIBUTE_DATATYPE_ARRAY[$cust_code]) ? $this->CUSTOM_ATTRIBUTE_DATATYPE_ARRAY[$cust_code] : 'text',
                                                'entityTypeId' => '4',
                                                'isRequired' => false,
                                                'options' => $option,
                                                'isUserDefined' => true,
                                                'defaultFrontendLabel' => trim($cust_label),
                                                'frontendLabels' =>array(),
                                                'note' => "",
                                                'backendType' => 'varchar',
                                                'isUnique' => 'No',
                                                'frontendClass' => '',
                                                'validationRules' =>array(),
                                                'customAttributes' =>array(),
                                          	),
                                        );

                        $product_attr = $this->magento->send_request("V1/products/attributes/",$post_data,'POST');                        
                            
                        $product_attr = json_decode($product_attr);                        
                        if(isset($product_attr->attribute_code))
                        {
                            $custom_attribute_array[$cust_code] = $cust_label;
                            $custom_attribute_detail[$cust_code] = $product_attr;
                            if(!empty($attr_set_group_id) && !empty($attr_set_id))
                            {    
                                $post_data_attr_set = array (
									'attributeSetId' => $attr_set_id,
									'attributeGroupId' => $attr_set_group_id,
									'attributeCode' => $cust_code,
									'sortOrder' => 0,
                                );

                                $product_attr_set = $this->magento->send_request("V1/products/attribute-sets/attributes",$post_data_attr_set,'POST');
                            }
                        }
                    }    
                }   
            }  
            
            foreach ($product_result as $key => $value) 
            {
                $error = Array();
                $magento_product_variation_result = $this->magento_pro_posting->getProductVariList($value['product_master_id'],$this->STORE_ID);  
                $magento_product_variation_result_array=$magento_product_variation_result->toArray();              

                $is_product_variations = 0;
                $product_instock       = 0;
                $product_links         = array();
                $config_color_ids      = array();
                $config_size_ids       = array();


                if (!empty($magento_product_variation_result) && !empty($magento_product_variation_result_array))
                {
                    $is_product_variations = 1;
                    $product_id_set = 0;

                    foreach ($magento_product_variation_result as $vari_key => $vari_value)
                    {
                        if(!empty($custom_attribute_array))
                        {
                            foreach ($custom_attribute_array as $cust_code => $cust_label)
                            {
                                if($this->CUSTOM_ATTRIBUTE_DATATYPE_ARRAY[$cust_code] == 'select')
                                {
                                    $sel_value = '';

                                    $cust_attr_d = $custom_attribute_detail[$cust_code];

                                    if (!empty($cust_attr_d->options)) 
                                    {
                                        foreach ($cust_attr_d->options as $key => $attr_value) 
                                        {
                                            if (!empty($attr_value->label)) 
                                            {
                                                if($vari_value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]] == $attr_value->label || $vari_value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]] == $attr_value->value)
                                                {     
                                                    $sel_value = $attr_value->value;
                                                }
                                            }
                                        }
                                    }                                   

                                    if(empty($sel_value))
                                    {
                                        $sel_value = $this->create_attribute_option($cust_code,$vari_value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]]);    
                                    }

                                    if(!empty($sel_value))
                                    {
                                        $custom_attributes[] = array('attribute_code' => $cust_code,'value' => $sel_value); 
                                    }     
                                }
                                else 
                                { 

                                    if($cust_code == 'category_ids')
                                    {
                                        $custom_attributes[] = array('attribute_code' => $cust_code,'value' => empty($vari_value['category']) ? array('2') : explode(',', $vari_value['category'])
                                                                );
                                    } 
                                    else if(in_array($cust_code, array('upc','isbn','ean')))
                                    {
                                        if($product_id_set == 0)
                                        {    
                                            $product_id_type = $vari_value['magento_product_id_type'];
                                            $product_id = $vari_value['magento_product_id'];
                                            $custom_attributes[] = array('attribute_code' => $product_id_type,'value' => $product_id);
                                            $product_id_set = 1;  
                                        }                        
                                    } 
                                    else
                                    {   
                                        $custom_attributes[] = array('attribute_code' => $cust_code,'value' => !empty($vari_value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]]) ? $vari_value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]] : '');
                                    }
                                }                                                       
                            }    
                        }
                        else
                        {
                            $custom_attributes = array();
                        }

                        if ($vari_value['quantity'] > 0) {
                            $product_instock = 1;
                        }

                        $postData = Array('product' => Array(
                            "sku"               => $vari_value['sku'],
                            "name"              => $vari_value['product_title'],
                            "price"             => $vari_value['selling_price'],
                            "status"            => 1,
                            'visibility'        => 0,
                            "type_id"           => "simple",
                            "attribute_set_id"  => 4,
                            'custom_attributes' => $custom_attributes,
                            "extension_attributes" => Array(
                                "stock_item" => Array(
                                    "qty"         => $vari_value['quantity'],
                                    "is_in_stock" => !empty($product_instock)?true:false,
                                ),
                            )
                        ));

                        if (!empty($vari_value['magento_product_weight'])) 
                        {
                            $postData['product']['weight'] = $vari_value['magento_product_weight'];
                        }                        

                        if ($vari_value['main_image_url']) 
                        {                            
                            $image_explode = explode('/', $vari_value['main_image_url']);
                            $image_name    = $image_explode[count($image_explode)-1];
                            $img_ext_title = explode('.', $image_name);
                            $img_ext = (!empty($img_ext_title[1]) && $img_ext_title[1] == 'png')?'png':'jpeg';

                            $postData['product']['media_gallery_entries'] = Array(
                               "entry" => Array
                                   (                                   
                                   'media_type' => 'image', // Use image instead of Image
                                   'label' => $img_ext_title[0],
                                   'position' => 0,
                                   'disabled' => false,
                                   'types' => Array
                                    (
                                        '0' => 'image',
                                        '1' => 'small_image',
                                        '2' => 'thumbnail',
                                        '3' => 'swatch_image',
                                    ),
                                    'file' => $vari_value['main_image_url'],
                                    'content' => Array
                                    (
                                        'base64_encoded_data' => base64_encode(file_get_contents($vari_value['main_image_url'])),
                                        'type' => 'image/'.$img_ext,
                                        'name' => strtok($image_name, '?'),
                                    ),
                                ),
                            );
                        }

                        if ($vari_value['image_details']) 
                        {                            
                            $multiple_image_array = unserialize($vari_value['image_details']);
                            foreach ($multiple_image_array as $images) 
                            {
                                foreach ($images as $key => $image_url) 
                                {
                                    $image_explode = explode('/', $image_url['image_url']);
                                    $image_name    = $image_explode[count($image_explode)-1];
                                    $img_ext_title = explode('.', $image_name);
                                    $img_ext = (!empty($img_ext_title[1]) && $img_ext_title[1] == 'png')?'png':'jpeg';
                                    $postData['product']['media_gallery_entries'][] = Array(
                                        'media_type' => 'image', // Use image instead of Image
                                        'label' => $img_ext_title[0],
                                        'position' => 0,
                                        'disabled' => false,                                       
                                        'file' => $image_url['image_url'],
                                        'content' => Array
                                        (
                                            'base64_encoded_data' => base64_encode(file_get_contents($image_url['image_url'])),
                                            'type' => 'image/'.$img_ext,
                                            'name' => strtok($image_name, '?'),
                                        ),
                                    );
                                }
                            }
                        }

                        if ($vari_value['variation_theme'] && !empty($vari_value['variation_theme_value'])) 
                        {
                            $variation_theme     = explode('||', strtolower($vari_value['variation_theme']));
                            $variation_theme_val = explode('||', strtolower($vari_value['variation_theme_value']));

                            foreach ($variation_theme as $theme_key => $theme) 
                            {
                                if (!isset($product_attr_options[$theme])) 
                                {                                    
                                    $attr_result     = $this->magento->send_request("V1/products/attributes/".$theme);
                                    $product_attr = json_decode($attr_result);
                                    if (isset($product_attr->attribute_id)) {
                                        $product_attribute_id[$theme] = $product_attr->attribute_id;
                                        if (!empty($product_attr->options)) {
                                            foreach ($product_attr->options as $key => $attr_value) {
                                                if (!empty($attr_value->label)) {
                                                    $product_attr_options[$product_attr->attribute_code][strtolower($attr_value->label)] = $attr_value->value;
                                                }
                                            }
                                        }
                                    }
                                }

                                $set_attr_val = array_combine($variation_theme, $variation_theme_val);

                                if (!isset($product_attr_options[$theme][$set_attr_val[$theme]])) {
                                    $product_attr_options = $this->create_color_option($theme, $set_attr_val[$theme]);
                                }
                                
                                $postData['product']['custom_attributes'][] = array('attribute_code' => $theme, 'value' => $product_attr_options[$theme][$set_attr_val[$theme]]);

                                $config_color_ids[$theme][] = array('value_index' => $product_attr_options[$theme][$set_attr_val[$theme]]);
                            }
                        }                        
                    
                        $result = $this->magento->send_request("V1/products/", $postData, 'POST');
                        $variation_post = json_decode($result);

                        if (isset($variation_post->id))
                        {
                            $upd_data[] = array(
                            		'id'=>$vari_value['id'],
                            		'magento_item_id' => $variation_post->id,
                                    "is_posted"             => "2", // Posted,
                                    "posting_result_status" => "1", // For Sucess,
                                    "posting_result"        => NULL,
                                );                            

                            Batch::update($this->magento_pro_posting,$upd_data,'id');
                            $product_links[] = $variation_post->id;
                            //echo "Varition product id ".$vari_value['id']." posted successfully...";
                        }
                        else 
                        {
                            if(isset($variation_post->message)) 
                            {
                                $error[] = array('error_type'=> 'error', 'long_msg' => $variation_post->message);
                                $upd_data[] = array(
                                	"id"=>$value['id'],
                                    "posting_result_status" => "3", // For Error,
                                    "posting_result" => serialize($error), 
                                );                                

                                Batch::update($this->magento_pro_posting,$upd_data,'id');

                            }
                            //echo "Varition product id ".$vari_value['id']." have error :-".$variation_post->message;

                            continue 2;
                        }
                    }
                }
                
                if(!empty($custom_attribute_array))
                {   
                    $product_id_set = 0;
                    foreach ($custom_attribute_array as $cust_code => $cust_label)
                    {
                        if(@$this->CUSTOM_ATTRIBUTE_DATATYPE_ARRAY[$cust_code] == 'select')
                        {
                            $sel_value = '';
                            $cust_attr_d = $custom_attribute_detail[$cust_code];
                            if (!empty($cust_attr_d->options)) 
                            {
                                foreach ($cust_attr_d->options as $key => $attr_value) 
                                {                                    
                                    if (!empty($attr_value->label)) 
                                    {
                                        if(strtoupper($value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]]) ==  $attr_value->label || $value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]] == $attr_value->value)
                                        {     
                                            $sel_value = $attr_value->value;
                                        }
                                    }
                                }
                            }
                           
                            if(empty($sel_value))
                            {
                                $insert_option_result = $this->create_attribute_option($cust_code,$value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]]);    
                                
                                if(!empty($insert_option_result))
                                {
                                    foreach ($insert_option_result as $key => $attr_value )
                                    {
                                        if(strtoupper($value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]]) ==  $attr_value->label || $value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]] == $attr_value->value)
                                        {     
                                            $sel_value = $attr_value->value;
                                        }
                                    }    
                                }
                            }

                            if(!empty($sel_value))
                            {
                                $custom_attributes[] = array('attribute_code' => $cust_code,'value' => $sel_value); 
                            }     
                        }
                        else 
                        {
                            if($cust_code == 'category_ids')
                            {
                                $custom_attributes[] = array(
                                                        'attribute_code' => $cust_code,
                                                        'value' => empty($value['category']) ? array('2') : explode(',', $value['category'])
                                                        );
                            } 
                            else if(in_array($cust_code, array('upc','isbn','ean')))
                            {
                                if($product_id_set == 0)
                                {    
                                    $product_id_type = $value['magento_product_id_type'];
                                    $product_id = $value['magento_product_id'];
                                    $custom_attributes[] = array('attribute_code' => $product_id_type,'value' => $product_id);
                                    $product_id_set = 1;  
                                }                        
                            } 
                            else
                            {   
                                $custom_attributes[] = array('attribute_code' => $cust_code,'value' => !empty($value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]]) ? $value[$this->CUSTOM_ATTRIBUTE_DATABASE_ARRAY[$cust_code]] : '');
                            }
                        }                                                
                    }    
                }
                else
                {
                    $custom_attributes = array();
                }
                
                // call for parent or normal product create or update
                $config_data = Array('product' => Array(
                    "sku"               => $value['sku'],
                    "name"              => $value['product_title'],
                    "price"             => $value['selling_price'],
                    "status"            => 1,
                    'visibility'        => 4, /* 'catalog', */
                    "type_id"           => !empty($is_product_variations) ? "configurable": "simple",
                    "attribute_set_id"  => 4,
                    'custom_attributes' => $custom_attributes,
                ));

                if (!empty($value['magento_product_weight'])) 
                {
                    $config_data['product']['weight'] = $value['magento_product_weight'];
                }

                if (!empty($is_product_variations))
                {
                    foreach ($product_attribute_id as $attr_key => $attr_val) 
                    {
                        $config_data['product']['extension_attributes']['configurable_product_options'][] = Array(
                            "attribute_id" => $attr_val,
                            "label"        => $attr_key,
                            "values"       => $config_color_ids[$attr_key],
                        );
                    }

                    $config_data['product']['extension_attributes']['configurable_product_links'] = $product_links;
                    
                    $config_data['product']['extension_attributes']['stock_item']['is_in_stock'] = !empty($product_instock)?true:false;
                }
                else 
                {
                    if($value['quantity'] > 0)
                    {
                        $product_instock=1;
                    }   

                    $config_data['product']['extension_attributes'] = Array(
                        "stock_item" => Array(
                            "qty"         => $value['quantity'],
                            "is_in_stock" => !empty($product_instock)?true:false,
                        ),
                    );
                }

                if ($value['main_image_url']) 
                {   
                    $image_explode = explode('/', $value['main_image_url']);
                    $image_name    = $image_explode[count($image_explode)-1];
                    $img_ext_title = explode('.', $image_name);
                    $img_ext = (!empty($img_ext_title[1]) && $img_ext_title[1] == 'png')?'png':'jpeg';
                    $config_data['product']['media_gallery_entries'] = Array(
                       "entry" => Array
                           (                           
                           'media_type' => 'image', // Use image instead of Image
                           'label' => $img_ext_title[0],
                           'position' => 0,
                           'disabled' => false,
                           'types' => Array
                            (
                                '0' => 'image',
                                '1' => 'small_image',
                                '2' => 'thumbnail',
                                '3' => 'swatch_image',
                            ),
                            'file' => $value['main_image_url'],
                            'content' => Array
                            (
                                'base64_encoded_data' => base64_encode(file_get_contents($value['main_image_url'])),
                                'type' => 'image/'.$img_ext,
                                'name' => strtok($image_name, '?'),
                            ),
                        ),
                    );
                }

                if ($value['image_details']) 
                {                    
                    $multiple_image_array = unserialize($value['image_details']);
                    
                    foreach ($multiple_image_array as $image_url) 
                    {

                        $image_explode = explode('/', $image_url);
                        $image_name    = $image_explode[count($image_explode)-1];
                        $img_ext_title = explode('.', $image_name);
                        $img_ext = (!empty($img_ext_title[1]) && $img_ext_title[1] == 'png')?'png':'jpeg';
                        $config_data['product']['media_gallery_entries'][] = Array(
                            'media_type' => 'image', // Use image instead of Image
                            'label' => $img_ext_title[0],
                            'position' => 0,
                            'disabled' => false,                               
                            'file' => $image_url,
                            'content' => Array
                            (
                                'base64_encoded_data' => base64_encode(file_get_contents($image_url)),
                                'type' => 'image/'.$img_ext,
                                'name' => strtok($image_name, '?'),
                            ),
                        );

                        // foreach ($images as $key => $image_url) 
                        // {
                        //     $image_explode = explode('/', $image_url['image_url']);
                        //     $image_name    = $image_explode[count($image_explode)-1];
                        //     $img_ext_title = explode('.', $image_name);
                        //     $img_ext = (!empty($img_ext_title[1]) && $img_ext_title[1] == 'png')?'png':'jpeg';
                        //     $config_data['product']['media_gallery_entries'][] = Array(
                        //        	'media_type' => 'image', // Use image instead of Image
                        //        	'label' => $img_ext_title[0],
                        //        	'position' => 0,
                        //        	'disabled' => false,                               
                        //         'file' => $image_url['image_url'],
                        //         'content' => Array
                        //         (
                        //             'base64_encoded_data' => base64_encode(file_get_contents($image_url['image_url'])),
                        //             'type' => 'image/'.$img_ext,
                        //             'name' => strtok($image_name, '?'),
                        //         ),
                        //     );
                        // }
                    }
                }      
                
                $result = $this->magento->send_request("V1/products/", $config_data, 'POST');
                $config_post = json_decode($result);
                
                if (isset($config_post->id))
                {
                    //add_plan_variable_log($this->USER_UNIQUE_ID, 'products');
                    
                    $upd_data[] = array('id' =>$value['id'],
                    		'magento_item_id' => $config_post->id,
                            "is_posted"             => "2", // Posted,
                            "posting_result_status" => "1", // For Sucess,
                            "posting_result"        => NULL,
                        );                    

                    Batch::update($this->magento_pro_posting,$upd_data,'id');
                    //echo $value['id']." product posted successfully.";
                }
                else 
                {
                    if(isset($config_post->message)) 
                    {
                        $error[] = array('error_type'=> 'error', 'long_msg' => $config_post->message);
                    } 
                    else 
                    {
                        $error[] = array('error_type'=> 'error', 'long_msg' => 'Something has been wrong with your magento store.');
                    }


                    $upd_data[] = array(
                    	"id"=>$value['id'],
                        "posting_result_status" => "3", // For Error,
                        "is_posted" => "3",
                        "posting_result" => serialize($error), 
                    );
                    
                    //echo $value['id']." product have error.".$config_post->message;
                    Batch::update($this->magento_pro_posting,$upd_data,'id');
                }
            }
            
            $operation_perform=1;
        }
        else 
        {
            $operation_perform=2;
            //echo "No product left for posting...";
        }     

        return $operation_perform;
    }    

    public function cron_start_end_update($cron_id=NULL,$cron_type, $cron_name, $store_id)
    {
    	try
		{
	    	if(!empty($cron_id))
	    	{
	    		$cron_up = Cron::find($cron_id);
				$cron_up->end_time = date('Y-m-d H:i:s');;
				$cron_up->save();
	    	}
	    	else
	    	{
	    		$newc_cron=new Cron;
	    		$cron_data=array(
		            'store_id' => $store_id,
		            'cron_name' => $cron_name,
		            'cron_type' => $cron_type,
		            'start_time' => date('Y-m-d H:i:s')
	        	);			
	    		//insert cron data call
	    		$insertedId=$newc_cron->insertGetId($cron_data); 	    		
	    		return $insertedId;//return cron id
	    	}
    	}
    	catch(Exception $ex)
		{
			return $this->sendError($ex->getMessage(), 400);
		}    	
    }
    
    public function get_all_categories($magento_category_array)
    {   
        foreach ($magento_category_array as $key => $value) 
        {
            $this->categories_array[] = Array('id' => $value->id,
                                            'parent_id' => $value->parent_id,
                                            'name' => $value->name,
                                            'is_active' => $value->is_active,
                                            'position' => $value->position,
                                            'level' => $value->level
                                            );

            if (!empty($value->children_data)) 
            {
                $this->get_all_categories($value->children_data);
            }            
        }
        return $this->categories_array;
    }
    
    public function create_color_option($theme, $option_label='')
    {
        if ($option_label && $theme) 
        {
            $option_data = array('option' => array(
                "label"        => $option_label,
                "sortOrder"    => 1,
                "is_default"   => false,
                "store_labels" => array (
                    array( "store_id" => '0', "label" => $option_label)
                )
            ));
            
            $result = $this->magento->send_request("V1/products/attributes/".$theme."/options", $option_data, 'POST');
            $chk_result = json_decode($result);

            if ($chk_result) 
            {
                $result     = $this->magento->send_request("V1/products/attributes/".$theme."/options");
                $color_attr = json_decode($result);
                if (!empty($color_attr)) 
                {
                    foreach ($color_attr as $key => $color_attr_value) 
                    {
                        if (!empty($color_attr_value->label)) 
                        {
                            $color_options[$theme][strtolower($color_attr_value->label)] = $color_attr_value->value;
                        }
                    }
                    return $color_options;
                }
            }
            else 
            {
                // return error;
            }
        }
    }

    public function create_attribute_option($attr_code, $option_label='')
    {
        $value = trim(strtolower($option_label));        
        $value = str_replace(" ","_",$value);        
        $option_data = array('option' => array(
                "label"        => $option_label,
                "value"        => 123,
                "sortOrder"    => 0,
                "is_default"   => false,
                "store_labels" => array(),
        ));        

        $result = $this->magento->send_request("V1/products/attributes/".$attr_code."/options", $option_data, 'POST');
        $chk_result = json_decode($result);        
        if ($chk_result) 
        {
            $result     = $this->magento->send_request("V1/products/attributes/".$attr_code."/options");            
            $result = json_decode($result);            
            return $result;
        }    

        return '';        
    }
}