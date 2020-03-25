<?php

namespace App\Http\Controllers;

use App\QCChecklist;
use Illuminate\Http\Request;

class QCCheckListController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('qc-checklist.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $process="add";
        $result=new QCChecklist;
        
        return view('qc-checklist.form',compact('process','result'));
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
     * @param  \App\QCChecklist  $qCChecklist
     * @return \Illuminate\Http\Response
     */
    public function show(QCChecklist $qCChecklist)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\QCChecklist  $qCChecklist
     * @return \Illuminate\Http\Response
     */
    public function edit(QCChecklist $qCChecklist,$id)
    {

         $process="edit";
        $result=QCChecklist::find($id);
        //print_r($result);exit;
        $points=\App\ChecklistPoint::where('qc_id',$result->id)->get();
        return view('qc-checklist.form',compact('process','result','points'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\QCChecklist  $qCChecklist
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QCChecklist $qCChecklist)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\QCChecklist  $qCChecklist
     * @return \Illuminate\Http\Response
     */
    public function destroy(QCChecklist $qCChecklist)
    {
        //
    }
    
    public function getQcList(Request $request)
    {
        
        if(empty($request->product_ids))
        {  
            return back()->withInput();
        }    
        $productInfo=\App\Products::with(array('bookingQCChecklist'=>function($query){
            $query->pluck('qc_list_id')->toArray();
        }))->select('id','title')->whereIn('id',explode(",", $request->product_ids))->get();
        
        $qc=\App\QCChecklist::with(array('checklistPoints'=>function($query){
            $query->select('*'); }))->get();
        $qc_list=\App\QCChecklist::get();
        return response()->json(['view' => view('material_receipt.ajax-qc',compact('qc','qc_list','productInfo'))->render()]); 
    }

     public function printQC(Request $request)
    {
        $qc=QCChecklist::find($request->id);
        $points=\App\ChecklistPoint::where('qc_id',$qc->id)->get();
        $title=$qc->name;
        
        return view('qc-checklist.print-qc',compact('title','points'));
       
        //return $pdf->download( 'test'. '.pdf');
    }
    
}
