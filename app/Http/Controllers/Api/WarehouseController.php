<?php
namespace App\Http\Controllers\Api;
use App\Warehouse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Cartons\CreateRequest;
use App\Http\Requests\Api\Cartons\UpdateRequest;
use Illuminate\Support\Facades\View;

class WarehouseController extends Controller
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
        2 => 'type',
        3 => 'contact_person',
        4 => 'phone_no'
      ];

      $params  = array(
        'order_column'    => $columns[$request->order[0]['column']],
        'order_dir'       => $request->order[0]['dir'],
        'search'          => $request->search['value']
      );
      
      $warehouse=Warehouse::getAllWarehouses($request->length, $params);            
      $data = [];
      
      if (!empty($warehouse)) 
      {
        $data = $warehouse->getCollection()->transform(function ($result) use ($data) 
        {
          $tempArray   = array();
          $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
          if(!empty($result->is_default))
          {
            $tempArray[] =View::make('components.list-title',['title'=>ucwords($result->name).' (Default) ','edit_url'=>route('warehouse.edit',$result->id),'btn_title'=>trans('messages.modules.warehouse_edit')])->render(); $result->name;
          }
          else
          {
            $tempArray[] =View::make('components.list-title',['title'=>ucwords($result->name),'edit_url'=>route('warehouse.edit',$result->id),'btn_title'=>trans('messages.modules.warehouse_edit')])->render(); $result->name; 
          }
          $tempArray[] = ucwords(WarehouseType($result->type));  //call to array helper function            
          $tempArray[] = ucwords($result->contact_person);
          $tempArray[] = $result->phone_no;              
          $viewActionButton = View::make('warehouse.action-buttons', ['object' => $result]);
          $tempArray[]      = $viewActionButton->render();
          return $tempArray;
        });
      }
          
      $jsonData = [
        "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
        "recordsTotal"    => $warehouse->total(), // Total number of records
        "recordsFiltered" => $warehouse->total(),
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
      if(isset($request->id) && !empty($request->id))
      {
        $warehouse_model= Warehouse::find($request->id);
      }
      else
      {
        $warehouse_model=new Warehouse;
      }
      
      $warehouse_model->name = $request->name;
      $warehouse_model->type = $request->type;
      $warehouse_model->contact_person = $request->contact_person;
      $warehouse_model->phone_no = $request->phone_no;           
      $warehouse_model->address_line1 = $request->address_line1;
      $warehouse_model->address_line2 = $request->address_line2;
      $warehouse_model->country = isset($request->country) ? $request->country : '0';
      if(isset($request->state_id))
       {
            $state=\App\State::where('name',$request->state_id)->where('country_id',$request->country)->first();
            if(empty($state))
            {
                $stateObj=new \App\State;
                $stateObj->name=$request->state_id;
                $stateObj->country_id=$request->country;
                $stateObj->save();
                $warehouse_model->state=$stateObj->id;
            }
            else
            {
                $warehouse_model->state = $state->id;
            }
       }
       if(isset($request->city_id))
       {
            $city=\App\City::where('name',$request->city_id)->where('state_id',$warehouse_model->state)->first();
          
            if(empty($city))
            {

                $cityObj=new \App\City;
                $cityObj->name=$request->city_id;
                $cityObj->state_id=$warehouse_model->state;
                $cityObj->save();
                $warehouse_model->city=$cityObj->id;
            }
            else
            {
                $warehouse_model->city = $city->id;
            }
       }           
      $warehouse_model->zipcode = $request->zipcode;
      $warehouse_model->is_default = $request->is_default;

      if(!empty($warehouse_model->is_default))
      {
         // Warehouse::update(array('is_default' => '0'));
        Warehouse::where('is_default', '=', 1)->update(['is_default' => 0]);
      }
      
      if(isset($request->id) && !empty($request->id))
      {
        $warehouse_model->modified_by = $request->user->id;
        if($warehouse_model->save())
        {
          $this->make_one_default();
          return $this->sendResponse('Warehouse has been updated successfully', 200);
        }
        else
        {
          return $this->sendError('Warehouse did not updated, please try again', 422);
        }
      }
      else
      {
        
        $warehouse_model->created_by = $request->user->id;           
        if($warehouse_model->save())
        {
          $this->make_one_default();
          return $this->sendResponse('Warehouse has been created successfully', 200);
        }else{
          return $this->sendError('Warehouse does not created, please try again', 422);
        }
      }
    } 
    catch (Exception $ex) 
    {
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
    
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request)
  {
    $warehouse=Warehouse::find($request->id);
    if($warehouse->delete())
    {
      $this->make_one_default();
      return $this->sendResponse('Warehouse has been deleted successfully', 200);
    }
    else
    {
      return $this->sendError('Warehouse did not deleted, please try again', 422);
    }
  }

  public function destroyMany(Request $request)
  {
    $ids = $request->ids;        
    if(Warehouse::whereIn('id',explode(",",$ids))->delete())
    {
      $this->make_one_default();
      return $this->sendResponse('Warehouse has been deleted successfully', 200);
    }
    else
    {
      return $this->sendError('Warehouse did not deleted, please try again', 422);
    }
  }

  public function make_one_default()
  {
    //check if anydefault exist or not in the system
    $warehouse_list=Warehouse::where('is_default','1')->get();    
    if(!empty($warehouse_list) && !empty($warehouse_list->toArray()))
    {

    }
    else
    {
      $warehouse_list=Warehouse::select('id')->orderBy('id','asc')->limit(1)->get();
      if(!empty($warehouse_list[0]['id']))
      {
        $warehouse_list_up= Warehouse::find($warehouse_list[0]['id']);
        $warehouse_list_up->is_default = '1';
        $warehouse_list_up->save();
      }      
    }
  }
}