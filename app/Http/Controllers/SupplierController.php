<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Lang;
use App\Country;
use App\State;
use App\City;
use App\SupplierMaster;
use App\SupplierReferences;


class SupplierController extends Controller
{
     /**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:supplier-list', ['only' => ['index']]);

        $this->middleware('permission:supplier-create', ['only' => ['create','store']]);

        $this->middleware('permission:supplier-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:supplier-delete', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the resource.
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd(Env('mail_username'));
        $page_title = $prefix_title = Lang::get('messages.modules.supplier_list');

        return view('supplier.index', compact('page_title', 'prefix_title'));
    }

    // /**
    //  * Show the form for creating a new resource.
    //  * @author : Shubham Dayma
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     $result = array();

    //     $country_states = array();
            
    //     $state_cities = array();

    //     $countries = Country::get();        
        
    //     $page_title = $prefix_title = Lang::get('messages.modules.supplier_add');

    //     return view('supplier.form',compact('page_title', 'prefix_title', 'countries', 'result', 'country_states', 'state_cities'));
    // }

    /**
     * Show the form for editing the specified resource.
     * @author : Shubham Dayma
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function form($id = "")
    {
        // get details by id
        $countries = Country::get();
        
        $country_states = array();
            
        $state_cities = array();

        $reference_data = array();

        $page_title = $prefix_title = Lang::get('messages.modules.supplier_add');
        $state_name="";
        $city_name="";
        $result = SupplierMaster::find($id);

        if(!empty($result))
        {   
            // create country id , state id array to excute single query to retrive states, cities
            $country_array = array();
            
            $state_array = array();

            if($result->country_id)
            {
                $country_array[] = $result->country_id;    
            }

            if($result->bene_country)
            {
                $country_array[] = $result->bene_country;    
            }

            if($result->bank_country)
            {
                $country_array[] = $result->bank_country;    
            }

            if($result->state_id)
            {
                $state_array[] = $result->state_id;    
            }

            if($result->bene_state)
            {
                $state_array[] = $result->bene_state;    
            }

            if($result->bank_state)
            {
                $state_array[] = $result->bank_state;    
            }

            // get states
            $country_states = State::whereIn('country_id',$country_array)->orderBy('name','ASC')->get();
            
            $country_states = object_to_array($country_states);
            
            // get states array with array key country_id
            $country_states = helper_array_column_multiple_key($country_states, array('country_id'), TRUE);

            // get cities
            $state_cities = City::whereIn('state_id',$state_array)->orderBy('name','ASC')->get();
            
            $state_cities = object_to_array($state_cities);
            
            // get cities array with array key state_id
            $state_cities = helper_array_column_multiple_key($state_cities, array('state_id'), TRUE);

            //get supplier reference            
            $reference_data = SupplierReferences::select('supplier_references.*')->selectRaw('CONCAT_WS(" ",users.first_name,users.last_name) as email_send_user')->leftJoin('users','users.id','=','supplier_references.created_by')->where('supplier_id',$id)->orderBy('id', 'desc')->take(3)->get();            
            $state_name=($result->state_id!=0)?\App\State::find($result->state_id)->name:'';
            $city_name=($result->city_id!=0)?\App\City::find($result->city_id)->name:'';
            $page_title = $prefix_title = Lang::get('messages.modules.supplier_edit');
        }
        
        return view('supplier.form',compact('page_title', 'prefix_title', 'countries', 'result', 'country_states', 'state_cities','reference_data', 'id','state_name','city_name'));    
    }

    public function supplier_contacts($supplier_id = "")
    {
        if(!empty($supplier_id))
        {
            $result = SupplierMaster::find($supplier_id);
            
            echo view('supplier.contact-info', compact('result'));
        } 
        else
        {
            echo '';
        }   
    }
}
