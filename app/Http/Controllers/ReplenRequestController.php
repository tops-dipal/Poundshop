<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Locations;

use App\Warehouse;

use App\Tags;

use App\Replen;
class ReplenRequestController extends Controller
{
    //
    public function index(Request $request)
    {
    	# code...
    	$priorityArr=priorityTypes();
    	$aisleData=Locations::groupBy('aisle')->where('status',1)->pluck('aisle')->toArray();
        $pickaisleData=Locations::whereIn('type_of_location',replenPickLocationType())->where('status',1)->groupBy('aisle')->pluck('aisle')->toArray();
        $bulkaisleData=Locations::whereIn('type_of_location',replenBulkLocationType())->where('status',1)->groupBy('aisle')->pluck('aisle')->toArray();
    	$siteData=Warehouse::select('name','id','is_default')->get();
    	$staticTags=product_logic_base_tags();

    	$dynamicTags=Tags::get();

        $lastCalledDate=\App\Cron::select('end_time')->where('cron_type','REPLEN_CRON')->WHERE('end_time','!=',NULL)->orderBy('id','desc')->first();
       // dd($lastCalledDate);
        if(!empty($lastCalledDate))
        {
            $cronDataLastUpdtedDate=dateFormateShowDate(strtotime($lastCalledDate->end_time));
        }
        else
        {
            $cronDataLastUpdtedDate=dateFormateShowDate();
        }
        $recordFetch=\App\Cron::select('id','cron_type')->where('cron_type','REPLEN_CRON');
        $recordFetch=$recordFetch->where(function($q){
            $q->orwhere('start_time',NULL)->orwhere('end_time',NULL);
        });
        $recordFetch=$recordFetch->orderBy('id','desc')->first();
     
            if(!empty($recordFetch))
            {
                
                $disabledStatus="True";
            }
            else
            {
                $disabledStatus="False";
            }
       // dd($recordFetch);
        if($request->ajax())
        {
            $returnArr['date']=$cronDataLastUpdtedDate;
            $returnArr['btnDisabledStatus']=$disabledStatus;
            return $returnArr;
        }
       
       
    	return view('replen-request.index',compact('priorityArr','aisleData','siteData','staticTags','dynamicTags','pickaisleData','bulkaisleData','cronDataLastUpdtedDate','disabledStatus'));
    }

    public function assignAisle(Request $request)
    {
        $siteData=Warehouse::select('name','id','is_default')->get();
        $defaultSiteAisleUserData=[];
        $defaultSiteAisleData=[];
        $defaultSiteUsersData=[];
        foreach ($siteData as $key => $value) {
            
            $existingData=\App\ReplenUserAisle::where('warehouse_id',$value->id)->select('id','user_id','aisle')->get()->toArray();
            $priorityData=array();
            $priorityData=Replen::aisleWiseProductCount($value->id);
            
            $siteData[$key]['priorityData']=json_encode($priorityData);
            $siteData[$key]['bulkAisle']=implode(",", array_column($priorityData, 'aisle'));
            $siteData[$key]['assignAisleData']=json_encode($existingData);
            //$siteUsers=\App\User::where('site_id',$value->id)->role('Replen Team')->select('id','first_name','last_name')->get();
            $siteUsers=\App\User::role('Replen Team')->select('id','first_name','last_name')->get();
            
            $siteData[$key]['siteUsers']=json_encode($siteUsers);
          
            if($value->is_default==1)
            {
                $defaultSiteAisleUserData=$existingData;
                $defaultSiteAisleData=array_column($priorityData, 'aisle');
                $defaultSiteUsersData=$siteUsers;
                $defaultSitePriorityData=$priorityData;
            }
        }

        if($request->ajax())
        {
            //dd($request->id);
            $existingData=\App\ReplenUserAisle::where('warehouse_id',$request->id)->select('id','user_id','aisle')->get()->toArray();
            $priorityData=array();
            $priorityData=Replen::aisleWiseProductCount($request->id);
            //dd($priorityData);
            $siteData['priorityData']=json_encode($priorityData);
            $siteData['bulkAisle']=implode(",", array_column($priorityData, 'aisle'));
            $siteData['assignAisleData']=json_encode($existingData);
            //$siteUsersAll=\App\User::where('site_id',$request->id)->with('roles')->select('id','first_name','last_name')->get();
            $siteUsersAll=\App\User::with('roles')->select('id','first_name','last_name')->get();
            $siteUsers = $siteUsersAll->reject(function ($user, $key) {
                return !$user->hasRole('Replen Team');
            });
            $siteData['siteUsers']=json_encode($siteUsers->toArray());
            
                $defaultSiteAisleUserData=$existingData;
                $defaultSiteAisleData=array_column($priorityData, 'aisle');
                $defaultSiteUsersData=$siteUsers;
                $defaultSitePriorityData=$priorityData;
            
             return response()->json(["view"=>view('replen-request.include-assign-aisle',compact('siteData','defaultSiteAisleUserData','defaultSiteAisleData','defaultSiteUsersData','defaultSitePriorityData'))->render(),'showAddbtnStatus'=>(count($defaultSiteUsersData)==0 || count($defaultSiteAisleData)==0) ? 'hide' :'show']);
        }
        //dd($defaultSiteAisleUserData);

       /* echo "<pre>";
        print_r($siteData);exit;*/

        return view('replen-request.assign-aisle',compact('siteData','defaultSiteAisleUserData','defaultSiteAisleData','defaultSiteUsersData','defaultSitePriorityData'));
    }


   
}
