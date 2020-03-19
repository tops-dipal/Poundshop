/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";

    var poundShopCartons = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopCartons.prototype;
    
    c._initialize = function ()
    {
        var options={
            format: 'dd-M-yyyy',
            todayHighlight: true,
            autoclose: true,
         };
          $("#po_date,#po_cancel_date,#exp_deli_date").datepicker(options);
           $('.custom-select-search').selectpicker({
              liveSearch:true,
              size:10
          });

    };
    

     $("#create-po-form").validate({
            focusInvalid: false, // do not focus the last invalid input
            invalidHandler: function(form, validator) {

            if (!validator.numberOfInvalids())
                return;

            $('html, body').animate({
                scrollTop: $(validator.errorList[0].element).offset().top-30
            }, 1000);
                               },
            errorElement: 'span',
            errorClass: 'invalid-feedback', // default input error message class
            ignore: [],
            rules: {
                
                "supplier": {
                    required: true,
                },
                "supplier_contact": {
                    required: true,
                },
                "po_status": {
                    required: true,
                },
                "recev_warehouse": {
                    required: true,
                },
                "po_import_type":{
                    required:true
                },
//                "supplier_order_number":{
//                     required:true
//                }
            },
            messages:{
                "supplier": {
                    required: "Please select supplier.",
                },
                "supplier_contact": {
                    required: "Please select supplier contact.",
                },
                "po_status": {
                    required: "Please select po status.",
                },
                "recev_warehouse": {
                    required: "Please select receiving warehouse.",
                },
                "po_import_type": {
                    required: "Please select U.K PO/Import PO.",
                },
//                "supplier_order_number":{
//                     required:"Please enter supplier order number."
//                }
                

            },
            errorPlacement: function (error, element) {
                if(element.attr("name") == "supplier"){
                    error.insertAfter(element.closest(".dropdown"));
                }else{
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
                var dataString = $("#create-po-form").serialize();
                $('#create-po-button').attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: $("#create-po-form").attr("action"),
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
                        $('#create-po-button').attr('disabled', false);
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            setTimeout(function () {
                                window.location.href = WEB_BASE_URL +'/purchase-orders/'+response.data.data['id']+'/edit#items';
                            }, 1000);
                        }
                    },
                    error: function (xhr, err) {
                        $('#create-po-button').attr('disabled', false);
                        $("#page-loader").hide();
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });

            }
        });
        
      $("#country_id").change(function(event){
          if($(this).val() !== '230'){
             $("#po_import_type").val(2);
             $("#hidden_country").val($(this).val());
             $("#po_import_type").attr("disabled",true);
             $(".incorn_mode").show();
          }else{
              $("#po_import_type").val(1);
              $("#hidden_country").val(230);
              $("#po_import_type").attr("disabled",true);
              $(".incorn_mode").hide();
          }
      });
     //Supplier Selection
     $("#supplier").change(function(event){
         var currentObj=$(this);
         //var options="";
         //options += "<option value=''>Select Supplier Contact</option>";
//         if($(this).find('option:selected').attr('data-supplier') !== undefined && $(this).find('option:selected').attr('data-supplier') !== ""){
//             var supplier_contact=$.parseJSON($(this).find('option:selected').attr('data-supplier')); 
//            if(supplier_contact.length > 0){
//                for(var i=0;i<supplier_contact.length;i++){
//                    if(supplier_contact[i]['is_primary'] == 1){
//                        options += "<option selected='selected' value='"+supplier_contact[i]['id']+"'>"+supplier_contact[i]['name']+"</option>";
//                    }else{
//                        options += "<option value='"+supplier_contact[i]['id']+"'>"+supplier_contact[i]['name']+"</option>";
//                    }
//                }
//            }
//         }


         var supplier_country=$(this).find('option:selected').attr('data-country');
         
         if(supplier_country !== '230'){
             $("#po_import_type").val(2);
             $("#po_import_type").attr("disabled",true);
             $("#country_id").val(supplier_country)
             $("#hidden_country").val(supplier_country);
             $("#country_id").attr("disabled",false);
             $("#country_id option[value='230']").hide();
             //$('#country_id').selectpicker('refresh')
             $(".incorn_mode").show();
         }else{
             $(".incorn_mode").hide();
             $("#country_id option[value='230']").show();
             $("#hidden_country").val(230);
             $("#country_id").val(supplier_country)
             $("#country_id").attr("disabled",true);
             $("#po_import_type").val(1);
             $("#po_import_type").attr("disabled",true);
            // $('#country_id').selectpicker('refresh')
    
         }
         
         var supplierId = $(this).val();
         
         $.ajax({
                url: BASE_URL+'api-supplier-contacts?supplier_id='+supplierId,
                type: "GET",
                datatype: 'JSON',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    currentObj.closest("div").addClass("control-loading");
                    currentObj.attr("disabled",true)
                    
                },
                success: function (response) 
                {
                    
                    currentObj.closest("div").removeClass("control-loading");
                    currentObj.attr("disabled",false)
                    if(response.status == true)
                    {
                       $("#supplier_contact").html(response.data);
                    }    else{
                        $("#po_import_type").val("");
                                     $("#country_id option[value='230']").show();

                    }
                },
                error: function (xhr, err) {
                    $("#po_import_type").val("");
                                 $("#country_id option[value='230']").show();

                    currentObj.closest("div").removeClass("control-loading");
                    currentObj.attr("disabled",false)
                    
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
         
         
         
       //  $("#supplier_contact").html(options);
     });
     
     //warehouse change
     $("#recev_warehouse").change(function(event){
         var warehouse=$(this).find('option:selected');
         
         if(warehouse.val() !== undefined && warehouse.val() !== ""){
            var warehouse_detail=$.parseJSON(warehouse.attr('data-info'));
            $("#address1").val(warehouse_detail['address_line1']);
            $("#address2").val(warehouse_detail['address_line2']);
            $("#pincode").val(warehouse_detail['zipcode']);
            $("#country").val($.parseJSON(warehouse.attr('data-country'))['name']);
            $("#state").val($.parseJSON(warehouse.attr('data-state'))['name']);
            $("#city").val($.parseJSON(warehouse.attr('data-city'))['name']);
         }else{
            $("#address1").val('');
            $("#address2").val('');
            $("#pincode").val('');
            $("#country").val('');
            $("#state").val('');
            $("#city").val('');
         }
        
         
         
                 
     });
     //supplier order number
     $(document).on('keydown', '#supplier_order_number', function(e) {
                var a = e.key;
                if (a.length == 1) return /[a-z]|[0-9]|&/i.test(a);
                return true;
        })
     
window.PoundShopApp = window.PoundShopApp || {}
window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);


$(window).on('load', function() 
{
    $('#recev_warehouse').trigger('change');
});