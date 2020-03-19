<?php

namespace App\Http\Controllers\Api;
use App\Locations;
use App\LocationsSetting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
//use App\Http\Requests\Api\Cartons\CreateRequest;
//use App\Http\Requests\Api\Cartons\UpdateRequest;
use App\Http\Requests\Api\Common\CreateRequest;
use Illuminate\Support\Facades\View;
use Batch;

class LocationsController extends Controller
{
    function __construct()
    {
        CreateRequest::$roles_array = [];
    }
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
          1 => 'aisle',
          2 => 'rack',
          3 => 'floor',
          4 => 'box',
          5 => 'location',
          6 => 'type_of_location',
          7 => 'case_pack',                
          8 => 'length',                
          9 => 'width',                
          10 => 'height',                
          11 => 'cbm',                
          12 => 'storable_weight',
          13 => 'status'                
        ];
        
        $adv_search_array=array();
        if(!empty($request->advanceSearch))
        {
          parse_str($request->advanceSearch, $adv_search_array);
        }        

        $params  = array(
          'order_column'    => $columns[$request->order[0]['column']],
          'order_dir'       => $request->order[0]['dir'],
          'search'          => $request->search['value'],
          'advance_search'  => $adv_search_array,
        );
        
        $locations=Locations::getAllLocations($request->length, $params);                
        $data = [];
        
        if (!empty($locations)) 
        {
          $data = $locations->getCollection()->transform(function ($result) use ($data,$params) 
          {
            $tempArray   = array();
            $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
            $tempArray[] = '<a href="javascript:void(0);" onclick=edit_location("'.$result->id.'")>'.ucwords($result->aisle).'</a>';
            $tempArray[] = ucwords($result->rack);
            $tempArray[] = ucwords($result->floor);
            $tempArray[] = ucwords($result->box);
            $tempArray[] = ucwords($result->location);
            $location_type = LocationType();  //call to array helper function                        
            $html='<select id="location_type" class="form-control location_type" style="width:150px;" name="location_type" data-id="'.$result->id.'">';
            $i=1;
            if(!empty($location_type))
            {
              foreach($location_type as $row)
              {
                if($result->type_of_location==$i)
                {
                  $html.='<option value="'.$i.'" selected title="'.$row.'" >'.ucwords($row).'</option>';
                }
                else
                {
                  $html.='<option value="'.$i.'" title="'.$row.'">'.ucwords($row).'</option>'; 
                }
                $i++;
              }
            }
            $html.='</select>';            
            $tempArray[] = $html;

            $html1='<select id="case_pack" class="form-control case_pack" style="width:60px;" name="case_pack" data-id="'.$result->id.'">';            
            $html1.='<option value="0" title="No">No</option>';
            if($result->case_pack=='1')
            {            
              $html1.='<option value="1" selected title="Yes">Yes</option>';
            }
            else
            {
              $html1.='<option value="1" title="Yes">Yes</option>'; 
            }
            $html1.='</select>'; 
            $tempArray[] = $html1;
            $tempArray[] = !empty($result->length)?$result->length:'0';
            $tempArray[] = !empty($result->width)?$result->width:'0';
            $tempArray[] = !empty($result->height)?$result->height:'0';
            $tempArray[] = !empty($result->cbm)?$result->cbm:'0';
            $tempArray[] = !empty($result->storable_weight)?$result->storable_weight:'0';            
            $tempArray[] = !empty($result->status)?'Active':'Inactive';
            $viewActionButton = View::make('locations.action-buttons', ['object' => $result,'myparam' => $params]);
            $tempArray[]      = $viewActionButton->render();
            return $tempArray;
          });
        }
        
        $jsonData = [
            "draw"            => intval($request->draw), // For every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw.
            "recordsTotal"    => $locations->total(), // Total number of records
            "recordsFiltered" => $locations->total(),
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
        //dd($request->toArray());
        $pallet_model=new Locations;        
        
        //aisle data
        $aisle = $request->aisle;        
        $aisle_range_start = $request->aisle_range;         
        //check range whether it is character or numeric
        $aisle_range_start_last=substr($aisle_range_start, -1);   
        $aisle_range_prefix=substr($aisle_range_start, 0, -1);     
        $aisle_array=$this->generate_excel_cell_names($aisle,$aisle_range_start_last);

        //racks data
        $rack = $request->rack;
        $rack_range_start = $request->rack_range;
        //check range whether it is character or numeric
        $rack_range_start_last=substr($rack_range_start, -1);
        $rack_range_prefix=substr($rack_range_start, 0, -1);
        $rack_array=$this->generate_excel_cell_names($rack,$rack_range_start_last);

        //floor data
        $floor = $request->floor;
        $floor_range_start = $request->floor_range;
        //check range whether it is character or numeric
        $floor_range_start_last=substr($floor_range_start, -1);
        $floor_range_prefix=substr($floor_range_start, 0, -1);
        $floor_array=$this->generate_excel_cell_names($floor,$floor_range_start_last);

        //box data
        $box = $request->box;
        $box_range_start = $request->box_range;
        //check range whether it is character or numeric
        $box_range_start_last=substr($box_range_start, -1);
        $box_range_prefix=substr($box_range_start, 0, -1);
        $box_array=$this->generate_excel_cell_names($box,$box_range_start_last);        

        $complete_array=array();     

        //get all the location exist
        $locations = array_column($complete_array, 'location');
        $exist_locations = $pallet_model->get();   
        $exist_locations_array=array();
        if(!empty($exist_locations))
        {
          foreach($exist_locations as $newr)
          {
            $exist_locations_array[]=$newr->location;
          }
        }  

        $insert_status=0;      
        
        if(!empty($aisle) && !empty($rack) && !empty($floor) && !empty($box))
        {
          foreach($aisle_array as $row)
          {
            foreach($rack_array as $row1)
            {
              foreach($floor_array as $row2)
              {
                foreach($box_array as $row3)
                {
                  $aisle_data='';
                  if(is_numeric($row))
                  { 
                    $aisle_data= $aisle_range_prefix.sprintf('%02d',$row);
                  }
                  else
                  { 
                    $aisle_data= $aisle_range_prefix.$row;
                  }

                  $rack_data='';
                  if(is_numeric($row1))
                  { 
                    $rack_data= $rack_range_prefix.sprintf('%02d',$row1);
                  }
                  else
                  { 
                    $rack_data= $rack_range_prefix.$row1;
                  }

                  $floor_data='';
                  if(is_numeric($row2))
                  { 
                    $floor_data= $floor_range_prefix.sprintf('%02d',$row2);
                  }
                  else
                  { 
                    $floor_data= $floor_range_prefix.$row2;
                  }

                  $box_data='';
                  if(is_numeric($row3))
                  { 
                    $box_data= $box_range_prefix.sprintf('%02d',$row3);
                  }
                  else
                  { 
                    $box_data= $box_range_prefix.$row3;
                  }

                  $location_data=$aisle_data.$rack_data.$floor_data.$box_data;
                  if(in_array($location_data,$exist_locations_array))
                  {
                    $insert_status=2;                 
                  }
                  else
                  {
                    $complete_array[] = array(
                      'site_id'=>$request->site_id,
                      'aisle'=>$aisle_data,
                      'rack'=>$rack_data,
                      'floor'=>$floor_data,
                      'box'=>$box_data,
                      'location'=>$location_data,
                      'created_by'=>$request->user->id
                    );
                  }
                }
              }
            }
          }
        }        
                
        if(!empty($complete_array))
        {
          if($pallet_model->insert($complete_array))
          {
            if(!empty($insert_status))
            {
              return $this->sendResponse('Some of locations are already exist other are created successfully', 200);            
            }
            else
            {
              return $this->sendResponse('Locations has been created successfully', 200);
            }
          }
          else
          {
            return $this->sendError('Locations does not created, please try again', 422);
          }
        }
        else
        {
          return $this->sendError('Locations does not created, locations are already exist.', 422);
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
    public function update(Request $request, $id)
    {
       try
       {
           $location_model= Locations::find($id);
           // $pallet_model->name = $request->name;
           // $pallet_model->length = $request->length;
           // $pallet_model->width = $request->width;
           // $pallet_model->height = $request->height;
           // $pallet_model->max_volume = ($request->length*$request->width*$request->height);
           // $pallet_model->max_weight = $request->max_weight_carry;
           // $pallet_model->quantity = $request->qty;           
           // $pallet_model->created_by = $request->user->id;
           // $pallet_model->modified_by = $request->user->id;
           if($location_model->save()){
               return $this->sendResponse('Locations has been updated successfully', 200);
           }else{
               return $this->sendError('Locations did not updated, please try again', 422);
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
        $location=Locations::find($request->id);
        if($location->delete())
        {
          return $this->sendResponse('Locations has been deleted successfully', 200);
        }
        else
        {
          return $this->sendError('Locations did not deleted, please try again', 422);
        }
    }

    public function destroyMany(Request $request)
    {
        $ids = $request->ids;        
        if(Locations::whereIn('id',explode(",",$ids))->delete())
        {
          return $this->sendResponse('Locations has been deleted successfully', 200);
        }
        else
        {
           return $this->sendError('Locations did not deleted, please try again', 422);
        }
    }

    public function generate_excel_cell_names($col_cnt,$excel_col)
    {
      $excel_cells = [];    
      for ($col_index = 1; $col_index <= $col_cnt; $col_index++)
      {
        $excel_cells[$col_index] = $excel_col;
        $excel_col++;
      }    
      return $excel_cells;
    }

    public function activeMany(Request $request)
    {
        $ids = $request->ids;  
        $ids=explode(',',$ids);
        $pallet_model=new Locations;   
        $sup_update_location=array();
        if(!empty($ids))
        {
          foreach($ids as $row)
          {
            $sup_update_location[] = array(
              'id'=>$row,
              'status'=>'1',
            );
          }
        }  

        $data=Batch::update($pallet_model, $sup_update_location, 'id');
        if(!empty($data) || $data==0)
        {
          return $this->sendResponse('Locations has been actived successfully', 200);
        }
        // else if($data==0)
        // {
        //   return $this->sendResponse('All Locations are already active', 200);
        // }
        else
        {
           return $this->sendError('Locations did not active, please try again', 422);
        }
    }

    public function inactiveMany(Request $request)
    {
        $ids = $request->ids;  
        $ids=explode(',',$ids);
        $pallet_model=new Locations;   
        $sup_update_location=array();
        if(!empty($ids))
        {
          foreach($ids as $row)
          {
            $sup_update_location[] = array(
              'id'=>$row,
              'status'=>'0',
            );
          }
        }  

        $data=Batch::update($pallet_model, $sup_update_location, 'id');
        if(!empty($data) || $data==0)
        {
          return $this->sendResponse('Locations has been inactived successfully', 200);
        }
        // else if($data==0)
        // {
        //   return $this->sendResponse('All Locations are already inactive', 200);
        // }
        else
        {
           return $this->sendError('Locations did not inactive, please try again', 422);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function locationSetting(CreateRequest $request)
    {
      try
      {
        //dd($request->toArray());
        $id='';
        if(!empty($request->id))
        {
          $id=$request->id;          
          $location_setting_model= LocationsSetting::find($id);                    
        }
        else
        {
          $location_setting_model=new LocationsSetting;
        }        
        
        $location_setting_model->dist_aisle_rack = $request->dist_aisle_rack;
        $location_setting_model->walk_speed = $request->walk_speed;
        $location_setting_model->time_multipick = $request->time_multipick;
        $location_setting_model->time_singlepick = $request->time_singlepick;        
        $location_setting_model->storage_buffer = $request->storage_buffer;        
        if($location_setting_model->save())
        {
          return $this->sendResponse('Settings has been saved successfully', 200);
        }
        else
        {
          return $this->sendError('Settings does not saved, please try again', 422);
        }
      } 
      catch (Exception $ex) 
      {
        return $this->sendError($ex->getMessage(), 400);
      }
    }

    public function inlineUpdate(Request $request)
    {      
      $record_id=$request->record_id;
      $location_type=$request->location_type;
      $case_pack=$request->case_pack;

      if(!empty($record_id))
      {
        $location_model= Locations::find($record_id);
        if(isset($location_type) && !empty($location_type))
        {
          $location_model->type_of_location=$location_type;
        }
        else
        {
          $location_model->case_pack=$case_pack;
        }

        if($location_model->save())
        {
           return $this->sendResponse('Location has been updated successfully', 200);
        }
        else
        {
           return $this->sendError('Location did not updated, please try again', 422);
        }
      }
      else
      {
        return $this->sendError('something went wrong, please try again', 422);
      }
    }
    
    public function rowUpdate(Request $request)
    {
      $record_id=$request->edit_record_id;      
      if(!empty($record_id))
      {
        $location_model= Locations::find($record_id);
        $location_model->type_of_location=isset($request->edi_location_type)?$request->edi_location_type:'';
        $location_model->case_pack=isset($request->edi_case_pack)?$request->edi_case_pack:'';
        $location_model->length=isset($request->edi_length)?$request->edi_length:0;
        $location_model->width=isset($request->edi_width)?$request->edi_width:0;
        $location_model->height=isset($request->edi_height)?$request->edi_height:0;
        $location_model->cbm=($location_model->length*$location_model->width*$location_model->height)/1000000;
        $location_model->storable_weight=isset($request->edi_stor_weight)?$request->edi_stor_weight:0;        
        if($location_model->save())
        {
           return $this->sendResponse('Location has been updated successfully', 200);
        }
        else
        {
           return $this->sendError('Location did not updated, please try again', 422);
        }
      }
      else
      {
        return $this->sendError('something went wrong, please try again', 422);
      }
    }

    public function rowCopy(Request $request)
    {
      $record_id=$request->record_id;      
      //get below row ids
      $order_column=$request->copy_order_column;
      $order_dir=$request->copy_order_dir;
      $search=$request->copy_search;
      $advanceSearch=unserialize($request->copy_advance_search);      
      $params  = array(
        'order_column'    => $order_column,
        'order_dir'       => $order_dir,
        'search'          => $search,
        'advance_search'  => $advanceSearch,
      );
      
      $locations=Locations::getAllLocationsSelected('5000', $params); //as of now limit taken to 5000   
      
      //copy to other rows below process
      $location_model=new Locations;   
      $sup_update_location=array();
      $check=0;
      if(!empty($locations))
      {
        foreach($locations as $row)
        {
          if($row->id==$record_id)
          {
            $check=1;
          }

          if(!empty($check) && $row->id!=$record_id)
          {
            $sup_update_location[] = array(
              'id'=>$row->id,
              'type_of_location'=>isset($request->location_type_val)?$request->location_type_val:'',
              'case_pack'=>isset($request->case_pack_val)?$request->case_pack_val:'',
              'length'=>isset($request->length_val)?$request->length_val:0,
              'width'=>isset($request->width_val)?$request->width_val:0,
              'height'=>isset($request->height_val)?$request->height_val:0,
              'cbm'=>isset($request->cbm_val)?$request->cbm_val:0,
              'storable_weight'=>isset($request->sto_weight_val)?$request->sto_weight_val:0,
            );
          }
        }
      }

      $data=Batch::update($location_model, $sup_update_location, 'id');
      if(!empty($data) || $data==0)
      {
        return $this->sendResponse('Locations has been copied successfully', 200);
      }      
      else
      {
         return $this->sendError('Locations did not copied, please try again', 422);
      }
    }
}
