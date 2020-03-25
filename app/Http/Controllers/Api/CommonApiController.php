<?php
 
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Http\Requests\Api\Common\CreateRequest;

class CommonApiController extends Controller
{
	/**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */

    function __construct(Request $request)
    {
        $route = $request->route();
        
        if(!empty($route))
        {   
            $action_array = explode('@',$route->getActionName());
            
            $function_name = !empty($action_array[1]) ? $action_array[1] : ''; 
            
            if(!empty($function_name))
            { 
                if($function_name == 'find')
                {   
                    CreateRequest::$roles_array = [
                                        'model' => 'required',
                                        'p_id'  => 'required',
                                      ];
                }

                if($function_name == 'get')
                {   
                    CreateRequest::$roles_array = [
                                        'model' => 'required',
                                      ];
                }

                if($function_name == 'helperFunction')
                {
                	CreateRequest::$roles_array = [
                                        'function' => 'required',
                                      ];
                }

                if($function_name == 'paramVariables')
                {
                    CreateRequest::$roles_array = [
                                        'variable' => 'required',
                                      ];
                }	
            }
        }
    }

    public function find(CreateRequest $request)
    {
    	try
        {
	    	$model = $request->model;
	    	
	    	$p_id = $request->p_id;
	    	
	    	$model = 'App\\'.$model;

	    	if(class_exists($model))
	    	{	
	    		$model = new $model;

		    	$data = $model->findorfail($p_id);
		    	
		    	if(!empty($data))
		    	{	
		    		return $this->sendResponse('Record found', 200, $data);	
		    	}
		    	else
		    	{
		    		return $this->sendError('No record found.', 400);
		    	}
		    }
		    else
		    {
		    	return $this->sendError('Model does not exist.', 400);
		    }
	    }
	    catch (Exception $ex) 
        {
            return $this->sendError($ex->getMessage(), 400);
        }	
    }            

    public function get(CreateRequest $request)
    {
    	try
        {
	    	$model = $request->model;
	    	
	    	$p_id = $request->p_id;
	    	
	    	$model = 'App\\'.$model;

	    	if(class_exists($model))
	    	{	
	    		$model = new $model;

		    	$data = $model->get();
		    	
		    	if(!empty($data))
		    	{	
		    		return $this->sendResponse('Record found', 200, $data);	
		    	}
		    	else
		    	{
		    		return $this->sendError('No record found.', 400);
		    	}
		    }
		    else
		    {
		    	return $this->sendError('Model does not exist.', 400);
		    }
	    }
	    catch (Exception $ex) 
        {
            return $this->sendError($ex->getMessage(), 400);
        }	
    }

    public function helperFunction(CreateRequest $request)
    {
    	try
    	{
    		$function_name = $request->function;
    		
    		if (function_exists($function_name))
			{	

				$data = $function_name();

				if(!empty($data))
		    	{	
		    		return $this->sendResponse('Record found', 200, $data);	
		    	}
		    }

		    return $this->sendError('No record found.', 400);	
    	}	
	    catch (Exception $ex) 
        {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

    public function paramVariables(CreateRequest $request)
    {
        try
        {

            $data = config('params.'.$request->variable);
            
            if(!empty($data))
            {    
                return $this->sendResponse('Record found', 200, $data); 
            }

            return $this->sendError('No record found.', 400);   
        }   
        catch (Exception $ex) 
        {
            return $this->sendError($ex->getMessage(), 400);
        }
    }


}	