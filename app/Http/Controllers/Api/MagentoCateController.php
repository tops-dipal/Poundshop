<?php

namespace App\Http\Controllers\Api;
use App\MagentoCategories;
use App\ProductFetch;
use App\Cron;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Batch;
use App\Library\Magento;

class MagentoCateController extends Controller
{

	public $magento;

    public function index(Request $request)
    {
		try
		{
			$store_id='1';//default for magento
			if(isset($request->store_id))
			{
				$store_id=$request->store_id;
			}

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
	        	$this->CRON_TITLE = 'MAGENTO_CATEGORY';
	        	$this->CRON_NAME = $this->CRON_NAME . '_' . $this->STORE_ID;								
				//cron start code
				$cron_id=$this->cron_start_end_update('',$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);

				//get and dump category in database
				$operation_perform=$this->get_categories();

				//update cron data
				$this->cron_start_end_update($cron_id,$this->CRON_TITLE, $this->CRON_NAME, $this->STORE_ID);	

				//code for magento				
				if(!empty($operation_perform))
				{
					return $this->sendResponse('Categories has been stored successfully', 200);
				}
				else
				{
					return $this->sendError('Categories has not been stored successfully, please try again', 422);
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

    public function get_categories()
    {
    	//new call for complete category list
        $result = $this->magento->send_request("V1/categories/list?searchCriteria[sortOrders][0][field]=id&searchCriteria[sortOrders][0][direction]=desc");        
        $categories_data = json_decode($result,true);
        $operation_perform=0;
        
        //old categories list
		$category_model=new MagentoCategories;
		$old_categories=$category_model->select('id','category_id')->get();
		$old_categories_array=array();
		$old_ids=array();
		if(!empty($old_categories))
		{
			foreach($old_categories as $row)
			{
				$old_categories_array[]=$row->category_id;
				$old_ids[$row->category_id]=$row->id;
			}
		}

		//dump data in database
		$insert_categoryid_array=array();
		$update_categoryid_array=array();
		$delete_categoryid_array=array();
		$update_category_data=array();
		$insert_category_data=array();

		$category_data=array();
		if(isset($categories_data) && !empty($categories_data))
		{					
			$categories_data=$categories_data['items'];			
			foreach($categories_data as $row)
			{	
				$is_active=1;
				if(isset($row['is_active']) && empty($row['is_active']))
				{
					$is_active=0;
				}

				if(!empty($old_categories_array) && in_array($row['id'],$old_categories_array))
				{
					$update_categoryid_array[]=$row['id'];
					$update_category_data[]=array(
						'id'=>$old_ids[$row['id']],
						'name'=>$row['name'],
						'category_id'=>$row['id'],
						'parent_id'=>!empty($row['parent_id'])?$row['parent_id']:'0',
						'is_active'=>$is_active,
						'position'=>$row['position'],
						'level'=>$row['level'],
						'path'=>isset($row['path'])?$row['path']:'',
						'store_id'=>1,
						'created_at'=>$row['created_at'],
						'updated_at'=>$row['updated_at'],
					);
				}
				else
				{
					$insert_category_data[]=array(
						'name'=>$row['name'],
						'category_id'=>$row['id'],
						'parent_id'=>!empty($row['parent_id'])?$row['parent_id']:'0',
						'is_active'=>$is_active,
						'position'=>$row['position'],
						'level'=>$row['level'],
						'path'=>isset($row['path'])?$row['path']:'',
						'store_id'=>1,
						'created_at'=>$row['created_at'],
						'updated_at'=>$row['updated_at'],
					);
				}
				unset($is_active);
			}
		}			

		$delete_categoryid_array=array_diff($old_categories_array, $update_categoryid_array);		

		if(!empty($update_category_data))
		{
			$data=Batch::update($category_model, $update_category_data, 'id');	
			$operation_perform=1;
		}

		if(!empty($insert_category_data))
		{
			$data=$category_model->insert($insert_category_data); 
			$operation_perform=1;
		}

		if(!empty($delete_categoryid_array))
		{
			$category_model->whereIn('id',$delete_categoryid_array)->delete();
			$operation_perform=1;
		}     

		//update structure
		$all_categories=$category_model->select('id','category_id','name','path')->get();
		$category_wise_data=array();
		$final_update_data=array();
		if(!empty($all_categories))
		{
			foreach($all_categories as $row)
			{
				$category_wise_data[$row['category_id']]=$row['name'];
				$structure='';
				if(!empty($row['path']))
				{
					$patch=explode('/',$row['path']);
					if(!empty($patch))
					{
						foreach($patch as $row1)
						{
							if(!empty($structure))
							{
								$structure.=' >> '.$category_wise_data[$row1];
							}	
							else
							{
								$structure=$category_wise_data[$row1];
							}
						}
					}
				}
				$final_update_data[]=array(
					'id'=>$row['id'],					
					'structure'=>$structure,				
				);
			}
		}

		if(!empty($final_update_data))
		{
			$data=Batch::update($category_model, $final_update_data, 'id');				
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