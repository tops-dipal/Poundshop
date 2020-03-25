<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;
use Route;
class SettingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $moduleName="Vat Rates";
        $fields=Setting::where('module_name',$moduleName)->get();
        return view('setting.form',compact('fields'));
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
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function show($modulename)
    {
        $moduleName=$modulename;
        if($moduleName=='terms')
        {
           $request = Request::create('/api/api-setting-terms', 'GET');
            $instance = json_decode(Route::dispatch($request)->getContent());
            $data=$instance->data;
            //print_r($data->terms->terms_pound_uk);exit;
            return view('setting.term_condition',compact('data'));
        }
        if($moduleName=='general')
        {
            $fields=Setting::where('module_name','!=','vat_rates')->get();
            return view('setting.general-form',compact('fields'));
        }
        else
        {

            $fields=Setting::where('module_name',$moduleName)->get();
            return view('setting.form',compact('fields'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function edit(Setting $setting)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Setting $setting)
    {
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Setting  $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }

}
