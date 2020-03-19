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
        $('.custom-select-search').selectpicker({
            liveSearch:true,
            size:10
        });
    }

    $('body').on('click', '#save_and_post', function(){
        if($("#add_form").valid())
        {
            $('input[name="is_posted"]').val(1);
            
            $("#add_form").submit(); 
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $("#add_form").validate({
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
            "magento_product_id":{
                required: true,
            },
            "magento_product_type":{
                required: true,
            },
            "sku":{
                required: true,
            },
            "product_title":{
                required: true,
            },
            "date_to_go_live":{
                required: true,
            },
            "selling_price":{
                required: true,
            },
            "country_of_origin":{
                required: true,
            },
            "quantity":{
                required: true,
            },
            // "brand":{
            //     required: true,
            // },
            "category_ids[]":{
                required: true,
            },
            // "magento_product_length":{
            //     required: true,
            //     min: 1,
            // },
            // "magento_product_height":{
            //     required: true,
            //     min: 1,
            // },
            // "magento_product_width":{
            //     required: true,
            //     min: 1,
            // },
            // "magento_product_weight":{
            //     required: true,
            //     min: 1,
            // },
            "product_description":{
                required: true,
            },
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
            if(element.attr('name') == 'magento_product_id')
            {
               error.insertAfter(element.parent('div')); 
            }
            if(element.attr('name') == 'category_ids[]')
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
    
    $('#add_form').on('submit', function (e){
        e.preventDefault();
        
        if($('#add_form').valid())
        {    
            var dataString = new FormData($(this)[0]);
            
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

                    $('.btn-form').attr('disabled', false); 

                    if (response.status == 1) 
                    {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        
                        var is_posted = $('input[name="is_posted"]').val();

                        setTimeout(function () {
                                
                                if(is_posted == '0')
                                {
                                    window.location.href = WEB_BASE_URL + '/listing-manager/magento/to-be-listed';
                                }   
                                else
                                { 
                                    window.location.href = WEB_BASE_URL + '/listing-manager/magento/in-progress';
                                }
                            }, 1000);
                    }
                },
                error: function (xhr, err) {
                    $('.btn-form').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });    
        }
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
                if(size>20000000)
                { 
                    alert("Video size should be less than  or equal 20 MB");
                    $('.btn-blue').attr('disabled',true);
                }
                else
                {
                    $('.btn-blue').attr('disabled',false);
                    if(videoShower!="")
                    {
                        block.hide();
                        videoShower.show();
                        reader.onload = function (e) {
                        videoShower.attr('src', e.target.result);
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                    
                   else
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
                   }
                   
            }
            else
            {
                if(size>10000000)
                {
                    alert("Image size should be less than  or equal 10 MB");
                    $('.btn-blue').attr('disabled',true);
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

    $("input[name='main_image_url']").change(function(){
        previewImage(this, $('#magentoimagePreview'),$('#magento_img_remove'),$('.magentoVideoPreview'));
    });

    $('.input-images').imageUploader();
    
    window.PoundShopApp = window.PoundShopApp || {}
    
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);  

function delete_other_img(me)
{
    $(me).parents('.other_image_div').remove();
}