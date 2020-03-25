<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LocationAssign;
use App\Locations;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\Http\Requests\Api\Common\CreateRequest;

class LocationAssignController extends Controller {


      function __construct() {
          CreateRequest::$roles_array = [];
      }
    public
            function index(Request $request) {
        try {
            $columns          = [
                0  => 'id',
                1  => 'ros',
                2  => 'stock_hold_days',
                3  => 'pick_stock_required',
                4  => 'total_pick',
                5  => 'total_bulk',
                6 => 'box_turn',
                7=>'',
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }
            //print_r($adv_search_array);exit;
            $params                                = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                'search'         => $request->search['value'],
               'advance_search' => $adv_search_array,
                'length'=>$request->length,
               'page'=>$request->page,
            );
           
            $productList                               = LocationAssign::getAllProductsWithAllocatedLocation($request->length, $params);
            //dd($productList);
            $data                                  = [];
            if (!empty($productList)) {

                $data = $productList->transform(function ($result) use ($data) {
                    $tags = $result->tags->pluck('name')->toArray();
                    //dd($tags);
                    foreach (product_logic_base_tags() as $db_tag_field => $tag_caption) {
                        $db_tag_field = 'is_' . $db_tag_field;

                        if ($result->$db_tag_field == 1) {
                            $tags[] = $tag_caption;
                        }
                    }
                    $totalPickLocationQty=0;
                    $totalBulkLocationQty=0;
                    $sumOfBoxes=0;
                   
                    $tempArray = array();                  
                    $tempArray[]  = View::make('location-assignment.listing-image', ['object' => $result])->render();
                
                    $tempArray[]  =$result->ros;
                    $tempArray[]  = '<a onclick="showDayStockModal('.$result->id.','.$result->ros.','.$result->stock_hold_days.','.$result->pick_stock_required.')"><p style="color:'.$result->day_stock_color.'">'.$result->stock_hold_days.'</p></a>' ;
                   $tempArray[]= $result->pick_stock_required;
                    $tempArray[]  = $result->total_pick;
                    $tempArray[]  = $result->total_bulk;
                    $tempArray[]  = ($result->box_turn!=0) ? round($result->box_turn, 2) : '-';
                    $tags=!empty($tags) ? implode(', ', $tags) : '-';
                    $tempArray[] = "<div class='location-assigned-container'><span class='assign-count'>".$result->total_in_pick_count."</span><a id='assign_location_pro_".$result->id."' href='javascript:void(0)' class='assign_location_pro' attr-product-id=".$result->id." attr-tags=".$tags." attr-title='".$result->title."' >assign<br/> location</a></div>";
                    return $tempArray;
                });
            }


            

            $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => $productList->total(), // Total number of records
                "recordsFiltered" => $productList->total(),
                "data"            => $data ,// Total data array,
               
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {

        }
    }

     //right sidebar data in location assign module
    public function assignedPickLocations(Request $request)
    {
        try {
         $columns          = [
                0 => 'location',
                1 => 'available_qty',
                2 => 'qty_fit_in_location',
               
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }
             $productId      = $adv_search_array['product_id'];

            $params         = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                
            );
            
            $assignedPickLocations = LocationAssign::getAssignedPickLocations($params,$productId);
           // dd($assignedPickLocations);
            $data           = [];
            
            if (!empty($assignedPickLocations)) {

                $data = $assignedPickLocations->transform(function ($result) use ($data, $productId) {
                    $tempArray = array();

                    $tempArray[] = '<div class="min-h-35">'.$result->location.'-'.LocationType($result->type_of_location).'</div>';
                    $tempArray[] = "<span class='pl-12'>".$result->available_qty.'<span>';
                    $tempArray[] = "<span class='pl-12'><input type='hidden' name=
                    'loc_ass_id[]' value='".$result->id."'><input type='text' value=".$result->qty_fit_in_location ." class='form-control col-lg-4 qty_fit_in_location' attr-location-assign-id=".$result->id." id='location_assign_".$result->id."' old-data-val=".$result->qty_fit_in_location." name='qty_fit_in_location[]'></span>";
                    $tempArray[]="<a onclick='deleteLocationAssign(".$result->id.")' class='btn-delete location_assing_delete'><span style='font-size: 16px' class='icon-moon icon-Cancel'></span></a>";

                    return $tempArray;
                });
            }
            $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => count($assignedPickLocations), // Total number of records
                "recordsFiltered" => count($assignedPickLocations),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public
            function emptyLocationsData(Request $request) {
        try {
            $columns          = [
                0 => 'id',
                1 => 'aisle',
                2 => 'location',
                3 => 'type_of_location',
                4 => 'length',
                5 => 'width',
                6 => 'height',
                7 => 'cbm',
                8 => 'qty_fit_loc',
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }

            $params         = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : '',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : '',
                'search'         => $request->search['value'],
                'advance_search' => $adv_search_array
            );
            $productId      = $adv_search_array['location_for_product_id'];
            $aisleData      = \App\Locations::groupBy('aisle')->pluck('aisle')->toArray();
            $emptyLocations = LocationAssign::getEmptyLocations($request->length, $params, $productId);

            $data           = [];
            $locationAssign = LocationAssign::where('product_id', $productId)->pluck('location_id')->toArray();
            //dd($emptyLocations);
            if (!empty($emptyLocations)) {

                $data = $emptyLocations->transform(function ($result) use ($data, $productId, $locationAssign) {
                    $tempArray = array();

                    $tempArray[] = '<div class="min-h-35">'.'<label class="fancy-checkbox"><input type="checkbox" name="location_id[]" value="' . $result->id . '"><span><i></i></span></label>'.'</div>';

                    $tempArray[] = $result->aisle;
                    $tempArray[] = $result->location;
                    $tempArray[] = LocationType($result->type_of_location);
                    $tempArray[] = $result->length;
                    $tempArray[] = $result->width;
                    $tempArray[] = $result->height;
                    $tempArray[] = $result->cbm;
                    $tempArray[] = $result->qty_fit_loc;

                    return $tempArray;
                });
            }
            $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => $emptyLocations->total(), // Total number of records
                "recordsFiltered" => $emptyLocations->total(),
                "data"            => $data // Total data array
            ];
            return response()->json($jsonData);
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public function store(CreateRequest $request)
    {
        try{
            $product_id=$request->location_for_product_id;
            $storeArr=array();
            foreach ($request->location_id as $key => $value) {
                $warehouse=\App\Warehouse::where('is_default',1)->first();
                if(!empty($warehouse))
                {
                    $storeArr[$key]['warehouse_id']=$warehouse->id;
                }
                
                $storeArr[$key]['product_id']=$product_id;
                $storeArr[$key]['location_id']=$value;
                $storeArr[$key]['available_qty']=0;
                $storeArr[$key]['qty_fit_in_location']=0;
                $storeArr[$key]['is_mannual']=1;
                $storeArr[$key]['created_by']=$request->user()->id;
                $storeArr[$key]['modified_by']=$request->user()->id;
                $storeArr[$key]['created_at']=Carbon::now();
                $storeArr[$key]['updated_at']=Carbon::now();
            }
            if(count($storeArr)>0)
            {
                LocationAssign::insert($storeArr);
                return  $this->sendError("Success", 200);
            }
            return $storeArr;
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

      public function update(CreateRequest $request)
    {
        try{
            /*$locationAssign=LocationAssign::find($request->id);
            if(!empty($locationAssign))
            {
                $locationAssign->qty_fit_in_location=$request->qty_fit_in_location;
                $locationAssign->modified_by=$request->user()->id;
                if($locationAssign->save())
                {
                    return $this->sendResponse('Record updated successfully', 200);
                }
            }*/
            $updateReqRecords=$request->qty_fit_in_location;
            $updateReqIds=$request->loc_ass_id;
            $recordUpdateAffected=0;
            for ($i=0; $i <count($updateArr) ; $i++) { 
                $updateArr=array();
                $updateArr['qty_fit_in_location']=$updateReqRecords[$i];
                $updateArr['modified_by']=$request->user->id;
                $updateId=$updateReqIds[$i];
                $updateStatus=LocationAssign::where('id',$updateId)->update($updateArr);
                if($updateStatus){
                    $recordUpdateAffected=$recordUpdateAffected++;
                }
            }
            if($recordUpdateAffected>0){
                 return $this->sendResponse('Records updated successfully', 200);
            }
            else
            {
                 return $this->sendError('Something went wrong,please try again', 200);
            }
           
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    //bulk update qty in fit location
    public function bulkUpdate(CreateRequest $request)
    {
         try{
            
            $updateReqRecords=$request->qty_fit_in_location;
            $updateReqIds=$request->loc_ass_id;
            $recordUpdateAffected=0;
            for ($i=0; $i <count($updateReqRecords) ; $i++) { 
                $updateArr=array();
                $updateArr['qty_fit_in_location']=$updateReqRecords[$i];
                $updateArr['modified_by']=$request->user->id;
                $updateId=$updateReqIds[$i];
                $updateStatus=LocationAssign::where('id',$updateId)->update($updateArr);
                if($updateStatus==1){
                    $recordUpdateAffected=$recordUpdateAffected+1;
                }
            }
           
            if($recordUpdateAffected>0){
                 return $this->sendResponse('Records Updated Successfully', 200);
            }
            else
            {
                 return $this->sendError('Something went wrong,please try again', 200);
            }
           
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public
            function destroy(Request $request) {
        try {
            $locationAssign = LocationAssign::find($request->id);
            if (!empty($locationAssign)) {
                if ($locationAssign->delete()) {
                    return $this->sendResponse('Record deleted successfully', 200);
                }
            }
        }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }


    public function detailsOfInnerOuterBarcode(Request $request)
    {
        try {
            $columns          = [
                0  => '',
                1  => 'qty_per_box',
                2  => 'total_boxes',
                3  => 'location',
                4  => 'min_day_stock_holding',
               
            ];
            $adv_search_array = array();
            if (!empty($request->advanceSearch)) {
                parse_str($request->advanceSearch, $adv_search_array);
            }
            //print_r($adv_search_array);exit;
            $params                                = array(
                'order_column'   => !empty($columns[$request->order[0]['column']]) ? $columns[$request->order[0]['column']] : 'qty_per_box',
                'order_dir'      => !empty($request->order[0]['dir']) ? $request->order[0]['dir'] : 'asc',
               'advance_search' => $adv_search_array,
                'length'=>$request->length,
               'page'=>$request->page,
            );
           
         
            $barcodeData=LocationAssign::getRecordForStockPopUp($params);
             $data                                  = [];
            if (!empty($barcodeData)) {
                $i=0;
                $data = $barcodeData->transform(function ($result) use ($data,$i) {
                    $tempArray=array();
                    $checked='';


                    $tempArray[]='<label class="fancy-radio mr-3"><input type="radio" name="day_stock_val" value="'.$result->min_day_stock_holding.'"/><span><i></i></span></label>';
                    $tempArray[]=$result->qty_per_box;
                    $tempArray[]=$result->total_boxes;
                    $tempArray[]=$result->location;
                    $tempArray[]=$result->min_day_stock_holding;
                    $i++;
                    return $tempArray;
                });
            
             $jsonData = [
                "draw"            => intval($request->draw),
                "recordsTotal"    => $barcodeData->total(), // Total number of records
                "recordsFiltered" => $barcodeData->total(),
                "data"            => $data ,// Total data array,
               
            ];
            return response()->json($jsonData);
        }
    }
        catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
        
    }

}
