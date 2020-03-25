<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Slot;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use App\Http\Requests\Api\Slot\CreateRequest;
use App\Http\Requests\Api\Slot\UpdateRequest;
use Carbon\Carbon;

class SlotController extends Controller
{
    
    public function store(CreateRequest $request)
    {
       // return $request->all();
    	$loopCount=$request->slot_num;
    	$from=$request->from;
    	$to=$request->to;
    	$storeArr=array();
    	$updateStatus=0;
    	$storeStatus=0;
    	$j=0;
    	for($i=1;$i<=$loopCount;$i++)
    	{
    		if(isset($from[$i]) && isset($to[$i]))
    		{
    			$getSlot=Slot::where('from',$from[$i])->where('to',$to[$i])->withTrashed()->first();
    			if(!empty($getSlot))
    			{
    				$updateArr=array();
    				$updateArr['deleted_at']=NULL;
    				$updateArr['modified_by']=$request->user->id;
    				if($getSlot->update($updateArr));
    				{
    					$updateStatus++;
    				}
    			}
    			else
    			{
    				$storeArr[$j]['from']=date("H:i", strtotime($from[$i]));
	    			$storeArr[$j]['to']=date("H:i", strtotime($to[$i]));
	    			$storeArr[$j]['created_by']=$request->user->id;
	    			$storeArr[$j]['modified_by']=$request->user->id;
	    			$storeArr[$j]['created_at']=Carbon::now();
	    			$storeArr[$j]['updated_at']=Carbon::now();
	    			$j++;
    			}
    		}
    	}
    	if(count($storeArr)>0)
    	{
    		if(Slot::insert($storeArr))
    		{
    			$storeStatus=1;
    		}
    		else{
               return $this->sendError(trans('messages.api_responses.slot_add_error'), 422);
           }
    	}
    	if($storeStatus>0 || $updateStatus>0)
    	{
    		return $this->sendResponse(trans('messages.api_responses.slot_add_success'), 200);
    	}
    	else{
               return $this->sendError(trans('messages.api_responses.slot_add_error'), 422);
           }
    }
    public function destroy(Request $request)
    {
        $slot=Slot::find($request->id);
        if($slot->delete()){
            return $this->sendResponse(trans('messages.api_responses.slot_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.slot_delete_error'), 422);
        }
    }
}
