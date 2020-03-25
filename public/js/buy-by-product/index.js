/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = '';
   
    var poundShopCartons = function ()
    {
        var existingPoTable;
        var bindPoData;
        var bindProductInfo;
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopCartons.prototype;
    
    c._initialize = function ()
    {
       /*existingPoTable.draw();
        $('.custom-select-search').selectpicker({
          liveSearch:true,
          size:10
        });*/

    };
    getCookie=function(name){
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        }
        else
        {
            begin += 2;
            var end = document.cookie.indexOf(";", begin);
            if (end == -1) {
            end = dc.length;
            }
        }
    // because unescape has been deprecated, replaced with decodeURI
    //return unescape(dc.substring(begin + prefix.length, end));
        var ans=null;
        var str=decodeURI(dc.substring(begin + prefix.length, end));
        if(str!=null)
        {

            var strRes=str.split(";");
            ans=strRes[0];
        }
        return ans;
    }
    // bind table view in pop up of existing po
    bindPoData = function(data){
        varDataHtml = "";
        //console.log(document.cookie.indexOf('selectedPO='));
        var selectedPO=getCookie('selectedPO');
        for(var i = 0; i< data.length; i++){
            var varTotalCost='0.00';
            var expDeliDate='--';
            var checked="";
            if(data[i].total_cost.length!=0)
            {
                varTotalCost=data[i].total_cost;
            }
            if(data[i].exp_deli_date.length!=0)
            {
                expDeliDate=data[i].exp_deli_date;
            }
            if(selectedPO==data[i].id)
            {
                checked="checked";
            }
            varDataHtml+='<tr>';
            varDataHtml+="<td><div class='d-flex'><label class='fancy-radio mr-3'><input type='radio' name='po_id' value='"+data[i].id+"' "+checked+"/><span><i></i></span></label></div></td>";
            varDataHtml+='<td>'+data[i].po_number+'</td>';
            varDataHtml+='<td>'+data[i].date+'</td>';
            varDataHtml+='<td>'+expDeliDate+'</td>';
            varDataHtml+='<td>'+data[i].total_num_items+'</td>';
            varDataHtml+='<td>'+POUNDSHOP_MESSAGES.common.pound_sign+' '+varTotalCost+'</td>';
            varDataHtml+='</tr>';
            $("body").data( "information"+data[i].id, data[i] );
        }
        return varDataHtml;
    }


    //after product add to existing po and create po bind product info if product newly created
    bindProductInfo=function(data){
        $('#product_id').val(data['id']);
        var productInfoHtml=`<div class="container-info">
                                <div class="form">
                                    <div class="form-field">
                                        <label class="custom-lbl"><img src="`+data['main_image_internal_thumb']+`" width="100" height="50" style="object-fit: contain;"></label>
                                        
                                    </div>
                                    <div class="form-field">
                                        <label class="custom-lbl">Barcode:</label>
                                        <span>{{ $barcode }}</span>
                                    </div>
                                    <div class="form-field">
                                        <label class="custom-lbl">SKU:</label>
                                        <span class="total_vat">`+data['sku']+`</span>
                                    </div>
                                    <div class="form-field">
                                        <label class="custom-lbl">Product Title:</label>
                                        <span class="total_tax">`+data['title']+`</span>
                                    </div>
                                     <div class="form-field">
                                        <a class="btn btn-blue" href="{{ route('buy-by-product.index') }}"> Next Product </a>
                                        
                                    </div>
                                </div>               
                            </div>`;
        $('.load_data').html(productInfoHtml);
    }
 
    $(".actionBtn").click(function(){
         var btntext=$(this).attr('id');
         $('.perform_action').val(btntext);
    })
  //fetch all existing drafted po of selected supplier
    $('#supplierPosForm').validate({
        focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function(form, validator) {
            console.log(validator);
        if (!validator.numberOfInvalids())
            return;
        var errors = validator.numberOfInvalids();
        if (errors) {                    
            validator.errorList[0].element.focus();
        }
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-30
        }, 1000);
      },
        errorElement: 'span',
        errorClass: 'invalid-feedback', // default input error message class
        ignore: [],
        rules: {
            "supplier_id": {
                required: true,
            },
        },
        errorPlacement: function (error, element) {
            if(element.attr("id") == "supplier_id"){
            error.insertAfter(element.closest(".dropdown"));
            }
            else
            {
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
            var btntext=$(".perform_action").val();
            console.log(btntext);
            if(btntext=="create_po_btn")
            {
                var supplier_country=$("#supplier_id").find('option:selected').attr('data-country');
                //console.log(supplier_country);
                if(supplier_country !== '230'){
                    $(".po_import_type").val(2);
                    $("#country_id").val(supplier_country)
                }
                else
                {
                    $(".po_import_type").val(1);
                    $("#country_id").val(supplier_country)
                }
                var dataString = $("#supplierPosForm").serialize();
               // console.log(dataString);return false;
                $('.submit_create_po').attr('disabled', true);
                $.ajax({
                        type: "POST",
                        url: BASE_URL+'api-create-po-product',
                        data: dataString+'&barcode='+$('#barcode').val(),
                       // processData: false,
                        headers: {
                            'Authorization': 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                            console.log(response);
                            $('.submit_create_po').attr('disabled', false);
                            $("#page-loader").hide();
                            //console.log(response);return false;
                            if (response.status == 1) {
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                              // document.cookie="comesFrom=Buyer-Enquiry";
                                window.location.href = response.data.editPoURL;
                            }
                        },
                        error: function (xhr, err) {
                            $('.submit_create_po').attr('disabled', false);
                            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                        }
                    });
            }
            else
            {
                var supplier_id = $("#supplier_id").val();
           
                $('.add_to_existing_po_btn').attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: BASE_URL+'api-existing-draft-pos',
                    data: "supplier_id="+supplier_id,
                    processData: false,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        $('.add_to_existing_po_btn').attr('disabled', false);
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            $('#addToExistingPoModel').modal('show');
                            console.log(response.data.data.length);
                            
                               // $('.submit_add_product_to_po').attr('disabled',false);

                            $("#existingPosTable >tbody").html(bindPoData(response.data.data));

                        }
                         PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    },
                    error: function (xhr, err) {
                        $('.add_to_existing_po_btn').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });

            }
            
        }
    });
  

    //create new Po
   /* $(document.body).on('click', '#create_po_btn', function(){
        alert("Hiii="+$(this).text());
       $('#supplierPosForm').validate({
        focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function(form, validator) {
            console.log(validator);
        if (!validator.numberOfInvalids())
            return;
        var errors = validator.numberOfInvalids();
        if (errors) {                    
            validator.errorList[0].element.focus();
        }
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-30
        }, 1000);
      },
        errorElement: 'span',
        errorClass: 'invalid-feedback', // default input error message class
        ignore: [],
        rules: {
            "supplier_id": {
                required: true,
            },
        },
        errorPlacement: function (error, element) {
            console.log(error);
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
            var supplier_country=$("#supplier_id").find('option:selected').attr('data-country');
            if(supplier_country !== '230'){
                $(".po_import_type").val(2);
                $("#country_id").val(supplier_country)
            }
            else
            {
                $(".po_import_type").val(1);
                $("#country_id").val(supplier_country)
            }
            var dataString = $("#supplierPosForm").serialize();
            $('.submit_create_po').attr('disabled', true);
            $.ajax({
                    type: "POST",
                    url: BASE_URL+'api-create-po-product',
                    data: dataString+'&barcode='+$('#barcode').val(),
                    processData: false,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        console.log(response);
                        $('.submit_create_po').attr('disabled', false);
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            window.location.href = response.data.editPoURL;
                        }
                    },
                    error: function (xhr, err) {
                        $('.submit_create_po').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });

        }
    });
   });
    */
    //Search outside the datatables
    $('#search_data').on('input propertychange',function(event)
    {
        /*var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){*/
        $('#searchProductByBarcodeForm').attr('action',WEB_BASE_URL+'/search-barcode/'+$('#search_data').val());
        $('#searchProductByBarcodeForm').submit();
        //}
    });

    //initialize data table for pop up table in existing po list
   /* existingPoTable= $('#existingPosTable').DataTable({
      bFilter: false, 
      bInfo: false,
      processing:true,
    
      columns :[
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false}
      ],
      bPaginate: false,
      fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
        },
        fnDrawCallback: function (oSettings, json) {
            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
        },
  });*/
  
    $('.refresh').on('click',function()
    {    
        $('#search_data').val('');
        PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
    });
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);


var bindProductInfo=function(data){
        $('#product_id').val(data['id']);
        var productInfoHtml=`<div class="form">
                                    <div class="form-field">
                                        <label class="custom-lbl"><img src="`+data['main_image_internal_thumb']+`" width="100" height="50" style="object-fit: contain;"></label>
                                        
                                    </div>
                                    <div class="form-field">
                                        <label class="custom-lbl">Barcode:</label>
                                        <span>`+$('#barcode').val()+`</span>
                                    </div>
                                    <div class="form-field">
                                        <label class="custom-lbl">SKU:</label>
                                        <span class="total_vat">`+data['sku']+`</span>
                                    </div>
                                    <div class="form-field">
                                        <label class="custom-lbl">Product Title:</label>
                                        <span class="total_tax">`+data['title']+`</span>
                                    </div>
                                     <div class="form-field">
                                        <a class="btn btn-blue" href="`+WEB_BASE_URL+'/buy-by-product'+`"> Next Product </a>
                                        
                                    </div>
                                </div>`;
        $('.load_data').html(productInfoHtml);
    }
//add Product To selected existing po
function addProductToPo()
{
    $('.product_id').val($('#product_id').val());
    var dataString = $("#addProductToPoForm").serialize();
    
    if($("#existingPosTable input[name='po_id']:checked").val()==undefined)
    {
        bootbox.alert({
            title: "Alert",
            message: POUNDSHOP_MESSAGES.buy_by_product.alert_msg.alert_select_po,
            size: 'small'
        });
        return false
    }
    /*else
    {
        if($("#existingPosTable input[name='po_id']:checked").val()==$('#product_po_id').val())
        {
            bootbox.alert({
            title: "Alert",
            message: POUNDSHOP_MESSAGES.buy_by_product.product_already_in_po,
            size: 'small'
            });
            return false
        }

    }*/
    $('.submit_add_product_to_po').attr('disabled', true);
    $.ajax({
            type: "POST",
            url: BASE_URL+'api-add-product-to-existing-po',
             data: dataString+'&barcode='+$('#barcode').val(),
            processData: false,
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                $('.submit_add_product_to_po').attr('disabled', false);
                $("#page-loader").hide();
                if (response.status_code == 200) {
                    document.cookie="selectedPO="+response.data.selectedPO;
                    $('#addToExistingPoModel').modal('hide');
                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    bindProductInfo(response.data.productInfo);
                    $('#product_po_id').val(response.data.selectedPO);
                }
                else
                {
                    PoundShopApp.commonClass._displayErrorMessage(response.message);
                }
            },
            error: function (xhr, err) {
                 $("#page-loader").hide();
                console.log(xhr.responseJSON.message);
                 PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message);
                $('.submit_add_product_to_po').attr('disabled', false);
                
            }
        });
}

