

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

    "user strict";
    var activeTab=$('#active_tab').val();
    var productListingID = $("#productListingID");
    var scanProductTextBox  = $("#scan-product-textbox");
    var pendingColumnDiv = $("#pendingColumnDiv");
    var pendingQty = $("#pendingQty");
    var pendingProduct=$("#pendingProduct");
    var sortByGoodsIn = $(".put-away-filters");
    //var putAwayDetailScreen = $("#putAwayDetailScreen");
    var locationKeywordURL = $("#locationKeywordURL");
    var putAwayDetailScreen = $("#putAwayDetailScreen");
    var putAwayBarCodeContainer = $("#putAwayBarCodeContainer");
    var qtyBoxContainer = $("#qtyBoxContainer");
    

    function productSearch(searchFrom){
      var sortBy=$('#sort_by').val()
      var sortDirection=$('#sort_direction').val()
      var location = $("#scan-pallet-barcode-textbox").val()
      var productSearch=$("#scan-product-textbox").val()
        $.ajax({
            url: $("#putaway-product-url").val(),
            type: "get",
            data:{pallet_location:location,sort_by:sortBy,sort_direction:sortDirection,product_search:productSearch,search_by:searchFrom},
            headers: {
               Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                    $("#page-loader").hide();
                    if(searchFrom == 'pallet'){
                        if (response.status == 1) {
                            putAwayDetailScreen.hide();
                            productListingID.show();
                            scanProductTextBox.show();
                            pendingColumnDiv.show();
                            sortByGoodsIn.show();
                            pendingQty.text(response.data.total_pending_qty)
                            pendingProduct.text(response.data.total_pending_products)
                            $("#putaway-id").html(response.data.data);
                        }else{
                            productListingID.hide();
                            scanProductTextBox.hide();
                            pendingColumnDiv.hide();
                            sortByGoodsIn.hide();
                            pendingQty.text(0);
                            pendingProduct.text(0)
                            $("#putaway-id").html(response.data.data);
                           PoundShopApp.commonClass._displayErrorMessage(response.message);
                        }
                    }
                    else if(searchFrom == 'product'){
                        if (response.status == 1) {
                           productListingID.hide();
                           pendingColumnDiv.hide();
                           sortByGoodsIn.hide();
                           putAwayDetailScreen.show();
                           putAwayDetailScreen.html(response.data.data);
                           var selectedCase=putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case");
                           putAwayDetailScreen.find('#storeAs').val(selectedCase)
                           if(selectedCase == 2){ //inner then hide outer case){
                                putAwayDetailScreen.find("#storeAs option[value='3']").hide();
                           }
                            /*Hidden store data*/
                            
                            putAwayDetailScreen.find("#scanned_case_type").val(selectedCase);
                            putAwayDetailScreen.find("#warehouse_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-warehouse"));
                            putAwayDetailScreen.find("#scanned_pallet_location").val($("#scan-pallet-barcode-textbox").val());
                            putAwayDetailScreen.find("#put_away_best_before_date").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bestbeforedate"));
                            putAwayDetailScreen.find("#put_away_barcode").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-barcode"));
                            putAwayDetailScreen.find("#put_away_booking_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingid"));
                            putAwayDetailScreen.find("#put_away_po_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-poid"));
                            putAwayDetailScreen.find("#put_away_product_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-product"));
                            putAwayDetailScreen.find("#put_away_booking_po_product_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingpoproductid"));
                            putAwayDetailScreen.find("#put_away_booking_po_product_case_details_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingpoproductcasedetailsid"));
                            putAwayDetailScreen.find("#put_away_booking_po_product_location_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingpoproductlocationid"));
                            putAwayDetailScreen.find("#qty_per_box").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-perqty"));

                            putAwayDetailScreen.find("#location_transaction_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-transactionid"));

                            /*Hidden store data*/
                             if(selectedCase == 1){ //loose qty
                                putAwayDetailScreen.find("#noOfBoxContainer").hide();
                                putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",false);
                                putAwayDetailScreen.find("#storeAs option[value='3']").hide();
                                putAwayDetailScreen.find("#storeAs option[value='2']").hide();

                            }else{ //outer or inner case 
                                if(selectedCase == 3){ //outer
                                    putAwayDetailScreen.find("#outer_qty_per_box").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-perqty"));
                                    putAwayDetailScreen.find("#storeAs option[value='3']").show();
                                    putAwayDetailScreen.find("#storeAs option[value='2']").show();
                                }else if(selectedCase == 2){ //inner
                                    putAwayDetailScreen.find("#storeAs option[value='3']").hide();
                                    putAwayDetailScreen.find("#storeAs option[value='2']").show();
                                }

                                putAwayDetailScreen.find("#noOfBoxContainer").show();
                                putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",true);
                            }
                        
                        }else{
                          PoundShopApp.commonClass._displayErrorMessage('Product not found,Please select product from Below.');
                        }
                    }else{
                        if (response.status == 1) {
                        pendingQty.text(response.data.total_pending_qty)
                        pendingProduct.text(response.data.total_pending_products)
                        $("#putaway-id").html(response.data.data);
                    }
                    }
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
               if(searchFrom == 'pallet'){
                   productListingID.hide();
                   pendingColumnDiv.hide();
                   sortByGoodsIn.hide();
                   scanProductTextBox.val("").hide();
               }
                
               $("#putaway-id").html("");
            }

        });
    }
  
//    $(document).on("input","#scan-pallet-barcode-textbox",function(event){
//        $("#scan-product-textbox").val("");
//       productSearch('pallet');
//    })
    
    
    $(document).on("keypress","#scan-product-textbox",function(event){
         
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            productSearch('product');
        }
	
    })
    
    $(document).on("click",'td.sorting[sort-by],td.sorting_asc[sort-by],td.sorting_desc[sort-by]',function(e)
    {
      $('#sort_by').val($(this).attr("sort-by"))
      $('#sort_direction').val($(this).attr("sort-order"))
      productSearch();
      return false;
    });
    
    $(document).on("click",'#sortByGoodsIn',function(e)
    {
      $('#sort_by').val('goods-in')
      $('#sort_direction').val('desc')
      productSearch();
      return false;
    });
    
    /**
     * @author Hitesh Tank
     * @desc open product detail container
     */
    $(document).on('click','.put-away-detail-btn',function(e){
       //putAwayDetailScreen.modal("show"); 
       var currentObj=$(this);
       $.ajax({
            url: $(this).attr('data-url'),
            type: "get",
            data:{warehouse_id:$(this).attr("data-warehouse"),po_id:$(this).attr("data-po"),booking_id:$(this).attr("data-booking"),pallet_location:$(this).attr("data-pallet"),search:$(this).attr("data-searchtext"),product_id:$(this).attr("data-productid"),putaway_type:$(this).attr("data-putawaytype")},
            headers: {
               Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                    $("#page-loader").hide();
                    if (response.status == true) {
                        productListingID.hide();
                        pendingColumnDiv.hide();
                        sortByGoodsIn.hide();
                        putAwayDetailScreen.show();
                        putAwayDetailScreen.html(response.data.data)
                        scanProductTextBox.val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-barcode"));
                        var selectedCase=putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case");
                        putAwayDetailScreen.find('#storeAs').val(selectedCase)
                        
                        if(selectedCase == 2){ //inner then hide outer case){
                            putAwayDetailScreen.find("#storeAs option[value='3']").hide();
                        }
                        /*Hidden store data*/
                       
                        
                        putAwayDetailScreen.find("#scanned_case_type").val(selectedCase);
                        putAwayDetailScreen.find("#scanned_pallet_location").val($("#scan-pallet-barcode-textbox").val());
                        putAwayDetailScreen.find("#warehouse_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-warehouse"));
                        putAwayDetailScreen.find("#put_away_best_before_date").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bestbeforedate"));
                        putAwayDetailScreen.find("#put_away_barcode").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-barcode"));
                        putAwayDetailScreen.find("#put_away_booking_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingid"));
                        putAwayDetailScreen.find("#put_away_po_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-poid"));
                        putAwayDetailScreen.find("#put_away_product_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-product"));
                        putAwayDetailScreen.find("#put_away_booking_po_product_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingpoproductid"));
                        putAwayDetailScreen.find("#put_away_booking_po_product_case_details_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingpoproductcasedetailsid"));
                        putAwayDetailScreen.find("#put_away_booking_po_product_location_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-bookingpoproductlocationid"));
                        putAwayDetailScreen.find("#qty_per_box").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-perqty"));
                        putAwayDetailScreen.find("#location_transaction_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-transactionid"));
                        /*Hidden store data*/
                        
                        if(selectedCase == 1){ //loose qty
                            putAwayDetailScreen.find("#noOfBoxContainer").hide();
                            putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",false);
                            putAwayDetailScreen.find("#storeAs option[value='3']").hide();
                            putAwayDetailScreen.find("#storeAs option[value='2']").hide();

                        }else{ //outer or inner case 
                            if(selectedCase == 3){ //outer
                                putAwayDetailScreen.find("#outer_qty_per_box").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-perqty"));
                                putAwayDetailScreen.find("#storeAs option[value='3']").show();
                                putAwayDetailScreen.find("#storeAs option[value='2']").show();
                            }else if(selectedCase == 2){ //inner
                                putAwayDetailScreen.find("#storeAs option[value='3']").hide();
                                putAwayDetailScreen.find("#storeAs option[value='2']").show();
                            }
                            
                            putAwayDetailScreen.find("#noOfBoxContainer").show();
                            putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",true);
                        }
                        
                    }else{
                       PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
       
    });
    
    /**
     * @author Hitesh Tank
     * @Desc Radio button change
     */
    $(document).on("change",".case-detail-radio",function(e){
        putAwayDetailScreen.find('#storeAs').val($(this).attr("data-case"))
        //store hidden values
        putAwayDetailScreen.find("#warehouse_id").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-warehouse"));
        putAwayDetailScreen.find("#scanned_case_type").val($(this).attr("data-case"));
        putAwayDetailScreen.find("#put_away_best_before_date").val($(this).attr("data-bestbeforedate"));
        putAwayDetailScreen.find("#put_away_barcode").val($(this).attr("data-barcode"));
        putAwayDetailScreen.find("#put_away_booking_id").val($(this).attr("data-bookingid"));
        putAwayDetailScreen.find("#put_away_po_id").val($(this).attr("data-poid"));
        putAwayDetailScreen.find("#put_away_product_id").val($(this).attr("data-product"));
        putAwayDetailScreen.find("#put_away_booking_po_product_id").val($(this).attr("data-bookingpoproductid"));
        putAwayDetailScreen.find("#put_away_booking_po_product_case_details_id").val($(this).attr("data-bookingpoproductcasedetailsid"));
        putAwayDetailScreen.find("#put_away_booking_po_product_location_id").val($(this).attr("data-bookingpoproductlocationid"));
       putAwayDetailScreen.find("#location_transaction_id").val($(this).attr("data-transactionid"));
        if($(this).attr("data-case") == 1){ //loose qty
            putAwayDetailScreen.find("#noOfBoxContainer").hide().find("#no_of_box").val("");
            putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",false).val("");
            putAwayDetailScreen.find("#putAwayBarCodeContainer").hide().find("#put_away_barcode_textbox").val("")
            putAwayDetailScreen.find("#storeAs option[value='3']").hide();
            putAwayDetailScreen.find("#storeAs option[value='2']").hide();
            
        }else{ //outer or inner case 
            if($(this).attr("data-case") == 2){ //inner then hide outer case){
                    putAwayDetailScreen.find("#qty_per_box").val($(this).attr("data-perqty"));
                    putAwayDetailScreen.find("#storeAs option[value='3']").hide();
                    putAwayDetailScreen.find("#storeAs option[value='2']").show();
                    putAwayDetailScreen.find("#putAwayBarCodeContainer").hide().find("#put_away_barcode_textbox").val("")
                    putAwayDetailScreen.find("#qtyBoxContainer").hide().find("#qty_box").val("");

            }else{
                 putAwayDetailScreen.find("#outer_qty_per_box").val($(this).attr("data-perqty"));
                 putAwayDetailScreen.find("#noOfBoxContainer").show().find("#no_of_box").val("");
                 putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",true).val("");
                 putAwayDetailScreen.find("#storeAs option[value='3']").show();
                 putAwayDetailScreen.find("#storeAs option[value='2']").show();
            }
            
        }
    });
    
    $(document).on("keydown","#no_of_box,#qty,#qty_box,#no_of_box,#qty_fit_location", function(e) {
        var key = e.charCode || e.keyCode || 0;
               // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
               // home, end, period, and numpad decimal
               if (!(key == 65 || key == 8 || 
                   key == 9 ||
                   key == 13 ||
                   key == 46 ||
                   (key >= 35 && key <= 40) ||
                   (key >= 48 && key <= 57) ||
                   (key >= 96 && key <= 105))){
                       e.preventDefault();
               }
    });
    
    $(document).on("change","#storeAs", function(e) {
        putAwayDetailScreen.find("#put_away_store_as").val($(this).val());
        
        if(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case") == 3 ){
            if($(this).val() == 1){ //loose qty
                putAwayDetailScreen.find("#qtyBoxContainer").hide().find("#qty_box").val("");
                putAwayDetailScreen.find("#noOfBoxContainer").hide().find("#no_of_box").val("");
                putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",false).val("");
                putAwayDetailScreen.find("#putAwayBarCodeContainer").show()

            }else{ //outer or inner case 
                if($(this).val() != 3 && (putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case") == 2 || putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case") == 3)){
                    putAwayDetailScreen.find("#putAwayBarCodeContainer").show().find("#put_away_barcode_textbox").val("");
                    putAwayDetailScreen.find("#qtyBoxContainer").show().find("#qty_box").val("");
                    putAwayDetailScreen.find("#outer_qty_per_box").val("");
                    putAwayDetailScreen.find("#qty_per_box").val($(this).attr("data-perqty"));

                }else{
                    putAwayDetailScreen.find("#outer_qty_per_box").val(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-perqty"));
                    putAwayDetailScreen.find("#qtyBoxContainer").hide();
                    putAwayDetailScreen.find("#putAwayBarCodeContainer").hide();
                }
                putAwayDetailScreen.find("#noOfBoxContainer").show().find("#no_of_box").val("");
                putAwayDetailScreen.find("#qtyContainer").find("#qty").val("").prop("readonly",true).val("");
            }
        }else{
            if(putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case") == 2){
                if($(this).val() == 1){ //loose qty
                    putAwayDetailScreen.find("#qtyBoxContainer").hide().find("#qty_box").val("");
                    putAwayDetailScreen.find("#noOfBoxContainer").hide().find("#no_of_box").val("");
                    putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",false).val("");
                    putAwayDetailScreen.find("#putAwayBarCodeContainer").show()
                }else{ //outer or inner case 
                    putAwayDetailScreen.find("#qty_per_box").val($(this).attr("data-perqty"));
                    putAwayDetailScreen.find("#putAwayBarCodeContainer").hide();
                    putAwayDetailScreen.find("#qtyBoxContainer").hide();
                    putAwayDetailScreen.find("#noOfBoxContainer").show().find("#no_of_box").val("");
                    putAwayDetailScreen.find("#qtyContainer").find("#qty").val("").prop("readonly",true).val("");
                }
            }else{
                    if($(this).val() == 1){ //loose qty
                    putAwayDetailScreen.find("#qtyBoxContainer").hide().find("#qty_box").val("");
                    putAwayDetailScreen.find("#noOfBoxContainer").hide().find("#no_of_box").val("");
                    putAwayDetailScreen.find("#qtyContainer").find("#qty").prop("readonly",false).val("");
                    putAwayDetailScreen.find("#putAwayBarCodeContainer").hide()

                }else{ //outer or inner case 
                    if($(this).val() != 3 && (putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case") == 2 || putAwayDetailScreen.find('.case-detail-radio:checked').attr("data-case") == 3)){
                        putAwayDetailScreen.find("#putAwayBarCodeContainer").show().find("#put_away_barcode_textbox").val("");
                        putAwayDetailScreen.find("#qtyBoxContainer").show().find("#qty_box").val("");
                        putAwayDetailScreen.find("#qty_per_box").val($(this).attr("data-perqty"));
                    }else{
                        putAwayDetailScreen.find("#qtyBoxContainer").hide();
                        putAwayDetailScreen.find("#putAwayBarCodeContainer").hide();
                    }
                    putAwayDetailScreen.find("#noOfBoxContainer").show().find("#no_of_box").val("");
                    putAwayDetailScreen.find("#qtyContainer").find("#qty").val("").prop("readonly",true).val("");
                }
            }
            
        }
        
    });
    
    
    /**
     * @author Hitesh Tank
     * @Desc input no of box to calculate the quantity
     */
    $(document).on("input","#no_of_box",function(e){
        var selectedRadioBtn=putAwayDetailScreen.find('.case-detail-radio:checked');
        var textQty = putAwayDetailScreen.find("#qty");
        var selectStoreAs = putAwayDetailScreen.find("#storeAs").val();
        var selectedCase=selectedRadioBtn.attr("data-case");
        var qty = parseInt(selectedRadioBtn.attr("data-perqty"));
        var noOfBox=parseInt(selectedRadioBtn.attr("data-box"));
        var total=parseInt(selectedRadioBtn.attr("data-total"));
        var putAwayQty = parseInt(selectedRadioBtn.attr("data-putawayqty"));
        
        if(selectedCase == 3){
            if(selectStoreAs == 2){
                var qtyBox=parseInt(putAwayDetailScreen.find("#qty_box").val());
                var totalQty=qtyBox*$(this).val();
                if(totalQty > total){
                    PoundShopApp.commonClass._displayErrorMessage("No of boxes should be not exceed selected case.");
                    $(this).val("")
                    putAwayDetailScreen.find("#qty_box").val("")
                    return false;
                }else{
                    textQty.val(totalQty);
                }
            }else{
                if($(this).val() == ""){
                    $(this).val("")
                    textQty.val("")
                }else{
                    var totalQty = $(this).val() * qty;
                    if(totalQty > total){
                        PoundShopApp.commonClass._displayErrorMessage("Total qty should not be exceed pending putaway qty");
                        $(this).val("");
                        textQty.val("");
                        return false;
                    }else{
                        textQty.val(totalQty)
                    }
                }
            }
        }else{
           if($(this).val() == ""){
                $(this).val("")
                textQty.val("")
            }else{
                var totalQty = $(this).val() * qty;
                if(totalQty > total){
                    PoundShopApp.commonClass._displayErrorMessage("Total qty should not be exceed pending putaway qty");
                    $(this).val("");
                    textQty.val("");
                    return false;
                }else{
                    textQty.val(totalQty)
                }
            }
        }
        
        
        
        
     });
     
     function undefinedValue(value){
      if(value !== undefined && value !==""){
          return value;
      }else{
          return 0;
      }
  }
  
     $(document).on("input","#qty_box",function (e) {
        var selectedRadioBtn = putAwayDetailScreen.find('.case-detail-radio:checked');
        var textQty = putAwayDetailScreen.find("#qty");
        var selectStoreAs = putAwayDetailScreen.find("#storeAs").val();
        var selectedCase = selectedRadioBtn.attr("data-case");
        var qty = parseInt(selectedRadioBtn.attr("data-perqty"));
        var noOfBox = parseInt(selectedRadioBtn.attr("data-box"));
        var total = parseInt(selectedRadioBtn.attr("data-total"));
        var putAwayQty = parseInt(selectedRadioBtn.attr("data-putawayqty"));
        
        if($(this).val() !== 0 && $(this).val() !== undefined){
            if((qty % $(this).val()) == 0){ //must be devisable
                var noOfBox=undefinedValue(putAwayDetailScreen.find("#no_of_box").val());
                var totalQty=noOfBox*$(this).val();
                if(totalQty > total){
                    PoundShopApp.commonClass._displayErrorMessage("Total quantity should not be exceed putaway qty.");
                    $(this).val("");
                    putAwayDetailScreen.find("#no_of_box").val("")
                    putAwayDetailScreen.find("#qty").val("");
                }else{
                    putAwayDetailScreen.find("#qty").val(totalQty);
                }
            }else{
                PoundShopApp.commonClass._displayErrorMessage("Qty/Box must be divisable by no of inner boxes.");
                $(this).val("");
                return false;
            }
        }
        
    });
     /**
     * @author Hitesh Tank
     * @Desc input quantity
     */
      $(document).on("input","#qty",function(e){
        var selectedRadioBtn=putAwayDetailScreen.find('.case-detail-radio:checked');
        var selectStoreAs = putAwayDetailScreen.find("#storeAs").val();
        var selectedCase=selectedRadioBtn.attr("data-case");
        var qty = parseInt(selectedRadioBtn.attr("data-perqty"));
        var noOfBox=parseInt(selectedRadioBtn.attr("data-box"));
        var total=parseInt(selectedRadioBtn.attr("data-total"));
        var putAwayQty=parseInt(selectedRadioBtn.attr("data-putawayqty"));
        
        if($(this).val() == ""){
           $(this).val("") 
        }else{
            if($(this).val() > total){
                PoundShopApp.commonClass._displayErrorMessage("Quantity should be not exceed put away pending quantity.");
                $(this).val(total);
                return false;
            }
        }
        
     });
    
    /**
     * @author Hitesh Tank
     * @Desc scan the location for moving product
     */
    $("body #move_location").scannerDetection(function(e)
	{   
    	init_set_location_detatils('1'); 
	});

	$('body').on('input','#move_location', function(e) {
		var el = this;
		setTimeout(function() {
		    init_set_location_detatils(2,el);
		},300);
    });
    
    $("body #scan-pallet-barcode-textbox").scannerDetection(function(e)
	{   
    	init_set_pallet_location('1'); 
	});

	$('body').on('input','#scan-pallet-barcode-textbox', function(e) {
		var el = this;
		setTimeout(function() {
		    init_set_pallet_location(2,el);
		},300);
    });
    
    
    init_set_location_detatils = function(type,el)
    {
            $(el).prev('span.scan_type').remove();

            if(type == '1')
            {
                    html = '<span class="scan_type font-10-dark bold d-block mt-1">Scanner</span>';
            }
            else 
            {
                    html = '<span class="scan_type font-10-dark bold d-block mt-1">Manual</span>';
                    init_typeahead(el);
            }

            // $(el).before(html);
    }
    
    init_set_pallet_location = function(type,el)
    {
            $(el).prev('span.scan_type').remove();

            if(type == '1')
            {
                    html = '<span class="scan_type font-10-dark bold d-block mt-1">Scanner</span>';
            }
            else 
            {
                    html = '<span class="scan_type font-10-dark bold d-block mt-1">Manual</span>';
                    init_typelocation(el);
            }

            // $(el).before(html);
    }
    
    init_typeahead = function(el = $('.set_location_details')){
		
        var QtyFitLocation = $(document).find("#quantity-fit-location");
        
        var warehouse_id = $('#warehouse_id').val();

        var booking_id = $('#booking_id').val();

        $(el).next('span.location_type').remove();
		
        $(el).typeahead({
            ajax: {
                    url: BASE_URL+'api-location-auto-suggest-on-input',
                    method: 'GET',
                    extra_data: {
                            "keyword" : $(el).val(), 
                            'module' : 'putaway'
                    },
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                },
                autoSelect: true,
                grepper: false,
                highlighter: false,
                displayField: 'location',
                valueField: 'location_type',
                scrollBar: false,
                on_select_target_blank: false,
                item: '<li><a href="#"></a></li>',
                onSelect: function (value)
                {
                    
                    $(el).val(value.text);
                    $(el).next('span.location_type').remove();
                    html = '<span class="location_type font-10-dark bold d-block mt-1">'+value.value+'</span>';
                    $(el).after(html);
                    $(document).find("#put_away_location_id").val($(value.item[0]).attr("data-id"))
                    $(document).find("#put_away_warehouse_id").val($(value.item[0]).attr("data-site_id"))
                    QtyFitLocation.show();
                    if($(value.item[0]).attr("data-qty_fit_in_location") !== "" && $(value.item[0]).attr("data-qty_fit_in_location") !== undefined && $(value.item[0]).attr("data-qty_fit_in_location")!==0){
                        QtyFitLocation.find("#qty_fit_location").val($(value.item[0]).attr("data-qty_fit_in_location"));
                    }else{
                        console.log('else');
                        QtyFitLocation.find("#qty_fit_location").val("");
                    }
                }
        });
    }
    
    init_typelocation = function(el = $('.set_location_details')){
		
    //    var QtyFitLocation = $(document).find("#quantity-fit-location");
        
     //   var warehouse_id = $('#warehouse_id').val();

        //var booking_id = $('#booking_id').val();

        $(el).next('span.location_type').remove();
		
        $(el).typeahead({
            ajax: {
                    url: BASE_URL+'api-location-auto-suggest-on-input',
                    method: 'GET',
                    extra_data: {
                            "keyword" : $(el).val(), 
                            'module' : 'putaway-pallet'
                    },
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                },
                autoSelect: true,
                grepper: false,
                highlighter: false,
                displayField: 'location',
                valueField: 'location_type',
                scrollBar: false,
                on_select_target_blank: false,
                item: '<li><a href="#"></a></li>',
                onSelect: function (value)
                {
                    $(el).val(value.text);
                    $(el).next('span.location_type').remove();
                    html = '<span class="location_type font-10-dark bold d-block mt-1">'+value.value+'</span>';
                    $(el).after(html);
                    $("#scan-product-textbox").val("");
                    productSearch('pallet');
                    
                }
        });
    }
    //        $("#scan-product-textbox").val("");
//       productSearch('pallet');
//       
//       
//     $(document).on('input','#move_location',function(e){
//         var cObj=$(this);
//         $(this).next('span.location_type').remove();
//         if($(this).val() != "")
//		{
//         $.ajax({
//                url:locationKeywordURL.val() ,
//                type: "GET",
//                data: {
//                	"keyword" : $(this).val(),
//                },
//                datatype: 'JSON',
//                headers: {
//                    'Authorization': 'Bearer ' + API_TOKEN,
//                },
//                beforeSend: function () {
//                    
//                },
//                success: function (response) {
//                	if(response.status == 1)
//                	{
//                                var QtyFitLocation = $(document).find("#quantity-fit-location");
//                		if(typeof response.data.type_of_location != 'undefined')
//                		{	
//                			html = '<span class="location_type font-10-dark bold d-block mt-1">'+response.data.location_type+'</span>';
//                			cObj.after(html);
//                                         $(document).find("#put_away_location_id").val(response.data.id)
//                                         $(document).find("#put_away_warehouse_id").val(response.data.site_id)
//                                         
//                                        QtyFitLocation.show();
//                                        if(response.data.stock_suggestion !== 0){
//                                            QtyFitLocation.find("#qty_fit_location").val(response.data.stock_suggestion);
//                                        }else{
//                                                QtyFitLocation.find("#qty_fit_location").val("");
//                                        
//                                        }
//                                        
//                		}else{
//                                    QtyFitLocation.hide();
//                                }
//                	}
//                	else
//                	{
//                		cObj.val('');
//                	}
//                },
//                error: function (xhr, err) {
//                  cObj.val('');
//                  $("#page-loader").hide();
//                  PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
//                }
//            });	
//        }
//     });
     
     
   