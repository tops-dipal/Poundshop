/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var poundShopSetting = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopSetting.prototype;    
    c._initialize = function ()
    {        

    };
    
    $("#terms-form").validate({
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
            'terms_pound_non_uk':{
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
              
            },
        "terms_pound_uk": {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
               
            }},
        errorPlacement: function (error, element) {
            error.insertAfter(element.closest('div').children('div'));
        },
        highlight: function (element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        submitHandler: function (form) {
            var dataString = $("#terms-form").serialize();
            $('.btn-blue').attr('disabled', true);
            $.ajax({
                type: "post",
                url: BASE_URL + $("#terms-form").attr("action"),
                data: dataString,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $('.btn-blue').attr('disabled', false);
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
        
    
    
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopSetting = new poundShopSetting();

})(jQuery);

