<?php

namespace App\Http\Controllers;

use App\Totes;
use Illuminate\Http\Request;
use Route;
use Spatie\Permission\Models\Role;

class TotesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     function __construct()
    {
        $this->middleware('permission:totes-list|totes-create|totes-edit|totes-delete', ['only' => ['index','store']]);

        $this->middleware('permission:totes-create', ['only' => ['create','store']]);

        $this->middleware('permission:totes-edit', ['only' => ['edit','update']]);

        $this->middleware('permission:totes-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        return view('totes.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('totes.create');
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
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function show(Totes $totes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try{
            $totes=Totes::find($id);
            
            if($totes){
                return view('totes.edit', compact('totes'));
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
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Totes $totes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Totes  $totes
     * @return \Illuminate\Http\Response
     */
    public function destroy(Totes $totes)
    {
        //
    }
}
