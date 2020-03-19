<?php

namespace App\Http\Controllers;
use App\Pallet;
use Illuminate\Http\Request;
use Route;

class PalletsController extends Controller
{

    function __construct()
    {
        $this->middleware('permission:pallet-list|pallet-create|pallet-edit|pallet-delete', ['only' => ['index','store']]);

        $this->middleware('permission:pallet-create', ['only' => ['create','store']]);

        $this->middleware('permission:pallet-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:pallet-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('pallets.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pallets.create');
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
        try
        {
            $pallets=Pallet::find($id);
            
            if($pallets){
                return view('pallets.edit', compact('pallets'));
            }else{
               abort(404);    
            }
        } catch (Exception $ex) {
            abort(404);
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
