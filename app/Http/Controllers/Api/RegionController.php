<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Country;
use App\State;
use App\City;

class RegionController extends Controller
{
    public function getAllCountry(Request $request)
    {
    	$countries=Country::select('id','name','sortname','phonecode')->cursor();
    	$jsonData=['status'=>'success',
    			'code'=>'200',
    			'data'=>$countries
    		];
    	return response()->json($jsonData);
    }

    public function getAllState(Request $request)
    {
    	
    	$states=State::select('id','name','country_id')->where('country_id',$request->id)->orderBy('name','ASC')->cursor();
    	$jsonData=['status'=>'success',
    			'code'=>'200',
    			'data'=>$states
    		];
    	return response()->json($jsonData);
    }

    public function getAllStateAndAdd(Request $request)
    {
        
        $states=State::select('id','name','country_id')->where('country_id',$request->id)->cursor();
        $jsonData=['status'=>'success',
                'code'=>'200',
                'data'=>$states
            ];
        return response()->json($jsonData);
    }

    public function getAllCity(Request $request)
    {
    	$cities=City::select('id','name','state_id')->where('state_id',$request->id)->cursor();
    	$jsonData=['status'=>'success',
    			'code'=>'200',
    			'data'=>$cities
    		];
    	return response()->json($jsonData);
    }

    public function getCityList(Request $request)
    {
       $state=State::select('id','name','country_id')->where('name',$request->state_name)->where('country_id',$request->country_id)->first();
       if(!empty($state))
       {
            $cities=City::select('id','name','state_id')->where('state_id',$state->id)->orderBy('name','ASC')->cursor();
       }
       else
       {
            $cities=array();
       }
       
        $jsonData=['status'=>'success',
                'code'=>'200',
                'data'=>$cities,
            ];
        return response()->json($jsonData);
    }

    
}
