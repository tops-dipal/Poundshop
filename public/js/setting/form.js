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
    
    $("#setting-form").validate({
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
            "zero_rated":{
                required:true,
                maxlength:5,
            },
            "standard_vat_rate":{
                required:true,
                maxlength:5,
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
            var dataString = $("#setting-form").serialize();
            $('.btn-blue').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#setting-form").attr("action"),
                data: dataString,
                // processData: false,
                // contentType: false,
                // cache: false,
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
                       
                        setTimeout(function () {
                             PoundShopApp.commonClass._displaySuccessMessage(response.message);
                           //location.reload();
                        }, 1000);
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

function fun_AllowOnlyAmountAndDot(txt)
{
    if(event.keyCode > 47 && event.keyCode < 58 || event.keyCode == 46)
    {
        var txtbx=document.getElementById(txt);
        var amount = document.getElementById(txt).value;
        var present=0;
        var count=0;

        if(amount.indexOf(".",present)||amount.indexOf(".",present+1));
        {
        // alert('0');
        }

        /*if(amount.length==2)
        {
        if(event.keyCode != 46)
        return false;
        }*/
        do
        {
            present=amount.indexOf(".",present);
            if(present!=-1)
            {
                count++;
                present++;
            }
        }
        while(present!=-1);
        if(present==-1 && amount.length==0 && event.keyCode == 46)
        {
            event.keyCode=0;
            //alert("Wrong position of decimal point not  allowed !!");
            return false;
        }

        if(count>=1 && event.keyCode == 46)
        {

            event.keyCode=0;
            //alert("Only one decimal point is allowed !!");
            return false;
        }

        if(count==1)
        {
            var lastdigits=amount.substring(amount.indexOf(".")+1,amount.length);
            if(lastdigits.length>=2)
            {
                //alert("Two decimal places only allowed");
                event.keyCode=0;
                return false;
            }
        }
        return true;
    }
    else
    {
        event.keyCode=0;
        //alert("Only Numbers with dot allowed !!");
        return false;
    }
}

