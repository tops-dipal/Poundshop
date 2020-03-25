<?php

namespace App\Http\Controllers\Api;
use App\Pallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Common\CreateRequest;
use App\Http\Requests\Api\Pallets\UpdateRequest;
use Illuminate\Support\Facades\View;

class PalletsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        CreateRequest::$roles_array = [
                            'name' => 'required|unique:pallets_master',
           /* 'barcode'=>'unique:pallets_master'*/
                          ];
    }
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
                6 => 'returnable', 
                7 => 'sellable',                
        ];

        $params  = array(
          'order_column'    => $columns[$request->order[0]['column']],
          'order_dir'       => $request->order[0]['dir'],
          'search'          => $request->search['value']
        );

        $pallets=Pallet::getAllPallets($request->length, $params);
            
        $data = [];
        
        if (!empty($pallets)) 
        {
          $data = $pallets->getCollection()->transform(function ($result) use ($data) {
              $tempArray   = array();
              $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
              $tempArray[] = View::make('components.list-title',['title'=>ucwords($result->name),'edit_url'=>route('pallets.edit',$result->id),'btn_title'=>trans('messages.modules.pallets_edit')])->render();
              $tempArray[] = apply_float_value($result->length);
              $tempArray[] = apply_float_value($result->width);
              $tempArray[] = apply_float_value($result->height);
              $tempArray[] = apply_float_value($result->max_weight);
              $tempArray[] = ($result->returnable==0) ? 'No' :'Yes';
              $tempArray[] =  ($result->sellable==0) ? 'No' :'Yes';         
              $viewActionButton = View::make('pallets.action-buttons', ['object' => $result]);
              $tempArray[]      = $viewActionButton->render();
              return $tempArray;
          });
        }
            
        $jsonData = [
            "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $pallets->total(), // Total number of records
            "recordsFiltered" => $pallets->total(),
            "data"            => $data // Total data array
        ];
        
        return response()->json($jsonData);
      } 
      catch (Exception $ex) 
      {
          return 'error';
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
       try
       {
           $pallet_model=new Pallet;
           $pallet_model->name = $request->name;
           $pallet_model->length = $request->length;
           $pallet_model->width = $request->width;
           $pallet_model->height = $request->height;
           $pallet_model->max_volume = ($request->length*$request->width*$request->height)/1000000;
           $pallet_model->max_weight = $request->max_weight_carry;
           $pallet_model->quantity = $request->qty;   
           //$pallet_model->barcode = $request->barcode; 
           $pallet_model->returnable=$request->returnable;
           $pallet_model->sellable=$request->sellable;       
           $pallet_model->created_by = $request->user->id;
           $pallet_model->modified_by = $request->user->id;
           if($pallet_model->save()){
               return $this->sendResponse(trans('messages.api_responses.pallet_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.pallet_add_error'), 422);
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
           $pallet_model= Pallet::find($id);
           $pallet_model->name = $request->name;
           $pallet_model->length = $request->length;
           $pallet_model->width = $request->width;
           $pallet_model->height = $request->height;
           $overall=round((($request->length*$request->width*$request->height)/1000000),2);
           $pallet_model->max_volume = $overall;
           $pallet_model->max_weight = $request->max_weight_carry;
           $pallet_model->quantity = $request->qty;  
           //$pallet_model->barcode = $request->barcode; 
           $pallet_model->returnable=$request->returnable;
           $pallet_model->sellable=$request->sellable;         
           $pallet_model->created_by = $request->user->id;
           $pallet_model->modified_by = $request->user->id;
           if($pallet_model->save()){
               return $this->sendResponse(trans('messages.api_responses.pallet_edit_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.pallet_edit_error'), 422);
           }
       } 
       catch (Exception $ex) 
       {
          return $this->sendError($ex->getMessage(), 400);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $pallet=Pallet::find($request->id);
        if($pallet->delete()){
            return $this->sendResponse(trans('messages.api_responses.pallet_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.pallet_delete_error'), 422);
        }
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->ids;        
        if(Pallet::whereIn('id',explode(",",$ids))->delete())
        {
          return $this->sendResponse(trans('messages.api_responses.pallet_delete_success'), 200);
        }
        else
        {
           return $this->sendError(trans('messages.api_responses.pallet_delete_error'), 422);
        }
    }
}
