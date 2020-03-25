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

    public function getStaticDataForRangeBudget()
    {
        $data=array();
        $i=0;
        $budgetarray=array('instock'=>rand(1000,5000),'Jan'=>rand(10,100),'Feb'=>rand(10,100),'March'=>rand(10,100),'April'=>rand(10,100),'May'=>rand(10,100),'Jun'=>rand(10,100),'July'=>rand(10,100),'Aug'=>rand(10,100),'Sep'=>rand(10,100),'Oct'=>rand(10,100),'Nov'=>rand(10,100),'Dec'=>rand(10,100));
        $parentCatArra=['Food & Drinks','Health & Beauty'];
        for($i=0;$i<2;$i++)
        {
            $data[$i]['category_name']=$parentCatArra[$i];
          
            if($i==1)
            {   
                $childArr=array('Cosmetics','Hair Care','Deodrant/Body Spray','Shaving/Hair Removal');
                 for($j=0;$j<count($childArr);$j++)
                {
                      $data[$i]['child'][$j]['category_name']=$childArr[$j];
                    if($j==2)
                    {
                        $childArr1=array('Shower/Bath');
                        for($k=0;$k<1;$k++)
                        {
                            $data[$i]['child'][$j]['subchild'][$k]['category_name']=$childArr1[$k];
                            $data[$i]['child'][$j]['subchild'][$k]['budget']=$budgetarray;
                        }
                    }
                    else
                    {
                      
                        $data[$i]['child'][$j]['budget']=$budgetarray;
                    }
                }
                $data[$i]['total']=$budgetarray;
            }
            if($i==0)
            {
                $childArr=array('Breakfast','Sweets');

                for($j=0;$j<count($childArr);$j++)
                {
                    $data[$i]['child'][$j]['category_name']=$childArr[$j];
                    $childArr1=array('Food Cupboard');
                    if($j==0)
                    {
                        for($k=0;$k<1;$k++)
                        {
                            $data[$i]['child'][$j]['subchild'][$k]['category_name']=$childArr1[$k];
                            $data[$i]['child'][$j]['subchild'][$k]['budget']=$budgetarray;
                        }
                    }
                    else
                    {
                        $data[$i]['child'][$j]['budget']=$budgetarray;
                    
                    }
                   
                }

                $data[$i]['total']=$budgetarray;
            }
        }
        $result=array();
        $result['data']=$data;
        return $result;
    }

    
}
