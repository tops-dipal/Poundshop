<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ProductFetch;
use App\Cron;
use Batch;
use App\Library\Magento;
use App\MagentoProduct;
use App\MagentoPriceLog;

class MagentoPriceUpdController extends Controller
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
	        	$this->CRON_TITLE = 'MAGENTO_PRODUCT_PRICE_UPDATE';
	        	$this->CRON_NAME = $this->CRON_NAME . '_' . $this->STORE_ID;								
				
                //cron start code
				$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);				
				//get and dump category in database
				$operation_perform=$this->update_price();

				//update cron data
				$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);	

				//code for magento				
				if(!empty($operation_perform))
				{
					return $this->sendResponse('Products Price has been updated successfully', 200);
				}
				else
				{
					return $this->sendError('Products Price has not been updated successfully, please try again', 422);
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

    function update_price()
    {
    	$operation_perform=0;
    	$this->magento_pro= new MagentoProduct;
    	$product_list=$this->magento_pro->get_price_product_list($this->STORE_ID);   
        if(!empty($product_list))
        {
            $update_array = NULL;
            $update_into_db = NULL;  
            $update_into_mp=NULL;          
            foreach ($product_list as $key => $value)
            {
                $update_array[$value["sku"]] = array(
                    "sku"=> $value["sku"],
                    "price"=> $value["selling_price"],
                    "id"=> $value["log_id"],
                    "magento_id"=>$value["magento_id"]
                );
            }           
            

            if(!empty($update_array))
            {                
                foreach ($update_array as $value) 
                {
                	$sku = $value['sku'];                    
                    $postData = Array('product' => Array("price" => $value['price']));                    
                    if (!isset($_SESSION["magento_token"])) 
                    {
                        $this->magento->get_token();
                    }
                    $this->magento->TOKEN = $_SESSION["magento_token"];                     
                    $result = $this->magento->send_request("V1/products/$sku",$postData,'PUT');                    
                    $resultData = json_decode($result);
                                        
                    if($resultData->sku==$sku) 
                    {
                        $update_into_db[] = array(
                            "id"                 => $value['id'],
                            "is_selling_price_posted" => "1",
                            "modified_date"      => date('Y-m-d H:i:s')
                        );

                        $update_into_mp[]=array(
                            "id"=>$value['magento_id'],
                            "selling_price"=>$value['price']
                        );

                    } 
                    else 
                    {
                        echo "<pre>";
                        echo print_r(json_decode($resultData));
                    }
                }
                
                if(!empty($update_into_db)) 
                {
                	$magentopricelog= new MagentoPriceLog;
                    $data=Batch::update($magentopricelog, $update_into_db, 'id');	
                    $operation_perform=1;
                }

                if(!empty($update_into_mp)) 
                {
                    $magentoprod= new MagentoProduct;
                    Batch::update($magentoprod, $update_into_mp, 'id');
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
