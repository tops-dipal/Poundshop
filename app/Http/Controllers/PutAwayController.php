<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WareHouse;

class PutAwayController extends Controller {

    public
            function getDashboard() {
        $active_tab = "put-away-dashboard";
        $wareHouses = WareHouse::getWareHouse();
        return view('put-away.put_away_dashboard_tab', compact('wareHouses', 'active_tab'));
    }

    public
            function getPutAway() {
        $active_tab = "put-away";
        return view('put-away.put_away_tab', compact('active_tab'));
    }

    public
            function getPutAwayJobList() {
        $active_tab = "put-away-job-list";
        return view('put-away.put_away_job_list_tab', compact('active_tab'));
        //return view('put-away-joblist.index', compact('active_tab'));
    }

}
