<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Response;

class RoleController extends Controller
{
    //
    /**
	 * User Login
	 * @author Shubham
	 * @param void()
	 * @return type
	 * @throws type
	*/
    function permissionList()
    {
    	try {
	    	$result = Permission::get();
	    	$result = makeNulltoBlank($result); 
	    	return $this->sendResponse('Success', 200, $result);
    	} 
    	catch (Exception $ex) {
            return $this->sendError($ex->getMessage(), 400);
        }
    }

}
