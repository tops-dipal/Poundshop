<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Replen;
use App\Http\Requests\Api\Common\CreateRequest;
use App\Cron;

class ReplenRequestController extends Controller
{
    
    function __construct() {
      CreateRequest::$roles_array = [];
    }
    public function index(Request $request)
    {
    	try {
            $columns          = [
                0  => 'title',
                1  => 'total_in_warehouse',
                2  => 'total_in_pick',
                3  => 'total_in_bulk',
                4  => 'allocated',
                5  => 'ros',
                6  => 'stock_hold_days',
                7  => 'stock_hold_qty',
                8  => 'replan_qty',
                9  => 'priority',
                10 => 'title',
                11 => 'title',
                12 => '',
                
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }
            
            $params                                = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                'search'         => $request->search['value'],
               'advance_search' => $adv_search_array,
               'length'=>$request->length,
               'page'=>$request->page,
            );
           
            $productList                               = Replen::getReplenRequestRecords($request->length, $params);

            //dd(($productList));
            $data                                  = [];

            if (!empty($productList)) {

                $data = $productList->transform(function ($result) use ($data,$params) {
                    $tempArray = array();

                    $tempArray[]  = View::make('replen-request.index-product-info', ['object' => $result])->render();
                    if(isset($params['advance_search']['warehouse_id']) && $params['advance_search']['warehouse_id']!='')
                    {
                        $stockTotal=\App\LocationAssign::where('product_id',$result->id)->where('warehouse_id',$params['advance_search']['warehouse_id'])->sum('total_qty');
                    }
                    else
                    {
                        $stockTotal=\App\LocationAssign::where('product_id',$result->id)->sum('total_qty');
                    }
                    $tempArray[]  =$stockTotal;
                     $tempArray[]  = View::make('replen-request.index-pick-locations', ['object' => $result])->render();
                     $tempArray[]  = View::make('replen-request.index-bulk-locations', ['object' => $result])->render();
                    
                    $tempArray[]  =\App\LocationAssign::where('product_id',$result->id)->sum('allocated_qty');
                    $tempArray[]  = $result->ros;
                   	$tempArray[]= $result->stock_hold_days;
                   	$tempArray[]= $result->stock_hold_qty;
                    $tempArray[]  = $result->replan_qty;
                    $tempArray[]  = priorityTypes($result->priority);
                    $tempArray[]  = !empty($result->replen_status)?replenStatus($result->replen_status):'-';
                    $tempArray[]  = '-';
                    $tempArray[]  = View::make('replen-request.action-buttons', ['object' => $result,'stockTotal'=>$stockTotal])->render();
                   
                 
                   
                    return $tempArray;
                });
            }


            

            $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => $productList->total(), // Total number of records
                "recordsFiltered" => $productList->total(),
                "data"            => $data ,// Total data array,
               
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {

        }
    }

    function editOverride(CreateRequest $request)
    {
        try {
            $replenData = \App\Replen::find($request->replen_id);
            if (!empty($replenData)) {
                   $replenUser=new \App\ReplenUpdate;
                   $replenUser->replen_id=$request->replen_id;
                   $replenUser->user_id=$request->user()->id;
                   $replenUser->priority=$request->override_priority;
                   $replenUser->qty=$request->override_qty;
                   $replenUser->prev_priority=$request->prev_priority;
                   $replenUser->prev_qty=$request->prev_qty;
                   if($replenUser->save())
                   {
                    $replenData->priority=$request->override_priority;
                    $replenData->replan_qty=$request->override_qty;
                    if($replenData->save())
                     {
                        return $this->sendResponse('Record updated successfully', 200);
                     }
                     else
                     {
                        return $this->sendResponse('Record doesnot updated,please try again', 200);
                     }
                   }
                   else
                   {
                         return $this->sendResponse('Something went wrong,please try again', 200);
                   }
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public function searchProductReplenInfo(Request $request)
    {
        $params                                = array(
                'order_column'   => 'id',
                'order_dir'      => 'desc',
                'search'         => $request->search,
               'length'=>1,
               'page'=>1,
            );
           
            $productList                               = Replen::getReplenRequestRecords($request->length, $params);
            if(count($productList)>0)
            {
                $data=$productList[0];
            }
            else
            {
                $data=[];
            }
            return $this->sendResponse('Success', 200,$data);
    }

    public function storeAssignAisle(CreateRequest $request)
    {
        try{
           // dd($request->all());
            $assigenAisleCOunt=count($request->aisle);
            $aisleData=$request->aisle;
            $updateId=$request->update_id;
            $userData=$request->user_id;
            $insertArr=array();
            $updateArr=array();
            $updateStatus=0;
            for($i=0;$i<$assigenAisleCOunt;$i++)
            {
                if(isset($updateId[$i]) && $updateId[$i]!='')
                {
                    $assignAisleObject=\App\ReplenUserAisle::find($updateId[$i]);
                    if(!empty($assignAisleObject))
                    {
                        $assignAisleObject->user_id=$userData[$i];
                        $assignAisleObject->aisle=$aisleData[$i];
                        if($assignAisleObject->save())
                        {
                            $updateStatus++;
                        }   

                    }
                    else
                    {
                        $insertArr[$i]['warehouse_id']=$request->warehouse_id;
                        $insertArr[$i]['aisle']=$aisleData[$i];
                        $insertArr[$i]['user_id']=$userData[$i];
                        $insertArr[$i]['created_by']=$request->user->id;
                        $insertArr[$i]['modified_by']=$request->user->id;
                        $insertArr[$i]['created_at']=date('Y-m-d H:i:s');
                        $insertArr[$i]['updated_at']=date('Y-m-d H:i:s');
                    }
                }
                else
                {
                    $insertArr[$i]['warehouse_id']=$request->warehouse_id;
                    $insertArr[$i]['aisle']=$aisleData[$i];
                    $insertArr[$i]['user_id']=$userData[$i];
                    $insertArr[$i]['created_by']=$request->user->id;
                    $insertArr[$i]['modified_by']=$request->user->id;
                    $insertArr[$i]['created_at']=date('Y-m-d H:i:s');
                    $insertArr[$i]['updated_at']=date('Y-m-d H:i:s');
                }
            }
            
            if(count($insertArr)>0 || $updateStatus>0)
            {
                if(\App\ReplenUserAisle::insert($insertArr) || $updateStatus>0){
                      return $this->sendResponse('Record saved successfully', 200);
                }
                else if($updateStatus>0)
                {
                      return $this->sendResponse('Record saved successfully', 200);
                }
                else{
                     return $this->sendResponse('Something went wrong', 422);
                   }
            }
                
        
        } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function deleteAssignAisle(Request $request)
    {
         try{

            if(\App\ReplenUserAisle::where('id',$request->id)->delete())
            {
                return $this->sendResponse('Record deleted successfully', 200);
            }
            else
            {
                return $this->sendResponse('Something went wrong', 422);
            }  
        
        } catch (Exception $ex) {
                return $this->sendError($ex->getMessage(), 400);
           
        }
            
    }


    //these funcion called from replen request , buttn update live data
    public function storeCronCall(Request $request)
    {
        try{
            $cron_data=new Cron;
            $cron_data->warehouse_id=\App\Warehouse::where('is_default',1)->first()->id;
            $cron_data->cron_name= 'CRON_' . time(); 
            $cron_data->cron_type= 'REPLEN_CRON'; 

            if($cron_data->save())
            {
                return $this->sendResponse('Replen Data will be updated soon..', 200);
            }
            else
            {
                return $this->sendResponse('Something went wrong', 422);
            }  
        
        } catch (Exception $ex) {
                return $this->sendError($ex->getMessage(), 400);
           
        }
    }
}
