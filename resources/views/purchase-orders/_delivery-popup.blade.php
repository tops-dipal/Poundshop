<table class="table custom-table cell-align-top" id="delivery-content-table">
    <thead>
        <tr>
            <td class="w-20">Barcodes</td>
            <td class="w-8">Cases</td>
            <td class="w-8">Qty/Box</td>
            <td class="w-8">No of Boxes</td>
            <td class="w-8">Total</td>
            <td class="w-15">Move Boxes</td>
            <td class="w-15">Location</td>
            <td class="w-15">Best Before Date</td>
        </tr>
    </thead>
    <tbody>
        @if(isset($product->outerCaseDetails) && @count($product->outerCaseDetails)>0)
        @foreach($product->outerCaseDetails as $caseDetail)
        <tr>
            <td>{{$caseDetail->barcode}}</td>
            <td>{{$caseDetail->case_type}}</td>
            <td>{{$caseDetail->qty_per_box}}</td>
            <td>{{$caseDetail->no_of_box}}</td>
            <td>{{$caseDetail->total}}</td>
            <td>
                @foreach($caseDetail->caseLocations as $bookingPOProductCase)
                {{$caseDetail->qty_per_box ==0 ? 1 : ($bookingPOProductCase->qty/$caseDetail->qty_per_box)}}&nbsp;({{$bookingPOProductCase->qty}}) &nbsp; to
                @endforeach
            </td>
            <td>
                @foreach($caseDetail->caseLocations as $bookingPOProductCase)
                {{$bookingPOProductCase->locationDetails->location}} - On &nbsp;
                <strong class="bold">{{LocationType($bookingPOProductCase->locationDetails->type_of_location)}}</strong>
                @endforeach
            </td>
            <td>
                @foreach($caseDetail->caseLocations as $bookingPOProductCase)
                {{!empty($bookingPOProductCase->best_before_date) ? date('d-M-Y',strtotime($bookingPOProductCase->best_before_date)) : '-' }}
                @endforeach
            </td>
        </tr>
        @endforeach
        @endif
    </tbody>
</table>

<div class="row">
    <div class="col-4 my-2">
        <label style="font-weight: bold">Quantity Ordered : {{!empty($product->purchaseOrderProduct->total_quantity) ? $product->purchaseOrderProduct->total_quantity : 0}}</label>
    </div>
    <div class="col-4 my-2">
        <label>
            <span style="font-weight: bold"> Over </span> :
            @php $totalOver=0; @endphp
            @foreach($product->bookingPODiscrepancy as $discrepancy)
            @if($discrepancy->discrepancy_type == 2 && $discrepancy->is_added_by_system == 1)
            @php $totalOver +=$discrepancy->qty; @endphp
            @endif
            @endforeach
            <span style="color:red">{{$totalOver}}</span>
        </label>
    </div>
    <div class="col-12">
        @php $totalOnPalletLocation=0; $totalOnReturnSupplier=0; $totalOnHoldLocation=0; @endphp
        @foreach($product->outerCaseDetails as $caseDetail)

        @foreach($caseDetail->caseLocations as $bookingPOProductCase)
        @if(in_array($bookingPOProductCase->locationDetails->type_of_location,[1,2,3,4]))
        @php $totalOnPalletLocation+=$bookingPOProductCase->qty; @endphp
        @endif
        @if(in_array($bookingPOProductCase->locationDetails->type_of_location,[10]))
        @php $totalOnReturnSupplier+=$bookingPOProductCase->qty; @endphp
        @endif

        @if(in_array($bookingPOProductCase->locationDetails->type_of_location,[9]))
        @php $totalOnHoldLocation+=$bookingPOProductCase->qty; @endphp
        @endif

        @endforeach

        @endforeach
        <label class="font-14-dark my-2">On Pallet Location: {{$totalOnPalletLocation}}</label>
    </div>
    <div class="col-12">
        <label class="font-14-dark my-2">On Return to Supplier Location : {{$totalOnReturnSupplier}}</label>
    </div>
    <div class="col-12">
        <label class="font-14-dark my-2">On Hold Location : {{$totalOnHoldLocation}}</label>
    </div>
</div>

@csrf
<div class="row">
    <div class="col-lg-2 form-row">
        <label class="col-lg-12 col-form-label bold">Keep</label>
        <div class="col-lg-8">
            <input type="text" name="keep" class="form-control w-100" id="keep"  maxlength="6">
        </div>
    </div>
    <div class="col-lg-3 form-row">
        <label class="col-lg-12 col-form-label bold">Return to Supplier</label>
        <div class="col-lg-8">
            <input type="text" name="return_to_supplier" class="form-control w-100" id="return_to_supplier" readonly="readonly" value="0">
        </div>
    </div>
</div>

<input type="hidden" name="booking_po_product_id" id="booking_po_product_id" value="{{$po_product_id}}" />
<input type="hidden" name="po_id" id="po_id"  value="{{$po_id}}" />
<input type="hidden" name="discrepancy_id" id="discrepancy_id" value="{{$dis_id}}" />
<input type="hidden" name="over_qty" id="over_qty" value="{{$totalOver}}" />



<script>

    var keepReturnBtn = $("#keep-return-supplier-form-btn");
    var keepReturnForm = $("#keep-return-supplier-form");
    var overQty = $("#over_qty");
    var returnToSupplierQty = $("#return_to_supplier");
    var deliveryPopUp = $("#deliveryModal");

    function undefinedValue(value) {
        if (value !== undefined && value !== "") {
            return value;
        } else {
            return 0;
        }
    }

    $(document).ready(function () {
        console.log(keepReturnForm);
        keepReturnForm.validate({
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

                "keep": {
                    required: true,
                },
            },
            messages: {
                "keep": {
                    required: "Please enter keep value",
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter(element);
            },
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },
            submitHandler: function (form) {
                var dataString = keepReturnForm.serialize();
                keepReturnBtn.attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: keepReturnForm.attr("action"),
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
                        keepReturnBtn.attr('disabled', false);
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            $("#delivery-content-table tbody").html(response.data);
                            setTimeout(function () {
                                deliveryPopUp.modal("hide");
                            }, 500);
                        }
                    },
                    error: function (xhr, err) {
                        keepReturnBtn.attr('disabled', false);
                        $("#page-loader").hide();
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });

            }
        });

        $(document).on("keydown", "#keep", function (e) {
            var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            if (!(key == 65 || key == 8 ||
                    key == 9 ||
                    key == 13 ||
                    key == 46 ||
                    (key >= 35 && key <= 40) ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105))) {
                e.preventDefault();
            }
        });

        $(document).on("input", "#keep", function (e) {
            var inputKeepValue = $(this);
            var keepData = undefinedValue(inputKeepValue.val());
            if (keepData <= parseInt(overQty.val())) {
                returnToSupplierQty.val(parseInt(overQty.val()) - keepData)
            } else {
                if (keepData == "") {
                    inputKeepValue.val(0);
                } else {
                    inputKeepValue.val(overQty.val() - 1);
                    returnToSupplierQty.val(parseInt(overQty.val()) - undefinedValue(inputKeepValue.val()))
                    e.preventDefault();
                }
            }
        });

    })

</script>