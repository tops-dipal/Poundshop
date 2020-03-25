<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Common\CreateRequest;
use Illuminate\Support\Facades\Storage;
use App\MagentoProductPosting;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\MagentoProduct;

class ListingManagerController extends Controller
{
	function __construct(Request $request)
    {
	 	ini_set('memory_limit','10240M');

		$route = $request->route();
        
        if(!empty($route))
        {   
            $action_array = explode('@',$route->getActionName());
            
            $function_name = !empty($action_array[1]) ? $action_array[1] : ''; 
            
            if(!empty($function_name))
            {    
            	if($function_name == 'store')
                {    
                    CreateRequest::$roles_array = [
                                        'sku' => 'required',
                                        'product_title' => 'required',
                                        'product_master_id' => 'required',
                                        'store_id' => 'required',
                                        'date_to_go_live' => 'required',
                                        'selling_price' => 'required',
                                        'quantity' => 'required',
                                        'country_of_origin' => 'required',
                                        // 'brand' => 'required',
                                        'category_ids.0' => 'required',
                                        'category_ids.*' => 'required',
                                        // 'magento_product_length' => 'required',
                                        // 'magento_product_height' => 'required',
                                        // 'magento_product_width' => 'required',
                                        // 'magento_product_weight' => 'required',
                                        'product_description' => 'required',
                                        'product_type' => 'required',
                                      ];
                }

                if($function_name == 'update')
                {    
                    CreateRequest::$roles_array = [
                                        'sku' => 'required',
                                        'product_title' => 'required',
                                        'magento_item_id' => 'required',
                                        'store_id' => 'required',
                                        'selling_price' => 'required',
                                        'quantity' => 'required',
                                       // 'brand' => 'required',
                                        'category_ids.0' => 'required',
                                        'category_ids.*' => 'required',
                                        'magento_product_length' => 'required',
                                        'magento_product_height' => 'required',
                                        'magento_product_width' => 'required',
                                        'magento_product_weight' => 'required',
                                        'product_description' => 'required',
                                        'product_type' => 'required',
                                      ];
                }

                if($function_name == 'setDateToGoLive')
                {    
                    CreateRequest::$roles_array = [
                                        'ids.*' => 'required',
                                        'store_id' => 'required',
                                        'date_to_go_live' => 'required',
                                      ];
                }
            }
        }
    }
            	
   
    public function index(Request $request)
    {
    	try
        {
        
            $columns=[
                0 => 'id',
                1 => 'title',
                2=>'sku'
            ];
            $adv_search_array=array();
            if(!empty($request->advanceSearch))
            {
              parse_str($request->advanceSearch, $adv_search_array);

            } 
            $params  = array(
                 'order_column'    => $columns[$request->order[0]['column']],
                 'order_dir'       => $request->order[0]['dir'],
                 'search'          => $request->search['value'],
                 'advance_search'  => $adv_search_array,
            );
            $codes=MagentoProductPosting::getToBeListedRecords($request->length, $params);

            $data = [];
            
            if (!empty($codes)) {
            	$data = $codes->getCollection()->transform(function ($result) use ($data,$adv_search_array) {
                    $tempArray   = array();
                    $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                    
                    if(!empty($result->magento_main_image_url))
                    {
                        $image = $result->magento_main_image_url;
                    }
                    else
                    {
                        $image = $result->main_image_marketplace;
                    }

                    $tempArray[] = View::make('listing-manager.listing-image',['image'=> $image])->render();
                     $edit_url=route('magento.add',['id'=>$result->id,'store_id'=>$adv_search_array['store_id']]);
                  	$tempArray[] = View::make('components.list-title',['title'=>ucwords($result->title),'edit_url'=>$edit_url,'btn_title'=>trans('messages.inventory.product_edit')])->render();
                   
                  	$tempArray[] = $result->sku;
                  	// $tempArray[] = 'New';
                  	$tempArray[] = 0;
                  	$tempArray[] = trans('messages.common.pound_sign').$result->single_selling_price;
                     $tempArray[] =(!empty($result->date_to_go_live)) ? system_date($result->date_to_go_live) : '-';
                    $viewActionButton = View::make('listing-manager.action-buttons', ['object' => $result,'listing'=>'to-be-listed','store_id'=>$adv_search_array['store_id']]);
                    $tempArray[]      = $viewActionButton->render();
                    return $tempArray;
                });
            }
                
            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $codes->total(), // Total number of records
                "recordsFiltered" => $codes->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        } 
        catch (Exception $ex) {
            return 'error';
        }
    }

    public function store(CreateRequest $request)
    {
		try{
			
	    	$db_array['sku'] = $request->sku;
	    	$db_array['magento_product_id_type'] = $request->magento_product_id_type;
	    	$db_array['magento_product_id'] = $request->magento_product_id;
	    	$db_array['product_type'] = $request->product_type;
	    	$db_array['product_master_id'] = $request->product_master_id;
	    	$db_array['store_id'] = $request->store_id;
	    	$db_array['product_title'] = $request->product_title;
	    	$date_to_go_live = NULL;
            if(!empty($request->date_to_go_live))
            {
                $date_to_go_live = $request->date_to_go_live;

                $date_to_go_live = str_replace('/', '-', $date_to_go_live);

                $date_to_go_live = date('Y-m-d H:i:s', strtotime($date_to_go_live));
            }    
            $db_array['date_to_go_live'] = $date_to_go_live;
	    	$db_array['selling_price'] = $request->selling_price;
	    	$db_array['bulk_selling_price'] = $request->bulk_selling_price;
	    	$db_array['quantity'] = $request->quantity;
	    	$db_array['country_of_origin'] = $request->country_of_origin;
	    	$db_array['brand'] = $request->brand;
	    	$db_array['category_ids'] = !empty($request->category_ids) ? implode(',', $request->category_ids) : '';
	    	$db_array['magento_product_length'] = $request->magento_product_length;
	    	$db_array['magento_product_height'] = $request->magento_product_height;
	    	$db_array['magento_product_width'] = $request->magento_product_width;
	    	$db_array['magento_product_weight'] = $request->magento_product_weight;
	    	$db_array['short_description'] = $request->magento_short_description;
	    	$db_array['product_description'] = $request->product_description;
	    	$db_array['meta_title'] = $request->meta_title;
	    	$db_array['meta_keyword'] = $request->meta_keyword;
	    	$db_array['meta_description'] = $request->meta_description;
	    	$db_array['variation_theme_id'] = !empty($request->variation_theme_id) ? $request->variation_theme_id : NULL;
	    	$db_array['is_posted'] = isset($request->is_posted) ? $request->is_posted : 0;
	    	$db_array['posting_result_status'] = 0;
	    	$db_array['status'] = 1;
            $db_array['main_image_url'] = !empty($request->main_image_url) ? $request->main_image_url : '';

	    	if((strrpos($db_array['main_image_url'], 'https:') === false && strrpos($db_array['main_image_url'], 'http:') === false) && !empty($db_array['main_image_url']))
	    	{
	    		$protocol = $request->secure() ? 'https:' : 'http:';

	    		$db_array['main_image_url'] = $protocol.$db_array['main_image_url'];	
	    	}	

	    	// set protocol
	    	if(!empty($request->file('main_image_url')))
	    	{
	    		$main_image_url	= $this->upload_product_img($request->file('main_image_url'), $db_array['product_master_id']);
				
				$db_array['main_image_url'] = url('storage/uploads/'.$main_image_url);
			}	
	    	
	    	$other_images = array();

	    	// set protocol
	    	if(!empty($request->image_details))
	    	{
	    		foreach($request->image_details as $image_detail)
	    		{
	    			if((strrpos($image_detail, 'https:') === false && strrpos($image_detail, 'http:') === false) && !empty($image_detail))
			    	{
			    		$protocol = $request->secure() ? 'https:' : 'http:';

			    		$image_detail = $protocol.$image_detail;	
			    	}	

	    			$other_images[] = $image_detail;
	    		}	
	    	}	

	    	if(!empty($request->file('images')))
	    	{
	    		foreach($request->file('images') as $upload_other_images)
	    		{	
	    		
	    			$upload_path = $this->upload_product_img($upload_other_images, $db_array['product_master_id']);
					
					$other_images[] = url('storage/uploads/'.$upload_path);
				}	
			}

			$db_array['image_details'] = !empty($other_images) ? serialize($other_images) : '';	

	    	if(!empty($db_array))
	    	{
	    		$posting_details = MagentoProductPosting::select('id', 'main_image_url', 'image_details')->where(function($q) use ($db_array){
															    		$q->where('product_master_id', $db_array['product_master_id']);
															    		$q->where('store_id', $db_array['store_id']);
															    		$q->where('is_revised', 0);
															    	})->first();
	    		
	    		
	    		if(!empty($posting_details))
	    		{
	    			if($posting_details->main_image_url != $db_array['main_image_url'])
	    			{
	    				$this->dropImage($posting_details->main_image_url);
	    			}	

	    			if(!empty($posting_details->image_details))
	    			{
	    				$existing_imgs = unserialize($posting_details->image_details);

	    				foreach($existing_imgs as $existing_img)
	    				{
	    					if(!in_array($existing_img, $other_images))
	    					{
	    						$this->dropImage($existing_img);
	    					}
	    				}	
	    			}	

	    			MagentoProductPosting::where('id', $posting_details->id)->update($db_array);
	    			
	    			$parent_id = $posting_details->id;
	    		}
	    		else
	    		{
	    			// MagentoProductPosting::insert($db_array);
	    			$parent_id = MagentoProductPosting::create($db_array)->id;
	    		}	

	    		if(!empty($parent_id) && $db_array['product_type'] == 'parent')
	    		{
	    			// to be continued...
	    		}	
	    		
	    		return $this->sendResponse('Product successfully added for posting', 200);
	    	}
	    	else
	    	{
	    		return $this->sendValidation(array('No data received for posting'), 422);  
	    	}

	    } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }		
    }

	public function upload_product_img($img, $id)
	{

	    $path = "";
	    
	    // images
	    if(!empty($img))
	    {    
	        $extension = strtolower($img->getClientOriginalExtension());

	        $folder="magento-images/".$id;
	        
	        if (!Storage::exists($folder)) 
	        {
	            Storage::makeDirectory($folder, 0777, true);
	        }
	        
	        $uploadedFile=$img;
	        
	        if($extension=="mp4")
	        {
	            $filename = $uploadedFile->getClientOriginalName();

	            $name = md5($filename . time()).'.'.$uploadedFile->getClientOriginalExtension();
	             
	            $path = Storage::disk('local')->putFileAs($folder, $uploadedFile, $name);
	        }
	        else
	        {
	            $path = Storage::putFile(($folder), $uploadedFile);
	        }
	    }
	   
	    return $path;   
	} 

    public function alreadyListedRecords(Request $request)
    {
        try
        {
            $columns=[
                0 => 'id',
                1 => 'title',
                2=>'sku'
            ];
            $adv_search_array=array();
            if(!empty($request->advanceSearch))
            {
              parse_str($request->advanceSearch, $adv_search_array);
            } 

            $params  = array(
                'order_column'    => $columns[$request->order[0]['column']],
                'order_dir'       => $request->order[0]['dir'],
                'search'          => $request->search['value'],
                'advance_search'  => $adv_search_array,
            );
            $codes=MagentoProductPosting::getAlreadyListingRecords($request->length, $params);
           
            $data = [];
            if (!empty($codes)) {
            	$data = $codes->getCollection()->transform(function ($result) use ($data,$adv_search_array) {
                    $tempArray   = array();
                    $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                    $tempArray[] = View::make('listing-manager.listing-image',['image'=>$result->main_image_url])->render();
                    $tempArray[] = View::make('components.list-title',['title'=>ucwords($result->product_title),'edit_url'=>route('magento.edit',$result->id),'btn_title'=>trans('messages.inventory.product_edit')])->render();
                  	$tempArray[] = $result->sku;
                  	// $tempArray[] = 'New';
                    $price=0;
                    $qty=0;
                    if(count($result->magentoPriceLog)>0)
                    {
                        $price=$result->magentoPriceLog[0]->selling_price;
                    }
                    else
                    {
                        $price=$result->selling_price;
                    }
                    if(count($result->magentoQtyLog)>0)
                    {
                        $qty=$result->magentoQtyLog[0]->quantity;
                    }
                    else
                    {
                        $qty=$result->quantity;
                    }
                    
                  	$tempArray[] = View::make('listing-manager.list-qty',['qty'=>$qty,'id'=>$result->id])->render();

                  	$tempArray[] = View::make('listing-manager.list-price',['price'=>$price,'id'=>$result->id])->render();
                    $tempArray[] =(!empty($result->magento_create_date)) ? system_date($result->magento_create_date) : '-';
                    $viewActionButton = View::make('listing-manager.action-buttons', ['object' => $result,'listing'=>'already-listed','store_id'=>$adv_search_array['store_id']]);
                    $tempArray[]      = $viewActionButton->render();
                    return $tempArray;
                });
            }
                
            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $codes->total(), // Total number of records
                "recordsFiltered" => $codes->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        } 
        catch (Exception $ex) {
            return 'error';
        }
    }

    public function inProgressRecords(Request $request)
    {
    	try
        {
        
           $columns=[
                0 => 'id',
                1 => 'title',
                2=>'sku'
            ];
            $adv_search_array=array();
            if(!empty($request->advanceSearch))
            {
              parse_str($request->advanceSearch, $adv_search_array);
            } 
            $params  = array(
                'order_column'    => $columns[$request->order[0]['column']],
                'order_dir'       => $request->order[0]['dir'],
                'search'          => $request->search['value'],
                'advance_search'  => $adv_search_array,
            );
            $codes=MagentoProductPosting::getInprogressRecords($request->length, $params);
              
            $data = [];
            
            if (!empty($codes)) {
            	
                $data = $codes->getCollection()->transform(function ($result) use ($data,$adv_search_array) {
                    $tempArray   = array();
                    $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                    $tempArray[] = View::make('listing-manager.listing-image',['image'=>$result->main_image_url])->render();
                    $edit_url='';
                    if($result->posting_result_status==0 || $result->posting_result_status==3)
                    {
                        if($result->is_revised==1)
                        {
                            $edit_url=route('magento.edit',$result->magento_id);
                        }
                        else
                        {
                            $edit_url=route('magento.add',['id'=>$result->product_master_id,'store_id'=>$adv_search_array['store_id']]);
                        }
                    }
                    //$edit_url=route('magento.add',['id'=>$result->id,'store_id'=>$adv_search_array['store_id']]);
                    $tempArray[] = View::make('components.list-title',['title'=>ucwords($result->product_title),'edit_url'=>$edit_url,'btn_title'=>trans('messages.inventory.product_edit')])->render();
                  	
                  	$tempArray[] = $result->sku;
                  	// $tempArray[] = 'New';
                  	$tempArray[] = $result->quantity;
                  	$tempArray[] = trans('messages.common.pound_sign').$result->selling_price;
                     $tempArray[] =(!empty($result->date_to_go_live)) ? system_date($result->date_to_go_live) : '-';
                    $viewActionButton = View::make('listing-manager.action-buttons', ['object' => $result,'listing'=>'inprogress','store_id'=>$adv_search_array['store_id']]);
                    $tempArray[]      = $viewActionButton->render();
                    return $tempArray;
                });
            }
                
            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $codes->total(), // Total number of records
                "recordsFiltered" => $codes->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        } 
        catch (Exception $ex) {
            return 'error';
        }
    }	

    public function dropImage($url)
    {
    	if(!empty($url))
    	{
    		if(strpos($url, 'magento-images') !== false)
    		{
    			$path = str_replace(url('storage/uploads'), '', $url);
    			
    			if(!empty($path))
    			{
    				Storage::delete($path);
    			}	
    		}	
    	}	
    }

    public function delistMagentoProduct(Request $request)
    {
        $ids = $request->ids;
      
         if(\App\MagentoProduct::whereIn('id',explode(",",$ids))->where('store_id',$request->store_id)->update(['is_deleted_product'=>1])){
            return $this->sendResponse('Products has been delisted successfully', 200);
        }else{
             return $this->sendError('Products did not delisted, please try again', 422);
        }
    }


    public function update(CreateRequest $request, $id = "")
    {
        try{
            if(!empty($id))
            {    
                $db_array['magento_id'] = $id;
                $db_array['magento_item_id'] = $request->magento_item_id;
                $db_array['sku'] = $request->sku;
                $db_array['magento_product_id_type'] = $request->magento_product_id_type;
                $db_array['magento_product_id'] = $request->magento_product_id;
                $db_array['product_type'] = $request->product_type;
                $db_array['product_master_id'] = $request->product_master_id;
                $db_array['store_id'] = $request->store_id;
                $db_array['product_title'] = $request->product_title;
                $db_array['date_to_go_live'] =  NULL;
                $db_array['selling_price'] = $request->selling_price;
                $db_array['bulk_selling_price'] = $request->bulk_selling_price;
                $db_array['quantity'] = $request->quantity;
                $db_array['country_of_origin'] = $request->country_of_origin;
                $db_array['brand'] = $request->brand;
                $db_array['category_ids'] = !empty($request->category_ids) ? implode(',', $request->category_ids) : '';
                $db_array['magento_product_length'] = $request->magento_product_length;
                $db_array['magento_product_height'] = $request->magento_product_height;
                $db_array['magento_product_width'] = $request->magento_product_width;
                $db_array['magento_product_weight'] = $request->magento_product_weight;
                $db_array['short_description'] = $request->magento_short_description;
                $db_array['product_description'] = $request->product_description;
                $db_array['meta_title'] = $request->meta_title;
                $db_array['meta_keyword'] = $request->meta_keyword;
                $db_array['meta_description'] = $request->meta_description;
                $db_array['variation_theme_id'] = !empty($request->variation_theme_id) ? $request->variation_theme_id : NULL;
                $db_array['is_posted'] = 1;
                $db_array['posting_result_status'] = 0;
                $db_array['status'] = 1;
                $db_array['is_revised'] = 1;

                $db_array['main_image_url'] = !empty($request->main_image_url) ? $request->main_image_url : '';

                if((strrpos($db_array['main_image_url'], 'https:') === false && strrpos($db_array['main_image_url'], 'http:') === false) && !empty($db_array['main_image_url']))
                {
                    $protocol = $request->secure() ? 'https:' : 'http:';

                    $db_array['main_image_url'] = $protocol.$db_array['main_image_url'];    
                }   

                // set protocol
                if(!empty($request->file('main_image_url')))
                {
                    $main_image_url = $this->upload_product_img($request->file('main_image_url'), $db_array['product_master_id']);
                    
                    $db_array['main_image_url'] = url('storage/uploads/'.$main_image_url);
                }   
                
                $other_images = array();

                // set protocol
                if(!empty($request->image_details))
                {
                    foreach($request->image_details as $image_detail)
                    {
                        if((strrpos($image_detail, 'https:') === false && strrpos($image_detail, 'http:') === false) && !empty($image_detail))
                        {
                            $protocol = $request->secure() ? 'https:' : 'http:';

                            $image_detail = $protocol.$image_detail;    
                        }   

                        $other_images[] = $image_detail;
                    }   
                }   

                if(!empty($request->file('images')))
                {
                    foreach($request->file('images') as $upload_other_images)
                    {   
                    
                        $upload_path = $this->upload_product_img($upload_other_images, $db_array['product_master_id']);
                        
                        $other_images[] = url('storage/uploads/'.$upload_path);
                    }   
                }

                $db_array['image_details'] = !empty($other_images) ? serialize($other_images) : ''; 

                if(!empty($db_array))
                {
                    $posting_details = MagentoProductPosting::select('id', 'main_image_url', 'image_details')->where(function($q) use ($db_array){
                                                                            $q->where('magento_id', $db_array['magento_id']);
                                                                            $q->where('is_revised', 1);
                                                                        })->first();
                    
                    
                    if(!empty($posting_details))
                    {
                        if($posting_details->main_image_url != $db_array['main_image_url'])
                        {
                            $this->dropImage($posting_details->main_image_url);
                        }   

                        if(!empty($posting_details->image_details))
                        {
                            $existing_imgs = unserialize($posting_details->image_details);

                            foreach($existing_imgs as $existing_img)
                            {
                                if(!in_array($existing_img, $other_images))
                                {
                                    $this->dropImage($existing_img);
                                }
                            }   
                        }   

                        MagentoProductPosting::where('id', $posting_details->id)->update($db_array);
                        
                        $parent_id = $posting_details->id;
                    }
                    else
                    {
                        // MagentoProductPosting::insert($db_array);
                        $parent_id = MagentoProductPosting::create($db_array)->id;
                    }   

                    if(!empty($parent_id) && $db_array['product_type'] == 'parent')
                    {
                        // to be continued...
                    }   
                    
                    return $this->sendResponse('Product successfully added for revise', 200);
                }
                else
                {
                    return $this->sendValidation(array('No data received for revise'), 422);  
                }
            }    

        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }           

    public function storeMagentoQtyLog(CreateRequest $request)
    {
        try
        {
            
            MagentoProduct::where('id',$request->magento_id)->update(array('is_detail_processed'=>'0'));
            $qty_model=\App\MagentoQtyLog::where('is_quantity_posted',0)->where('magento_id',$request->magento_id)->orderBy('id','desc')->first();
            if(!empty($qty_model))
            {
                $qty_model->magento_id=$request->magento_id;
                $qty_model->quantity=$request->quantity;
                $qty_model->last_modified=Carbon::now();
                $qty_model->modified_by=$request->user->id;
                if($qty_model->save())
                {
                    return $this->sendResponse('Quantity has been added successfully', 200);
                }
                else
                {
                    return $this->sendError('Quantity does not added, please try again', 422);
                }
            }
            else
            {
                $qty_model=new \App\MagentoQtyLog;
                $qty_model->magento_id=$request->magento_id;
                $qty_model->quantity=$request->quantity;
                $qty_model->modified_by=$request->user->id;
                $qty_model->created_by=$request->user->id;
                if($qty_model->save())
                {
                    return $this->sendResponse('Quantity has been added successfully', 200);
                }
                else
                {
                    return $this->sendError('Quantity does not added, please try again', 422);
                }
            }
           
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    
    public function storeMagentoSellPriceLog(CreateRequest $request)
    {
        try{
            
            MagentoProduct::where('id',$request->magento_id)->update(array('is_detail_processed'=>'0'));

            $price_model=\App\MagentoPriceLog::where('is_selling_price_posted',0)->where('magento_id',$request->magento_id)->orderBy('id','desc')->first();
            if(!empty($price_model))
            {
                $price_model->magento_id=$request->magento_id;
                $price_model->selling_price=$request->selling_price;
                $price_model->last_modified=Carbon::now();
                $price_model->modified_by=$request->user->id;
                if($price_model->save())
                {
                    return $this->sendResponse('Price has been added successfully', 200);
                }
                else
                {
                    return $this->sendError('Price does not added, please try again', 422);
                }
            }
            else
            {
                $price_model=new \App\MagentoPriceLog;
                $price_model->magento_id=$request->magento_id;
                $price_model->selling_price=$request->selling_price;
                $price_model->modified_by=$request->user->id;
                $price_model->created_by=$request->user->id;
                if($price_model->save())
                {
                    return $this->sendResponse('Price has been added successfully', 200);
                }
                else
                {
                    return $this->sendError('Price does not added, please try again', 422);
                }
            }
           
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    function makeMagentoProductEnableDisabled(Request $request)
    {
        try{
            $ids=$request->ids;
            $make_enabled_status=$request->make_enabled_status;
            if($make_enabled_status==1)
            {
                if(\App\MagentoProduct::whereIn('id',explode(",",$ids))->where('store_id',$request->store_id)->update(['is_enabled'=>1,'is_enabled_updated'=>1]))
                {
                    return $this->sendResponse(trans('messages.api_responses.mp_enabled_success'), 200);
                }
                else
                {
                    return $this->sendError(trans('messages.api_responses.mp_enabled_error'), 422);
                }
            }
            else
            {
                if(\App\MagentoProduct::whereIn('id',explode(",",$ids))->where('store_id',$request->store_id)->update(['is_enabled'=>0,'is_enabled_updated'=>1]))
                {
                    return $this->sendResponse(trans('messages.api_responses.mp_disabled_success'), 200);
                }
                else
                {
                    return $this->sendError(trans('messages.api_responses.mp_disabled_error'), 422);
                }
            }
            
           
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function addToList(CreateRequest $request)
    {
        try{
        $product_master_ids = explode(",", $request->ids);
        $posting_details = MagentoProductPosting::select('id','product_master_id')->where(function($q) use ($request,$product_master_ids){
                $q->whereIn('product_master_id', $product_master_ids);
                $q->where('store_id', $request->store_id);
                $q->where('is_revised', 0);
                $q->where('is_posted', 0);
        })->get();

        $update_array=array();
        $updateId=array();
        
        foreach ($posting_details as  $posting_detail) {
            array_push($updateId, $posting_detail->id);
            $update_array['is_posted'] =  1;
           if (($key = array_search($posting_detail->product_master_id, $product_master_ids)) !== false) {
                unset($product_master_ids[$key]);
            }
        }
        if(!empty($product_master_ids))
        {    
            $product_master_details = \App\Products::whereIn('id', $product_master_ids)->get();
            $insert_array=array();
            foreach($product_master_details as $key => $product)
            {
                $insert_array[$key]=array();
                $insert_array[$key]['sku'] = $product->sku;
                $insert_array[$key]['magento_product_id_type'] = $product->magento_product_id_type;
                $insert_array[$key]['magento_product_id'] = $product->magento_product_id;
                $insert_array[$key]['product_type'] = $product->product_type;
                $insert_array[$key]['product_master_id'] = $product->id;
                $insert_array[$key]['store_id'] = $request->store_id;
                $insert_array[$key]['product_title'] = $product->title;
                $insert_array[$key]['date_to_go_live'] = !empty($product->date_to_go_live) ? db_date($product->date_to_go_live) : NULL;
                $insert_array[$key]['selling_price'] = $product->single_selling_price;
               
                $insert_array[$key]['country_of_origin'] = $product->country_of_origin;
                $insert_array[$key]['brand'] = $product->brand;
                $insert_array[$key]['category_ids'] = !empty($product->buying_category_id) ? $product->buying_category_id : '';
                $insert_array[$key]['magento_product_length'] = $product->product_length;
                $insert_array[$key]['magento_product_height'] = $product->product_height;
                $insert_array[$key]['magento_product_width'] = $product->product_width;
                $insert_array[$key]['magento_product_weight'] = $product->product_weight;
                $insert_array[$key]['short_description'] = $product->short_description;
                $insert_array[$key]['product_description'] = $product->long_description;
                /*$insert_array[$key]['meta_title'] = $product->meta_title;
                $insert_array[$key]['meta_keyword'] = $product->meta_keyword;
                $insert_array[$key]['meta_description'] = $product->meta_description;*/
                $insert_array[$key]['variation_theme_id'] = !empty($product->variation_theme_id) ? $product->variation_theme_id : NULL;
                $insert_array[$key]['is_posted'] = 1;
                $insert_array[$key]['posting_result_status'] = 0;
                $insert_array[$key]['status'] = 1;
                $insert_array[$key]['main_image_url'] = !empty($product->main_image_marketplace) ? $product->main_image_marketplace : '';

                if((strrpos($insert_array[$key]['main_image_url'], 'https:') === false && strrpos($insert_array[$key]['main_image_url'], 'http:') === false) && !empty($insert_array[$key]['main_image_url']))
                {
                    $protocol = $request->secure() ? 'https:' : 'http:';

                    $insert_array[$key]['main_image_url'] = $protocol.$insert_array[$key]['main_image_url'];    
                }  
                $other_images = array();

                // set protocol
                if(!empty($product->productImages))
                {
                    foreach($product->productImages as $image_detail)
                    {
                        $image_detail->image=(!empty($image_detail->image))?  url('/storage/uploads') .'/'. $image_detail->image : '';
                        if((strrpos($image_detail->image, 'https:') === false && strrpos($image_detail->image, 'http:') === false) && !empty($image_detail->image))
                        {
                            $protocol = $request->secure() ? 'https:' : 'http:';

                            $image_detail->image = $protocol.$image_detail->image;    
                        }   

                        $other_images[] = $image_detail->image;
                    }   
                } 
                $insert_array[$key]['image_details'] = !empty($other_images) ? serialize($other_images) : '';


            }  
            //print_r($insert_array);exit;  
        }
        $status=0;
        if(!empty($insert_array))
        {
            // insert
            if(\App\MagentoProductPosting::insert($insert_array))
            {
                $status=1;
            }
        }

        if(!empty($updateId))
        {
            // update


             if(\App\MagentoProductPosting::whereIn('id',$updateId)->update($update_array))

            {
                $status=1;
            }
           // print_r($update_array);exit;
        } 
        if($status==1)
        {
            return $this->sendResponse('Products added for listing', 200);
        }
        else{
            return $this->sendError("Products not listed, please try again", 422);
        } 
         
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       } 
    }

    public function setDateToGoLive(CreateRequest $request)
    {
        try
        {
            $product_master_ids = $request->ids;
            
            $posting_details = MagentoProductPosting::select('id','product_master_id')->where(function($q) use ($request,$product_master_ids){
                                                                            $q->whereIn('product_master_id', $product_master_ids);
                                                                            $q->where('store_id', $request->store_id);
                                                                            $q->where('is_revised', 0);
                                                                            $q->where('is_posted', 0);
                                                                        })->get();

            $updateId = array();
            
            foreach ($posting_details as  $posting_detail) {
                
                array_push($updateId, $posting_detail->id);

                if (($key = array_search($posting_detail->product_master_id, $product_master_ids)) !== false)
                {
                    unset($product_master_ids[$key]);
                }
            }
            
            if(!empty($product_master_ids))
            {    
                $product_master_details = \App\Products::whereIn('id', $product_master_ids)->get();

                $insert_array=array();
                
                foreach($product_master_details as $key => $product)
                {
                    $insert_array[$key]=array();
                    
                    $date_to_go_live = NULL;
                    
                    if(!empty($request->date_to_go_live))
                    {
                        $date_to_go_live = $request->date_to_go_live;

                        $date_to_go_live = str_replace('/', '-', $date_to_go_live);

                        $date_to_go_live = date('Y-m-d H:i:s', strtotime($date_to_go_live));
                    }    
                    $insert_array[$key]['date_to_go_live'] = $date_to_go_live;
                    $insert_array[$key]['sku'] = $product->sku;
                    $insert_array[$key]['magento_product_id_type'] = $product->magento_product_id_type;
                    $insert_array[$key]['magento_product_id'] = $product->magento_product_id;
                    $insert_array[$key]['product_type'] = $product->product_type;
                    $insert_array[$key]['product_master_id'] = $product->id;
                    $insert_array[$key]['store_id'] = $request->store_id;
                    $insert_array[$key]['product_title'] = $product->title;
                    $insert_array[$key]['selling_price'] = $product->single_selling_price;
                   // $insert_array[$key]['quantity'] = $product->quantity;
                    $insert_array[$key]['country_of_origin'] = $product->country_of_origin;
                    $insert_array[$key]['brand'] = $product->brand;
                    $insert_array[$key]['category_ids'] = !empty($product->buying_category_id) ? $product->buying_category_id : '';
                    $insert_array[$key]['magento_product_length'] = $product->product_length;
                    $insert_array[$key]['magento_product_height'] = $product->product_height;
                    $insert_array[$key]['magento_product_width'] = $product->product_width;
                    $insert_array[$key]['magento_product_weight'] = $product->product_weight;
                    $insert_array[$key]['short_description'] = $product->short_description;
                    $insert_array[$key]['product_description'] = $product->long_description;
                    $insert_array[$key]['variation_theme_id'] = !empty($product->variation_theme_id) ? $product->variation_theme_id : NULL;
                    $insert_array[$key]['is_posted'] = 1;
                    $insert_array[$key]['posting_result_status'] = 0;
                    $insert_array[$key]['status'] = 1;
                    $insert_array[$key]['main_image_url'] = !empty($product->main_image_marketplace) ? $product->main_image_marketplace : '';

                    if((strrpos($insert_array[$key]['main_image_url'], 'https:') === false && strrpos($insert_array[$key]['main_image_url'], 'http:') === false) && !empty($insert_array[$key]['main_image_url']))
                    {
                        $protocol = $request->secure() ? 'https:' : 'http:';

                        $insert_array[$key]['main_image_url'] = $protocol.$insert_array[$key]['main_image_url'];    
                    }  
                    $other_images = array();

                    // set protocol
                    if(!empty($product->productImages))
                    {
                        foreach($product->productImages as $image_detail)
                        {
                            $image_detail->image=(!empty($image_detail->image))?  url('/storage/uploads') .'/'. $image_detail->image : '';

                            if((strrpos($image_detail->image, 'https:') === false && strrpos($image_detail->image, 'http:') === false) && !empty($image_detail->image))
                            {
                                $protocol = $request->secure() ? 'https:' : 'http:';

                                $image_detail->image = $protocol.$image_detail->image;    
                            }   

                            $other_images[] = $image_detail->image;
                        }   
                    } 
                    $insert_array[$key]['image_details'] = !empty($other_images) ? serialize($other_images) : '';
                }  
            }
            
            $status=0;
            
            if(!empty($insert_array))
            {
                // insert
                if(\App\MagentoProductPosting::insert($insert_array))
                {
                    $status=1;
                }
            }

            if(!empty($updateId))
            {
                // update
                $update_array['is_posted'] =  1;

                $date_to_go_live = NULL;
                    
                if(!empty($request->date_to_go_live))
                {
                    $date_to_go_live = $request->date_to_go_live;

                    $date_to_go_live = str_replace('/', '-', $date_to_go_live);

                    $date_to_go_live = date('Y-m-d H:i:s', strtotime($date_to_go_live));
                }

                $update_array['date_to_go_live'] =  $date_to_go_live;

                if(\App\MagentoProductPosting::whereIn('id',$updateId)->update($update_array))
                {
                    $status=1;
                }
            } 
            
            if($status==1)
            {
                return $this->sendResponse('Products added for listing', 200);
            }
            else
            {
                return $this->sendError("Products not listed, please try again", 422);
            } 
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        } 
    }
}
