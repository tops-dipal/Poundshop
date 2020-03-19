/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
  "user strict";

  var poundShopLocations = function ()
  {
    $(document).ready(function ()
    {
        c._initialize();
    });
  };
  var c = poundShopLocations.prototype;
  
  c._initialize = function ()
  {        

  };
    
  $("#locations-settings-form").validate({
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
      "dist_aisle_rack": {
        required: true,
        number:true
      },
      "walk_speed": {
        required: true,
        number:true
      },
      "time_multipick": {
        required: true,
        number:true              
      },
      "time_singlepick": {
        required: true,
        number:true
      },
      "storage_buffer": {
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
    submitHandler: function (form) 
    {
      var dataString = $("#locations-settings-form").serialize();
      $('.btn-primary').attr('disabled', true);
      $.ajax({
        type: "POST",
        url: $("#locations-settings-form").attr("action"),
        data: dataString,
        processData: false,
        headers: {
          'Authorization': 'Bearer ' + API_TOKEN,
        },
        beforeSend: function () {
            $("#page-loader").show();
        },
        success: function (response) 
        {
          $('.btn-primary').attr('disabled', false);
          $("#page-loader").hide();
          if (response.status == 1) 
          {
            PoundShopApp.commonClass._displaySuccessMessage(response.message);
            setTimeout(function () 
            {
              window.location.href = WEB_BASE_URL + '/locations';
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
  
  $("#dist_aisle_rack,#walk_speed,#time_multipick,#time_singlepick,#storage_buffer").keydown(function(e)
  {
    var key = e.charCode || e.keyCode || 0;        
    return (key == 8 || key == 9 || key == 13 || key == 46 || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) ||
          (key >= 96 && key <= 105));
  });    

  window.PoundShopApp = window.PoundShopApp || {}
  window.PoundShopApp.poundShopLocations = new poundShopLocations();
})(jQuery);