<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Replen;
use App\ReplenUserPallet;
use App\Http\Requests\Api\Common\CreateRequest;
use App\WareHouse;
use App\LocationAssign;
use App\ReplenUserAisle;//newly added

class ReplenController extends Controller
{

    public function index(CreateRequest $request)
    {	
        $aisle_array=$this->getReplenAisle($request);
        $user_id=$request->user->id;
        $default_warehouse_id=$this->defaultWarehouse();
        $pallet_pick_location=$this->getPalletPickLocation($default_warehouse_id,$user_id);
        return view('replen.index',compact('pallet_pick_location','aisle_array','default_warehouse_id'));
    }

    public function edit(CreateRequest $request,$id) 
    {
        try 
        {            
            $user_id=$request->user->id;
            $default_warehouse_id=$this->defaultWarehouse();
            $pallet_pick_location=$this->getPalletPickLocation($default_warehouse_id,$user_id);
            $aisle_array=$this->getReplenAisle($request);

            //check if pick pallet location is assigned or not if not then terminate the process
            if(!empty($pallet_pick_location) && !empty($pallet_pick_location->toArray()))
            {
                //get replen data
                $replenObj = new Replen();
                $replen_data     = $replenObj->getReplenDetailData($id,$aisle_array);                
                $product_id='';
                if(!empty($replen_data) && !empty($replen_data->toArray()))
                {
                    $product_id=isset($replen_data[0]->product_id)?$replen_data[0]->product_id:'';

                    $locationAssignObj=new LocationAssign();
                    $current_storage_detail_array=$locationAssignObj->getCurrentStorageDetail($default_warehouse_id,$product_id);                     
                    $current_storage_detail=array();

                    $pick_location_data=array();
                    $bulk_location_data=array();
                    $pick_putaway_location_data=array();
                    $bulk_putaway_location_data=array();
                    $dispatch_location_data=array();
                    $dropshipping_location_data=array();
                    $aerosol_cage_location_data=array();
                    $quarantine_location_data=array();
                    $hold_location_data=array();
                    $return_supplier_location_data=array();
                    $other_location=array();
                    $aerosol_bulk_cage_location_data=array();

                    if(!empty($current_storage_detail_array->toArray()) && !empty($current_storage_detail_array))
                    {
                        $current_storage_detail=$current_storage_detail_array->toArray();
                        if(!empty($current_storage_detail))
                        {
                            foreach($current_storage_detail as $row)
                            {
                                if($row['type_of_location']=='1')
                                {
                                    array_push($pick_location_data,$row);
                                }
                                else if($row['type_of_location']=='2')
                                {
                                    array_push($bulk_location_data,$row);
                                }
                                else if($row['type_of_location']=='3')
                                {
                                    array_push($pick_putaway_location_data,$row);
                                }
                                else if($row['type_of_location']=='4')
                                {
                                    array_push($bulk_putaway_location_data,$row);
                                }
                                else if($row['type_of_location']=='5')
                                {
                                    array_push($dispatch_location_data,$row);
                                }
                                else if($row['type_of_location']=='6')
                                {
                                    array_push($dropshipping_location_data,$row);
                                }
                                else if($row['type_of_location']=='7')
                                {
                                    array_push($aerosol_cage_location_data,$row);
                                }
                                else if($row['type_of_location']=='8')
                                {
                                    array_push($quarantine_location_data,$row);
                                }
                                else if($row['type_of_location']=='9')
                                {
                                    array_push($hold_location_data,$row);
                                }
                                else if($row['type_of_location']=='10')
                                {
                                    array_push($return_supplier_location_data,$row);
                                }
                                else if($row['type_of_location']=='12')
                                {
                                    array_push($aerosol_bulk_cage_location_data,$row);
                                }
                                else
                                {
                                    array_push($other_location,$row);
                                }
                            }
                        }
                    } 
                    
                    return view('replen.detail', compact('replen_data','pallet_pick_location','current_storage_detail','pick_location_data','bulk_location_data','pick_putaway_location_data','bulk_putaway_location_data','dispatch_location_data','dropshipping_location_data','aerosol_cage_location_data','quarantine_location_data','hold_location_data','return_supplier_location_data','aerosol_bulk_cage_location_data','other_location'));
                }
                else
                {
                    abort(404);
                }
            }
            else 
            {
                abort(404);
            }
        }
        catch (Exception $ex) {
            abort(404);
        }
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