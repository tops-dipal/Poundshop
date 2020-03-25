<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Lang;

class ReportController extends Controller
{
	/**
     * Restrict users without permissions
     * @author : Shubham Dayma
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        $this->middleware('permission:excess-qty-received-report', ['only' => ['excessQtyReceivedReport']]);
    }
    
	public function excessQtyReceivedReport(Request $request)
	{
		$page_title = $prefix_title = Lang::get('messages.excess_qty_report.excess_qty_report');

        return view('reports.excess_qty_received_report', compact('page_title', 'prefix_title'));
	}
}

