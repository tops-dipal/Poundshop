/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
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

    };
   $("#qty").keypress(function(value) {
        if (String.fromCharCode(value.keyCode).match(/[^0-9]/g)) return false;
      });
    $("#create-totes-form").validate({
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
            "name": {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                maxlength: 40,
                minlength: 3,
            },
            "length": {
                required: true,
               number:true
            },
            "width": {
                required: true,
               number:true
            },
            "height": {
                required: true,
               number:true
            },
            "max_volume": {
                required: true,
               number:true
            },
            "max_weight": {
                required: true,
               number:true
            },
            "quantity": {
                required: true,
               number:true
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
        submitHandler: function (form) {
            var dataString = $("#create-totes-form").serialize();
            $('.btn-primary').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#create-totes-form").attr("action"),
                data: dataString,
                processData: false,

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
                        $('#submit').attr('disabled', false);
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            //$("#create-carton-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            setTimeout(function () {
                                window.location.href = WEB_BASE_URL + '/totes';
                            }, 1000);
                        }
                    },
                    error: function (xhr, err) {
                        $('.btn-primary').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
            });

        }
    });
        
    $("#length,#width,#height").keyup(function()
    {          
        var multiple_volume = 0;
        var length=0;
        var width=0
        var heigth=0;

        if($("#length").val() !== undefined && $("#length").val() !==""){
        length= parseFloat($("#length").val());
        }
        if($("#width").val() !== undefined && $("#width").val() !==""){
        width= parseFloat($("#width").val());
        }
        if($("#height").val() !== undefined && $("#height").val() !=="" ){
        heigth= parseFloat($("#height").val());
        }
        var max_volume='';

        if(length!='' && width!='' && heigth!='')
        {
            max_volume=length*width*heigth;
        }
        else if(length!='' && width!='' && heigth=='')
        {
            max_volume=length*width;
        }
        else if(length!='' && width=='' && heigth!='')
        {
            max_volume=length*heigth;
        }
        else if(length=='' && width!='' && heigth!='')
        {
            max_volume=width*heigth;
        }
        else if(length!='' && width=='' && heigth=='')
        {
            max_volume=length;   
        }
        else if(length=='' && width!='' && heigth=='')
        {
            max_volume=width;   
        }
        else if(length=='' && width=='' && heigth!='')
        {
            max_volume=heigth;   
        }
        max_volume=max_volume/1000000;
        $("#max_volume").val(max_volume.toFixed(2));
    });
    
    $("#reset").click(function()
    {
        window.location.href = WEB_BASE_URL + '/totes';
    })

    $("#qty").keydown(function(e)
    {
        var key = e.charCode || e.keyCode || 0;
        // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
        // home, end, period, and numpad decimal
        return (
            key == 8 || 
            key == 9 ||
            key == 13 ||
            key == 46 ||
            (key >= 35 && key <= 40) ||
            (key >= 48 && key <= 57) ||
            (key >= 96 && key <= 105));
    });

    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopTotes = new poundShopTotes();

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
$('#length').on('keydown' , function(event) {

        if(! $(event.target).is('input')) {
            console.log(event.which);
           //event.preventDefault();
           if(event.which == 8) {
            //  alert('backspace pressed');
            return false;
         }
        }
});