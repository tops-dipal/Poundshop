<?php

namespace App\Http\Controllers;

use App\CommodityCodes;
use Illuminate\Http\Request;

class CommodityCodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function __construct()
    {
        $this->middleware('permission:commoditycode-list', ['only' => ['index']]);

        $this->middleware('permission:commoditycode-create', ['only' => ['create','store']]);

        $this->middleware('permission:commoditycode-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:commoditycode-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        
        return view('commodity-codes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $process="add";
        $code=new CommodityCodes;
        return view('commodity-codes.form',compact('process','code'));
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
     * @param  \App\CommodityCodes  $commodityCodes
     * @return \Illuminate\Http\Response
     */
    public function show(CommodityCodes $commodityCodes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CommodityCodes  $commodityCodes
     * @return \Illuminate\Http\Response
     */
    public function edit(CommodityCodes $commodityCodes,$id)
    {
        $process="edit";

        $code=CommodityCodes::find($id);
        return view('commodity-codes.form',compact('process','code'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CommodityCodes  $commodityCodes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CommodityCodes $commodityCodes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CommodityCodes  $commodityCodes
     * @return \Illuminate\Http\Response
     */
    public function destroy(CommodityCodes $commodityCodes)
    {
        //
    }
}
