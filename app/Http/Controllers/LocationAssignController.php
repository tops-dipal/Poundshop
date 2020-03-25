<?php

namespace App\Http\Controllers;

use App\LocationAssign;
use App\Locations;
use App\Warehouse;
use Illuminate\Http\Request;

class LocationAssignController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $aisleData=Locations::groupBy('aisle')->where('status',1)->pluck('aisle')->toArray();
         $siteData=Warehouse::select('name','id','is_default')->get();
        return view('location-assignment.index',compact('aisleData','siteData'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\LocationAssign  $locationAssign
     * @return \Illuminate\Http\Response
     */
    public function show(LocationAssign $locationAssign)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\LocationAssign  $locationAssign
     * @return \Illuminate\Http\Response
     */
    public function edit(LocationAssign $locationAssign)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\LocationAssign  $locationAssign
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LocationAssign $locationAssign)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\LocationAssign  $locationAssign
     * @return \Illuminate\Http\Response
     */
    public function destroy(LocationAssign $locationAssign)
    {
        //
    }
}
