<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CategoryMapping;
use Illuminate\Support\Facades\View;
use App\Http\Requests\Api\Mapping\MappingRequest;
use App\Http\Requests\Api\Mapping\UpdateRequest;

class CategoryMappingController extends Controller
{

    function __construct()
    {
       
        
    }
    public function index(Request $request)
    {
    	try
        {
        
           $columns=[
                    0 => 'id',
                    1 => 'category_name',
                    2 => 'name',
            ];
            $params  = array(
                 'order_column'    => $columns[$request->order[0]['column']],
                 'order_dir'       => $request->order[0]['dir'],
                 'search'          => $request->search['value']
            );

            $mapping=CategoryMapping::getAllMapping($request->length, $params);
           
            $data = [];
            
            if (!empty($mapping)) {
                    $data = $mapping->getCollection()->transform(function ($result) use ($data) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                        $tempArray[] = $result->path;
                        $tempArray[] = $result->structure;
                        $viewActionButton = View::make('category-mapping.action-buttons', ['object' => $result]);
                        $tempArray[]      = $viewActionButton->render();
                        return $tempArray;
                    });
                }
                
                $jsonData = [
                    "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                    "recordsTotal"    => $mapping->total(), // Total number of records
                    "recordsFiltered" => $mapping->total(),
                    "data"            => $data // Total data array
                ];
                return response()->json($jsonData);
        } 
        catch (Exception $ex) {
            return 'error';
        }
    }

    public function store(MappingRequest $request)
    {
        try{
         
           $map_model=new CategoryMapping;
           $map_model->range_id = $request->range_id;
           $map_model->magento_category_id =$request->magento_category_id;
           $map_model->created_by = $request->user->id;
           $map_model->modified_by = $request->user->id;
           if($map_model->save()){
               return $this->sendResponse(trans('messages.api_responses.map_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.map_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function update(UpdateRequest $request)
    {
        try{
           
           $map_model=CategoryMapping::find($request->id);
           $map_model->range_id = $request->range_id;
           $map_model->magento_category_id =$request->magento_category_id;
           $map_model->modified_by = $request->user->id;
           if($map_model->save()){
               return $this->sendResponse(trans('messages.api_responses.map_update_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.map_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function destroy(Request $request)
    {
        $code=CategoryMapping::find($request->id);
        if($code->delete()){
            return $this->sendResponse(trans('messages.api_responses.map_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.map_delete_error'), 422);
        }
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->ids;
        
         if(CategoryMapping::whereIn('id',explode(",",$ids))->delete()){
            return $this->sendResponse(trans('messages.api_responses.map_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.map_delete_error'), 422);
        }
    }
}
