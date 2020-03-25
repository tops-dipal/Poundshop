<?php

namespace App\Http\Controllers\Api;
use App\Totes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Totes\CreateRequest;
use App\Http\Requests\Api\Totes\UpdateRequest;
use Illuminate\Support\Facades\View;
use Auth;

class TotesController extends Controller
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
                    2 => 'category',
                    3 => 'length',
                    4 => 'width',
                    5 => 'height',
                    6 => 'max_weight',
                    7 => 'quantity',
            ];
            $params  = array(
                 'order_column'    => $columns[$request->order[0]['column']],
                 'order_dir'       => $request->order[0]['dir'],
                 'search'          => $request->search['value']
            );

            $totes=Totes::getAllTotes($request->length, $params);
                
            $data = [];
            
            if (!empty($totes)) {
                    $data = $totes->getCollection()->transform(function ($result) use ($data) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                        $tempArray[] = View::make('components.list-title',['title'=>ucwords($result->name),'edit_url'=>route('totes.edit',$result->id),'btn_title'=>trans('messages.totes.totes_edit')])->render();
                        $category='-';
                        if($result->category==1)
                        {
                            $category="Next Day";
                        }
                        else if($result->category==2)
                        {
                            $category="Standard";
                        }
                        else if($result->category==3)
                        {
                            $category="European";
                        }
                        $tempArray[] = $category;
                        $tempArray[] = apply_float_value($result->length);
                        $tempArray[] = apply_float_value($result->width);
                        $tempArray[] = apply_float_value($result->height);
                        $tempArray[] = apply_float_value($result->max_weight);
                        $tempArray[] = $result->quantity;
                        $viewActionButton = View::make('totes.action-buttons', ['object' => $result]);
                        $tempArray[]      = $viewActionButton->render();
                        return $tempArray;
                    });
                }
                
                $jsonData = [
                    "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
                    "recordsTotal"    => $totes->total(), // Total number of records
                    "recordsFiltered" => $totes->total(),
                    "data"            => $data // Total data array
                ];
                return response()->json($jsonData);
        } 
        catch (Exception $ex) {
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
        try{
           $totes_model=new Totes;
           $totes_model->name = $request->name;
           $totes_model->length = $request->length;
           $totes_model->width = $request->width;
           $totes_model->height = $request->height;
           $overall=round((($request->length*$request->width*$request->height)/1000000),2);
           $totes_model->max_volume = $overall;
           $totes_model->max_weight = $request->max_weight;
           $totes_model->quantity = $request->quantity;
           $totes_model->category = $request->category;
           //$totes_model->barcode = $request->barcode;
           $totes_model->created_by = $request->user->id;
           $totes_model->modified_by = $request->user->id;
           if($totes_model->save()){
               return $this->sendResponse(trans('messages.api_responses.totes_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.totes_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function show(Totes $totes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function edit(Totes $totes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $id)
    {
        try
       {
           $totes_model= Totes::find($id);
           $totes_model->name = $request->name;
           $totes_model->length = $request->length;
           $totes_model->width = $request->width;
           $totes_model->height = $request->height;
           $overall=round((($request->length*$request->width*$request->height)/1000000),2);
           $totes_model->max_volume = $overall;
           $totes_model->max_weight = $request->max_weight;
           $totes_model->quantity = $request->quantity;
           //$totes_model->barcode = $request->barcode;
           $totes_model->category = $request->category;
           $totes_model->modified_by = $request->user->id;
           if($totes_model->save()){
               return $this->sendResponse(trans('messages.api_responses.totes_edit_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.totes_edit_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $totes=Totes::find($request->id);
        if($totes->delete()){
            return $this->sendResponse(trans('messages.api_responses.totes_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.totes_delete_error'), 422);
        }
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->ids;
        
         if(Totes::whereIn('id',explode(",",$ids))->delete()){
            return $this->sendResponse(trans('messages.api_responses.totes_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.totes_delete_error'), 422);
        }
    }
}
