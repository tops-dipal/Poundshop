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
        $('select[readonly]').find('option').not('[selected]').attr('disabled',"disabled");

        calculate_estimated_margin();

        variationDocLoadUpdate();

        set_category_data();

        set_selected_category_data();

        initalize_tab_switch_save();

        set_header_tab_menus();

        $('body').keypress('#category-search-box',function(event){
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                event.preventDefault();
                get_categories_by_keyword($('#category_search'));
            }
        });

    };

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var filecollection=[];
    /*function bytesToSize(bytes) {
       var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
       if (bytes == 0) return '0 Byte';
       var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
       return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
    }*/

    
    /*$(document).on('change', '.image', function(e){
     
        var files = e.target.files;
        var imageExtension=['jpeg','jpg','png'];
        var videoExtension=['mp4'];
        var indexStartFrom=$('#totalUploadedImages').val()+1;
        $.each( files , function(i, file){
        var reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(e){
            // get filename ..
            var FileType = files[i].type;
            var size=files[0].size;
            
            console.log(e);
            // get file extension..
            var fileExtension = FileType.substr((FileType.lastIndexOf('/') + 1));
            var Extension = fileExtension.toUpperCase();
            filecollection.push(file); // i created an array and insert selected files to it.
            
            var imageStr='';
            
            if(jQuery.inArray( fileExtension, imageExtension )!=-1)
            {
                if(size>4000000)
                {
                    var imageError="Image size should be less than  or equal 10 MB and your file size is "+bytesToSize(size);
                }
                else
                {
                    var imageError="";
                }
                imageStr=`<div class="col-lg-6" id="imgUpload_`+indexStartFrom+`">
                            <div class="form-group row">
                                <div class="imagePreview col-lg-2">
                                  <img src="`+e.target.result+`" class="thumbnail" style="max-width:150px; max-height:150px;" id="imagePreview_`+indexStartFrom +`" />
                                 
                                    <span class="invalid-feedback" style="display:block">`+imageError+`</span>
                                    <div class="remove_`+indexStartFrom+`">
                                        <button type="button" class="btn btn-danger removeImage" id="removeImage_`+indexStartFrom+`" attr-temp-name="`+file['name']+`" data-id="`+i+`">X</button>
                                    </div>
                                </div>
                                  
                            </div>
                        </div>`;
            }
            if(jQuery.inArray( fileExtension, videoExtension )!=-1)
            {
                if(size>20000000)
                {
                    var imageError="Video size should be less than  or equal 20 MB your file size is "+bytesToSize(size);
                }
                else
                {
                    var imageError="";
                }
                imageStr=`<div class="col-lg-6" id="imgUpload_`+indexStartFrom+`">
                            <div class="form-group row">
                                <div class="imagePreview col-lg-2">
                                <video style="max-width:150px; max-height:150px;" controls="controls" preload="metadata" id="videoPreview_`+indexStartFrom+`">
                                    <source src="`+e.target.result+`#t=0.5" type="video/mp4">
                                    <span class="invalid-feedback" style="display:block">`+imageError+`</span>
                                </video>
                                 
                                    <div class="remove_`+indexStartFrom+`">
                                        <button type="button" class="btn btn-danger removeImage" id="removeImage_`+indexStartFrom+`" attr-temp-name="`+file['name']+`" data-id="`+i+`">X</button>
                                    </div>
                                </div>
                                  
                            </div>
                        </div>`;
            }
            
            indexStartFrom++;
            $('.productImages').append(imageStr);
        };
       

        console.log(filecollection);
        });
    });*/
    
    $(document).on('change', '#main_image_marketplace, #main_image_internal', function(){
    
        if($(this).attr('id')=="main_image_marketplace")
        {
            if ($('#magentoimagePreview').css('display') == 'none') {
               
                $('#magentoimagePreview').show();
            }
             previewImage(this, $('#magentoimagePreview'),$('#magento_img_remove'),$('.magentoVideoPreview'));
        }
        else
        {
            if ($('#InternalImgPreview').css('display') == 'none') {
                
                $('#InternalImgPreview').show();
            }
              previewImage(this, $('#InternalImgPreview'),$('#internal_img_remove'),$('.InternalVideoPreview'));
        }
    });
    $(document).on('click', '#magento_img_remove, #internal_img_remove', function(){
    
        var id=$(this).attr('id');

        var remove_image_type="main_image_marketplace";
        if(id=='internal_img_remove')
        {
            remove_image_type="main_image_internal";
            var previewClass=$('#InternalImgPreview');
        }
        else
        {
            remove_image_type="main_image_marketplace";
            var previewClass=$('#magentoimagePreview');
        }

       bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to delete image/video? This process cannot be undone.",
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
                    url: $("#remove-img-url").val(),
                    data: {'removeId':$('input[name="id"]').val(),'remove_image_type':remove_image_type},
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        $("#page-loader").hide();
                        
                        if (response.status_code == 200) 
                        {
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            setTimeout(function () {
                                     location.reload();
                             }, 2000);
                        }
                    },
                     error: function (xhr, err) {
                        $('.btn-form').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });
            }
        }
    });
    });

    $(document).on('click', '.addMore', function(){
        var idArr=$(this).attr('id').split("_");
        var nextNum=(parseInt(idArr[1])+parseInt(1));
         $('.'+$(this).attr('id')).hide();
        //alert(nextNum);
        $.ajax({
                type: "POST",
                url: $("#addMoreURL").val(),
                data: {'nextNum':nextNum},
                datatype: 'JSON',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                success: function (response) {
                   
                     $('.productImages').append(response.view);
                },
                error: function (xhr, err) {
                   
                }
            });
       
    });

      let inputFile = $('#image_1');
      let button = $('#myButton');
      let buttonSubmit = $('#mySubmitButton');
      let filesContainer = $('#myFiles');
      let files = [];
    let newFiles = []; 
    inputFile.change(function() {
        
        for(let index = 0; index < inputFile[0].files.length; index++) {
              let file = inputFile[0].files[index];
              newFiles.push(file);
              files.push(file);
        }
    });
    
    // fileElement.click(function(event) {
    //     let fileElement = $(event.target);
    //     let indexToRemove = files.indexOf(fileElement.data('fileData'));
    //     fileElement.remove();
    //     files.splice(indexToRemove, 1);
    //   });


    $(document).on('click', '.removeImage', function(){
       files.splice(0, 1);
       

        var idArr=$(this).attr('id').split("_");
        
        if(parseInt(idArr[1])!=0)
        {
                var id=parseInt(idArr[1])-parseInt(1);
        }
        else
        {
            var id=0;
        }

        
        var removeImageId=$('#removeImageId_'+idArr[1]).val();

      

         $('#'+$(this).attr('id')).hide();
         $('.addMore_'+id).show();
         $('#addMore_'+id).removeClass('hidden');
         
       
        if(removeImageId)
        {

            bootbox.confirm({ 
            title: "Confirm",
            message: "Are you sure you want to delete image/video? This process cannot be undone.",
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
                    $('#imgUpload_'+idArr[1]).remove();
                    $.ajax({
                        type: "POST",
                        url: $("#remove-img-url").val(),
                        data: {'removeId':removeImageId,'remove_image_type':'other'},
                        headers: {
                            'Authorization': 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                            $("#page-loader").hide();
                           
                            if (response.status_code == 200) 
                            {
                               $('.remove_'+idArr[1]).parent().remove();
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                refreash_tab('form-images');
                            }
                        },
                         error: function (xhr, err) {
                            $('.btn-form').attr('disabled', false);
                            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                        }
                    });
                }
            }
            });
        }
        else if( typeof $(this).attr('attr-temp-name') != 'undefined')
        {
           
            if($(this).attr('attr-temp-name').length > 0)
            {
               /* var remove_html = '<input type="hidden" name="remove_time_images[]" value="'+$(this).attr('attr-temp-name')+'" />';

                $('.otherImages').append(remove_html);
                 var appendId = $(this).attr('data-id');*/
                
               
               // filecollection.splice(filecollection.indexOf(filecollection[appendId]), 1);
            }    
        }    

       
    });
$('.input-images').imageUploader();

    $("#form-stock-file").validate({
        // focusInvalid: false, // do not focus the last invalid input
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
            "product_identifier":{
                required: true,
            },
            "product_identifier_type":{
                required: true,
            },
            "sku":{
                required: true,
            },
            // "brand":{
            //     required: true,
            // },
            "single_selling_price":{
                required: true,
            },
            // "long_description":{
            //     // required_ckeditor: true,
            //     required: true,
            // },
            "title": {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                maxlength: 255,
                minlength: 3,
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
            if(element.attr('name') == 'product_identifier')
            {
               error.insertAfter(element.parent('div')); 
            }
        },
        highlight: function (element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
    });

    // $("#form-buying-range").validate({
    //     focusInvalid: false, // do not focus the last invalid input
    //     invalidHandler: function(form, validator) {
    //         if (!validator.numberOfInvalids())
    //             return;
    //         $('html, body').animate({
    //             scrollTop: $(validator.errorList[0].element).offset().top-30
    //         }, 1000);
    //     },
    //     errorElement: 'span',
    //     errorClass: 'invalid-feedback', // default input error message class
    //     ignore: [],
    //     rules: {
    //         "buying_category_id":{
    //             required: true,
    //         },
    //     },
    //     messages:{
    //         buying_category_id : {
    //             required : 'Please select category',
    //         },
    //     },
    //     errorPlacement: function (error, element) {
            
    //         if(element.attr('name') == 'buying_category_id')
    //         {
                
    //         }  
    //         else
    //         {
    //             error.insertAfter(element);    
    //         }  
    //     },
    //     highlight: function (element) { // hightlight error inputs
    //         $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
    //     },
    //     success: function (label) {
    //         label.closest('.form-group').removeClass('has-error');
    //         label.remove();
    //     },
    // });

    $("#form-variation").validate({
        // focusInvalid: false, // do not focus the last invalid input
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
            // "var_barcode[]":{
            //     required: true,
            // },
        },
        messages:{
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
    });
    
    $("#form-barcodes-popup").validate({
        // focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function(form, validator) {
            if (!validator.numberOfInvalids())
                return;
            $('html, body').animate({
                scrollTop: $(validator.errorList[0].element).offset().top-30
            }, 1000);
        },
        errorElement: 'span',
        errorClass: 'invalid-feedback', // default input error message class
        // ignore: [],
        rules: {
            "barcode":{
                required: true,
            },
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
    });

    window.PoundShopApp = window.PoundShopApp || {}
    
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

    $('form').on('submit', function (e){
        
        e.preventDefault();

        if($(this).valid())
        {    
            var dataString = new FormData($(this)[0]);
                
            $('.btn-form').attr('disabled', true);

            var form_id = $(this).attr("id");

            $.ajax({
                type: "POST",
                url: $(this).attr("action"),
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

                    if (response.status == 1) 
                    {
                        if(form_id == 'form-barcodes')
                        {
                            $('#barcodeModal').modal('hide');
                        }
                           
                        if(form_id == 'form-stock-file')
                        {
                            $('select[name="product_type"]').attr('readonly', 'readonly');

                            $('select[readonly]').find('option').not(':selected').attr('disabled',"disabled");

                            $('textarea[name="long_description"]').attr('old-value', $('textarea[name="long_description"]'));
                        }  

                        update_serialize_form_data($(this).attr('id'));

                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        
                        if(typeof response.data.record_created != 'undefined')
                        {   
                            if(response.data.record_created == true)
                            { 
                                setTimeout(function () {
                                    window.location.href = WEB_BASE_URL + '/product/form/'+response.data.id+'?active_tab=stock-file';
                                }, 1000);
                            }
                        }
                        else if(typeof response.data.reload != 'undefined')
                        {
                            if(response.data.reload == true)
                            {
                                refreash_tab(form_id);
                            }    
                        }
                        else if(form_id == 'form-buying-range')
                        {
                            // Refreash Stock File tab for
                            refreash_tab(form_id);
                            refreash_tab(form_id, 'form-stock-file', 'refreash_url_stock');
                        } 

                        $('.btn-form').attr('disabled', false); 
                    }
                },
                error: function (xhr, err) {
                    $('.btn-form').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }    
    });

    $('input[name="title"]').on('change', function(){
        write_description($(this).val()); 
    });

    var previewImage = function(input, block,nextbtn="",videoShower=""){
        var fileTypes = ['jpg', 'jpeg', 'png','mp4'];
        var extension = input.files[0].name.split('.').pop().toLowerCase();  
        var isSuccess = fileTypes.indexOf(extension) > -1; 
         
        if(isSuccess){
            var size=(input.files[0].size);
            
            var reader = new FileReader();
            if(fileTypes.indexOf(extension)==3)
            {
                if(size>10000000)
                { 
                    bootbox.alert({
                        title: "Alert",
                        message: "Video size should be less than  or equal 10 MB.",
                        size: 'small'
                    });
                    $('.btn-blue').attr('disabled',true);
                    return false;
                }
                else
                {
                    $('.btn-blue').attr('disabled',false);
                    //console.log(block);return false;
                    if(videoShower!="")
                    {
                        block.hide();
                        videoShower.show();
                        reader.onload = function (e) {
                        videoShower.attr('src', e.target.result);
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                    
                   /*else
                   {
                        
                        var  fileReader=reader;
                           var file=input.files[0];

                            fileReader.onload = function() {
                          var blob = new Blob([fileReader.result], {type: file.type});
                          var url = URL.createObjectURL(blob);

                          var video = document.createElement('video');
                          
                          var timeupdate = function() {
                            
                            if (snapImage()) {
                                
                              video.removeEventListener('timeupdate', timeupdate);
                              video.pause();
                            }
                          };
                          video.addEventListener('loadeddata', function() {
                            if (snapImage()) {
                              video.removeEventListener('timeupdate', timeupdate);
                            }
                          });
                          var snapImage = function() {
                            var canvas = document.createElement('canvas');
                            
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                            var image = canvas.toDataURL();
                            var success = 1;
                            if (success) {
                              var img = document.createElement('img');
                              img.src = image;
                              
                              block.attr('src', image);
                              
                            }

                            return success;
                          };
                          video.addEventListener('timeupdate', timeupdate);
                          video.preload = 'metadata';
                          video.src = url;
                          
                          video.muted = true;
                          video.playsInline = true;
                          video.play();
                        };
                        fileReader.readAsArrayBuffer(file);
                        
                        }
                   }*/
               }    
            }
            else
            {
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
                    videoShower.hide();
                    reader.onload = function (e) {
                        block.attr('src', e.target.result);
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
          
            if(nextbtn!='')
            {
                nextbtn.show();
            }
        }else{
            alert('Please select video or image file.');
        }

    };
})(jQuery);

var previewVariationImage = function(input, block,nextbtn="",videoShower=""){
        var fileTypes = ['jpg', 'jpeg', 'png','mp4'];
        var extension = input.files[0].name.split('.').pop().toLowerCase();  
        var isSuccess = fileTypes.indexOf(extension) > -1; 
        block.hide();
        block.parents('td').find('.btn-remove-img').remove(); 
        if(isSuccess){
            var size=(input.files[0].size);
            
            var reader = new FileReader();
            if(fileTypes.indexOf(extension)==3)
            {
                if(size>10000000)
                { 
                    bootbox.alert({
                        title: "Alert",
                        message: "Video size should be less than  or equal 10 MB.",
                        size: 'small'
                    });
                    $('.btn-blue').attr('disabled',true);
                    return false;
                }
                else
                {
                    $('.btn-blue').attr('disabled',false);
                    
                    if(videoShower!="")
                    {
                        videoShower.show();
                    
                        reader.onload = function (e) {
                            videoShower.attr('src', e.target.result);
                            videoShower.after('<button type="button" class="btn-remove-img" onclick="delete_variation_img(this)" attr-original-url="">&times</button>');
                        };

                        reader.readAsDataURL(input.files[0]);
                    }
                    
                   /*else
                   {
                        
                        var  fileReader=reader;
                           var file=input.files[0];

                            fileReader.onload = function() {
                          var blob = new Blob([fileReader.result], {type: file.type});
                          var url = URL.createObjectURL(blob);

                          var video = document.createElement('video');
                          
                          var timeupdate = function() {
                            
                            if (snapImage()) {
                                
                              video.removeEventListener('timeupdate', timeupdate);
                              video.pause();
                            }
                          };
                          video.addEventListener('loadeddata', function() {
                            if (snapImage()) {
                              video.removeEventListener('timeupdate', timeupdate);
                            }
                          });
                          var snapImage = function() {
                            var canvas = document.createElement('canvas');
                            
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
                            var image = canvas.toDataURL();
                            var success = 1;
                            if (success) {
                              var img = document.createElement('img');
                              img.src = image;
                              
                              block.attr('src', image);
                              
                            }

                            return success;
                          };
                          video.addEventListener('timeupdate', timeupdate);
                          video.preload = 'metadata';
                          video.src = url;
                          
                          video.muted = true;
                          video.playsInline = true;
                          video.play();
                        };
                        fileReader.readAsArrayBuffer(file);
                        
                        }
                   }*/
               }    
            }
            else
            {
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
                    videoShower.hide();
                    reader.onload = function (e) {
                        block.attr('src', e.target.result);
                        block.after('<button type="button" class="btn-remove-img" onclick="delete_variation_img(this)" attr-original-url="">&times</button>');
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            }
          
            if(nextbtn!='')
            {
                nextbtn.show();
            }
        }else{
            $(input).val('');
            alert('Please select video or image file.');
        }

    };

$('body').on('keyup', 'input[name="single_selling_price"]', function(){
    calculate_estimated_margin();    
}); 

function calculate_estimated_margin()
{
    var sp = parseFloat($('input[name="single_selling_price"]').val());
    
    var cp = parseFloat($('#last_cost_price').val());
    
    if(!isNaN(sp) && !isNaN(cp))
    {   
        var estimated_margin = (sp - cp);

        $('#estimated_margin').val(estimated_margin);
    }
}

function addSupplier(me)
{
    $('#invalid-supplier').hide();

    if($('#supplier_id').val() != "")
    {
        $('#addSupplierId').attr('disabled', true);
        
        if(save_tab_data('form-suppliers', true) == true)
        {
            ajaxSaveSupplier();
        }  
        else
        {
            bootbox.confirm({ 
                title: "Confirm",
                message: "Your changes will be lost, please save them before adding new supplier. Continue anyway ?",
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'Yes',
                        className: 'btn-red'
                    }
                },
                callback: function (result) {
                    if(result == true)
                    {
                        ajaxSaveSupplier();
                    }
                    else
                    {
                        $('#addSupplierId').attr('disabled', false);
                    }
                }
            });     
        }  
        
        
    }
    else
    {
        $('#invalid-supplier').show();
    }
}

function ajaxSaveSupplier()
{
    $.ajax({
        type: "POST",
        url: BASE_URL+'api-product-save-supplier',
        data: {
                'product_id' : $('input[name="id"]').val(),
                'supplier_id' : $('#supplier_id').val(),
            },
        datatype: 'JSON',
        headers: {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        beforeSend: function () {
            $("#page-loader").show();
        },
        success: function (response) {
            $("#page-loader").hide();
            
            $('#addSupplierId').attr('disabled', false);
            
            if (response.status == 1) 
            {
                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                
                location.reload(true);
            }
        },
        error: function (xhr, err) {
            $('#addSupplierId').attr('disabled', false);
            
            if(xhr.responseJSON.message.length > 0)
            {    
                PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message, err);
            }
            else
            {
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        }
    });
}

function deleteSupplier(me)
{
    var ids = [];

    ids = getListingCheckboxIds('child-checkbox-supplier');   

     if(ids.length > 0)
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
            callback: function (result) {
                if(result == true)
                {    
                    $.ajax({
                        url: BASE_URL+'api-product-supplier-remove',
                        type: "POST",
                        datatype:'JSON',
                        data:{'id':ids},
                        headers: {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                                $("#page-loader").hide();
                                if (response.status == 1) {
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    location.reload(true);
                                }
                        },
                        error: function (xhr, err) {
                           $("#page-loader").hide();
                        }
                    });
                }
            }
        }); 
    }
    else
    {
        bootbox.alert({
            title: "Alert",
            message: "Please select atleast one record to delete.",
            size: 'small'
        });
        return false;
    }          
}


function resetBardcodeModal()
{
    $('span.invalid-feedback').remove();
    
    $('input[name="barcode_id"]').val('');
    
    $('#form-barcodes').trigger('reset');
    
    $('#barcodeModal input[name="barcode_type"][value="1"]').trigger('change');
}

$('#barcodeModal').on('hidden.bs.modal', function () {
    resetBardcodeModal();
});

function addBarcode()
{
    $('#exampleModalLabel').text('Add Barcode');
    
    resetBardcodeModal();

    $('#barcodeModal').modal('show');
}

$('input[name="barcode_type"]').on('change', function(){
    
    if($(this).val() != '1')
    {
        $('input[name="case_quantity"]').attr('required', 'required');

        if($(this).val() == '2')
        {
            $('#case_qty_label').text(POUNDSHOP_MESSAGES.inventory.inner_case_qty);
        }   
        else
        {
            $('#case_qty_label').text(POUNDSHOP_MESSAGES.inventory.outer_case_qty);
        } 

        $('#case_quantity').show();
    }  
    else
    {
        $('input[name="case_quantity"]').removeAttr('required', 'required');
        $('#case_quantity').hide();
    }  
})

function deleteBarcode(me)
{
    var ids = [];

    if(typeof $(me).attr('attr-id') != 'undefined')
    {
        ids.push($(me).attr('attr-id'));
    }
    else
    {
        ids = getListingCheckboxIds('child-checkbox-barcode');   
    }    

    if(ids.length > 0)
    {  
        bootbox.confirm({ 
            title: "Confirm",
            message: "Are you sure you want to delete selected records?",
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
            callback: function (result) {
                if(result == true)
                {   
                    $.ajax({
                        url: BASE_URL+'api-product-barcode-remove',
                        type: "POST",
                        datatype:'JSON',
                        data:{'id':ids},
                        headers: {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                                $("#page-loader").hide();
                                if (response.status == 1) {
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    location.reload(true);
                                }
                        },
                        error: function (xhr, err) {
                           $("#page-loader").hide();
                        }
                    });
                }
            }
        });         
    }
    else
    {
        bootbox.alert({
                title: "Alert",
                message: "Please select atleast one record to delete.",
                size: 'small'
            });
            return false;
    }    
}

function editBarcode(me)
{
    if($(me).attr('attr-id').length > 0)
    {
        $('#exampleModalLabel').text('Edit Barcode');

        $('#barcodeModal input[name="barcode_id"]').val($(me).attr('attr-id'));
        $('#barcodeModal input[name="barcode"]').val($(me).attr('attr-barcode'));
        $('#barcodeModal input[name="case_quantity"]').val($(me).attr('attr-case_quantity'));
        $('#barcodeModal input[name="barcode_type"][value='+$(me).attr('attr-barcode_type')+']').prop('checked', true);

        $('#barcodeModal input[name="barcode_type"][value='+$(me).attr('attr-barcode_type')+']').trigger('change');

        $('#barcodeModal').modal('show');
    }    
}

function set_variation(me)
{
    if($(me).val() == 'parent')
    {
        $('#variation-li').show();
    }  
    else
    {
        $('#variation-li').hide();
    }  
}

var variation_theme_change = function (current_object) 
{   
    hide_variations();
    
    if ($(current_object).val() != "") 
    {
        if ($("table tbody tr.variation-row-template").nextAll("tr").length > 0) 
        {
            bootbox.confirm({
                size: "small",
                title: '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Warning',
                message: "Changes in variation will delete all existing variation product.Continue anyway?",
                callback: function (result) {
                    if (result) {
                        $("table tbody tr.variation-row-template").nextAll("tr").each(function () {
                            $("table tbody tr.variation-row-template").nextAll("tr").find("input.child-checkbox").prop("checked", true);
                            
                            remove_variation();
                            
                            show_variations(current_object);

                            variation_column_hide_show(1, "hide");
                            
                            variation_column_hide_show(2, "hide");
                        });

                    }
                    else 
                    {
                        if ($("select[name=variation_theme]").data("last_value") != 'undefined') 
                        {
                            $("select[name=variation_theme]").val($("select[name=variation_theme]").data("last_value"));
                        }

                        if($("select[name=variation_theme]").val().length > 0)
                        {    
                            show_variations(current_object);

                            variation_add_edit();
                        }
                    }
                }
            });
        }
        else 
        {
            show_variations(current_object);
        }       
    }
}

function hide_variations()
{
    $('#make_variations').hide();

    $("#theme_2_div").hide();

    $("#theme_1_div").hide();
}

function show_variations(current_object)
{
    $("select[name^=variation_theme]").data("last_value", $("select[name^=variation_theme]").val())
    
    variation_add_edit('show');

    option = $(current_object).find("option:selected");

    if (option.length > 0) 
    {
        var theme_1 = $(option).attr("theme_1");
        
        var theme_2 = $(option).attr("theme_2");

        if (theme_1.length > 0) 
        {
            $(current_object).attr("value", "size");

            $(".theme_1_label").html(theme_1);

            $("table th.variation-size-header span").html(theme_1);

            $("#theme_1_div").show();
        }

        if (theme_2.length > 0) {
            $(current_object).attr("value", "size-color");

            $(".theme_2_label").html(theme_2);
            
            $("table th.variation-color-header span").html(theme_2);
            
            $("#theme_2_div").show();
        }

        $('#make_variations').show();
    }
    else
    {
        hide_variations();
    }   
}

$.fn.variation_add = function () {
    strlength = $.trim($(this).val()).length;
    if ($(this).parent().nextAll("div").length == 0 && $.trim($(this).val()) != "") 
    {
        clone_input = $(this).parent().clone();
        $(clone_input).find("input").val("");
        $(this).parent().after(clone_input);
    }
};

$('body').on('click', '.btn-add-variation', function (e) {
    variation_type = $("select[name=variation_theme]").attr("value");

    option = $("select[name=variation_theme]").find("option:selected");
    
    var theme_1 = $(option).attr("theme_1");
    
    var theme_2 = $(option).attr("theme_2");

    variation_box_count = 0;

    if (variation_type == "size") {
        variation_box_count = $("input.size-init-input-box").filter_input_not_blank().length
    }
    else if (variation_type == "color") {
        variation_box_count = $("input.color-init-input-box").filter_input_not_blank().length
    }
    else if (variation_type == "size-color") {

        variation_box_count = $("input.size-init-input-box").filter_input_not_blank().length
        
        if ($("input.color-init-input-box").filter_input_not_blank().length < variation_box_count) 
        {
            variation_box_count = $("input.color-init-input-box").filter_input_not_blank().length;
        }
    }

    var theme_array = [];

    size = $("input.size-init-input-box:visible").filter_input_not_blank();

    color = $("input.color-init-input-box:visible").filter_input_not_blank();

    color_index = [];

    if (variation_box_count > 0) {
        if (variation_type == "size") {
            if (size.length > 0) {
                size.each(function () {
                    size_value = $(this).val();
                    theme_array.push({size: size_value});
                });
            }
        }
        else if (variation_type == "color") {
            if (color.length > 0) {
                color.each(function () {
                    color_value = $(this).val();
                    theme_array.push({color: color_value});
                });
            }
        }
        else if (variation_type == "size-color") {
            if (size.length > 0 && color.length > 0) {
                color.each(function () {
                    color_value = $(this).val();
                    size.each(function (index) {
                        size_value = $(this).val();
                        theme_array.push({size: size_value, color: color_value});
                    });
                });
            }

        }
    }

    if (variation_box_count != null && variation_box_count > 0 && theme_array.length > 0) {

        for (var i = 0; i < theme_array.length; i++) 
        {
            var var_row_clone = $("tr.variation-row-template:first").clone().removeClass("display-none").removeClass("variation-row-template");

            $(var_row_clone).find("input,select").prop("disabled", false);

            $(var_row_clone).find('input[name^=var_qty]').prop('disabled', true);
            
            // $(var_row_clone).find('input[name^=var_barcode]').attr('required', 'required');

            var variation_title = $('input[name="title"]').val();

            if (variation_type == "size") {
                $(var_row_clone).find("input[name^=var_size]").val(theme_array[i].size)
                variation_title += ', '+theme_1+':'+theme_array[i].size;
            }
            else if (variation_type == "color") {
                variation_column_hide_show(2)
                $(var_row_clone).find("input[name^=var_color]").val(theme_array[i].color)
                variation_title += ', '+theme_2+':'+theme_array[i].color;
            }
            else if (variation_type == "size-color") {
                variation_column_hide_show(1)
                variation_column_hide_show(2)
                $(var_row_clone).find("input[name^=var_size]").val(theme_array[i].size)
                $(var_row_clone).find("input[name^=var_color]").val(theme_array[i].color)
                variation_title += ', '+theme_1+':'+theme_array[i].size;
                variation_title += ', '+theme_2+':'+theme_array[i].color;
            }

            barcode = $('input[name="product_identifier"]').val();
            $(var_row_clone).find("input[name^=var_title]").val(variation_title)
            $(var_row_clone).find("input[name^=var_barcode]").val(barcode)
            get_variation_sku(var_row_clone)
            $("table.variation-table tr:last").after(var_row_clone);
        }

        variation_column_hide_show(1, "hide");

        variation_column_hide_show(2, "hide");

        if (variation_type == "size") {
            variation_column_hide_show(1)
        }
        else if (variation_type == "color") {
            variation_column_hide_show(2)
        }
        else if (variation_type == "size-color") {
            variation_column_hide_show(1)

            variation_column_hide_show(2)
        }

        $("input.size-init-input-box:not(:first)").parent().remove();

        $("input.size-init-input-box:first").val("");

        $("input.color-init-input-box:not(:first)").parent().remove();

        $("input.color-init-input-box:first").val("")

        variation_add_edit();
    }
    else {
        PoundShopApp.commonClass._displayErrorMessage('Please enter variation theme combination.');
    }
});

function variation_column_hide_show (index, type) {
    if (type == "hide") {
        $("table tbody tr:not(.variation-row-template):first").same_level_children().find("td:nth-child(" + (index + 2) + ")").hide();

        $("table thead tr:not(.variation-row-template):first").same_level_children().find("th:nth-child(" + (index + 2) + ")").hide();
    }
    else {
        $("table tbody tr:not(.variation-row-template):first").same_level_children().find("td:nth-child(" + (index + 2) + ")").show();
        $("table thead tr:not(.variation-row-template):first").same_level_children().find("th:nth-child(" + (index + 2) + ")").show();
    }

}

$.fn.filter_input_not_blank = function()
{   
      return $(this).map(function()
      {
          if($.trim($(this).val())!="")
          {
             return this; 
          }
          
      });
};

$.fn.same_level_children = function()
{   
    return jQuery.merge($(this),$(this).siblings());
};  

variation_add_edit = function ($hide_show) {
    if ($hide_show == 'show') {
        $('#theme_values').show();
        $("#add_variation_title, #add_variation_button").show();

        $("#edit_variation_button, #edit_variation_title").hide();
    }
    else {
        $('#theme_values').hide();
        $("#add_variation_title, #add_variation_button").hide();

        $("#edit_variation_button, #edit_variation_title").show();
    }

}

$('body').on('click', '#edit_variation_button', function (e) {
    variation_add_edit("show");
});

remove_variation = function () {
    $("table tbody input.child-checkbox:checked:not([disabled])").closest("tr").each(function () 
    {
        if ($(this).find("input[type=hidden][name^=var_id]").length > 0) 
        {
            $("form#form-variation").append("<input type='hidden' name='var_remove_product_id[]' value='" + $(this).find("input[type=hidden][name^=var_id]").val() + "' />");
        }
        
        $(this).remove();
    });
}

remove_variation_single = function(me){

    var tr = $(me).closest('tr');
    
    if(tr.length > 0)
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
            callback: function (result) {
                if(result == true)
                {  
                    if ($(tr).find("input[type=hidden][name^=var_id]").length > 0) 
                    {
                        $("form#form-variation").append("<input type='hidden' name='var_remove_product_id[]' value='" + $(tr).find("input[type=hidden][name^=var_id]").val() + "' />");
                    }
                    
                    $(tr).remove();
                }
            }        
        });   
    }     
}

function bulk_variation_delete()
{
    if($("table tbody input.child-checkbox:checked:not([disabled])").length > 0)
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
            callback: function (result) {
                if(result == true)
                {  
                    remove_variation();
                }
            }
        });          
    }
    else
    {
        PoundShopApp.commonClass._displayErrorMessage('Please select atleast one record.');
    }    
}

function get_variation_sku(current_obj)
{
    return $.ajax({
            url: BASE_URL+'api-product-get-sku',
            type: "GET",
            datatype:'JSON',
            data:{},
            headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                $("#page-loader").hide();
                if(response.status == true && typeof response.data.sku != 'undefined')
                {
                    $(current_obj).find("input[name^=var_sku]").val(response.data.sku);
                    
                    // return response.data.sku;
                }   
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
            }
        });
}

function variationDocLoadUpdate()
{ 
    $('select[name=variation_theme]').on('change', function(){
        variation_theme_change(this);
    });

    $('body').on('keypress change blur keyup', 'input.size-init-input-box, input.color-init-input-box', function (e) {
        $(this).variation_add();
    });

    if($("select[name=variation_theme]").val() != "" && typeof $("select[name=variation_theme]").val() != 'undefined')
    { 
        show_variations($("select[name=variation_theme]"));

        variation_add_edit();

        option = $("select[name=variation_theme]").find("option:selected");

        var theme_1 = $(option).attr("theme_1");

        var theme_2 = $(option).attr("theme_2");

        if (theme_1.length > 0) 
        {
            variation_column_hide_show(1);
        }

        if (theme_2.length > 0) 
        {    
            variation_column_hide_show(2);
        }
    }
    else
    {
        hide_variations();
    }    
}

$('a[data-toggle="tab"]').not('.buying-range-child').on('click',function(e){
    
    let active_tab = $('a[data-toggle="tab"].active').not('.buying-range-child').attr('href');
    
    let active_id = active_tab.replace('#','form-');
    
    if(!$('#'+active_id).valid())
    {
        e.stopImmediatePropagation();
    }
    else
    {               
        save_tab_data(active_id);

        let new_active_tab = $(this).attr('href');

        if(new_active_tab.length > 0)
        {
            $('a[data-toggle="tab"]').not('.buying-range-child').removeClass('active');
                
            $('div.tab-content .tab-pane').not('.buying-range-child').removeClass('active');

            $(this).addClass('active');

            $(new_active_tab).addClass('active');

            new_active_tab = new_active_tab.replace('#','');
            
            $('#head-submit').attr('form', 'form-'+new_active_tab);

            set_header_tab_menus();

            set_query_para('active_tab', new_active_tab); 
        }    
    }   
});

$(document).on('click', 'a[href="#buying-range"]' ,function(e){
    reInitSlick();
});

$(document).on('shown.bs.tab', 'a.buying-range-child[href="#buying-view-range"]', function (e) {
    reInitSlick();
});

$(".select2-tag").select2({
    tags: true,
    dropdownParent: $('#select_2_dropdown')
    // tokenSeparators: [',', ' ']
})

function get_categories_by_keyword(me)
{
    set_category_id();

    $('#categories_by_keyword').html('');

    $('input[name="buying_category_id"]').val('');
    
    var keyword = $('#category-search-box').val();
    
    keyword = $.trim(keyword);
    
    if(keyword.length > 0)
    {
        $.ajax({
                url: BASE_URL+'api-range-search',
                type: "POST",
                datatype:'JSON',
                data:{'keyword':keyword},
                headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $("#page-loader").hide();
                    
                    if (response.status == 1) {
                        if(response.data.ranges.length > 0)
                        {
                            var search_result_html = "";
                            
                            $.each(response.data.ranges, function(i, value){
                                
                                html = $('#searched_cat_template').clone();

                                // set radio value
                                $(html).find('input[name="sel_category"]').val(value.id);
                                
                                // set radio label
                                $(html).find('span.category_radio_label').html(function(){
                                    return $(this).html().replace("category_label_delimeter", value.category_name); 
                                });
                                
                                // set category path
                                $(html).find('.category_path').html(value.path);

                                search_result_html += $(html).html();
                            });

                            $('#categories_by_keyword').html(search_result_html);
                            
                            $('#categories_by_keyword').find('input[name="sel_category"]:first').attr('checked', 'checked');
                            
                            $('#categories_by_keyword').find('input[name="sel_category"]:first').trigger('change');
                        }
                        else
                        {
                            $('#categories_by_keyword').html('<p>No Records Found.</p>');
                        }
                        
                    }
                },
                error: function (xhr, err) {
                   $("#page-loader").hide();
                }
            });
    }
    else
    {
        $('#categories_by_keyword').html('<p>No Records Found.</p>');
        
        bootbox.alert({
                title: "Alert",
                message: "Please enter the keyword.",
                size: 'small'
            });
        return false;
    }    
}

function set_category_id(me = "")
{
    $('input[name="buying_category_id"]').val('');
    
    if(typeof $(me).val() != 'undefined')
    {    
        if($(me).val().length > 0)
        {   
            $('input[name="buying_category_id"]').val($(me).val());
        }
    }

    set_category_info($('#category-search-box'));
}

  

function get_buying_category_nodes(me, getMappingDetails = true)
{
    var cat_id = $(me).attr('attr-id');

    var nodesJson = $('body').data(cat_id); 
    
    var bredcrum_html = "";

    $('input[name="buying_category_id"]').val(cat_id);
    
    if(getMappingDetails == true)
    {    
        set_category_info(me);
    }
    $(me).parents('div.category-level').find('a[attr-id]').removeClass('active');

    $(me).parents('div.slick-active').nextAll().remove();

    $(me).addClass('active');

    $(me).parents('#categoryLevelDiv').find('a[attr-id].active').each(function(){
        let cat_name = $(this).attr('attr-cat-name'); 
        bredcrum_html += '<li><a href="#">'+cat_name+'</a></li>';
    });

    $('#category-breadcrumbs-ul').html(bredcrum_html);
    
    if(typeof nodesJson != 'undefined')
    {
        if(nodesJson.length > 0)
        {
            html = '';

            var nodes = jQuery.parseJSON(nodesJson);

            html += '<div class="category-level">';
            
            html +='<ul>';
            
            $.each(nodes, function (i, val){
                
                var child_nodes = "";

                var addChildArrow = "";
                
                if(typeof val.children != 'undefined')
                {   
                    if(val.children.length > 0)
                    {
                        child_nodes = val.children;        

                        addChildArrow = '<span class="icon-moon icon-Right-Arrow"></span>';
                        
                        var child_nodesJson = JSON.stringify(val.children);
                        
                        var category_id = val.id;
                        
                        category_id = category_id.toString();

                        $('body').data(category_id, child_nodesJson);
                    }    
                }   

                html +='<li>';
                
                html +='<a href="javascript:void(0)" attr-id="'+val.id+'" attr-cat-name="'+val.category_name+'" onclick="get_buying_category_nodes(this)">'+val.category_name+addChildArrow+'</a>';
                
                html +='</li>';
                
                   
            });    

            html +='</ul>';
            
            html += '</div>'
            
            html = $.parseHTML(html)

            $('div.category-level:last').after(html);
            
            reInitSlick();
        }       
    }    
}

function set_category_data()
{
    $('#categoryLevelDiv a[attr-id]').each(function(){
        id = $(this).attr('attr-id');
        data = $(this).attr('attr-child-nodes');
        $('body').data(id, data);
    });
}

function set_selected_category_data()
{
    var parent_ids = $('#sel_buying_range_parent_ids').val();
    
    if(parent_ids.length > 0)
    {
        parent_id_array = parent_ids.split('>');
        
        $.each(parent_id_array, function (i, val){
            get_buying_category_nodes($('#categoryLevelDiv').find('a[attr-id="'+val+'"]'), false);
        });
    }    
}

function set_category_info(me)
{
    var buying_category_id = $('input[name="buying_category_id"]').val();
    
    $(me).parents('.buying-range-child').find('#magento_range_content').html(""); 

    if(buying_category_id.length > 0)
    {
        $.ajax({
            type: "GET",
            url: WEB_BASE_URL+'/product/magento-range-content',
            data: {
                    'buying_category_id' : buying_category_id,
                },
            datatype: 'HTML',
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            success: function (response) {
                $(me).parents('.buying-range-child').find('#magento_range_content').html(response); 
            },
           
        });
    }   
}

function initalize_tab_switch_save()
{
    if($('form[tab_switch_save]').length > 0)
    {   
        $('form[tab_switch_save]').each(function()
        {
            var formId = (typeof $(this).attr('id') != 'undefined') ? $(this).attr('id') : 'main_form';

            on_load_form_data[formId] = $(this).serialize();
        });
    }
}

function save_tab_data(form_id, do_not_save = false)
{
    var result = true;
    
    if(form_id == 'form-images')
    {
        if(do_not_save == false)
        {
            $('#'+form_id).submit();

            update_serialize_form_data(form_id);
        }   
    }    
    else if(on_load_form_data[form_id].length > 0)
    {   
        if(on_load_form_data[form_id] != $('#'+form_id).serialize())
        {
            result = false;

            if(do_not_save == false)
            {
                $('#'+form_id).submit();

                update_serialize_form_data(form_id);
                
                result = true;
            }   
        }
    }

    return result;
}

function update_serialize_form_data(formId = 'main_form')
{
    if($('#'+formId+'[tab_switch_save]').length > 0)
    {
        on_load_form_data[formId] = $('#'+formId).serialize();
    }   
}

function refreash_tab(form_id = '', html_div_id = "", refreash_url_attr = "refreash_url")
{
    if(form_id.length > 0)
    {
        var refreash_url = $('#'+form_id).attr(refreash_url_attr);

        var data = {};

        if(form_id == 'form-buying-range')
        {
           let active_range_view = $('a.buying-range-child.active').attr('href');
            
            data = {
                        'active_tab' : active_range_view,
                    }
        }  
        
        if(refreash_url.length > 0)
        {
            $.ajax({
                type: "GET",
                url: refreash_url,
                data:data,
                datatype: 'html',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                success: function (response) {
                   if($('#'+html_div_id).length > 0)
                   {
                        $('#'+html_div_id).html(response);
                   } 
                   else
                   {
                        $('#'+form_id).html(response);
                   }
                   if(form_id=='form-images')
                   {
                        $('body .input-images').imageUploader();
                   }
                    if(form_id == 'form-variation')
                    {
                        variationDocLoadUpdate();
                    }

                    if(form_id == 'form-buying-range' && html_div_id == 'form-stock-file')
                    {
                        $(".select2-tag").select2({
                            tags: true,
                            dropdownParent: $('#select_2_dropdown')
                        })
                        
                        $('.reInitSclick').hide();

                        setTimeout(function(){ 
                            initialise_set_data_slick();
                        }, 100);
                    } 
                },
                error: function (xhr, err) {
                   
                }
            });
        }

    }
}

function initialise_set_data_slick()
{
    $(".category-list-holder").not('.slick-initialized').slick({
        infinite: false,      
        slidesToShow: 5,
        arrows: true,

        responsive: [{

          breakpoint: 1600,
          settings: {
            slidesToShow: 4             
          }

        }, 
        {

          breakpoint: 1025,
          settings: {
            slidesToShow: 3             
          }

        }, {

          breakpoint: 769,
          settings: {
            slidesToShow: 2
          }

        }, {

          breakpoint: 300,
          settings: "unslick" // destroys slick

        }],
        onAfterChange: function(){
        }   
    });

    $('.reInitSclick').show();
    
    reInitSlick();

    set_selected_category_data();
}

function set_header_tab_menus()
{
    $('#head-submit').show();

    $('#add-barcode-btn').hide();

    let active_tab = $('a[data-toggle="tab"].active').attr('href');

    if(active_tab == '#barcodes')
    {
        $('#head-submit').hide();
        $('#add-barcode-btn').show();
    }
}  

function write_description(value = "")
{
    if($('textarea[name="long_description"]').attr('old-value') == "")
    {
        CKEDITOR.instances['long_description'].setData(value);
    }    
}

function delete_variation_img(me)
{
    if(typeof $(me).attr('attr-original-url') != 'undefined')
    {
        if($(me).attr('attr-original-url').length > 0)
        {
            $("form#form-variation").append("<input type='hidden' name='var_remove_product_image[]' value='" + $(me).attr('attr-original-url') + "' />");
        } 

        img = $(me).parents('td').find('img');
        video = $(me).parents('td').find('video');
        $(img).show();
        $(video).hide();
        $(img).attr('src', NO_PRODUCT_IMG_URL);   
        
        $(me).parents('td').find('input[name^="var_img"]').val('');
        
        $(me).remove();
    }    
}
