<div class="d-flex mb-3" style="border: 1px solid #dee2e6">
    <table class="table tbl-replane-product mb-0 table-striped" style="width: 40%;margin-right: -1px;">
        <thead>
            <tr>
                <th>Product Info.</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Product Title: <span class="bold">{{$productData->title}}</span></td>
            </tr>
            <tr>
                <td class="cell-border-right cell-border-bottom">
                    <div class="d-flex align-items-center">
                        @if (!empty($productData->main_image_internal_thumb))
                        <a href="{{$productData->main_image_internal}}" data-rel="lightcase">
                            <img src="{{url('/img/img-loading.gif') }}" data-original="{{$productData->main_image_internal_thumb}}"  width="80" height="80" alt="">

                        </a>
                        @else
                        <a href="{{url('/img/no-image.jpeg')}}" data-rel="lightcase">
                            <img src="{{url('/img/img-loading.gif') }}"  data-original="{{url('/img/no-image.jpeg')}}" width="80" height="80" alt=""> </a>

                        @endif
                        <div class="pl-3">
                            <p class="mb-2">Barcode: <span>{{$productData->barcode}}</span></p>
                            <p class="mb-2">SKU: <span>{{$productData->sku}}</span></p>
                            <p  class="mb-2">Supplier SKU: <span>{{$productData->supplier_sku}}</span></p>
                            <span class="font-12-dark d-inline-block px-3 py-1 bold alert alert-success">In Stock</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <!-- <td class="bold">Pick</td>
                            <td>10</td> -->
            </tr>
            <tr>
                <!-- <td class="bold cell-border-bottom">Dropship</td>
                            <td class="cell-border-bottom">100</td> -->
            </tr>
        </tbody>
    </table>

    <table class="table tbl-replane-storage mb-0 table-striped" style="width: 60%;">
        <thead>
            <tr>

                <th>Aisle.</th>
                <th>Current Location</th>
                <th>Quantity</th>
                <th>Available Space</th>
                <th>Best Before Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productData->locationAssign as $aisle)
            <tr>
                <td>{{$aisle->aisle}}</td>
                <td>{{$aisle->location}}
                    <br>
                    <p class="mt-2">{{LocationType($aisle->type_of_location)}}</p></td>
                <td>{{!empty($aisle->total_qty) ? $aisle->total_qty : 0}}</td>
                <td>{{($aisle->qty_fit_in_location - $aisle->available_qty)}}</td>
                <td>

                    @if(isset($aisle->bestBeforeDate->best_before_date) && !empty($aisle->bestBeforeDate->best_before_date))
                    {{date('d-M-Y',strtotime($aisle->bestBeforeDate->best_before_date))}}
                    @else
                    {{'--'}}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<table class="table custom-table table-striped">
    <thead>
        <tr>
            <td style="width: 50px;"></td>
            <td class="w-25">Barcode</td>
            <td class="w-25">Case/Loose</td>
            <td class="w-10">Qty/Box</td>
            <td class="w-10">No Of Boxes</td>
            <td class="w-10">Total</td>
            <td class="w-15">Best Before Date</td>
        </tr>
    </thead>
    <tbody>
        @php $barcodeFound = false; @endphp
        @foreach($productData->putAwayCase as $key=>$caseDetail)
        @if($caseDetail->barcode == $product_search)
        @php $barcodeFound = true; @endphp
        @endif
        @endforeach
        @foreach($productData->putAwayCase as $key=>$caseDetail)
        <tr>
            <td class="border-none">
                <label class="fancy-radio">
                    <input data-transactionid="{{$caseDetail->id}}"
                           data-warehouse="{{$caseDetail->warehouse_id}}"
                           data-case="{{$caseDetail->case_type}}"
                           data-perqty="{{$caseDetail->qty_per_box}}"
                           data-box="{{$caseDetail->total_boxes}}"
                           data-total="{{$caseDetail->qty}}"
                           data-putawayqty="{{ $caseDetail->total- $caseDetail->put_away_qty}}"
                           data-product="{{$caseDetail->product_id}}"
                           data-barcode="{{$caseDetail->barcode}}"
                           data-bestbeforedate="{{$caseDetail->best_before_date}}"
                           data-bookingid="{{$caseDetail->booking_id}}"
                           data-poid="{{$caseDetail->po_id}}"
                           data-bookingpoproductid="{{$caseDetail->booking_po_product_id}}"
                           data-bookingpoproductcasedetailsid="{{$caseDetail->booking_po_case_detail_id}}"
                           data-bookingpoproductlocationid="{{$caseDetail->booking_po_product_location_id}}"
                           value="{{$caseDetail->barcode}}"
                           type="radio" class="case-detail-radio"
                           name="case-detail-radio" @if($barcodeFound == false) @if($key ==0 ) checked="checked" @endif @else  @if($caseDetail->barcode == $product_search) checked="checked" @endif
                           @endif />
                           <span><i></i></span>
                </label>
            </td>
            <td class="v-align-middle border-none">{{$caseDetail->barcode}}</td>
            <td class="v-align-middle  border-none">{{barcodeType($caseDetail->case_type)}}</td>
            <td class="v-align-middle  border-none">{{$caseDetail->qty_per_box}}</td>
            <td class="v-align-middle  border-none">{{$caseDetail->total_boxes}}</td>
            <td class="v-align-middle  border-none">{{$caseDetail->qty}}</td>
            <td class="v-align-middle  border-none">{{!empty($caseDetail->best_before_date) ? date('d-M-Y',strtotime($caseDetail->best_before_date)) : '--'}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h3 class="p-title mb-3">Move SKU To:</h3>
<form name="putAwayForm" id="putAwayForm" method="post" action="{{route('put-aways.products-store')}}">
    <div class="row m-0 mb-3">
        <div class="col-lg-3 pl-0">
            <label class="font-14-dark mb-1">Scan To Location</label>
            <input type="text" class="form-control" name="move_location" id="move_location" autocomplete="off">
            <span class="location_type font-10-dark bold d-block mt-1"></span>
        </div>
        <div class="col-lg-2 pl-0" id="quantity-fit-location" style="display: none;">
            <label class="font-14-dark mb-1">Storage Capacity</label>
            <input type="text" class="form-control" name="qty_fit_location" id="qty_fit_location" value="" >
        </div>
        <div class="col-lg-2">
            <label class="font-14-dark mb-1">Store As</label>
            <select class="form-control" name="store_as_case" id="storeAs">
                <option value="3">Outer</option>
                <option value="2">Inner</option>
                <option value="1">Singles</option>
            </select>
        </div>
        <div class="col-lg-2" id="putAwayBarCodeContainer" style="display: none;">
            <label class="font-14-dark mb-1">Barcode</label>
            <input type="text" class="form-control" name="put_away_barcode_textbox" id="put_away_barcode_textbox">
        </div>
        <div class="col-lg-2" id="qtyBoxContainer" style="display: none;">
            <label class="font-14-dark mb-1">Qty/Box</label>
            <input type="text" class="form-control" name="qty_per_box" id="qty_box" maxlength="5">
        </div>
        <div class="col-lg-2" id="noOfBoxContainer">
            <label class="font-14-dark mb-1">No. of Boxes</label>
            <input type="text" class="form-control" name="total_box" id="no_of_box" maxlength="5">
        </div>
        <div class="col-lg-2" id="qtyContainer">
            <label class="font-14-dark mb-1">Quantity</label>
            <input type="text" class="form-control" name="qty" id="qty" readonly="readony" maxlength="6">
        </div>
        <!--    <div class="col-lg-2">
                <label class="font-14-dark mb-1">Quantity</label>
                <p class="font-14-dark bold pt-3">-</p>
            </div>-->
        <div class="col-lg-3 pt-4 text-right">
            <button type="submit" class="btn btn-green font-12 bold mt-2" id="putAwaySubmit">Put Away</button>
            <button class="btn btn-blue font-12 bold mt-2">Report to Stock Control</button>
        </div>
    </div>
    <input type="hidden" id="location_transaction_id" name="location_transaction_id" />
    <input type="hidden" name="request_putaway_type" value="{{$request_putaway_type}}" />
    <input type="hidden" id="scanned_pallet_location" name="scanned_pallet_location" />
    <input type="hidden" id="scanned_case_type" name="scanned_case_type" />
    <input type="hidden" id="outer_qty_per_box" name="outer_qty_per_box" />
    <input type="hidden" id="qty_per_box" name="inner_qty_per_box" />

    <input type="hidden" id="put_away_warehouse_id" name="put_away_warehouse_id" />
    <input type="hidden" id="warehouse_id" name="warehouse_id" />
    <input type="hidden" id="put_away_best_before_date" name="put_away_best_before_date" />
    <input type="hidden" id="put_away_location_id" name="put_away_location_id" />
    <input type="hidden" id="put_away_barcode" name="put_away_barcode" />
    <input type="hidden" id="put_away_store_as" name="put_away_store_as" />
    <input type="hidden" id="put_away_booking_id" name="put_away_booking_id" />
    <input type="hidden" id="put_away_po_id" name="put_away_po_id" />
    <input type="hidden" id="put_away_product_id" name="put_away_product_id" />
    <input type="hidden" id="put_away_booking_po_product_id" name="put_away_booking_po_product_id" />
    <input type="hidden" id="put_away_booking_po_product_case_details_id" name="put_away_booking_po_product_case_details_id" />
    <input type="hidden" id="put_away_booking_po_product_location_id" name="put_away_booking_po_product_location_id" />



</form>

<script>
    /**
     * @author Hitesh Tank
     * @Desc : putaway action
     */
    var putAwaySubmit = $("#putAwaySubmit");
    var locationKeywordURL = $("#locationKeywordURL");

    $("#putAwayForm").validate({
        focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function (form, validator) {
            if (!validator.numberOfInvalids())
                return;
            $('html, body').animate({
                scrollTop: $(validator.errorList[0].element).offset().top - 30
            }, 1000);
        },
        errorElement: 'span',
        errorClass: 'invalid-feedback', // default input error message class
        ignore: [],
        rules: {
            "move_location": {
                required: true,
            },
//            "no_of_box": {
//                required: function (element) {
//                    if ($("#store_as_case").val() == '1') {
//                        return false;
//                    } else {
//                        return true;
//                    }
//                },
//                digits: function (element) {
//                    if ($("#store_as_case").val() == '1') {
//                        return false;
//                    } else {
//                        return true;
//                    }
//                },
//                min: function (element) {
//                    if ($("#store_as_case").val() == '1') {
//                        return false;
//                    } else {
//                        return true;
//                    }
//                },
//            },
            "qty": {
                required: true,
                digits: true,
                min: 1,
            },
        },
        messages: {
            "move_location": {
                required: "Please select location.",
            },
            "no_of_box": {
                required: "Please enter no of box.",
                digits: "Digit only",
                min: '> 0',
            },
            "qty": {
                required: "Please enter quantity.",
                digits: "Digit only",
                min: '> 0',
            },
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "supplier") {
                error.insertAfter(element.closest(".dropdown"));
            } else {
                error.insertAfter(element);
            }

        },
        highlight: function (element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        submitHandler: function (form) {
            var dataString = $("#putAwayForm").serialize();
            putAwaySubmit.attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#putAwayForm").attr("action"),
                data: dataString,
                processData: false,
//                    contentType: false,
//                    cache: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    putAwaySubmit.attr('disabled', false);
                    $("#page-loader").hide();
                    if (response.status == 1) {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        setTimeout(function () {
                            $("#scan-product-textbox").val("");
                            if (response.data.is_empty_pallet == false) {
                                //$("#scan-pallet-barcode-textbox").trigger('input');
                                triggerEvent();
                            }
                            $("#putAwayDetailScreen").hide();
                        }, 1000);
                    } else {
                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }
                },
                error: function (xhr, err) {
                    console.log(xhr);
                    putAwaySubmit.attr('disabled', false);
                    $("#page-loader").hide();
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }
    });

    function triggerEvent() {
        var cObj = $("#scan-pallet-barcode-textbox");
        cObj.next('span.location_type').remove();
        $("#page-loader").show();
        $.ajax({
            url: locationKeywordURL.val(),
            type: "GET",
            data: {
                "keyword": cObj.val(),
            },
            datatype: 'JSON',
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {

            },
            success: function (response) {
                $("#page-loader").hide();
                if (response.status == 1)
                {
                    html = '<span class="location_type font-10-dark bold d-block mt-1">' + response.data.location_type + '</span>';
                    cObj.after(html);
                    productSearch('pallet');

                } else
                {
                    cObj.val('');
                }
            },
            error: function (xhr, err) {
                cObj.val('');
                $("#page-loader").hide();
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
    }
</script>