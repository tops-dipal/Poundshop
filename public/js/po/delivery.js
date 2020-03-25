/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
     var poIds = [];  
     var disIds = [];  
     var totalQty=[];
     var deliveryTable=$("#delivery-content-table");
     var deliveryPopUp=$("#deliveryModal");
    var poundShopDelivery = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopDelivery.prototype;
    
    c._initialize = function ()
    {
    };
    
    function getFilters(){
        var filters=[];
        if($("#show_over_delivered").val() == 1){
            filters.push(2)
        }
        if($("#show_under_delivered").val() == 1){
            filters.push(1)
        }
        return filters;
    }
    
    /**
     * @author Hitesh tAnk
     * Desc: Cancelled PO
     */
    $(document).on("click",".cancelled-item-po",function(event){
        $.ajax({
            url: $("#productCancelledURL").val(),
            type: "post",
            data:{booking_id:$("#booking_id").val(),"po_id":$(this).attr("data-purchaseid"),"discrepancy_id":$(this).attr("data-disc"),"product_id":$(this).attr("data-productid"),'qty':$(this).attr("data-qty")},            
            headers: {
               Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                    $("#page-loader").hide();
                    if (response.status == 1) {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        deliveryTable.find("tbody").html(response.data);
                    }else{
                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }

            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
    })
    
    /**
     * @author Hitesh tAnk
     * Desc: move to new po
     */
    $(document).on("click",".move-new-po-btn",function(event){
         $.ajax({
            url: $("#moveToNewPOURL").val(),
            type: "post",
            data:{booking_id:$("#booking_id").val(),"po_id":$(this).attr("data-purchaseid"),"discrepancy_id":$(this).attr("data-disc"),"product_id":$(this).attr("data-productid"),'qty':$(this).attr("data-qty")},
            headers: {
               Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                    $("#page-loader").hide();
                    if (response.status == 1) {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        deliveryTable.find("tbody").html(response.data);
                    }else{
                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }

            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
        
    })
    
    /**
     * @author Hitesh tAnk
     * Desc: move to new po
     */
    $("#send-email-supplier-btn").click(function(event){
        bootbox.confirm({
            title: "Confirm",
            message: "Are you sure to send email to supplier?",
            buttons: {
                cancel: {
                    label: 'No',
                    className: 'btn-gray'
                },
                confirm: {
                    label: 'Yes',
                    className: 'btn-red'
                }
            },
            callback: function (result)
            {
                if (result) {
                    $.ajax({
                        url: BASE_URL+'api-material-receipt-supplier-email',
                        type: "POST",
                        datatype:'JSON',
                        data:{'id':$("#booking_id").val()},
                        headers: 
                        {
                            'Authorization': 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () 
                        {
                            $("#page-loader").show();
                        },
                        success: function (response) 
                        {
                            $("#page-loader").hide();
                            if (response.status == 1) {
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                PoundShopApp.commonClass.table.draw();
                            }
                        },
                        error: function (xhr, err) 
                        {
                           $("#page-loader").hide();
                        }
                    });
                } 
            }
        });
    });
    
    /**
     * @author Hitesh Tank
     * Desc: Return to Supplier / Keep it items function
     */
    $(document).on("click",".returntoSupplierBtn",function(event){
        //$("#deliveryModal").modal("show");
        $.ajax({
            url: $("#productLocationURL").val(),
            type: "get",
            data:{po_product_id:$(this).attr("data-productid"),"po_id":$(this).attr("data-purchaseid"),"discrepancy_id":$(this).attr("data-disc")},
            headers: {
               Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                    $("#page-loader").hide();
                    if (response.status == 1) {
                        deliveryPopUp.modal("show");
                        deliveryPopUp.find(".modal-body").html(response.data);
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
 
    
    $(document).on("change","#show_over_delivered,#show_under_delivered,#show_no_discrepancy",function(e){
        if ($(this).is(':checked')) {
            $(this).val(1);
          } else {
            $(this).val(0);
          }
          var filters=getFilters();
         
        var noDis=0;
         if($(this).attr("id") == 'show_no_discrepancy'){
            noDis=1; 
         }
           $.ajax({
            url: $("#deliveryData").val(),
            type: "get",
            data:{'po_id':$("#po_id").val(),'filters':filters,'dis':noDis},
            headers: {
               Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                    $("#page-loader").hide();
                    if (response.status == 1) {
                         $("#delivery-content-table tbody").html(response.data);
                    }else{
                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }

            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
          
          
          
          
    })
    
 
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopDelivery = new poundShopDelivery();

})(jQuery);