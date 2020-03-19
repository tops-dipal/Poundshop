/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";

    var poundShopWarehouses = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopWarehouses.prototype;
    
    c._initialize = function ()
    {
      if($('input[name="id"]').val()=='')
      {
        $('.country_id').select2().trigger('change');
      }
      else
      {
        $('.country_id').select2();
      }

      getGoogleAddress();
    };
    function getGoogleAddress()
    {
        var autocomplete = new google.maps.places.Autocomplete($("#address")[0], {});
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            $(".country_id").val("");
            $("#state_id").val("");
            $("#city_id").val("");
            $('#address_line1').val("");
            $('#address_line2').val("");
            $('#zipcode').val("");
            var place = autocomplete.getPlace();
            
            var addressType1=['premise','street_number','route'];
            var addressType2=['neighborhood','sublocality_level_1','sublocality_level_2'];

            var cityType=['locality',"postal_town"];
            var postCodeType='postal_code';
            var countryType='country';
            console.log(place.address_components);
            var country_type="non-uk";
             $('body').data('country_type', country_type);
            $.each(place.address_components.reverse(), function( index, value ) {
                $.each(value.types, function( typeindex, typeval ) {
                   
                    
                    if(jQuery.inArray(typeval, addressType1 )!=-1)
                    {
                        $('#address_line1').val(value.long_name+' '+$('#address_line1').val());
                    }
                    if(jQuery.inArray(typeval, addressType2 )!=-1)
                    {
                        $('#address_line2').val(value.long_name+' '+$('#address_line2').val());
                    }
                    if(jQuery.inArray(typeval, cityType )!=-1)
                    {
                        $('#city_id').removeAttr('disabled');
                        $('#city_id').val(value.long_name);
                        $('body').data('city_id', value.long_name);
                    }
                    if(typeval==countryType)
                    {
                        if(value.long_name=="United Kingdom")
                        {
                            country_type="uk";
                             $('body').data('country_type', country_type);
                        }
                        else
                        {
                            country_type="non-uk";
                             $('body').data('country_type', country_type);
                        }
                      $(".country_id option").each(function() {
                      
                            if($(this).text() == value.long_name) {
                                var selectedCountry=$(this).attr('value');
                                console.log(selectedCountry);
                                $('.country_id').val(selectedCountry);
                                $('.country_id').select2().trigger('change');
                            }                        
                        });
                    }
                    if(typeval==postCodeType)
                    {
                        $('#zipcode').val(value.long_name);
                    }
                    
                    if(typeval=="administrative_area_level_1" && $('body').data('country_type')=='non-uk')
                    {
                        console.log("non-uk=="+value.long_name);
                        $('body').data('state_id', value.long_name);
                       
                        $('#state_id').val(value.long_name);
                    }
                    if(typeval=="administrative_area_level_2" && $('body').data('country_type')=='uk')
                    {
                        $('body').data('state_id', value.long_name);
                        $('body').data('state_id_uk', value.long_name);
                        console.log("uk=="+value.long_name);
                        $('#state_id').val(value.long_name).trigger('change');
                    }
                   
              
                });
                
            });
            if($('#address_line1').val()=="")
            {
                $('#address_line1').val($('#address_line2').val());
            }
            $('#state_id').val($('body').data("state_id")).trigger('change');
        });
    }
    $("#create-warehouse-form").validate({
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
          "contact_person": {
              required: true,           
              maxlength: 40,
              minlength: 3,
          },
          "phone_no": {
              required: true,
              number:true
          },
          "address_line1": {
              required: true                
          },
          "address_line2": {
              required: true                
          },
          "country": {
              required: true,                
          },
          "state": {
              required: true,                
          },
          "city": {
              required: true,                
          },
          "zipcode": {
              required: true,  
              //number:true              
          },            
        },
        errorPlacement: function (error, element) {
          if(error[0].id=="country_id-error")
            {
                console.log(element.closest('div'));
                error.insertAfter('.select2-selection__arrow');
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
            var dataString = $("#create-warehouse-form").serialize();
            $('.btn-primary').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#create-warehouse-form").attr("action"),
                data: dataString,
                processData: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {                    
                    $('.btn-primary').attr('disabled', false);
                    $("#page-loader").hide();
                    if (response.status == 1) 
                    {                        
                      PoundShopApp.commonClass._displaySuccessMessage(response.message);
                      setTimeout(function () {
                          window.location.href = WEB_BASE_URL + '/warehouse';
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

    //$("#length,#width,#height").keyup(function(){
    $("#phone_no").keydown(function(e)
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
    $('#zipcode').keypress(function (e) {
        var regex = new RegExp("^[a-zA-Z0-9\\-\\s]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        }

        e.preventDefault();
        return false;
    });

    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopWarehouses = new poundShopWarehouses();

})(jQuery);