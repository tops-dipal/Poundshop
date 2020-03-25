<div class="row">
	<div class="col-lg-2">		
        <label for="inputPassword" class="col-form-label">Location Quantity:<span class="asterisk">*</span></label>
        
        <a href="javascript:void(0)" class="d-block" onclick="showLocationQty('{{ $result->id }}','{{ $countArrOfWarehouse["location_qty"] }}')">{{  $countArrOfWarehouse["location_qty"] }}</a>
    </div>
   {{-- <div class="col-lg-4">
		<div class="form-group row">
            <label for="inputPassword" class="col-lg-4 text-right col-form-label">Quantity Allocated:<span class="asterisk">*</span></label>
            <div class="col-lg-8">
                <a onclick="showQtyAllocated('{{ $result->id }}')">10</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
		<div class="form-group row">
            <label for="inputPassword" class="col-lg-4 col-form-label">Stock Take Adjustments:<span class="asterisk">*</span></label>
            <div class="col-lg-8">
              <a onclick="showStockTakeAdjustments('{{ $result->id }}')">10</a>
            </div>
        </div>
    </div>--}}

    <div class="col-lg-3">		
        <label for="inputPassword" class="col-form-label">On Po But Not Booked-In:<span class="asterisk">*</span></label>
        
        <a href="javascript:void(0)" class="d-block" onclick="showOnPONotBookedIn('{{ $result->id }}','{{ $countArrOfWarehouse["on_po_not_booked_in"] }}')">{{ $countArrOfWarehouse["on_po_not_booked_in"] }}</a>        
    </div>
    <div class="col-lg-3">		
        <label for="inputPassword" class="col-form-label">Booked-In but not arrived Yet:<span class="asterisk">*</span></label>        
        <a href="javascript:void(0)" class="d-block"  onclick="showBookedInNotArrived('{{ $result->id }}','{{ $countArrOfWarehouse["not_arrived_qty"] }}')">{{ $countArrOfWarehouse["not_arrived_qty"] }}</a>        
    </div>

    <div class="col-lg-3">		
        <label for="inputPassword" class="col-form-label">Waiting to be Put Away:<span class="asterisk">*</span></label>
        <a href="javascript:void(0)" class="d-block"  onclick="showWaitingToBEPutAway('{{ $result->id }}','{{ $countArrOfWarehouse["waiting_put_away"] }}')">{{ $countArrOfWarehouse["waiting_put_away"] }}</a>
    </div>
</div>