<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use App\BookingPallet;
use App\Http\Requests\Api\Common\CreateRequest;
use App\Http\Requests\Api\Common\UpdateRequest;

class BookingPalletController extends Controller
{
    function __construct()
    {
        CreateRequest::$roles_array = [
            'receive_pallets' => 'required','return_pallets'=>"required"
        ];
    }
    
    public function store(CreateRequest $request)
    {
    	try{
            if(count($request->receive_pallets)==0 && count($request->return_pallets)>0)
            {
                return $this->sendError('Add Receive or Return Pallets to save changes', 422); 
            }
            $returnPalletsStatus=$receivePalletsStatus="fail";
            if(isset($request->receive_pallets))
            {
                 if(count($request->receive_pallets)>0)
                {
                    $receivePalletsStatus=$this->addUpdateReceivePallet($request);
                }
            }
            if(isset($request->return_pallets))
            {
                if(count($request->return_pallets)>0)
                {
        		  $returnPalletsStatus=$this->addUpdateReturnPallet($request);
                }
            }
            if($receivePalletsStatus =="success" || $returnPalletsStatus=="success")
            {
                return $this->sendResponse(trans('messages.api_responses.material_pallet_success'), 200); 
            }
            else
            {
               
                return $this->sendError('Add more Receive or Return Pallets to save changes', 422); 
            }
    	}
    	catch (Exception $ex) {
            abort(404);
        }
    }

    public function destroy($id)
    {
        $bookingPallet=BookingPallet::find($id);
        if($bookingPallet->delete()){
            return $this->sendResponse(trans('messages.api_responses.material_pallet_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.common.something_wrong'), 422);
        }
    }

    public function addUpdateReceivePallet(CreateRequest $request)
    {
        try{
           
            $receivePallets=$request->receive_pallets;
            $updateIdArr=$request->update_id;
            $i=0;
            $receiveNumOfPallet=$request->receive_num_of_pallets;
            $storeArr=array();
            $updateStatus=0;
            foreach ($receivePallets as $key => $value) {
                if(!empty($value) && !empty($receiveNumOfPallet[$key]))
               {
                    $updateId=(isset($updateIdArr[$key])) ? $updateIdArr[$key] : 0;
                    if($updateId!=0)
                    {   
                        $palletExists=BookingPallet::find($updateId);
                        if(!empty($palletExists))
                        {
                            $palletExists->num_of_pallets=$receiveNumOfPallet[$key];
                            $palletExists->pallet_id=$value;
                            $palletExists->update();
                            $updateStatus++;

                        }
                        else
                        {
                            $storeArr[$i]['booking_id']=$request->booking_id;
                            $storeArr[$i]['num_of_pallets']=$receiveNumOfPallet[$key];
                            $storeArr[$i]['pallet_type']="1";
                            $storeArr[$i]['pallet_id']=$value;
                            $storeArr[$i]['created_by']=$request->user->id;;
                            $storeArr[$i]['modified_by']=$request->user->id;;
                            $storeArr[$i]['created_at']=Carbon::now();
                            $storeArr[$i]['updated_at']=Carbon::now();
                            $i++;
                        }
                    }
                    else
                    {
                        $storeArr[$i]['booking_id']=$request->booking_id;
                        $storeArr[$i]['num_of_pallets']=$receiveNumOfPallet[$key];
                        $storeArr[$i]['pallet_type']="1";
                        $storeArr[$i]['pallet_id']=$value;
                        $storeArr[$i]['created_by']=$request->user->id;;
                        $storeArr[$i]['modified_by']=$request->user->id;;
                        $storeArr[$i]['created_at']=Carbon::now();
                        $storeArr[$i]['updated_at']=Carbon::now();
                        $i++;
                    }
               }
                
                
            }
            //return $storeArr;
            if(count($storeArr)>0)
            {
                if(BookingPallet::insert($storeArr))
                {
                    return "success";
                }
                else
                {
                    return "success";
                }
            }
            else
            {
                if($updateStatus)
                {
                    return "success";
                }
                else
                {
                    return "fail";
                }
                
            }

        }
        catch (Exception $ex) {
            abort(404);
        }
    }

     public function addUpdateReturnPallet(CreateRequest $request)
    {
        try{
            //return $request->all();
            $returnPallets=$request->return_pallets;
            $updateIdArr=$request->update_return_id;
            $i=0;
            $returnNumOfPallet=$request->return_num_of_pallets;
            $storeArr=array();
            $updateStatus=0;
            foreach ($returnPallets as $key => $value) {
                //$storeArr[$key]=array();
                if(!empty($value) && !empty($returnNumOfPallet[$key]))
                {
                    $updateId=(isset($updateIdArr[$key])) ? $updateIdArr[$key] : 0;
                    if($updateId!=0)
                    {   
                        $palletExists=BookingPallet::find($updateId);
                        if(!empty($palletExists))
                        {
                            $palletExists->num_of_pallets=$returnNumOfPallet[$key];
                            $palletExists->pallet_id=$value;
                            $palletExists->update();
                            $updateStatus++;

                        }
                        else
                        {
                            $storeArr[$i]['booking_id']=$request->booking_id;
                            $storeArr[$i]['num_of_pallets']=$returnNumOfPallet[$key];
                            $storeArr[$i]['pallet_type']="2";
                            $storeArr[$i]['pallet_id']=$value;
                            $storeArr[$i]['created_by']=$request->user->id;;
                            $storeArr[$i]['modified_by']=$request->user->id;;
                            $storeArr[$i]['created_at']=Carbon::now();
                            $storeArr[$i]['updated_at']=Carbon::now();
                            $i++;
                        }
                    }
                    else
                    {
                        $storeArr[$i]['booking_id']=$request->booking_id;
                        $storeArr[$i]['num_of_pallets']=$returnNumOfPallet[$key];
                        $storeArr[$i]['pallet_type']="2";
                        $storeArr[$i]['pallet_id']=$value;
                        $storeArr[$i]['created_by']=$request->user->id;;
                        $storeArr[$i]['modified_by']=$request->user->id;;
                        $storeArr[$i]['created_at']=Carbon::now();
                        $storeArr[$i]['updated_at']=Carbon::now();
                        $i++;
                    }
                }
                
                
            }
            //return $storeArr;
            if(count($storeArr)>0)
            {
                if(BookingPallet::insert($storeArr))
                {
                    return "success";
                }
                else
                {
                    return "success";
                }
            }
            else
            {
                if($updateStatus)
                {
                    return "success";
                }
                else
                {
                    return "fail";
                }
            }

        }
        catch (Exception $ex) {
            abort(404);
        }
    }
}
