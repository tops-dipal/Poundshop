<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Setting;
use Carbon\Carbon;
use App\Terms;
use App\Http\Requests\Api\Common\CreateRequest;
use App\Http\Requests\Api\Setting\UpdateRequest;

class SettingController extends Controller
{
	function __construct()
    {
        CreateRequest::$roles_array = [
                            'terms_pound_uk' => 'required','terms_pound_non_uk'=>"required"
                          ];
    }
    public function store(Request $request)
    {
    	
    	$success=0;
    	foreach ($request->all() as $key => $value) {
    		
    		$setting=Setting::where('column_key',$key)->first();
    		$updateArr=array();
    		$updateArr['column_val']=$value;
    		//return $updateArr;
    		if($setting->update($updateArr))
    		{
    			$success++;
    		}
    	}
    	if($success>0)
    	{
    		return $this->sendResponse('Record has been updated successfully', 200);
    	}
    }

    public function getTerms(Request $request)
    {
        $terms=\App\Terms::where('status',1)->first();
        $data=array();
        if($terms)
        {
            $data['terms']=$terms;
            $user=\App\User::find($terms->modified_by);
            $data['terms']->updated_by=$user->first_name.' '.$user->last_name;
            $data['terms_uk_updated_at']= Carbon::parse($terms->updated_at)->format('d-M-Y');
            $data['terms_import_updated_at']=Carbon::parse($terms->updated_at)->format('d-M-Y');
        }
        return $this->sendResponse('success', 200,$data);
    }

    public function storeTerms(CreateRequest $request)
    {
         try{

           $term_model=new Terms;
           $term_model->terms_pound_uk = $request->terms_pound_uk;
           $term_model->terms_pound_non_uk = $request->terms_pound_non_uk;
           $term_model->created_by = $request->user->id;
           $term_model->modified_by = $request->user->id;
           $term_model->status = 1;
           if($term_model->save()){
               return $this->sendResponse('Terms and Condition has been created successfully', 200);
           }else{
               return $this->sendError('Terms and Condition does not created, please try again', 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }

    public function updateTerms(UpdateRequest $request)
    {
         try{

           $term_model=Terms::find($request->id);
           $term_model->terms_pound_uk = $request->terms_pound_uk;
           $term_model->terms_pound_non_uk = $request->terms_pound_non_uk;
           $term_model->modified_by = $request->user->id;
           //$term_model->status = $request->status;
           if($term_model->save()){
               return $this->sendResponse('Terms and Condition has been updated successfully', 200);
           }else{
               return $this->sendError('Terms and Condition did not updated, please try again', 422);
           }
       } catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
       }
    }
}
