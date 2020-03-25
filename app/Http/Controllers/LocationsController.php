<?php

namespace App\Http\Controllers;

use App\Warehouse;
use App\Locations;
use App\LocationsSetting;
use App\Cartons;
use Illuminate\Http\Request;
use Route;
use Lang;
use Batch;

class LocationsController extends Controller {

    function __construct() {
        $this->middleware('permission:locations-list|locations-create|locations-edit|locations-delete', ['only' => ['index', 'store']]);
        $this->middleware('permission:locations-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:locations-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:locations-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function index() {
        $location_type = LocationType();
        $warehouses    = Warehouse::get();
        $cartons       = Cartons::get();
        return view('locations.index', compact('warehouses', 'location_type', 'cartons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function create() {
        $warehouses   = Warehouse::get();
        $page_title   = $prefix_title = Lang::get('messages.modules.locations_add');
        return view('locations.form', compact('page_title', 'prefix_title', 'warehouses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public
            function store(Request $request) {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function show($id) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function edit($id) {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public
            function destroy($id) {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function setting() {
        $location_settings = LocationsSetting::latest()->first();
        $page_title        = $prefix_title      = Lang::get('messages.modules.setting');
        return view('locations.setting', compact('page_title', 'prefix_title', 'location_settings'));
    }

    public
            function updateAllLocationData() {
        $locationObj    = new Locations();
        $location_array = $locationObj->getLocationWithPrefix();
        $i              = 0;
        $updated_array  = array();
        if (!empty($location_array) && !empty($location_array->toArray())) {
            foreach ($location_array as $row) {
                $upd_location = '';
                if (!empty($row->prefix)) {
                    $upd_location .= $row->prefix;
                }

                if (!empty($upd_location)) {
                    $upd_location .= '.';
                }

                $upd_location                  .= $row->aisle . $row->rack . '.' . $row->floor . $row->box;
                $updated_array[$i]['id']       = $row->id;
                $updated_array[$i]['location'] = $upd_location;
                $i++;
            }
        }

        if (!empty($updated_array)) {
            Batch::update($locationObj, $updated_array, 'id');
        }
    }

}
