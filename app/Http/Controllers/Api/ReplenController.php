<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Booking;
use Illuminate\Support\Facades\View;
use App\Replen;
use App\WareHouse;
use App\LocationAssign;
use App\Http\Requests\Api\Common\CreateRequest;
use App\ReplenUserPallet;
use App\User;
use App\Locations;
use App\ProductBarcode;
use App\LocationAssignTrans;
use App\Http\Controllers\Api\ReplenCronController;
use App\ReplenUserAisle;//newly added

class ReplenController extends Controller 
{
    /**
     * @author Mohit Trivedi
     * @param \App\Http\Requests\Api\PutAway\PutAwayRequest $request
     */
    public function index(CreateRequest $request) 
    {
        try 
        {
            $default_warehouse_id=$this->defaultWarehouse();
            $user_id=$request->user->id;
            $aisle_array=$this->getReplenAisle($request);

            if(!empty($request->sort_by) && !empty($request->sort_direction))
            {
                $sort_by=$request->sort_by;
                $sort_direction=$request->sort_direction;
            }
            else
            {
                if(!empty($request->user->replen_sort_by) && !empty($request->user->replen_sort_direction))
                {
                    $sort_by=$request->user->replen_sort_by;
                    $sort_direction=$request->user->replen_sort_direction;
                }
                else
                {
                    $sort_by='location';
                    $sort_direction='asc';
                }
            }

            $this->update_user_sorting($user_id,$sort_by,$sort_direction);

            $selected_priority=$request->selected_priority;

            $params= ['sortBy'        => $sort_by,'sortDirection' => $sort_direction
                , 'productSearch' => $request->product_search,'selected_priority'=>$selected_priority,'show_aisle'=>$aisle_array];
            
            $replenObj = new Replen();
            
            $productData     = $replenObj->getReplenData($params);              

            $pallet_pick_location=$this->getPalletPickLocation($default_warehouse_id,$user_id);                                    
            //Total Pending Qty
            if (isset($productData) && !empty($productData) && @count($productData) > 0) 
            {
                $html             = View::make('replen._replen_products', ['productData' => $productData, 'params' => $params,'pallet_pick_location' =>$pallet_pick_location]);
                $data             = $html->render();                
                return $this->sendResponse('Product listing', 200, ['data' => $data]);
            }
            else 
            {
                $productData=array();
                $html             = View::make('replen._replen_products', ['productData' => $productData, 'params' => $params,'pallet_pick_location' =>$pallet_pick_location]);
                $data             = $html->render();                
                return $this->sendResponse('No Records Found', 200, ['data' => $data]);

                //return $this->sendError('No Records Found.', 200, []);
            }
        }
        catch (Exception $ex) 
        {
            return $this->sendError('Bad Request', 400);
        }
    }

    public function update_user_sorting($user_id,$sort_by,$sort_direction)
    {
       $users_model= User::find($user_id);
       $users_model->replen_sort_by = $sort_by;
       $users_model->replen_sort_direction = $sort_direction;
       $users_model->save();
    }

    public function getPalletPickLocation($default_warehouse_id,$user_id)
    {        
        $replenUserObj=new ReplenUserPallet();
        $pallet_pick_location_array=$replenUserObj->getPalletPickLocation($default_warehouse_id,$user_id);        
        return $pallet_pick_location_array;
    }

    public function defaultWarehouse()
    {
        $default_ware=0;
        $warehouse_obj=new WareHouse;
        $default_warehouse_data=$warehouse_obj->select('id')->where('is_default',1)->get();
        if(!empty($default_warehouse_data) && !empty($default_warehouse_data->toArray()))
        {
            $default_ware=isset($default_warehouse_data[0]->id)?$default_warehouse_data[0]->id:'';
        }

        return $default_ware;
    }

    public function replenSelectPallet(CreateRequest $request)
    {
        $user_id=$request->user->id;
        $location=$request->selected_location;
        $default_warehouse_id=$this->defaultWarehouse();
        if(!empty($location) && !empty($user_id))
        {
            //3-Pick Putaway Pallet
            //first check the location is exist or not as a pallet location and get the id
            $location_data=Locations::select('id')->where('status',1)->where('location',$location)->where('type_of_location',3)->get();
            
            if(!empty($location_data) && !empty($location_data->toArray()))
            {
                $location_id=isset($location_data[0]->id)?$location_data[0]->id:'';
                if(!empty($location_id))
                {
                    //check if this location is already assigned or not                    
                    $exist_pallet_location=ReplenUserPallet::select('id','user_id')->where('warehouse_id',$default_warehouse_id)->where('location_id',$location_id)->get();
                    
                    //check if exist or assigned already or work is already started
                    if(!empty($exist_pallet_location) && !empty($exist_pallet_location->toArray()))
                    {
                        $check_user_id=isset($exist_pallet_location[0]->user_id)?$exist_pallet_location[0]->user_id:'';
                        if($check_user_id!=$user_id)
                        {
                            return $this->sendError('This pick putaway pallet location already assigned to other user', 422);
                        }
                        else
                        {
                            return $this->sendResponse('Pallet selected again', 200, ['data' => '']);
                        }
                    }
                    else
                    {
                        //check if work is not already started by assigned in other task or used somewhere else
                        $check_assigned=LocationAssign::select('id')->where('location_id',$location_id)->get();                        
                        if(!empty($check_assigned) && !empty($check_assigned->toArray()))
                        {
                            return $this->sendError('This pick putaway pallet location already assigned', 422);
                        }
                        else
                        {
                            $replenUserObj=new ReplenUserPallet;
                            $replenUserObj->warehouse_id = $default_warehouse_id;
                            $replenUserObj->user_id = $user_id;
                            $replenUserObj->location_id = $location_id;
                            $replenUserObj->created_at=date('Y-m-d H:i:s');
                            if($replenUserObj->save())
                            {
                               return $this->sendResponse('Pick Pallet selected Successfully', 200);
                            }
                            else
                            {
                               return $this->sendError('Bad Request', 400);
                            }
                        }
                    }
                }                
            }
            else
            {
                return $this->sendError('This pick putaway pallet location does not exist', 422);
            }
        }
        else
        {
            return $this->sendError('Bad Request', 400);
        }        
    }

    public function replenFinishPallet(CreateRequest $request)
    {
        $user_id=$request->user->id;
        $location=$request->selected_location;
        $default_warehouse_id=$this->defaultWarehouse();
        if(!empty($location) && !empty($user_id) && !empty($default_warehouse_id))
        {

            $exist_pallet_location=ReplenUserPallet::select('id')->where('warehouse_id',$default_warehouse_id)->where('location_id',$location)->where('user_id',$user_id)->get();
            
            if(!empty($exist_pallet_location) && !empty($exist_pallet_location->toArray()))
            {
                $id= isset($exist_pallet_location[0]->id)?$exist_pallet_location[0]->id:'';

                if(ReplenUserPallet::where('id', $id)->delete())
                {
                    return $this->sendResponse('Pick Pallet finished Successfully', 200);
                }
                else
                {
                    return $this->sendError('Bad Request', 400);
                }
            }
            else
            {
                return $this->sendError('This pick putaway pallet location does not exist', 422);
            }
        }
        else
        {
            return $this->sendError('Bad Request', 400);
        }        
    }

    public function replenProductList(CreateRequest $request)
    {
        $assign_aisle_array=$this->getReplenAisle($request);        
        $user_id=$request->user->id;        
        $product_id=$request->product_id;        
        $selected_pallet=$request->selected_pallet;
        $location=$request->scan_bulk_location;        
        $scan_pro_barcode=$request->scan_pro_barcode;
        $default_warehouse_id=$this->defaultWarehouse();
        if(!empty($location) && !empty($user_id))
        {
            //3-Pick Putaway Pallet
            //first check the location is exist or not as a pallet location and get the id
            $location_data=Locations::select('id','aisle')->where('status',1)->where('location',$location)->whereIn('type_of_location',array('2','4','12'))->get();

            if(!empty($location_data) && !empty($location_data->toArray()))
            {
                $location_id=isset($location_data[0]->id)?$location_data[0]->id:'';
                $aisle_data=isset($location_data[0]->aisle)?$location_data[0]->aisle:'';
                
                if((count($assign_aisle_array) > 0)  && !in_array($aisle_data,$assign_aisle_array))
                {
                    return $this->sendError('This aisle is not assign to you', 422);
                }
                else
                {
                    //check barcode is correct or not if not then show error
                    $barcode_id='';
                    if(!empty($scan_pro_barcode))
                    {
                        $barcode_data_array=ProductBarcode::select('id')->where('product_id',$product_id)->where('barcode',$scan_pro_barcode)->get();
                        if(!empty($barcode_data_array) && !empty($barcode_data_array->toArray()))
                        {
                            $barcode_id=isset($barcode_data_array[0]->id)?$barcode_data_array[0]->id:'';
                        }
                        else
                        {
                            return $this->sendError('This barcode of product does not exist or match', 422);
                        }
                    }

                    //now get complete data for the location and barcode(if scan) from location assign table

                    $locationAssignObj=new LocationAssign();
                    $locationsData=$locationAssignObj->getProductListReplen($default_warehouse_id,$product_id,$location_id,$barcode_id);
                    
                    //Total Pending Qty
                    if (isset($locationsData) && !empty($locationsData) && @count($locationsData) > 0) 
                    {
                        $html             = View::make('replen._detail_product_list', ['locationsData' => $locationsData]);
                        $data             = $html->render();                
                        return $this->sendResponse('Product listing', 200, ['data' => $data]);
                    }                
                    else 
                    {
                        return $this->sendError('This location does have any data', 422);
                    }
                }
            }
            else
            {
                return $this->sendError('This bulk location does not exist', 422);
            }            
        }
        else
        {
            return $this->sendError('Bad Request', 400);
        }
    }

    public function replenProduct(CreateRequest $request)
    {
        $assign_aisle_array=$this->getReplenAisle($request);        
        $user_id=$request->user->id;        
        $product_id=$request->product_id;        
        $selected_pallet=$request->selected_pallet;
        $location_assign_trans_id=$request->location_id;        
        $box_picked=$request->box_picked;
        $default_warehouse_id=$this->defaultWarehouse();

        if(!empty($user_id) && !empty($product_id) && !empty($selected_pallet) && !empty($location_assign_trans_id) && !empty($box_picked) && !empty($default_warehouse_id))
        {
            $exist_pallet_location=ReplenUserPallet::select('id')->where('warehouse_id',$default_warehouse_id)->where('location_id',$selected_pallet)->where('user_id',$user_id)->get();            
            if(!empty($exist_pallet_location) && !empty($exist_pallet_location->toArray()))
            {
                $assign_data=LocationAssignTrans::select('*')->where('id',$location_assign_trans_id)->get();
                if(!empty($assign_data) && !empty($assign_data->toArray()))
                {
                    $old_id=isset($assign_data[0]->id)?$assign_data[0]->id:'';
                    $old_loc_ass_id=isset($assign_data[0]->loc_ass_id)?$assign_data[0]->loc_ass_id:'';
                    $old_qty=isset($assign_data[0]->qty)?$assign_data[0]->qty:'';
                    $old_best_before_date=isset($assign_data[0]->best_before_date)?$assign_data[0]->best_before_date:'';
                    $old_barcode_id=isset($assign_data[0]->barcode_id)?$assign_data[0]->barcode_id:'';
                    $old_qty_per_box=isset($assign_data[0]->qty_per_box)?$assign_data[0]->qty_per_box:'';
                    $old_total_boxes=isset($assign_data[0]->total_boxes)?$assign_data[0]->total_boxes:'';
                    $old_case_type=isset($assign_data[0]->case_type)?$assign_data[0]->case_type:'';
                    //logical process start

                    //check if the data moved is lesser or equal to the real data
                    if($box_picked<=$old_total_boxes)
                    {
                        $remove_old_trans_entry=0;
                        $update_old_trans_entry=0;                        
                        if($box_picked==$old_total_boxes)
                        {
                            $remove_old_trans_entry=1;
                        }
                        else
                        {
                            $update_old_trans_entry=1;
                        }

                        //check all entry parent data
                        $locAssObj=new LocationAssign();
                        $old_parent_data=$locAssObj->getLocationWithType($old_loc_ass_id);
                        if(!empty($old_parent_data) && !empty($old_parent_data->toArray()))
                        {
                            $old_parent_id=isset($old_parent_data[0]->id)?$old_parent_data[0]->id:'';
                            $total_qty=isset($old_parent_data[0]->total_qty)?$old_parent_data[0]->total_qty:'';
                            $tot_qty=isset($old_parent_data[0]->total_qty)?$old_parent_data[0]->total_qty:0;
                            $all_qty=isset($old_parent_data[0]->allocated_qty)?$old_parent_data[0]->allocated_qty:0;

                            //new logic for available qty                            
                            $available_qty=$tot_qty-$all_qty;                            
                            if($available_qty < 0)
                            {
                                $available_qty=0;    
                            }

                            $old_loc_type=isset($old_parent_data[0]->type_of_location)?$old_parent_data[0]->type_of_location:'';                            
                            $remove_old_parent_entry=0;
                            $update_old_parent_entry=0;
                            $total_qty_replen=0;
                            $total_qty_replen=$box_picked*$old_qty_per_box;                            
                            if($total_qty_replen<=$available_qty)
                            {
                                if($total_qty_replen==$available_qty && ($old_loc_type=='3' || $old_loc_type=='4'))
                                {
                                    $remove_old_parent_entry=1;
                                }
                                else
                                {
                                    $update_old_parent_entry=1;
                                }
                                
                                //check if replen location and data already exist
                                $new_parent_data=LocationAssign::select('id','total_qty','available_qty','total_qty','allocated_qty')->where('product_id',$product_id)->where('warehouse_id',$default_warehouse_id)->where('location_id',$selected_pallet)->get();
                                $create_new_parent=0;
                                $update_new_parent=0;
                                $new_total_qty=0;
                                $new_available_qty=0;
                                if(!empty($new_parent_data) && !empty($new_parent_data->toArray()))
                                {
                                    $update_new_parent=1;
                                    $new_parent_id=isset($new_parent_data[0]->id)?$new_parent_data[0]->id:'';
                                    $new_total_qty=isset($new_parent_data[0]->total_qty)?$new_parent_data[0]->total_qty:'';
                                    //$new_available_qty=isset($new_parent_data[0]->available_qty)?$new_parent_data[0]->available_qty:'';
                                    $new_tot_qty=isset($new_parent_data[0]->total_qty)?$new_parent_data[0]->total_qty:0;
                                    $new_all_qty=isset($new_parent_data[0]->allocated_qty)?$new_parent_data[0]->allocated_qty:0;

                                    //new logic for available qty                            
                                    $new_available_qty=$new_tot_qty-$new_all_qty;                            
                                    if($available_qty < 0)
                                    {
                                        $new_available_qty=0;    
                                    }
                                }
                                else
                                {
                                    $create_new_parent=1;
                                }

                                //check if current new child location has data or not if not then we need to create new
                                $create_new_child=0;
                                $update_new_child=0;

                                if(!empty($update_new_parent) && !empty($new_parent_id))
                                {
                                    if(!empty($old_best_before_date))
                                    {
                                        $new_assign_trans_data=LocationAssignTrans::select('*')->where('loc_ass_id',$new_parent_id)->where('best_before_date',$old_best_before_date)->where('barcode_id',$old_barcode_id)->where('case_type',$old_case_type)->where('qty_per_box',$old_qty_per_box)->get();
                                    }
                                    else
                                    {
                                        $new_assign_trans_data=LocationAssignTrans::select('*')->where('loc_ass_id',$new_parent_id)->where('barcode_id',$old_barcode_id)->where('case_type',$old_case_type)->where('qty_per_box',$old_qty_per_box)->get();   
                                    }


                                    if(!empty($new_assign_trans_data) && !empty($new_assign_trans_data->toArray()))
                                    {
                                        $update_new_child=1;
                                        $new_child_id=isset($new_assign_trans_data[0]->id)?$new_assign_trans_data[0]->id:'';
                                        $new_qty=isset($new_assign_trans_data[0]->qty)?$new_assign_trans_data[0]->qty:'';
                                        $new_total_boxes=isset($new_assign_trans_data[0]->total_boxes)?$new_assign_trans_data[0]->total_boxes:'';
                                    }
                                    else
                                    {
                                        $create_new_child=1;
                                    }
                                }
                                else
                                {
                                    $create_new_child=1;
                                }                                

                                //now final calculation for updating deleting data and entries from parent and child table

                                if(!empty($create_new_parent))
                                {
                                    $insert_parent['warehouse_id']=$default_warehouse_id;
                                    $insert_parent['product_id']=$product_id;
                                    $insert_parent['location_id']=$selected_pallet;
                                    $insert_parent['qty_fit_in_location']=0;
                                    $insert_parent['putaway_type']=2;//putaway
                                    $insert_parent['is_mannual']=0;//default
                                    $insert_parent['total_qty']=$total_qty_replen;
                                    $insert_parent['available_qty']=$total_qty_replen;
                                    $insert_parent['created_by']=$user_id;
                                    $insert_parent['created_at']=date('Y-m-d H:i:s');
                                    $new_parent_id = LocationAssign::create($insert_parent)->id;
                                    unset($insert_parent);
                                }

                                if(!empty($update_new_parent) && !empty($new_parent_id))
                                {
                                    $update_new_parent_data['total_qty']=$new_total_qty+$total_qty_replen;
                                    $update_new_parent_data['available_qty']=$new_available_qty+$total_qty_replen;
                                    $update_new_parent_data['updated_at']=date('Y-m-d H:i:s');
                                    LocationAssign::where('id', $new_parent_id)->update($update_new_parent_data);
                                    unset($update_new_parent_data);
                                }

                                //insert in child table
                                if(!empty($create_new_child) && !empty($new_parent_id))
                                {
                                    $insert_child['loc_ass_id']=$new_parent_id;
                                    $insert_child['qty']=$total_qty_replen;
                                    $insert_child['best_before_date']=$old_best_before_date;
                                    $insert_child['barcode_id']=$old_barcode_id;
                                    $insert_child['qty_per_box']=$old_qty_per_box;
                                    $insert_child['total_boxes']=$box_picked;
                                    $insert_child['case_type']=$old_case_type;
                                    $new_child_id = LocationAssignTrans::create($insert_child)->id;
                                    unset($insert_child);
                                }

                                if(!empty($update_new_child) && !empty($new_child_id))
                                {
                                    $update_new_child_data['qty']=$new_qty+$total_qty_replen;
                                    $update_new_child_data['total_boxes']=$new_total_boxes+$box_picked;
                                    LocationAssignTrans::where('id', $new_child_id)->update($update_new_child_data);
                                    unset($update_new_child_data);
                                }

                                //now update or remove previous table data
                                if(!empty($remove_old_parent_entry) && !empty($old_parent_id))
                                {
                                    LocationAssign::where('id', $old_parent_id)->delete();
                                }

                                if(!empty($remove_old_trans_entry) && !empty($old_id))
                                {
                                    LocationAssignTrans::where('id', $old_id)->delete();
                                }

                                if(!empty($update_old_parent_entry) && !empty($old_parent_id))
                                {
                                    $update_old_parent['total_qty']=$total_qty-$total_qty_replen;
                                    if($update_old_parent['total_qty']<0)
                                    {
                                        $update_old_parent['total_qty']=0;
                                    }
                                    $update_old_parent['available_qty']=$available_qty-$total_qty_replen;
                                    if($update_old_parent['available_qty']<0)
                                    {
                                        $update_old_parent['available_qty']=0;
                                    }
                                    $update_old_parent['updated_at']=date('Y-m-d H:i:s');
                                    LocationAssign::where('id', $old_parent_id)->update($update_old_parent);
                                    unset($update_old_parent);
                                }

                                if(!empty($update_old_trans_entry) && !empty($old_id))
                                {
                                    $update_old_child['qty']=$old_qty-$total_qty_replen;
                                    if($update_old_child['qty']<0)
                                    {
                                        $update_old_child['qty']=0;
                                    }
                                    $update_old_child['total_boxes']=$old_total_boxes-$box_picked;
                                    if($update_old_child['total_boxes']<0)
                                    {
                                        $update_old_child['total_boxes']=0;
                                    }
                                    LocationAssignTrans::where('id', $old_id)->update($update_old_child);
                                    unset($update_old_child);
                                }
                                //call cron functon here
                                $returnval='';

                                $newcon=new ReplenCronController();
                                $value =$newcon->processReplan($request,$product_id);
                                if(!empty($value) && $value=='1')
                                {
                                    //check if qty left to replen
                                    $replenObj = new Replen();
                                    $productData_left     = $replenObj->select('id','default_location')->where('warehouse_id',$default_warehouse_id)->where('status',1)->where('product_id',$product_id)->where('replan_qty','!=',0)->get();

                                    if(!empty($productData_left) && !empty($productData_left->toArray()))
                                    {
                                        //check logic if default location aisle is not assign to me
                                        $default_location=!empty($productData_left[0]->default_location)?$productData_left[0]->default_location:'';
                                        if(!empty($default_location) && !empty($assign_aisle_array))
                                        {
                                            $locatin_data=Locations::select('id')->whereIn('aisle',$assign_aisle_array)->get();
                                            if(!empty($locatin_data) && !empty($locatin_data->toArray()))
                                            {
                                                $returnval=1;       
                                            }
                                            else
                                            {
                                                $returnval=2;       
                                            }
                                        }
                                        else if(!empty($default_location) && empty($assign_aisle_array))
                                        {
                                            $returnval=1; //in case of admin       
                                        }
                                        else
                                        {
                                            $returnval=2; 
                                        }
                                    }
                                    else
                                    {
                                        $returnval=2;
                                    }
                                }

                                if($returnval==1)
                                {
                                    return $this->sendResponse('Selected qty replen to the pallet', 200,1);
                                }
                                else if($returnval==2)
                                {
                                    return $this->sendResponse('Replen done for the selected product', 200,2);
                                }
                                else
                                {
                                    return $this->sendError('Bad Request', 400);       
                                }
                            }
                            else
                            {
                                return $this->sendError('Bad Request', 400);
                            }
                        }
                        else
                        {
                            return $this->sendError('Bad Request', 400);
                        }

                    }
                    else
                    {
                        return $this->sendError('Bad Request', 400);
                    }
                }
            }
            else
            {
                return $this->sendError('Please select Pick pallet location to replen', 422);
            }
        }
        else
        {
            return $this->sendError('Bad Request', 400);
        }  
    }

    public function getReplenAisle($request)
    {
        $user=$request->user;
        $user_id=$user->id;

        $role_name_data=$user->getRoleNames();
        $role_name='';
        if(!empty($role_name_data) && !empty($role_name_data->toArray()))
        {
            $role_name=isset($role_name_data[0])?$role_name_data[0]:'';
        } 

        $aisle_array=array();      

        if (strpos($role_name_data, 'admin') == false) //apply if role is not admin role
        {
            if(!empty($user->replen_job_access) && $user->replen_job_access!='1') //check if special aisle assign
            {
                $default_warehouse_id=$this->defaultWarehouse();
                $my_data_aisle=ReplenUserAisle::select('aisle')->where('warehouse_id',$default_warehouse_id)->where('user_id',$user_id)->get();
                if(!empty($my_data_aisle) && !empty($my_data_aisle))
                {
                    foreach($my_data_aisle as $row)
                    {
                        array_push($aisle_array,$row->aisle);
                    }
                }
                else
                {
                    array_push($aisle_array,0);
                }
            }            
        }
        
        return $aisle_array;        
    }
}