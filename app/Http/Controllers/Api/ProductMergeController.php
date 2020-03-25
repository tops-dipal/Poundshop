<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductFetch;
use App\Cron;
use Batch;
use App\Library\Magento;
use App\MagentoProduct;
use App\MagentoProductImage;
use App\Products;
use App\VariationThemes;
use App\ProductImage;
class ProductMergeController extends Controller
{  
	public function __construct() 
	{
		ini_set('max_execution_time', 0);

        ini_set('memory_limit', -1);

		$this->PRODUCT_LIMIT = 5000;

	    $this->TIME = time();
	        
	    $this->EXECUTION_TIME = 800;

	    $this->orphan_products = array();	
	}  

    public $magento;

    public $magento_pro;    

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

            $this->store_id=$store_id;
			$store_data=ProductFetch::where('id',$store_id)->get();
			$this->magento=new Magento;			
            
			$this->STORE_ID=$store_id;
			$this->CRON_NAME = 'CRON_' . time();   // CRON NAME
        	$this->CRON_TITLE = 'MAGENTO_PRODUCT_MERGE';
        	$this->CRON_NAME = $this->CRON_NAME . '_' . $this->STORE_ID;							
			
            //cron start code
			$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);				

			//get and dump category in database
			$operation_perform=$this->product_merge();

			//update cron data
			$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);	

			//code for magento				
			if(!empty($operation_perform) && $operation_perform==1)
			{
				return $this->sendResponse('Products has been merge successfully', 200);
			}
			else if(!empty($operation_perform) && $operation_perform==2)
			{
				return $this->sendError('No more product to merge, please try again', 422);	
			}
			else
			{
				return $this->sendError('Products has not been merge successfully, please try again', 422);
			}
			
		} 
		catch (Exception $ex) 
		{
			return $this->sendError($ex->getMessage(), 400);
		}
    } 


    public function product_merge()
    {
    	$operation_perform=0;
    	$this->magento_pro=new MagentoProduct;
    	$magento_products = $this->magento_pro->get_magento_products($this->PRODUCT_LIMIT);	
    	$magento_products_arr=$magento_products->toArray();
        $magentoParentIds = Array();
        if(!empty($magento_products) && !empty($magento_products_arr))
        {
	        foreach($magento_products as $value) 
	        {
	            if($value['product_type']=='parent') 
	            {
	                $magentoParentIds[] = $value['id'];
	            }
	        }

	        
	        $parent_products_theme_array = Array();
	        if(!empty($magentoParentIds)) 
	        {
	            $result = $this->magento_pro->getMagentoVariationTheme($magentoParentIds);            
	            if(!empty($result)) 
	            {
	                foreach($result as $tvalue) 
	                {
	                    $parent_products_theme_array[$tvalue['parent_id']] = $tvalue['variation_theme'];
	                }
	            }
	        }
        	
	        foreach ($magento_products as $magento_product) 
	        {
	            $magento_images =MagentoProductImage::where('magento_id',$magento_product["id"])->get();
	            $magento_images_arr=$magento_images->toArray();
	            
	            $update_fields = array();
	            $product_upc_ean = NULL;
	            $product_id_type = NULL;
	            $vendor_id = NULL;
	            $product_type = "normal";
	           	$product_id_type=1;
	            if (!empty($magento_product["upc"])) 
	            {
	                $product_upc_ean = $magento_product["upc"];
	                //$product_id_type = 'upc';	                
	            } 
	            elseif (!empty($magento_product["ean"])) 
	            {
	                $product_upc_ean = $magento_product["ean"];
	                //$product_id_type = 'ean';
	                $product_id_type=2;
	            }
	            elseif (!empty($magento_product["isbn"])) 
	            {
	                $product_upc_ean = $magento_product["isbn"];
	                //$product_id_type = 'isbn';	                
	            }

	            if (isset($magento_product["product_type"])) 
	            {
	                switch (strtolower($magento_product["product_type"])) 
	                {
	                    case 'parent' :
	                        $product_type = "parent";
	                        break;
	                    case 'variation' :
	                        $product_type = "variation";
	                        break;
	                }
	            }
	            $update_fields["marketplace_id"]=$magento_product["id"];
	            $update_fields["product_identifier"] = $product_upc_ean;
	            $update_fields["product_type"] = $product_type;
	            $update_fields["product_identifier_type"] = !empty($product_id_type)?$product_id_type:'';
	            $update_fields["sku"] = isset($magento_product["sku"]) && trim($magento_product["sku"]) != "" ? $magento_product["sku"] : $magento_product["id"];
	            $update_fields["title"] = isset($magento_product["product_title"]) ? $magento_product["product_title"] : NULL;
	            $update_fields["short_title"] = NULL;
	            $update_fields["brand"] = !empty($magento_product["brand"]) ? $magento_product["brand"] : NULL;
	            $update_fields["main_image_internal"] = NULL;
	            $update_fields["main_image_marketplace_url"] = isset($magento_product["main_image_url"]) ? $magento_product["main_image_url"] : NULL;
	            if(empty($update_fields["main_image_marketplace_url"]))
	            {
	            	$update_fields["mp_image_missing"] = '1';
	            }

	            if(!empty($magento_product["is_deleted_product"]))
	            {
	            	$update_fields["is_listed_on_magento"]='2';
	            }
	            else
	            {
	            	$update_fields["is_listed_on_magento"]='1';	
	            }

	            $update_fields["threshold_quantity"] = NULL;
	            $update_fields["single_selling_price"] = isset($magento_product["selling_price"]) ? $magento_product["selling_price"] : NULL;
	            $update_fields["long_description"] = isset($magento_product["description"]) ? $magento_product["description"] : NULL;
	            $update_fields["short_description"] = isset($magento_product["short_description"]) ? $magento_product["short_description"] : NULL;
	            $update_fields["country_of_origin"] = !empty($magento_product["country_of_origin"]) ? $magento_product["country_of_origin"] : NULL;
	                                
	            if ($product_type == 'parent') 
	            {
	                $update_fields["variation_theme"] = isset($parent_products_theme_array[$magento_product["id"]]) ? $parent_products_theme_array[$magento_product["id"]] : NULL;
	            } 
	            else 
	            {
	                $update_fields["variation_theme"] = isset($magento_product["variation_theme"]) ? $magento_product["variation_theme"] : NULL;
	            }
	            
	            $update_fields["variation_theme_value"] = isset($magento_product["variation_theme_value"]) ? $magento_product["variation_theme_value"] : NULL;	            
	            $update_fields["variation_theme_id"] = NULL;
	            $update_fields["variation_theme_value1"] = NULL;
	            $update_fields["variation_theme_value2"] = NULL;
	            $update_fields["product_length"] = !empty($magento_product["product_length"]) ? $magento_product["product_length"] : NULL;
	            $update_fields["product_width"] = !empty($magento_product["product_width"]) ? $magento_product["product_width"] : NULL;
	            $update_fields["product_height"] = !empty($magento_product["product_height"]) ? $magento_product["product_height"] : NULL;
	            $update_fields["product_weight"] = isset($magento_product["product_weight"]) ? $magento_product["product_weight"] : NULL;
	            $update_fields["images_array"] = helper_array_column($magento_images, "id", "image_url");


	            //$update_fields["parent_sku"] = isset($magento_product["parent_sku"]) ? $magento_product["parent_sku"] : NULL;
	            //$update_fields["product_kit_type"] = NULL;
	            //$update_fields["product_kit_weight_calculation_type"] = NULL;
	            //$update_fields["fullfillment_type"] = "warehouse";
	            //$update_fields["alt_product_id"] = NULL;
	            
	            //$update_fields["manufacturer_sku"] = NULL;
	            
	            //$update_fields["manufacturer_name"] = !empty($magento_product["manufacturer"]) ? $magento_product["manufacturer"] : NULL;
	            
	            //$update_fields["manufacturer_part_number"] = !empty($magento_product["manufacturer_part_number"]) ? $magento_product["manufacturer_part_number"] : NULL;
	            //$update_fields["item_condition"] = !empty($magento_product["magento_item_condition"]) ? $magento_product["magento_item_condition"] : "new";
	            //$update_fields["condition_notes"] = !empty($magento_product["magento_condition_notes"]) ? $magento_product["magento_condition_notes"] : NULL;
	            
	            //$update_fields["quantity"] = isset($magento_product["quantity"]) ? $magento_product["quantity"] : NULL;
	            //$update_fields["total_quantity"] = isset($magento_product["quantity"]) ? $magento_product["quantity"] : NULL;
	            //$update_fields["items_includes"] = NULL;
	            //$update_fields["vendor_id"] =  !empty($magento_product["magento_vendor_id"]) ? $magento_product["magento_vendor_id"] : NULL;
	            //$update_fields["product_vendor_id"] =  !empty($magento_product["magento_vendor_id"]) ? $magento_product["magento_vendor_id"] : NULL;
	            //$update_fields["buying_price"] = NULL;
	            //$update_fields["avg_buying_price"] = NULL;
	            
	            //$update_fields["minimum_price"] = NULL;
	            //$update_fields["minimum_advertised_price"] = NULL;
	            
	            // $update_fields["feature2"] = NULL;
	            // $update_fields["feature3"] = NULL;
	            // $update_fields["feature1"] = NULL;
	            // $update_fields["feature4"] = NULL;
	            // $update_fields["feature5"] = NULL;
	            // $update_fields["search_term1"] = NULL;
	            // $update_fields["search_term2"] = NULL;
	            // $update_fields["search_term3"] = NULL;
	            // $update_fields["search_term4"] = NULL;
	            // $update_fields["search_term5"] = NULL;
	            
	            //$update_fields["status"] = "1";
	            //$update_fields["variation_type"] = NULL;
	            //$update_fields["sale_price"] = NULL;
	            //$update_fields["sale_start_date"] = NULL;
	            //$update_fields["sale_end_date"] = NULL;
	           // $update_fields["size"] = NULL;
	            //$update_fields["color"] = NULL;
	            //$update_fields["is_drafted"] = "0";
	            //$update_fields["is_deleted"] = "0";
	            //$update_fields["reversing_from"] = "magento";
	            
	            //$update_fields["store_id"] = $magento_product["store_id"];
	            //$update_fields["warehouse_id"]='1';//as of now taken	            

	            // $update_fields["warehouse_id"] = $this->product_merge_model->get_associated_warehouse_id($magento_product["store_id"]);

	            $this->insert_update_product($update_fields, 'Magento');
	            if ($this->is_time_out())
	                break;
	        }
	    }
	    $operation_perform=1;
        //$this->product_merge_model->update_parent_child();
        return $operation_perform;
        
    }   

    public function set_variation($data = array()) 
    {
        if (isset($data["variation_theme"]) && !empty($data["variation_theme"])) 
        {
            $theme_seprator = "|||";
            $theme = $data["variation_theme"];
            $theme_value = $data["variation_theme_value"];
            $new_them_array = NULL;
            $variation_theme_id = NULL;
            $theme_value_array = NULL;
            if (!empty($theme)) 
            {
                $theme_array = explode("|||", $theme);
                $theme_value_array = explode("|||", $theme_value);
                if (!empty($theme_array)) 
                {
                    $theme1 = $theme_array[0];
                    $theme2 = NULL;
                    if (isset($theme_array[1])) 
                    {
                        $theme2 = $theme_array[1];
                    }
                    
                    //check if exit or not
                    $variation_theme_id_data=VariationThemes::select('id')->where('variation_theme_1',$theme1)->where('variation_theme_2',$theme2)->get();
                    $variation_theme_id_arr=$variation_theme_id_data->toArray();                	
                    if(!empty($variation_theme_id_arr))
                    {
                    	$variation_theme_id= isset($variation_theme_id_data[0]->id)?$variation_theme_id_data[0]->id:'';
                    }

                    //if not exist then create
                    if(empty($variation_theme_id))
                    {
                    	$insertArray=array('variation_theme_name'=>"{$theme1}".($theme2?" - {$theme2}":""),
                    		'variation_theme_1'=>$theme1,
                    		'variation_theme_2'=>$theme2,
                    		'combination_type'=>$theme2?"2":"1",
                    		'inserted_date'=>date('Y-m-d H:i:s'),
                    	);

                    	$variation_theme_id = VariationThemes::create($insertArray)->id;	
                    }                    
                }
            }
            $data["variation_theme_id"] = $variation_theme_id;
            $data["variation_theme_value1"] = isset($theme_value_array[0]) ? $theme_value_array[0] : NULL;
            $data["variation_theme_value2"] = isset($theme_value_array[1]) ? $theme_value_array[1] : NULL;
        }        
        return $data;
    }


    public function get_images_array($data = array()) 
    {
        $insert_images_array = NULL;
        if (isset($data["images_array"]) && !empty($data["images_array"])) 
        {
            foreach ($data["images_array"] as $value) 
            {
                $insert_images_array[] = array(
                    'product_id' => NULL,
                    'image_type'=>'2',
                    'image_url' => $value,                                        
                    'created_at' => date('Y-m-d H:i:s')
                );
            }
        }
        $data["insert_images_array"] = $insert_images_array;
        return $data;
    }

    public function get_warehouse_array($data) 
    {
        $insert_warehouse_array = NULL;
        if (isset($data["warehouse_id"]) && !empty($data["warehouse_id"]) && !empty($data["quantity"])) 
        {
            $insert_warehouse_array[] = array(
                "warehouse_id" => $data["warehouse_id"],
                "product_id" => NULL,
                "availible_quantity" => $data["quantity"],
                "reserved_quantity" => 0,
                "total_quantity" => $data["quantity"],
                "inserted_by" => 0,
                "inserted_date" => date('Y-m-d H:i:s')
            );
        }
        $data["insert_warehouse_array"] = $insert_warehouse_array;
        return $data;
    }

 	public function is_time_out() {
        if ((time() - $this->TIME >= $this->EXECUTION_TIME)) {
            return true;
        }
    }

    function insert_update_product($update_fields, $store_type) 
    {
        $product_id = NULL;
        $extra_update_data = array();
		$product_exists = Products::select('id','bulk_selling_quantity as quantity')->where('sku',$update_fields['sku'])->where('is_deleted','0')->get();        
		$product_exists_arr=$product_exists->toArray();

        $update_marketplace_column = 'sku';
        $update_marketplace_value = $update_fields["sku"];
        $store_id = !empty($update_fields['store_id']) ? $update_fields['store_id'] : '';
        $mp_selling_status = !empty($update_fields['mp_selling_status']) ? $update_fields['mp_selling_status'] : '';
		
        unset($update_fields["store_id"]);
        unset($update_fields["mp_selling_status"]);               

        if (isset($update_fields["marketplace_id"])) 
        {
            $update_marketplace_column = 'id';
            $update_marketplace_value = $update_fields["marketplace_id"];
            unset($update_fields["marketplace_id"]);
        }
        
        if (count($product_exists_arr) > 0 && isset($product_exists[0]->id) && $product_exists[0]->id != '') 
        {
           	$product_id = $product_exists[0]->id;
           	//$update_data[]=array('id'=>$product_id,
           	//'is_quantity_updated'=>'1'
       		//);
           //$products_mod= new Products;
           //Batch::update($products_mod, $update_data, 'id');	
            
        } 
        elseif (empty($mp_selling_status) || in_array($mp_selling_status, array('Active'))) 
        {
            $update_fields          = $this->set_variation($update_fields);            
            $update_fields          = $this->get_images_array($update_fields);
            //$update_fields          = $this->get_warehouse_array($update_fields);            
            //$update_fields          = $this->set_brand($update_fields);            
            //$update_fields          = $this->set_manufacturer($update_fields);           

            
            $insert_images_array    = $update_fields["insert_images_array"];
            //$insert_warehouse_array = $update_fields["insert_warehouse_array"];                   
            unset($update_fields["variation_theme"]);
            unset($update_fields["variation_theme_value"]);
            unset($update_fields["images_array"]);
            unset($update_fields["images_array"]);
            unset($update_fields["insert_images_array"]);
            unset($update_fields["insert_warehouse_array"]);
            unset($update_fields["warehouse_id"]);
            $update_fields['created_at'] = date('Y-d-d H:i:s');
            // $update_fields['created_by'] = '0';
            // $update_fields['modified_by'] = '0';
            //$update_fields['is_quantity_updated'] = '1';    		
            
            $product_id = Products::create($update_fields)->id;	  

            if (!empty($insert_images_array)) 
            {
                $insert_images_array = array_map(function($value) use($product_id) 
                {
                    $value["product_id"] = $product_id;
                    return $value;
                }, $insert_images_array);

                ProductImage::insert($insert_images_array);
            }
            
            // if(!empty($insert_warehouse_array))
            // {
            //     $insert_warehouse_array = array_map(function($value) use($product_id){ $value["product_id"] = $product_id; return $value; },$insert_warehouse_array);
            //     $this->product_merge_model->insert_warehouse_array($insert_warehouse_array);
            // }

            // Insert Product log
            // if(!empty($store_id))
            // {
            //     $store_deails = $this->common_model->select_single_specific_field_by_key('store_master','store_name','id',$store_id);
            //     $store_name_str = "(".$store_deails['store_name'].")"; 
            // }
            // else
            // {
            //     $store_name_str = "";
            // }    

            // $insert_qty_log = array(
            //     'product_id' => $product_id,
            //     'operation_type' => 'add',
            //     'new_availible_quantity' => $update_fields['quantity'],
            //     'old_availible_quantity' => '0',
            //     'new_reserved_quantity' => NULL,
            //     'old_reserved_quantity' => NULL,
            //     'new_total_quantity' => $update_fields['total_quantity'],
            //     'old_total_quantity' => '0',
            //     'warehouse_id' => NULL,
            //     'warehouse_location' => NULL,
            //     'module' => 'inventory',
            //     'module_id' => NULL,
            //     'inventory_logs' => $update_fields['quantity'].' QTY set for sku '.$update_fields['sku'].' because it is added via '.$store_type.' '.$store_name_str.' reverse sync.',
            //     'all_warehouse_data' => NULL,
            //     'quantity_change_reason' => NULL,
            //     'inserted_by' => NULL,
            //     'inserted_date' => get_inserted_date_time()
            // ); 

            // if (!empty($insert_qty_log)) 
            // {
            //     $this->common_model->insert_all('inventory_quantity_logs',$insert_qty_log);
            // }
        }        

        MagentoProduct::where($update_marketplace_column,$update_marketplace_value)->update(array('product_id'=>$product_id));

        /////////////////////
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
}