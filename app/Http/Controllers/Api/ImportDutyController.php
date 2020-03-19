<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\ImportDuty;
use App\Http\Requests\Api\Common\CreateRequest;
use App\Http\Requests\Api\ImportDuty\UpdateRequest;
use Illuminate\Support\Facades\View;

class ImportDutyController extends Controller
{	
	function __construct()
    {
        CreateRequest::$roles_array = [
        	'commodity_code_id' => 'required|unique_with:import_duty,country_id,NULL,id,deleted_at,NULL','country_id'=>"required",'rate'=>'required',
        ];
        CreateRequest::$message_array =['unique_with' => trans('messages.api_responses.duty_combination_error')];
    }


     public function destroy(Request $request)
    {
        $code=ImportDuty::find($request->id);
        if($code->delete()){
            return $this->sendResponse(trans('messages.api_responses.duty_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.duty_delete_error'), 422);
        }
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->ids;
        
         if(ImportDuty::whereIn('id',explode(",",$ids))->delete()){
            return $this->sendResponse(trans('messages.api_responses.duty_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.duty_delete_error'), 422);
        }
    }

     public function index(Request $request)
    {
    	try
        {
        
           $columns=[
                    0 => 'id',
                    1 => 'commodity_code_id',
                    2 => 'rate',
                    3 => 'country_id',
            ];
            $params  = array(
                 'order_column'    => $columns[$request->order[0]['column']],
                 'order_dir'       => $request->order[0]['dir'],
                 'search'          => $request->search['value']
            );

            $codes=ImportDuty::getAllDuty($request->length, $params);
                
            $data = [];
            
            if (!empty($codes)) {
                    $data = $codes->getCollection()->transform(function ($result) use ($data) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                        $tempArray[] = View::make('components.list-title',['title'=>$result->commodityCode->code,'edit_url'=>route('import-duty.edit',$result->id),'btn_title'=>trans('messages.import_duty_master.edit_duty')])->render();
                        $tempArray[] = $result->commodityCode->desc;
                        $tempArray[] = $result->rate.'%';
                        $tempArray[] = $result->country->name;
                        $is_default='-';
                        $viewActionButton = View::make('import-duty.action-buttons', ['object' => $result]);
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
           $duty_model=new ImportDuty;
           $duty_model->commodity_code_id = $request->commodity_code_id;
           $duty_model->rate = $request->rate;
           $duty_model->country_id = $request->country_id;
           
           $duty_model->created_by = $request->user->id;
           $duty_model->modified_by = $request->user->id;
           if($duty_model->save()){
               return $this->sendResponse(trans('messages.api_responses.duty_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.duty_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function update(UpdateRequest $request,$id)
    {
        try{
           $duty_model=ImportDuty::find($id);
           $duty_model->commodity_code_id = $request->commodity_code_id;
           $duty_model->rate = $request->rate;
           $duty_model->country_id = $request->country_id;
           $duty_model->modified_by = $request->user->id;
           if($duty_model->save()){
               return $this->sendResponse(trans('messages.api_responses.duty_edit_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.duty_edit_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    function getDescCode(Request $request)
    {
      try{
           $code=\App\CommodityCodes::find($request->id);
           
           if($code){
            $data['code']=$code;
               return $this->sendResponse('success', 200,$data);
           }else{
               return $this->sendError('error', 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    
}
