<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\QCChecklist;
use Illuminate\Support\Facades\View;
use Carbon\Carbon;
use App\ChecklistPoint;
use App\Http\Requests\Api\QC\CreateRequest;
use App\Http\Requests\Api\QC\UpdateRequest;
use PDF;
class QCCheckListController extends Controller
{
    public function index(Request $request)
    {
    	try
        {
        
           $columns=[
                    0 => 'id',
                    1 => 'name',
                    
            ];
            $params  = array(
                 'order_column'    => $columns[$request->order[0]['column']],
                 'order_dir'       => $request->order[0]['dir'],
                 'search'          => $request->search['value']
            );

            $codes=QCChecklist::getAllQCChecklist($request->length, $params);
                
            $data = [];
            
            if (!empty($codes)) {
                    $data = $codes->getCollection()->transform(function ($result) use ($data) {
                        $tempArray   = array();
                        $tempArray[] = View::make('components.list-checkbox',['object'=>$result])->render();
                        $tempArray[] = View::make('components.list-title',['title'=>$result->name,'edit_url'=>route('qc-checklist.edit',$result->id),'btn_title'=>trans('messages.qc.edit_checklist')])->render();
                        $tempArray[] =($result->created_at!=NULL) ? Carbon::createFromFormat('Y-m-d H:i:s', $result->created_at)->format('d-M-Y, h:i A') : '-'; 
                       
                        $viewActionButton = View::make('qc-checklist.action-buttons', ['object' => $result]);
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
        try
       {
            $points=$request->checklist_points;
           $object_model=new QCChecklist;
           $object_model->name = $request->name;
           $object_model->created_by = $request->user->id;
           $object_model->modified_by = $request->user->id;

           if($object_model->save()){
            $pointStorArr=array();
               foreach ($points as $key => $value) {
                   $pointStorArr[$key]['title']=$value;
                   $pointStorArr[$key]['created_by']=$request->user->id;
                   $pointStorArr[$key]['modified_by']=$request->user->id;
                   $pointStorArr[$key]['qc_id']=$object_model->id;
                   $pointStorArr[$key]['created_at']=Carbon::now();
                   $pointStorArr[$key]['updated_at']=Carbon::now();
               }
               if(!empty($pointStorArr))
               {
                    ChecklistPoint::insert($pointStorArr);
               }
               return $this->sendResponse(trans('messages.api_responses.qc_add_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.qc_add_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }
    
    public function update(UpdateRequest $request)
    {
        try
       {
        //return $request->remove_points_id;
          $points=$request->checklist_points;
           $object_model=QCChecklist::find($request->id);
           $object_model->name = $request->name;
           $object_model->modified_by =$request->user->id;
           $updateStatus=0;
           if(count($request->remove_points_id)>0 && !empty($request->remove_points_id))
           {
            ChecklistPoint::whereIn('id',$request->remove_points_id)->where('qc_id',$request->id)->delete();
           }
           if($object_model->update()){
            $pointStorArr=array();
            if(!empty($points))
            {
                foreach ($points as $key => $value) {
                    //echo $request->checklist_pointsId[$key];exit;
                    if(isset($request->checklist_pointsId[$key]))
                    {
                       $point=ChecklistPoint::find($request->checklist_pointsId[$key]);
                       $pointUpdateArr[$key]=array();
                       $pointUpdateArr[$key]['title']=$value;
                       $pointUpdateArr[$key]['modified_by']=$request->user->id;
                      if($point->update($pointUpdateArr[$key]))
                      {
                        $updateStatus=1;
                      }
                      
                    }
                    else
                    {
                       $pointStorArr[$key]['title']=$value;
                       $pointStorArr[$key]['created_by']=$request->user->id;
                       $pointStorArr[$key]['modified_by']=$request->user->id;
                       $pointStorArr[$key]['qc_id']=$request->id;
                       $pointStorArr[$key]['created_at']=Carbon::now();
                       $pointStorArr[$key]['updated_at']=Carbon::now();
                    }
               }
               if(!empty($pointStorArr))
               {
                    ChecklistPoint::insert($pointStorArr);
               }
              
            }
            return $this->sendResponse(trans('messages.api_responses.qc_edit_success'), 200);
           }else{
               return $this->sendError(trans('messages.api_responses.qc_edit_error'), 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }
    public function destroy(Request $request)
    {
        $qc=QCChecklist::find($request->id);
        if($qc->delete()){
            return $this->sendResponse(trans('messages.api_responses.qc_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.qc_delete_error'), 422);
        }
    }
     public function destroyMany(Request $request)
    {
        $ids = $request->ids;
        $points=\App\ChecklistPoint::whereIn('qc_id',explode(",",$ids))->pluck('id')->toArray();
         if(QCChecklist::whereIn('id',explode(",",$ids))->delete()){
            \App\ChecklistPoint::whereIn('id',$points)->delete();
            return $this->sendResponse(trans('messages.api_responses.qc_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.qc_delete_error'), 422);
        }
    }
    public function destroyPoints(Request $request)
    {
        $point=\App\ChecklistPoint::find($request->id);

        if($point->delete()){
            return $this->sendResponse(trans('messages.api_responses.qc_delete_success'), 200);
        }else{
             return $this->sendError(trans('messages.api_responses.qc_delete_error'), 422);
        }
    }

    
    public function getChecklistPoints(Request $request)
    {

        $qc=\App\QCChecklist::with(array('checklistPoints'=>function($query){
            $query->select('*');
        }))->whereIn('id',explode(",", $request->qc_ids))->get();
        $selectedQcList=\App\BookingQcChecklist::where('product_id',$request->product_id)->pluck('qc_list_id')->toArray();
        $product_id=$request->product_id;
        $booking_id=$request->booking_id;
        return response()->json(['view' => view('material_receipt.ajax-checklist-points',compact('qc','product_id','booking_id','selectedQcList'))->render()]); 
    }
    
}
