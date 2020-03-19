<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CommodityCodes;
use App\Http\Requests\Api\Common\CreateRequest;
use App\Http\Requests\Api\CommodityCodes\UpdateRequest;
use Illuminate\Support\Facades\View;
use Auth;
use Illuminate\Support\Str;

class CommodityCodesController extends Controller
{

	function __construct()
	{
			CreateRequest::$roles_array = [
				'code' => 'required|unique:commodity_codes,code,NULL,id,deleted_at,NULL','desc'=>"required"
			];
	}
	public function index(Request $request)
	{
		try
		{
			
			$columns=[
				0 => 'id',
				1 => 'code',
				2 => 'desc',
				3 => 'is_default',
			];
			$params  = array(
					 'order_column'    => $columns[$request->order[0]['column']],
					 'order_dir'       => $request->order[0]['dir'],
					 'search'          => $request->search['value']
			);

			$codes=CommodityCodes::getAllCodes($request->length, $params);
					
			$data = [];
			
			if (!empty($codes)) {
				$data = $codes->getCollection()->transform(function ($result) use ($data) {
						$tempArray   = array();
						$tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
						$tempArray[] = View::make('components.list-title',['title'=>$result->code,'edit_url'=>route('commodity-codes.edit',$result->id),'btn_title'=>trans('messages.commodity_code_master.edit_commodity_code')])->render();
						$tempArray[] = Str::limit($result->desc,50);
						$is_default='-';
						if($result->is_default==1)
						{
								$is_default="Yes";
						}
						else if($result->is_default==0)
						{
								$is_default="No";
						}
					 
						$tempArray[] = $is_default;
						$viewActionButton = View::make('commodity-codes.action-buttons', ['object' => $result]);
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
			 $code_model=new CommodityCodes;
			 $code_model->code = $request->code;
			 $code_model->is_default = $request->is_default;
			 $code_model->desc = $request->desc;
			 
			 $code_model->created_by = $request->user->id;
			 $code_model->modified_by = $request->user->id;
			 if($code_model->is_default=='1')
				{
						CommodityCodes::where('is_default',1)->update(['is_default'=>0]);
				}
			 if($code_model->save()){

					 return $this->sendResponse(trans('messages.api_responses.code_add_success'), 200);
			 
			 }else{
					 return $this->sendError(trans('messages.api_responses.code_add_error'), 422);
			 }
		 } catch (Exception $ex) {
					return $this->sendError($ex->getMessage(), 400);
		 }
	}

	public function update(UpdateRequest $request,$id)
	{
		try{
			 $code_model=CommodityCodes::find($id);
			 $code_model->code = $request->code;
			 $code_model->is_default = $request->is_default;
			 $code_model->desc = $request->desc;
			 $code_model->modified_by = $request->user->id;
			 if($code_model->save()){
					 return $this->sendResponse(trans('messages.api_responses.code_edit_success'), 200);
			 }else{
					 return $this->sendError(trans('messages.api_responses.code_edit_error'), 422);
			 }
		 } catch (Exception $ex) {
					return $this->sendError($ex->getMessage(), 400);
		 }
	}

	public function destroy(Request $request)
	{
		$code=CommodityCodes::find($request->id);
		if($code->delete()){
				return $this->sendResponse(trans('messages.api_responses.code_delete_success'), 200);
		}else{
				 return $this->sendError(trans('messages.api_responses.code_delete_error'), 422);
		}
	}

	public function destroyMany(Request $request)
	{
		$ids = $request->ids;
		$importDutyIds=\App\ImportDuty::whereIn('commodity_code_id',explode(",",$ids))->pluck('id')->toArray();
		if(CommodityCodes::whereIn('id',explode(",",$ids))->delete()){
				\App\ImportDuty::whereIn('id',$importDutyIds)->delete();
		
				return $this->sendResponse(trans('messages.api_responses.code_delete_success'), 200);
		}else{
				 return $this->sendError(trans('messages.api_responses.code_delete_error'), 422);
		}
	}
}
