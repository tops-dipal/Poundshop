/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    
    var poundShopBooking = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopBooking.prototype;

    c._initialize = function ()
    {
        $(document).find('#product_qc').trigger('change');
        $(document).find('.qc_list_dropdown').trigger('change');
        $(document).find(".qc_list_dropdown").selectpicker("refresh");

        $('.btn-checklist-toggle').click(function(){
            if($('.checklist-container').hasClass('open'))
            {
                $.ajax({
                url: $("#sidebar_access_url").val(),
                type: "post",
                //processData: false,
                data:{booking_id:$('#booking_id').val()},
                headers: {
                   Authorization: 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    //$("#page-loader").show();
                },
                 success: function (response) {
                       
                    //$('.checklist-container').html(response);
                     $(response).insertAfter('.checklist-container .btn-checklist-toggle')    
                    $(document).find('#product_qc').trigger('change');
                    $(document).find("#product_qc").selectpicker("refresh");
                    $(document).find('.qc_list_dropdown').trigger('change');
                    $(document).find(".qc_list_dropdown").selectpicker("refresh");
                 },
                 error: function (xhr, err) {
                    $("#page-loader").hide();
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                 }
            });
            }
        })
    };
    $.ajaxSetup({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
    });


    //add More Receive Pallets
    $(document).on('click', '#add_more_receive_pallet', function(){
        var receivePalletCount=parseInt($("input[name='total_receive_pallet']").val())+1;
        var palletListArr=jQuery.parseJSON($("#pallet_list").val());
        var addReceivePalletStr=`<div class="d-flex align-items-center mb-2" id="pallet_receive_div_`+receivePalletCount+`">
                <span class="font-14-dark mr-2 flex-one">
                    <select class="form-control" name="receive_pallets[]" id="receive_pallets_`+receivePalletCount+`">
                        <option value="">`+POUNDSHOP_MESSAGES.mr_sidebar.select_pallet+`</option>`;
        $.each(palletListArr, function( index, value ) {
          addReceivePalletStr+="<option value='"+value.id+"'>"+value.name+"</option>";
        });
                       
        addReceivePalletStr+= `</select>
                </span>
                <span class="font-14-dark mr-2 flex-one">
                    <input type="text" placeholder="`+POUNDSHOP_MESSAGES.common.quantity+`" name="receive_num_of_pallets[]" id="receive_num_of_pallets_`+receivePalletCount+`" value="" class="form-control">
                </span>
                <span class="font-14-dark ml-2">
                    <a title="`+POUNDSHOP_MESSAGES.common.delete+`" class="btn-delete btn-receive-delete-pallet" href="javascript:void(0);" attr-curr-div="pallet_receive_div_`+receivePalletCount+`"  id="pallet_receive_del_`+receivePalletCount+`"><span style="font-size: 16px" class="icon-moon icon-Cancel"></span></a>
                </span>
            </div>`;
        $('#add_more_receive_pallet_div').append(addReceivePalletStr);
        $("input[name='total_receive_pallet']").val(receivePalletCount);
    });

    //add More Return  Pallets
    $(document).on('click', '#add_more_return_pallet', function(){
        var returnPalletCount=parseInt($("input[name='total_return_pallet']").val())+1;
        var palletListArr=jQuery.parseJSON($("#pallet_list").val());

         var addReturnPalletStr=`<div class="d-flex align-items-center mb-2" id="pallet_return_div_`+returnPalletCount+`">
                <span class="font-14-dark mr-2 flex-one">
                    <select class="form-control" name="return_pallets[]" id="return_pallets_`+returnPalletCount+`">
                        <option value="">`+POUNDSHOP_MESSAGES.mr_sidebar.select_pallet+`</option>`;
        $.each(palletListArr, function( index, value ) {
          addReturnPalletStr+="<option value='"+value.id+"'>"+value.name+"</option>";
        });
        addReturnPalletStr+= `</select>
                </span>
                <span class="font-14-dark mr-2 flex-one">
                    <input type="text" placeholder="`+POUNDSHOP_MESSAGES.common.quantity+`" name="return_num_of_pallets[]" id="return_num_of_pallets_`+returnPalletCount+`" value="" class="form-control">
                     
                </span>
                <span class="font-14-dark ml-2">
                    <a title="`+POUNDSHOP_MESSAGES.common.delete+`"  class="btn-delete btn-return-delete-pallet" href="javascript:void(0);"  id="pallet_return_del_`+returnPalletCount+`" attr-curr-div="pallet_return_div_`+returnPalletCount+`"><span style="font-size: 16px" class="icon-moon icon-Cancel"></span></a>
                </span>
            </div>`;
        $('#add_more_return_pallet_div').append(addReturnPalletStr);
        $("input[name='total_return_pallet']").val(returnPalletCount);
    });


    //remove receive pallet specific div

    $(document).on('click', '.btn-receive-delete-pallet', function(){
        var deleteId=$(this).attr('data-delete');
        var removeDiv=$(this).attr('attr-curr-div');
        if(deleteId==undefined && deleteId!='')
        {
            var receivePalletCount=parseInt($("input[name='total_receive_pallet']").val());
            $("input[name='total_receive_pallet']").val(--receivePalletCount);
            $('#'+removeDiv).remove();
        }
        else
        {
            bootbox.confirm({ 
                title: "Confirm",
                message: "Are you sure you want to delete selected records? This process cannot be undone.",
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'Delete',
                        className: 'btn-red'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {
                       
                        $.ajax({
                            url: BASE_URL + 'api-booking-pallet/'+deleteId,
                            type: "delete",
                            processData: false,
                            data:{id:deleteId},
                            headers: {
                               Authorization: 'Bearer ' + API_TOKEN,
                            },
                            beforeSend: function () {
                                $("#page-loader").show();
                            },
                             success: function (response) {
                                     $("#page-loader").hide();
                                     if (response.status == 1) {
                                        var receivePalletCount=parseInt($("input[name='total_receive_pallet']").val());
                                        $("input[name='total_receive_pallet']").val(--receivePalletCount);
                                       
                                        $('#'+removeDiv).remove();
                                     
                                         PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                        // PoundShopApp.commonClass.table.draw();
                                     }
                             },
                             error: function (xhr, err) {
                                $("#page-loader").hide();
                                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                             }
                        });
                    }
                }
            });
        }
    });


    //remove return pallet specific div

    $(document).on('click', '.btn-return-delete-pallet', function(){
        var deleteId=$(this).attr('data-delete');
        var removeDiv=$(this).attr('attr-curr-div');
        if(deleteId==undefined && deleteId!='')
        {
            var returnPalletCount=parseInt($("input[name='total_return_pallet']").val());
            $("input[name='total_return_pallet']").val(--returnPalletCount);
            $('#'+removeDiv).remove();
        }
        else
        {
           bootbox.confirm({ 
                title: "Confirm",
                message: "Are you sure you want to delete selected records? This process cannot be undone.",
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'Delete',
                        className: 'btn-red'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {
                       
                        $.ajax({
                            url: BASE_URL + 'api-booking-pallet/'+deleteId,
                            type: "delete",
                            processData: false,
                            data:{id:deleteId},
                            headers: {
                               Authorization: 'Bearer ' + API_TOKEN,
                            },
                            beforeSend: function () {
                                $("#page-loader").show();
                            },
                             success: function (response) {
                                console.log("dfdfgg");
                                     $("#page-loader").hide();
                                     if (response.status == 1) {
                                        var returnPalletCount=parseInt($("input[name='total_return_pallet']").val());
                                        $("input[name='total_return_pallet']").val(--returnPalletCount);
                                       
                                        $('#'+removeDiv).remove();
                                     
                                         PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                        // PoundShopApp.commonClass.table.draw();
                                     }
                                     else
                                     {
                                         PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                                     }
                             },
                             error: function (xhr, err) {
                                console.log("dfdf");
                                $("#page-loader").hide();
                                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                             }
                        });
                    }
                }
            }); 
        }
    });


    // save pallets form
   submitPallets= function(){

        $("#pallet-form").validate({
       
            focusInvalid: false, // do not focus the last invalid input
            invalidHandler: function(form, validator) {

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
                "return_pallets[]": {
                    /*required:function(element){
                        var str=element.id;
                        var res = str.split("_");
                       
                        if(!$("#return_num_of_pallets_"+res[2]).val())
                        {
                            console.log("ggg");
                            return 1;
                        }

                       
                    }*/
                },
                "return_num_of_pallets[]": {
                    /*required:function(element){
                        var str=element.id;
                        var res = str.split("_");
                      
                         if(!$("#return_pallets_"+res[4]).val())
                        {
                            return 1;
                        }
                       
                    },*/
                    minlength:1,
                    maxlength:2,
                },
                "receive_pallets[]": {
                    /* required:function(element){
                         var str=element.id;
                        var res = str.split("_");
                        if(!$("#receive_num_of_pallets_"+res[2]).val())
                        {
                            return 1;
                        }
                        
                      
                    }*/
                     
                },
                "receive_num_of_pallets[]": {
                    /*required:function(element){
                    var str=element.id;
                        var res = str.split("_");
                        if(!$("#receive_pallets_"+res[4]).val())
                        {
                            return 1;
                        }
                    },   */
                    minlength:1,
                    maxlength:2,
                    number: true
                },
                
            },
            messages:{
                "receive_num_of_pallets[]":{
                    "maxlength":"Only 2 digits allowed",
                },
                "return_num_of_pallets[]":{
                    "maxlength":"Only 2 digits allowed",
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
                var dataString = new FormData($("#pallet-form")[0]);
                $.ajax({
                    type: "POST",
                    url: $('#pallet-form').attr('action'),
                    data: dataString,
                    datatype: 'JSON',
                    processData: false,
                    contentType: false,
                    cache: false,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                          $("#page-loader").hide();
                      getBookingPalletForm();
                        if (response.status_code == 200) {
                            //$("#create-totes-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        }
                         else{
                            PoundShopApp.commonClass._displayErrorMessage(response.message);
                        }
                      /* // console.log(document.URL +  ' .pallet_return_receive_div');
                        $('.pallet_return_receive_div').load(document.URL +  ' .pallet_return_receive_div');
                        if (response.status_code == 200) {
                            //$("#create-totes-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        }
                        else{
                            PoundShopApp.commonClass._displayErrorMessage(response.message);
                        }*/
                    },
                    error: function (data) {
                      console.log();
                          $("#page-loader").hide();
                        $('.btn-blue').attr('disabled', false);
                        PoundShopApp.commonClass._displayErrorMessage(data.responseJSON.message);
                         //  PoundShopApp.commonClass._displayErrorMessage(err);
                    }
                });
            }
        });
    }

    getBookingPalletForm=function(){
        $.ajax({
            type: "POST",
            url: WEB_BASE_URL+'/material-receipt/pallet-return-receive-data',
            data: {booking_id:$('#booking_id').val()},
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            success: function (response) {
              $('#pallet-form').remove();
              $(response).insertAfter('.checklist-detail #delivery-form');
            // console.log(response);
            },
            error: function (data) {
              /*  console.log("sdf");
                $('.btn-blue').attr('disabled', false);
                //PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                 //  PoundShopApp.commonClass._displayErrorMessage(err);*/
            }
        });
    }
    
    //get checklist point of qc based on qc change
    $(document).on('change', '.qc_list_dropdown', function(){

        var pointLoadDiv=$(this).attr('attr-div');
        var product_id=$(this).attr('attr-product-id');
        var booking_id=$('#bookingQC').val();
        var val=$(this).val();

        //console.log(val.join(","));return false;
        if(val!=undefined && val!='')
        {
            console.log(val);
            var join_selected_values = val.join(","); 
            $.ajax({
                url: BASE_URL+'api-checklist-points-qc',
                type: "POST",
                data: 'qc_ids='+join_selected_values+'&product_id='+product_id+'&booking_id='+booking_id,
                processData: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                success: function (response) {
                    $('#'+pointLoadDiv).html(response.view);
                    $(document).find(".qc_list_dropdown").selectpicker("refresh");
                    //$('.book_id').val($("input[name='booking_id']").val());
                },
                error: function (xhr, err) {
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }
        else
        {
            $('#'+pointLoadDiv).html('');
        }
    });

    
    //based on product change show and hide qc checklist of particular product
    $(document).on('change', '#product_qc', function(){
        
        var val=$(this).val();
        if(val!=undefined && val!='')
        {
            var join_selected_values = val.join(","); 
            $.ajax({
                url: WEB_BASE_URL+'/get-qc-list-products',
                type: "POST",
                data: 'product_ids='+join_selected_values,
                processData: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                success: function (response) {
                    $(document).find('.load_data').html(response.view);
                    $(document).find('.qc_list_dropdown').trigger('change');
                    $(document).find(".qc_list_dropdown").selectpicker("refresh");
                    //$('.book_id').val($("input[name='booking_id']").val());
                },
                error: function (xhr, err) {
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }
        else
        {
            $('.load_data').html('');
        }
    });

    //store and update delivery note

    $(document).on('click', '.saveDeliveryNoteData', function(){
        $("#delivery-form").validate({
            focusInvalid: false, // do not focus the last invalid input
            invalidHandler: function(form, validator) {

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
                "delivery_note_number": {
                    required: true,
                    maxlength: 40,
                    minlength: 3,
                },
                
            },
            errorPlacement: function (error, element) {

                if(element[0].id!='dn_file')
                    error.insertAfter(element);
            },
            highlight: function (element) {
            if(element[0].id!='dn_file') // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            success: function (label) {
               var dataString = new FormData($("#delivery-form")[0]);
                
                $.ajax({
                    type: "POST",
                    url: $("#delivery-form").attr("action"),
                    data: dataString,
                    datatype: 'JSON',
                    processData: false,
                    contentType: false,
                    cache: false,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    success: function (response) {
                        if (response.status == 1) {
                            //$("#create-totes-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        }
                    },
                    error: function (xhr, err) {
                        $('.btn-blue').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });
            }
        });
    });

    //preview image for delivery note image
    var previewImage = function(input, block){
        var fileTypes = ['jpg', 'jpeg', 'png'];
        var extension = input.files[0].name.split('.').pop().toLowerCase();  
        var isSuccess = fileTypes.indexOf(extension) > -1; 
         
        if(isSuccess){
            var size=(input.files[0].size);
            
            var reader = new FileReader();
            
                if(size>10000000)
                {
                    bootbox.alert({
                        title: "Alert",
                        message: "Image size should be less than  or equal 10 MB.",
                        size: 'small'
                    });
                   return false;
                    
                }else
                {
                    $('.btn-blue').attr('disabled',false);
                    block.show();
                    reader.onload = function (e) {
                        block.attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                   $('#deleteImagenull').toggle();
                  
                }
        }else{
            $('.btn-blue').attr('disabled',true);
          bootbox.alert({
                        title: "Alert",
                        message: "Please select image",
                        size: 'small'
                    });
                   return false;
        }

    };

    //preview Image for qc checklist points
    var previewImageQC = function(input, block,deleteBtnNull){
        var fileTypes = ['jpg', 'jpeg', 'png'];
        var extension = input.files[0].name.split('.').pop().toLowerCase();  
        var isSuccess = fileTypes.indexOf(extension) > -1; 
         
        if(isSuccess){
            var size=(input.files[0].size);
            
            var reader = new FileReader();
            
                if(size>10000000)
                {
                    bootbox.alert({
                        title: "Alert",
                        message: "Image size should be less than  or equal 10 MB.",
                        size: 'small'
                    });
                    $('.btn-blue').attr('disabled',true);
                   return false;
                    
                }else
                {
                    $('.btn-blue').attr('disabled',false);
                    block.show();
                    reader.onload = function (e) {
                        block.attr('src', e.target.result);
                    };
                    $('#'+deleteBtnNull).show();
                    reader.readAsDataURL(input.files[0]);
                }
        }else{
            $('.btn-blue').attr('disabled',true);
            bootbox.alert({
                        title: "Alert",
                        message: "Please select image",
                        size: 'small'
                    });

                   return false;
        }

    };

    // show uploaded image for delivery note
    $(document).on('change', '.delivery_notes_picture', function(){
        $('#magentoimagePreview').show();
        previewImage(this, $('#delivery_note_preview'));
        
    });

    //show upload image on image upload for qc checklist point
    $(document).on('change', '.image_qc', function(){
       var imageEle=$(this).attr('attr-id');
       var deleteBtn=$(this).attr('attr-delete-btn');
       var deleteBtnNull=$(this).attr('attr-delete-btn-null');
       console.log(deleteBtnNull+'------'+deleteBtn);
       previewImageQC(this,$('#'+imageEle),deleteBtn);
    });

    //add line on text and store it in database on checkbox change
    $(document).on('change', '.is_checked', function(){
    
       var checkBoxEle=$(this).attr('id');
       var checkBoxVal=$("#"+checkBoxEle).val();
       var formId = $(this).closest("form").attr('id');
       var dataString = new FormData($("#"+formId)[0]);
       var textEle=$(this).attr('attr-text');
       $.ajax({
            type: "POST",
            url: $("#"+formId).attr("action"),
            data: dataString,
            datatype: 'JSON',
            processData: false,
            contentType: false,
            cache: false,
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                $("#page-loader").hide();
                if (response.status == 1) {
                    if($('#'+checkBoxEle). prop("checked") == true){
                        $('#'+textEle).css("text-decoration","line-through");
                   }
                   else
                   {
                        $('#'+textEle).css("text-decoration","");
                   }
                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                }
            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
       
       // previewImageQC(this, $('#'));
        
    });

    //show and hide (comment, image ) div of qc checklist point
    $(document).on("click",'.expand',function(){
        var pointId=$(this).attr('attr-id');
        var expandDivId="detailDiv_"+pointId;
        $("#"+expandDivId).toggle();
        var expandDivImgId="detailImgDiv_"+pointId;
        $("#"+expandDivImgId).toggle();
        console.log(pointId);
      
    });
    
    $(document).on("click",'.submit_btn',function(){
        var formId = $(this).closest("form").attr('id');
        $("#"+formId).validate({
            focusInvalid: false, // do not focus the last invalid input
            invalidHandler: function(form, validator) {

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
               var dataString = new FormData($("#"+formId)[0]);
                $(this).attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: $("#"+formId).attr("action"),
                    data: dataString,
                    datatype: 'JSON',
                    processData: false,
                    contentType: false,
                    cache: false,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        $(this).attr('disabled', false);
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            //$("#create-totes-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        }
                    },
                    error: function (xhr, err) {
                        $('.btn-blue').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });
            }
        });
    });
        
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopBooking = new poundShopBooking();

})(jQuery);

//remove uploaded image
function removeImage(id)
{
    
    bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to delete image?",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Delete',
                className: 'btn-red'
            }
        },
        callback: function (result) 
        {
            if(result==true)
            {
                
                $.ajax({
                    type: "POST",
                    url: BASE_URL + 'api-delete-delivery-note-image',
                    data: {'id':id},
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        
                        $("#page-loader").hide();
                       
                        if (response.status == 1) {
                            $('#delivery_note_preview').attr('src',WEB_BASE_URL+'/img/no-img-black.png');
                            $('#deleteImage').hide();
                            $('#deleteImagenull').hide();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        }
                    },
                    error: function (xhr, err) {
                        
                        $('.btn-primary').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });
            }
        }
    });
     
}

//remove selected image and display no-image avilable
function removeImageQcNull(imageEle,deleteBtn)
{
    $('#'+imageEle).attr('src',WEB_BASE_URL+'/img/no-img-black.png');
    $('#'+deleteBtn).hide();
}

//remove qc point image
function removeImageQc(bookqcpointid,imageEle,deleteBtn)
{
     bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to delete image?",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Delete',
                className: 'btn-red'
            }
        },
        callback: function (result) 
        {
            if(result==true)
            {
                
                $.ajax({
                    type: "POST",
                    url: BASE_URL + 'api-delete-booking-qc-point-img',
                    data: {'id':bookqcpointid},
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        
                        $("#page-loader").hide();
                       
                        if (response.status == 1) {
                            $('#'+imageEle).attr('src',WEB_BASE_URL+'/img/no-img-black.png');
                            $('#'+deleteBtn).hide();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        }
                    },
                    error: function (xhr, err) {
                        
                        $('.btn-primary').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });
            }
        }
    });
}
function removeQCOfProduct(product_id,qc_id,booking_id)
{
    bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to delete Qc Checklist for this product?",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Delete',
                className: 'btn-red'
            }
        },
        callback: function (result) 
        {
            if(result==true)
            {
                
                $.ajax({
                    type: "POST",
                    url: BASE_URL + 'api-delete-booking-qc-point-img',
                    data: {'product_id':product_id,'qc_id':qc_id,'booking_id':booking_id},
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        
                        $("#page-loader").hide();
                       
                        if (response.status == 1) {
                            $('#'+imageEle).attr('src',WEB_BASE_URL+'/img/no-img-black.png');
                            $('#'+deleteBtn).hide();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        }
                    },
                    error: function (xhr, err) {
                        
                        $('.btn-primary').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });
            }
        }
    });
}

//print qc for products
printQCChecklist = function(bookingId)
{
    var left  = ($(window).width()/2)-(900/2);
    var top   = ($(window).height()/2)-(600/2);
    var popup = window.open(WEB_BASE_URL+'/print-product-qcchecklist-bookingwise?booking_id='+bookingId,"popupWindow", "width=900, height=600, scrollbars=yes,top="+top+", left="+left);
}


$(document).on("keypress","input[name='receive_num_of_pallets[]']",function(e){
    return isNumber(event, this)
});

$(document).on("keypress","input[name='return_num_of_pallets[]']",function(e){
    return isNumber(event, this)
});

function isNumber(evt, element) {

var charCode = (evt.which) ? evt.which : event.keyCode

if ((charCode < 48 || charCode > 57))
    return false;

return true;
}    