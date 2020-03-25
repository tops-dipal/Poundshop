<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Range\CreateRequest;
use App\Http\Requests\Api\Range\UpdateRequest;
use Illuminate\Support\Facades\View;
use Auth;
use App\Range;
use Carbon\Carbon;
class RangeController extends Controller
{
    function buildCategoryTree($elements = array(), $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {

            if ($element['parent_id'] == $parentId) 
            {

                $children = $this->buildCategoryTree($elements, $element['id']);
                $mapData=count($element['magento_categories']);
                if($mapData!=0)
                {
                    $element['map_status']="Mapped";
                   
                }
                else
                {
                     $element['map_status']="Not Mapped";
                }
                $element['edit_url']=route('range.edit',$element['id']);
                if ($children) {
                    $element['child_status']=1;
                    
                    $element['children'] = $children;

                }
                else
                {
                    $element['child_status']=0;
                }

                $branch[] = $element;

            }

        }
        return $branch;
    }
    public function index(Request $request)
    {
        $allRanges =  Range::getAllRangeWithMappedCategory()->toArray();
        $parent = $this->buildCategoryTree($allRanges);
        $process='add';
      
        return response()->json(['view' => view('range.range_list',compact('parent','process'))->render()]); 
    }
   
    public function store(CreateRequest $request)
    {
    	try{
           $range_model=new Range;
           $i=0;
           $categories=$request->category_name;
           $stockDays=$request->stock_hold_days;
           $seaseonalStatusArr=$request->seasonal_status;
           $fromMonthArr=$request->seasonal_range_frommonth;
           $toMonthArr=$request->seasonal_range_tomonth;
           $fromDateArr=$request->seasonal_range_fromdate;
           $toDateArr=$request->seasonal_range_todate;
           $keyForStatusArr=0;
           foreach ($categories as $key => $value) {
             if($value)
              {
                $storeCatArr[$i]['category_name']=$value;
                $storeCatArr[$i]['stock_hold_days']=$stockDays[$key];
                
                  $storeCatArr[$i]['parent_id'] = (empty($request->selected_parent) && ($request->selected_parent==0)) ? NULL: $request->selected_parent;
                
              
              if($storeCatArr[$i]['parent_id']!=Null)
              {
                $range=Range::find($storeCatArr[$i]['parent_id']);
                $storeCatArr[$i]['path']=$range->getParentPath().' >> '.$storeCatArr[$i]['category_name'];
              }
              else
              {
                 $storeCatArr[$i]['path']=$storeCatArr[$i]['category_name'];
              }
              
              if(!isset($seaseonalStatusArr[$keyForStatusArr]))
              {
                $keyForStatusArr++;
              }

              $storeCatArr[$i]['seasonal_status']=$seaseonalStatusArr[$keyForStatusArr];
               if($storeCatArr[$i]['seasonal_status']=="1")
               {
                //return $toDateArr[$key];
                   $fromDate = Carbon::createFromFormat('Y-m-d', '0000-'.$fromMonthArr[$keyForStatusArr].'-'.$fromDateArr[$keyForStatusArr]);
                   $toDate = Carbon::createFromFormat('Y-m-d', '0000-'.$toMonthArr[$keyForStatusArr].'-'.$toDateArr[$keyForStatusArr]);
                   $storeCatArr[$i]['seasonal_from'] = $fromDate;
                  $storeCatArr[$i]['seasonal_to'] = $toDate;
                 
               }
               else
               {
                $storeCatArr[$i]['seasonal_from'] = Null;
                  $storeCatArr[$i]['seasonal_to'] = Null;
               }
            $storeCatArr[$i]['created_by'] = $request->user->id;
            $storeCatArr[$i]['modified_by'] = $request->user->id;
            //print_r($storeCatArr[$i]);
                $i++;

                $keyForStatusArr++;
              }
           }
           
           if(Range::insert($storeCatArr)){
               return $this->sendResponse(trans('messages.api_responses.range_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.range_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function update(UpdateRequest $request,$id)
    {
    	try{
          
           $range_model=Range::find($id);
           $range_model->category_name = $request->category_name[0];
           $range_model->stock_hold_days = $request->stock_hold_days[0];
           if(isset($request->stock_hold_days[0]))
           {
            $this->updateProductStockDay($id,$request->stock_hold_days[0]);
           }
           if($request->selected_parent!=$id)
           {
            $range_model->parent_id = $request->selected_parent;
           }
           else
           {
            $range_model->parent_id = NULL;
           }
           if($range_model->parent_id!=NULL)
            {
                $range=Range::find($range_model->parent_id);
                $range_model->path=$range->getParentPath().' >> '.$range_model->category_name;
            }
            else
            {
                $range_model->path=$range_model->category_name;
            }
           $range_model->seasonal_status = $request->seasonal_status;
           if($range_model->seasonal_status==1)
           {
	           $fromDate = Carbon::createFromFormat('Y-m-d', '0000-'.$request->seasonal_range_frommonth.'-'.$request->seasonal_range_fromdate);
	           $toDate = Carbon::createFromFormat('Y-m-d', '0000-'.$request->seasonal_range_tomonth.'-'.$request->seasonal_range_todate);
	           $range_model->seasonal_from = $fromDate;
	           $range_model->seasonal_to = $toDate;
           }
          
           $range_model->modified_by = $request->user->id;
           if($range_model->save()){
               return $this->sendResponse(trans('messages.api_responses.range_edit_success'), 200,array('reset'=>1));
           }else{
               return $this->sendError(trans('messages.api_responses.range_edit_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function destroy(Request $request)
    {
        $range=Range::find($request->id);
        if($range->delete()){
        	$childRanges=Range::where('parent_id',$request->id)->delete();
            return $this->sendResponse(trans('messages.api_responses.range_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.range_delete_error'), 422);
        }
    }

    public function searchByKeyword(Request $request)
    {
      if(!empty($request->keyword))
      {
        $data = [];

        $resp_msg = "No range found with search.";

        $data['ranges'] = Range::searchRange($request->keyword);
        
        if(!empty($data['ranges']))
        {
          $resp_msg = 'Range found with given search.';
        }  

        return $this->sendResponse($resp_msg, 200, $data);
      } 
      else
      {
        return $this->sendValidation(array('Please enter keyword.'), 422);
      } 
    }   

    public function updateProductStockDay($rangeId,$stockDays)
    {
        $updateArr=array();
        $updateArr['stock_hold_days']=$stockDays;
        \App\Products::where('buying_category_id',$rangeId)->where('is_override',1)->update($updateArr);
        return;
    } 

}
