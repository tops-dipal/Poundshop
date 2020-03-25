

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
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
    var poundShopTotes = function ()
    {
        $(document).ready(function ()
        {
           c._initialize();
        });
    };

    var c = poundShopTotes.prototype;
    
    c._initialize = function ()
    {
        productSearch('pallet');
    };

    function productSearch(searchFrom){
        
      var sortBy=$('#sort_by').val()
      var sortDirection=$('#sort_direction').val()
      var jobType =  $('input[name="job_type"]:checked').val()
      var pickbulkjobs =  $('input[name="pickbulkjobs"]:checked').val()
    
      var productSearch=$("#scan-product-textbox").val()
        $.ajax({
            url: $("#putaway-joblist-product-url").val(),
            type: "get",
            data:{job_type:jobType,pickbulkjobs:pickbulkjobs,sort_by:sortBy,sort_direction:sortDirection,product_search:productSearch,search_by:searchFrom},
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
                            productListingID.show();
                            scanProductTextBox.show();
                            pendingColumnDiv.show();
                            pendingQty.text(response.data.total_pending_qty)
                            pendingProduct.text(response.data.total_pending_products)
                            $("#putaway-id").html(response.data.data);
                        }else{
                            productListingID.hide();
                            pendingColumnDiv.hide();
                            pendingQty.text(0);
                            pendingProduct.text(0)
                            $("#putaway-id").html("");
                            PoundShopApp.commonClass._displayErrorMessage(response.message);
                        }
                    }
                    else{
                        productListingID.hide();
                        pendingColumnDiv.hide();
                        pendingQty.text(0);
                        pendingProduct.text(0);
                        $("#putaway-id").html("");
                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                productListingID.hide();
                pendingColumnDiv.hide();
                pendingQty.text(0);
                pendingProduct.text(0)
                PoundShopApp.commonClass._displayErrorMessage(response.message);
            }

        });
    }
  
    
    $(document).on("keypress","#scan-product-textbox",function(event){
         
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            productSearch('pallet');
        }
	
    })
    
    $(document).on("click",'td.sorting[sort-by],td.sorting_asc[sort-by],td.sorting_desc[sort-by]',function(e)
    {
      $('#sort_by').val($(this).attr("sort-by"))
      $('#sort_direction').val($(this).attr("sort-order"))
      productSearch();
      return false;
    });
  
    $(document).on("change",".pickbulkjobs,.job_type",function(e){
        productSearch('pallet');
    });
    
  
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopTotes = new poundShopTotes();
})(jQuery);