<?php

namespace App\Http\Controllers\Api;
use App\References;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Common\CreateRequest;
use Illuminate\Support\Facades\View;
use Batch;
class ReferenceController extends Controller
{
    function __construct()
    {
        CreateRequest::$roles_array = [];
    }

    /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(CreateRequest $request)
  {    
    try
    {  
    	// dd($request->toArray());    
    	$reference_model=new References;    
    	
    	$id_array=$request->id;
    	$supp_name_array=$request->supp_name;
    	$cont_per_array=$request->cont_per;
    	$cont_numb_array=$request->cont_numb;
    	$cont_email_array=$request->cont_email;
    	//print_r($id_array);exit;
    	$insert_array=array();  
    	$update_array=array();  
		if(!empty($supp_name_array))
		{
			$i=0;
			foreach($supp_name_array as $row)
			{
				if(count(array_filter($id_array)) == 0)
				{
					$insert_array[] = array(
						'supplier_name'=>$supp_name_array[$i],
						'contact_person'=>$cont_per_array[$i],
						'contact_no'=>$cont_numb_array[$i],
						'contact_email'=>$cont_email_array[$i],								
						'created_by'=>$request->user->id
                    );
                    $i++;
					
				}
				else
				{
					$update_array[] = array(
						'id'=>$id_array[$i],
						'supplier_name'=>$supp_name_array[$i],
						'contact_person'=>$cont_per_array[$i],
						'contact_no'=>$cont_numb_array[$i],
						'contact_email'=>$cont_email_array[$i],						
						'modified_by'=>$request->user->id
                    );
                    $i++;
				}
			}
		}		
		
		if(count(array_filter($id_array)) == 0)
		{
			$data=$reference_model->insert($insert_array); 
        }
        else
        {
        	$data=Batch::update($reference_model, $update_array, 'id');	
        }
        if(!empty($data) || $data==0)
        {
          	return $this->sendResponse('References has been saved successfully', 200);
        }
        else
		{
      		return $this->sendError('References did not saved, please try again', 422);
    	}    	
      	
    } 
    catch (Exception $ex) 
    {
      return $this->sendError($ex->getMessage(), 400);
    }
  }
}
