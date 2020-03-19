<?php

namespace App\Http\Controllers;
use App\Warehouse;
use App\Country;
use App\State;
use App\City;
use Illuminate\Http\Request;
use Route;
use Lang;

class WarehouseController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:warehouse-list|warehouse-create|warehouse-edit|warehouse-delete', ['only' => ['index','store']]);

        $this->middleware('permission:warehouse-create', ['only' => ['create','store']]);

        $this->middleware('permission:warehouse-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:warehouse-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('warehouse.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $result = array();

        $country_states = array();
            
        $state_cities = array();

        $countries = Country::get();

        $page_title = $prefix_title = Lang::get('messages.modules.warehouse_add');
        $state_name="";
        $city_name="";
        return view('warehouse.form',compact('page_title','prefix_title','countries', 'result', 'country_states', 'state_cities','state_name','city_name'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = Warehouse::find($id);       
        
        if(!empty($result))
        {   
            $country_array = array();
            
            $state_array = array();

            if($result->country)
            {
                $country_array[] = $result->country;    
            }            

            if($result->state)
            {
                $state_array[] = $result->state;    
            }            

            $countries = Country::get();            

            $country_states = State::whereIn('country_id',$country_array)->orderBy('name','ASC')->get();            
            
            $country_states = json_decode(json_encode($country_states), TRUE);
            
            $country_states = helper_array_column_multiple_key($country_states, array('country_id'), TRUE);

            $state_cities = City::whereIn('state_id',$state_array)->orderBy('name','ASC')->get();
            
            $state_cities = json_decode(json_encode($state_cities), TRUE);
            
            $state_cities = helper_array_column_multiple_key($state_cities, array('state_id'), TRUE);
             $state_name=($result->state!=0)?\App\State::find($result->state)->name:'';
            $city_name=($result->city!=0)?\App\City::find($result->city)->name:'';
            
            $page_title = $prefix_title = Lang::get('messages.modules.warehouse_edit');

            return view('warehouse.form',compact('page_title', 'prefix_title', 'countries', 'result', 'country_states', 'state_cities','state_name','city_name'));
        }
        else
        {
            return back()->withInput();
        }                         
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
