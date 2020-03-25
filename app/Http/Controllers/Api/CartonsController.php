<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Cartons;
use App\Http\Requests\Api\Cartons\CreateRequest;
use App\Http\Requests\Api\Cartons\UpdateRequest;
use Illuminate\Support\Facades\View;
class CartonsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try
        {
        
        $columns=[
                0 => 'id',
                1 => 'name',
                2 => 'length',
                3 => 'width',
                4 => 'height',
                5 => 'max_weight',
                6 => 'quantity',
                7 => 'recycle_carton',
        ];
        $params  = array(
             'order_column'    => $columns[$request->order[0]['column']],
             'order_dir'       => $request->order[0]['dir'],
             'search'          => $request->search['value']
        );

        $cartons=Cartons::getAllCartons($request->length, $params);
            
        $data = [];
        
        if (!empty($cartons)) {
                $data = $cartons->getCollection()->transform(function ($result) use ($data) {
                    $tempArray   = array();
                    $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                    $tempArray[] = View::make('components.list-title',['title'=>ucwords($result->name),'edit_url'=>route('cartons.edit',$result->id),'btn_title'=>trans('messages.box_master.edit_box')])->render();
                    $tempArray[] = apply_float_value($result->length);
                    $tempArray[] = apply_float_value($result->width);
                    $tempArray[] = apply_float_value($result->height);
                    $tempArray[] = apply_float_value($result->max_weight);
                    $tempArray[] = $result->quantity;
                    $tempArray[] = config('params.boolean_data')[$result->recycle_carton];
                    $viewActionButton = View::make('cartons.action-buttons', ['object' => $result]);
                    $tempArray[]      = $viewActionButton->render();
                    return $tempArray;
                });
            }
            
            $jsonData = [
                "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                "recordsTotal"    => $cartons->total(), // Total number of records
                "recordsFiltered" => $cartons->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        } catch (Exception $ex) {

        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRequest $request)
    {
       try{
           $carton_model=new Cartons;
           $carton_model->name = $request->name;
           $carton_model->length = $request->length;
           $carton_model->width = $request->width;
           $carton_model->height = $request->height;
           $overall=round((($request->length*$request->width*$request->height)/1000000),2);
           $carton_model->max_volume = $overall;
           $carton_model->max_weight = $request->max_weight_carry;
            $carton_model->quantity = $request->qty;
            $carton_model->cost = $request->cost;
           $carton_model->recycle_carton = $request->is_recycled;
           //$carton_model->barcode = $request->barcode;
           $carton_model->created_by = $request->user->id;
           $carton_model->modified_by = $request->user->id;
           if($carton_model->save()){
               return $this->sendResponse(trans('messages.api_responses.box_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.box_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
       try
       {
           $carton_model= Cartons::find($id);
           $carton_model->name = $request->name;
           $carton_model->length = $request->length;
           $carton_model->width = $request->width;
           $carton_model->height = $request->height;
           $overall=round((($request->length*$request->width*$request->height)/1000000),2);
           $carton_model->max_volume = $overall;
           $carton_model->max_weight = $request->max_weight_carry;
           $carton_model->quantity = $request->qty;
           $carton_model->cost = $request->cost;
           //$carton_model->barcode = $request->barcode;
           $carton_model->recycle_carton = $request->is_recycled;
           $carton_model->modified_by = $request->user->id;
           if($carton_model->save()){
               return $this->sendResponse(trans('messages.api_responses.box_edit_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.box_edit_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $carton=Cartons::find($id);
        if($carton->delete()){
            return $this->sendResponse(trans('messages.api_responses.box_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.box_delete_error'), 422);
        }
    }
    
    public function destroyMany(Request $request)
    {
        $ids = $request->ids;
        
         if(Cartons::whereIn('id',explode(",",$ids))->delete()){
            return $this->sendResponse(trans('messages.api_responses.boxes_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.boxes_delete_error'), 422);
        }
    }
}
