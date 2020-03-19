<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductFetch;
use App\Cron;
use Batch;
use App\Library\Magento;
use App\MagentoProduct;


class MagentoEnabledUpdController extends Controller
{
    public $magento;

    public $magento_pro;

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
	        	$this->CRON_TITLE = 'MAGENTO_PRODUCT_ENABLED_UPDATE';
	        	$this->CRON_NAME = $this->CRON_NAME . '_' . $this->STORE_ID;								
				
                //cron start code
				$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);				
				//get and dump category in database
				$operation_perform=$this->update_product_enabled();

				//update cron data
				$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);	

				//code for magento				
				if(!empty($operation_perform) && $operation_perform==1)
				{
					return $this->sendResponse('Products status has been updated successfully', 200);
				}
				else if(!empty($operation_perform) && $operation_perform==2)
				{
					return $this->sendResponse('All product status already updated', 200);
				}
				else
				{
					return $this->sendError('Products status has not been updated successfully, please try again', 422);
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

    function update_product_enabled()
    {
    	$operation_perform=0;
    	$this->magento_pro= new MagentoProduct;
    	$product_list= MagentoProduct::select('id','is_enabled','sku')->where('is_enabled_updated','1')->where('is_deleted_product','0')->get();
    	$product_list_arr=$product_list->toArray();
        if(!empty($product_list) && !empty($product_list_arr))
        {
            $update_array = NULL;
            $update_db = NULL;            
            foreach ($product_list as $key => $value)
            {
                $update_array[$value["sku"]] = array(
                    "sku"=> $value["sku"],
                    "status"=> $value["is_enabled"],                    
                    "id"=> $value["id"]
                );
            }           
            

            if(!empty($update_array))
            {                
                foreach ($update_array as $value) 
                {	
                	$sku = $value['sku'];                    
                	
                	// if($value['status']==1)
                	// {
                	// 	$value['status']=4;
                	// }
                	// else if($value['status']==0)
                	// {
                	// 	$value['status']=1;
                	// }

                    $postData = Array('product' => Array("status" => $value['status']));
                    
                    if (!isset($_SESSION["magento_token"])) 
                    {
                        $this->magento->get_token();
                    }
                    $this->magento->TOKEN = $_SESSION["magento_token"];                     
                    $result = $this->magento->send_request("V1/products/$sku",$postData,'PUT');
                    $resultData = json_decode($result);                    
                    if($resultData->sku==$sku) 
                    {
                        $update_db[] = array(
                            "id"                 => $value['id'],
                            "is_enabled_updated" => "0",
                            "modified_date"      => date('Y-m-d H:i:s')
                        );
                    } 
                    else 
                    {
                        echo "<pre>";
                        echo print_r(json_decode($resultData));
                    }
                }
                
                if(!empty($update_db)) 
                {
                	$magentoprod= new MagentoProduct;
                    $data=Batch::update($magentoprod, $update_db, 'id');	
                    $operation_perform=1;
                }
            }
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
