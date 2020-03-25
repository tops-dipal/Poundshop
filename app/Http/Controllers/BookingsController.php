<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupplierMaster;
use App\Slot;
use App\WareHouse;
use App\Booking;
use Carbon\Carbon;
use Session;

class BookingsController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function index() {
        if(Session::has('book_date_detail'))
        {
            $date=Session::get('book_date_detail');
            Session::forget('book_date_detail');
        }
        else{
            $date = date("Y-m-d");
        }
        return view('bookings.index', compact('date'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
            function create(Request $request) {
        $booking     = null;
        $radioOption = 0;
        $date        = date("Y-m-d");
        if ($request->id) {
            $booking = Booking::find($request->id);
        }
        if (isset($request->selected_option)) {
            $radioOption = $request->selected_option;
        }
        $suppliers  = SupplierMaster::getAllSupplierList();
        $wareHouses = WareHouse::getWareHouse();
        $slots      = Slot::getSlots();
        return view('bookings.create', compact('suppliers', 'wareHouses', 'slots', 'booking', 'radioOption', 'date'));
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
            $booking    = Booking::find($id);
            $wareHouses = WareHouse::getWareHouse();
            $slots      = Slot::getSlots();
            $date       = date("Y-m-d");
            if ($booking) {
                return view('bookings.edit', compact('booking', 'wareHouses', 'slots', 'date'));
            }
            else {
                abort('404');
            }
        }
        catch (Exception $ex) {
            abort('404');
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

    public
            function bookingDayList($date) {
        Session::put('book_date_detail',$date);
        return view('bookings.booking_day_list');
    }

}
