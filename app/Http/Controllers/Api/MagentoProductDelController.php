<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductFetch;
use App\Cron;
use Batch;
use App\Library\Magento;
use App\MagentoProduct;
use App\Products;

class MagentoProductDelController extends Controller
{
    public $magento;

    public $magento_pro;

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

			$ids=array();
			if(isset($request->ids))
			{
				$ids=explode(',',$request->ids);
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
	        	$this->CRON_TITLE = 'MAGENTO_PRODUCT_END';
	        	$this->CRON_NAME = $this->CRON_NAME . '_' . $this->STORE_ID;								
				
                //cron start code
				$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);				
				//get and dump category in database
				$operation_perform=$this->product_delete($ids);
				//print_R($operation_perform);exit;
				//update cron data
				$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);	

				//code for magento				
				if(!empty($operation_perform) && $operation_perform==1)
				{
					return $this->sendResponse('Products has been deleted successfully', 200);
				}
				else if(!empty($operation_perform) && $operation_perform==2)
				{
					return $this->sendError('No more product to delete, please try again', 422);	
				}
				else
				{
					return $this->sendError('Products has not been deleted successfully, please try again', 422);
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

    function product_delete($ids=[])
    {
    	$operation_perform=0;
    	
    	$this->magento_pro= new MagentoProduct;
    	
    	$this->product_con= new Products;

    	if(count($ids)==0)
    	{
    		$product_result=$this->magento_pro->select("id","magento_product_id","product_id","sku")->where('is_deleted_product',1)->get();
    	}
    	else
    	{
    		$product_result=$this->magento_pro->select("id","magento_product_id","product_id","sku")->where('is_deleted_product',1)->whereIn('id',$ids)->get();
    	}
    	
		if (!empty($product_result)) 
		{            
            $updateSKU = array();
            $updatePro = array();
            foreach ($product_result as $key => $value) 
            {
                $result = $this->magento->send_request("V1/products/".urlencode($value['sku']), array(), 'DELETE');
                $posting_result = json_decode($result);
                if ($posting_result == '1') 
                {
                    $updateSKU[] = Array('id' => $value['id'],'is_deleted_product' => '2');
                    $updatePro[] = Array('id' => $value['product_id'],'is_listed_on_magento' => '2');
                }
                else 
                {
                    $updateSKU[] = Array('id' => $value['id'],'is_deleted_product' => '3');
                }
            }           
        	
            if(!empty($updatePro)) 
            {
                $data=Batch::update($this->product_con, $updatePro, 'id');	                
            }

            if(!empty($updateSKU)) 
            {
                $data=Batch::update($this->magento_pro, $updateSKU, 'id');	
                $operation_perform=1;
            }            
        }
        else 
        {
            $operation_perform=2;
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
}
