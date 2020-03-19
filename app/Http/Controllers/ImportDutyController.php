<?php

namespace App\Http\Controllers;

use App\ImportDuty;
use Illuminate\Http\Request;

class ImportDutyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function index()
    {
        
        return view('import-duty.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $process="add";
        $result=new ImportDuty;
        $countries = \App\Country::get();
        $codes=\App\CommodityCodes::get();
        return view('import-duty.form',compact('process','result','countries','codes'));
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
     * @param  \App\ImportDuty  $importDuty
     * @return \Illuminate\Http\Response
     */
    public function show(ImportDuty $importDuty)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ImportDuty  $importDuty
     * @return \Illuminate\Http\Response
     */
    public function edit(ImportDuty $importDuty)
    {
        
        $process="edit";
        $result=$importDuty;
        $countries = \App\Country::get();
        $codes=\App\CommodityCodes::get();
        return view('import-duty.form',compact('process','result','countries','codes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ImportDuty  $importDuty
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ImportDuty $importDuty)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ImportDuty  $importDuty
     * @return \Illuminate\Http\Response
     */
    public function destroy(ImportDuty $importDuty)
    {
        //
    }
}
