<div class="modal fade" id="locationQtyModal" tabindex="-1" role="dialog" aria-labelledby="locationQtyModal" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">Location Quantity:<span id="location_qty_count"></span></h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    
                </div>
            </div>
            <div class="modal-body">
                <form id="locationQtyForm" method="post">
                    <div class="form">
                        <div class="row">
                            <div class="col-lg-4 my-2">
                                 <label class="font-14-dark">Total Quantity in Pick Locations:</label>
                                <span class="font-14-dark bold total_pick_qty"></span>
                            </div>
                            <div class="col-lg-4 my-2">
                                <label class="font-14-dark">Total Quantity in Bulk Locations:</label>
                                <span class="font-14-dark bold total_bulk_qty"></span>
                            </div>
                            <div class="col-lg-4 my-2">
                                <label class="font-14-dark">Return Location Quantity:</label>
                                <span class="font-14-dark bold total_return_qty"></span>
                            </div>
                            <div class="col-lg-4 my-2">
                                <label class="font-14-dark">No. Pick Face Location:</label>
                                <span class="font-14-dark bold total_num_pick_location"></span>
                            </div>
                            <div class="col-lg-4 my-2">
                                <label class="font-14-dark">Total Bulk Face Location:</label>
                                <span class="font-14-dark bold total_num_bulk_location"></span>
                            </div>
                            <div class="col-lg-4 my-2">
                                <label class="font-14-dark">Last Scanned by:</label>                                
                                <span class="font-14-dark bold scanned_user"></span>
                            </div>
                        </div>
                </div>
                <hr/>
                    <table id="locationQtyInfo" class="display">
                        <thead>
                            <tr>
                                <th>Warehouse</th>
                                <th>Aisle</th>
                                <th>Location</th>
                                <th>Location Type</th>
                                <th>Quantity</th>
                                 <th>Best Before Date</th>
                                <th>Quantity</th>
                                </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                           
                </form>
            </div>                
        </div>
    </div>
</div>


<div class="modal fade" id="onPoNotBookedInModal" tabindex="-1" role="dialog" aria-labelledby="onPoNotBookedInModal" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">Quantity On PO but not Booked In:<span id="qty_on_po_but_not_booked_in_count"></span></h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    
                </div>
            </div>
            <div class="modal-body">
                <form id="onPoNotBookedInForm" method="post">
                    <table id="onPoNotBookedInInfo" class="display">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Supplier Order No.</th>
                                <th>Supplier Name</th>
                                <th>Quantity</th>
                                
                                </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                           
                </form>
            </div>                
        </div>
    </div>
</div>

<div class="modal fade" id="qtyBookedInNotArrivedModal" tabindex="-1" role="dialog" aria-labelledby="qtyBookedInNotArrivedModal" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">Quantities Booked-In but not arrived Yet:<span id="qty_booked_in_not_arrived_count"></span></h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    
                </div>
            </div>
            <div class="modal-body">
                <form id="qtyBookedInNotArrivedForm" method="post">
                    <table id="qtyBookedbutNotArrivedTable" class="display">
                        <thead>
                            <tr>
                                <th>Booking In Ref.</th>
                                <th>Book-in Date</th>
                                <th>Slot</th>
                                <th>POs</th>
                                <th>Supplier</th>
                                <th>Quantity</th>
                                
                                </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                           
                </form>
            </div>                
        </div>
    </div>
</div>

<div class="modal fade" id="waitiongToBePutAwayModal" tabindex="-1" role="dialog" aria-labelledby="waitiongToBePutAwayModal" aria-hidden="true">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">Quantity Waiting to be Put Away:<span id="putaway_qty"></span></h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    
                </div>
            </div>
            <div class="modal-body">
                <form id="waitingToBePutawayForm" method="post">
                   

                  
                    <table id="waitingToBePutawayTable" class="display">
                        <thead>
                            <tr>
                                <th>Pallet Locations</th>
                                <th>Location Type</th>
                                <th>Quantity</th>
                                
                                </tr>
                            </thead>
                        <tbody></tbody>
                    </table>
                           
                </form>
            </div>                
        </div>
    </div>
</div>

<form id="modalForm" method="post" name="modalForm">
    <input type="hidden" name="product_id" id="product_id" value="{{ $result->id }}">
    <input type="hidden" name="warehouse_id" id="warehouse_id">
</form>

