<?php
namespace App\Http\Controllers\Api;
use App\MagentoCategories;
use App\ProductFetch;
use App\Cron;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Batch;
use App\Library\Magento;
use App\MagentoProduct;
use App\Products;

class MagentoProController extends Controller
{
	public $magento;

	public $page_size = 500;

    public $magento_pro;

    public $store_id;

    public $existing_product_list;

    public $product_con;

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
			$this->magento->USER_NAME=isset($store_data[0]['magento_username'])?$store_data[0]['magento_username']:'';
			$this->magento->PASSWORD=isset($store_data[0]['magento_password'])?$store_data[0]['magento_password']:'';
			$this->magento->ENDPOINT  = isset($store_data[0]['magento_api_url'])?$store_data[0]['magento_api_url']:'';
			$this->magento->magento_web_url    = isset($store_data[0]['magento_web_url'])?$store_data[0]['magento_web_url']:'';
			
            if($this->magento->USER_NAME!='' && $this->magento->PASSWORD!='' && $this->magento->ENDPOINT!='') 
			{
				$this->STORE_ID=$store_id;
				$this->CRON_NAME = 'CRON_' . time();   // CRON NAME
	        	$this->CRON_TITLE = 'MAGENTO_PRODUCT_FETCH';
	        	$this->CRON_NAME = $this->CRON_NAME . '_' . $this->STORE_ID;								
				
                //cron start code
				$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);				
				//get and dump category in database
				$operation_perform=$this->get_product_repository();

				//update cron data
				$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);	

				//code for magento				
				if(!empty($operation_perform))
				{
					return $this->sendResponse('Products has been stored successfully', 200);
				}
				else
				{
					return $this->sendError('Products has not been stored successfully, please try again', 422);
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

    public function get_product_repository()
    {
		//$queryStringArray[] = 'searchCriteria[filterGroups][0][filters][0][field]=type_id';
        //$queryStringArray[] = 'searchCriteria[filterGroups][0][filters][0][value]=configurable,simple';
        //$queryStringArray[] = 'searchCriteria[filterGroups][0][filters][0][condition_type]=in';
        $queryStringArray[] = 'searchCriteria[pageSize]='.$this->page_size;
        $queryStringArray[] = 'searchCriteria[currentPage]=1';
        $queryString = implode('&', $queryStringArray);        
        $repository_result = $this->magento->send_request("V1/products?$queryString");        
        $ProductBundleArray = Array();
        $operation_perform=0;
        
        $this->product_con=new Products;

        if($repository_result) 
        {
            $product_result = json_decode($repository_result, TRUE);            
            if(isset($product_result['total_count'])) 
            {
                $totalRecords = $product_result['total_count'];                
                $totalPages = ceil($totalRecords/$this->page_size);
                $ProductBundleArray[] = $product_result['items'];
                if($totalPages>1) 
                {
                    for($i=2;$i<=$totalPages;$i++) 
                    {
                        $queryStringArray = Array();
                        $queryString = '';
                        $repository_result = Array();
                        $product_result = Array();
                        //$queryStringArray[] = 'searchCriteria[filterGroups][0][filters][0][field]=type_id';
                        //$queryStringArray[] = 'searchCriteria[filterGroups][0][filters][0][value]=configurable,simple';
                        //$queryStringArray[] = 'searchCriteria[filterGroups][0][filters][0][condition_type]=in';
                        $queryStringArray[] = 'searchCriteria[pageSize]='.$this->page_size;
                        $queryStringArray[] = 'searchCriteria[currentPage]='.$i;
                        $queryString = implode('&', $queryStringArray);
                        $repository_result = $this->magento->send_request("V1/products?$queryString");
                        if($repository_result) 
                        {
                            $product_result = json_decode($repository_result, TRUE);
                            if(isset($product_result['items'])) 
                            {
                                $ProductBundleArray[] = $product_result['items'];
                            }
                        }
                    }
                }                

                if(!empty($ProductBundleArray)) 
                {                
                    $this->magento_pro= new MagentoProduct;
                    $this->existing_product_list=$this->magento_pro->select('id','sku','magento_product_id','product_id','is_deleted_product')->where('store_id',$this->store_id)->get();
                    $this->existing_product_list = $this->helper_array_column($this->existing_product_list->toArray(), "sku");                    

                    $this->updateQty = Array();
                    $this->updateWarehouseZeroQty = Array();
                    $this->updateProductQty = Array();
                    $this->insert_qty_log = Array();
                    $deleted_array = array();
                    $deleted_product_array=array();

                    foreach($ProductBundleArray as $ProductsArray) 
                    {
                        if(!empty($ProductsArray)) 
                        {
                            $operation_perform=$this->insert_update_product_list($ProductsArray);
                        }
                    }                                       
                    
                    foreach ($this->existing_product_list as $key=>$val) 
                    {
                        $ended_result = $this->magento->send_request("V1/products/".urlencode($key));
                        $ended_product_detail = json_decode($ended_result);                        
                        if(empty($ended_product_detail->name) || empty($ended_product_detail))
                        {
                            $deleted_array[] = array(
                                'sku' => $key,                                
                                'is_deleted_product' => '2'                            
                            ); 

                            if(!empty($val['product_id']))
                            {
                                $deleted_product_array[] = array(
                                    'id' => $val['product_id'],
                                    'is_listed_on_magento' => '2'                            
                                );
                            }
                        }
                    }
                    
                    if (!empty($deleted_product_array)) 
                    {    
                        $data=Batch::update($this->product_con, $deleted_product_array, 'id');
                    } 

                    if (!empty($deleted_array)) 
                    {    
                        $data=Batch::update($this->magento_pro, $deleted_array, 'sku');  
                        $operation_perform=1;                        
                    }                                                          
                }
            }
        }    

        return $operation_perform;	
    }

    public function insert_update_product_list($ProductsArray) 
    {
        $insert_array = array();
        $update_array = array();        
        //$default_warehouse_id = $this->products_fetch_model->get_default_warehouse_id();
        $operation_perform=0;

        if (!empty($ProductsArray)) 
        {             
            foreach ($ProductsArray as $key => $value) 
            {
                $MagentoProductId = $value['id'];
                $ProductSKU = $value['sku'];
                $ProductStatus = $value['status'];
                $ProductEnabled = ($value['status']=='1') ? '1' : '0';
                $ProductVisibility = $value['visibility'];
                if (isset($this->existing_product_list[$ProductSKU])) 
                {
                    $update_product_array = array(                                        
                        "magento_product_id" => $MagentoProductId,
                        "sku" => $ProductSKU,
                        "status" => $ProductStatus,
                        "visibility" => $ProductVisibility,                                        
                        "magento_modified_date" => $value["updated_at"],
                        "selling_price" => (isset($value["price"]) ? $value["price"] : NULL),
                        "product_weight" => (isset($value["weight"]) ? $value["weight"] : NULL),                                        
                        "is_enabled" => $ProductEnabled,
                        // 'is_deleted_product' => '0',                  
                        "modified_date" => date('Y-m-d H:i:s')
                    );

                    if($this->existing_product_list[$ProductSKU]['is_deleted_product']!='1') 
                    {
                        $update_product_array['is_deleted_product'] = '0';
                    }

                    if(in_array($this->existing_product_list[$ProductSKU]['is_deleted_product'],array('4','2')) && !empty($this->existing_product_list[$ProductSKU]['product_id'])) 
                    {

                        $p_result = $this->magento->send_request("V1/products/".urlencode($ProductSKU));
                        $p_result = json_decode($p_result);
                        $p_stock_item_attributes = $p_result->extension_attributes->stock_item;
                        $p_qty = (int) $p_stock_item_attributes->qty;
                        $update_product_array["quantity"] = $p_qty;                                                
                        
                        //$this->db->where("warehouse_id", $default_warehouse_id);
                        // $this->db->where("product_id", $this->existing_product_list[$ProductSKU]['product_id']);    
                        // $this->db->limit(1);
                        // $this->db->update("warehouse_product_mapping",$updateWarehouseQty);
                    }

                    $update_array[] = $update_product_array;
                    unset($this->existing_product_list[$ProductSKU]);
                } 
                else 
                {
                    $customAttributes = $value['custom_attributes'];
                    $insert_array[] = array(
                        "store_id" => $this->STORE_ID,
                        "magento_product_id" => $MagentoProductId,
                        "sku" => $ProductSKU,
                        "product_type" => (($value["type_id"]=='configurable') ? 'parent' : 'normal'),
                        //"product_type" => (($value["type_id"]=='configurable') ? '3' : '1'),
                        "product_title" => (($value["name"]!='') ? $value["name"] : NULL),
                        "status" => $ProductStatus,
                        "visibility" => $ProductVisibility,
                        "magento_create_date" => $value["created_at"],
                        "magento_modified_date" => $value["updated_at"],
                        "selling_price" => (isset($value["price"]) ? $value["price"] : NULL),
                        "product_weight" => (isset($value["weight"]) ? $value["weight"] : NULL),                                        
                        "is_enabled" => $ProductEnabled,
                        "is_detail_processed" => "0",
                        "is_updated_in_product_master" => "0",
                        "inserted_date" => date('Y-m-d H:i:s')
                    );
                }
            }            

            if (!empty($insert_array) || !empty($update_array)) 
            {
                if (!empty($insert_array)) 
                {
                    $data=$this->magento_pro->insert($insert_array); 
                    $operation_perform=1;
                }
                
                if (!empty($update_array)) 
                {
                    $data=Batch::update($this->magento_pro, $update_array, 'sku');  
                    $operation_perform=1;
                }                
            }
        }

        return $operation_perform;
    }    

    public function helper_array_column($input, $array_index_key = NULL, $array_value = NULL) 
    {
        $result = array();
        if (count($input) > 0) 
        {
            foreach ($input as $key => $value) 
            {
                if (is_array($value)) 
                {
                    @$result[is_null($array_index_key) ? $key : (string) (is_callable($array_index_key) ? $array_index_key($value) : $value[$array_index_key])] = is_null($array_value) ? $value : (is_callable($array_value) ? $array_value($value, $key) : $value[$array_value]);
                } 
                else if (is_object($value)) 
                {
                    $result[is_null($array_index_key) ? $key : (string) $value->$array_index_key] = is_null($array_value) ? $value : $value->$array_value;
                } 
                else 
                {
                    $result[is_null($array_index_key) ? $key : (string) (is_callable($array_index_key) ? $array_index_key($value, $key) : $key)] = is_null($array_value) ? $value : (string) (is_callable($array_value) ? $array_value($value, $key) : $value);
                }
            }
        }

        return $result;
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