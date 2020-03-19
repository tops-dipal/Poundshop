<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartonController extends Controller {

    function __construct() {
        $this->middleware('permission:cartons-list|cartons-create|cartons-edit|cartons-delete', ['only' => ['index', 'store']]);

        $this->middleware('permission:cartons-create', ['only' => ['create', 'store']]);

        $this->middleware('permission:cartons-edit', ['only' => ['edit', 'update']]);

        $this->middleware('permission:cartons-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function index() {
        return view('cartons.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function create() {
        return view('cartons.create');
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
        try {
            $carton = \App\Cartons::find($id);

            if ($carton) {
                return view('cartons.edit', compact('carton'));
            }
            else {
                abort(404);
            }
        }
        catch (Exception $ex) {
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

}
