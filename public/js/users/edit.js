
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";

    var poundShopUsers = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
            $('#state_id').attr('autocomplete','off');
            $('#city_id').attr('autocomplete','off');
           
        });
    };
    var c = poundShopUsers.prototype;
    
    c._initialize = function ()
    {
        $('.check_to_update_password').hide();
        $('.country_id').select2();
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
    $('#user_role').change(function(){        
        var selected_string=$(this).find('option:selected').text();
        if(selected_string.includes('Replen'))
        {
            $('.replen_team_option').css('display','block');
        }
        else
        {
            $('.replen_team_option').css('display','none');
        }
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
   
    $('#profile_pic').change(function(){
        var size=(this.files[0].size);
        var fileTypes = ['jpg', 'jpeg', 'png'];
        var extension = this.files[0].name.split('.').pop().toLowerCase();
        var isSuccess = fileTypes.indexOf(extension) > -1;
        if(isSuccess)
        {
         if(size>2000000)
            { 
                $('.invalid-feedback').show();
                $('.imageError').html("Image size should be less than  or equal 2 MB.");
                $('.btn-blue').attr('disabled', true);
            }
            else
            {
                $('.invalid-feedback').hide();
                $('.imageError').html('');
                $('.btn-blue').attr('disabled', false);
            }
        }
    });
     $("#edit-user-form").validate({
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
                "first_name": {
                    required: true,
                    normalizer: function (value) {
                        return $.trim(value);
                    },
                    maxlength: 40,
                    minlength: 3,
                },
                "last_name": {
                    required: true,
                    normalizer: function (value) {
                        return $.trim(value);
                    },
                    maxlength: 40,
                    minlength: 3,
                },
                "password" : {
                    
                    minlength : 6
                },
                "c_password" : {
                    
                    minlength : 6,
                    equalTo : "#password"
                },
                "email": {
                    required: true,
                    email: true,
                },
                "zipcode":{
                    maxlength:12
                },
                "profile_pic": {
                    accept:"jpg,png,jpeg",
                },
                "country_id": {
                    required: true,
                },
                "state_id": {
                    required: true,
                },
                "city_id": {
                    required: true,
                },
            },
            messages:{
                    "profile_pic":{
                        accept:"Only image type jpg/png/jpeg is allowed",
                    },
            },
            errorPlacement: function (error, element) {
                if(error[0].id=="profile_pic-error")
                {
                    error.insertAfter(element.closest('div'));
                }
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
               // var dataString = $("#edit-user-form").serialize();
                var dataString = new FormData($("#edit-user-form")[0]);
                $('.btn-blue').attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: $("#edit-user-form").attr("action"),
                    data: dataString,
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
                        $('.btn-blue').attr('disabled', false);
                        $("#page-loader").hide();
                       // console.log(response);return false;
                        if (response.status == 1) {
                          //  $("#edit-user-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            setTimeout(function () {
                                window.location.href = WEB_BASE_URL + '/users';
                            }, 2000);
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
window.PoundShopApp.poundShopUsers = new poundShopUsers();

})(jQuery);
/*function getStatesCountrywise()
{
    $.ajax({
        type: "GET",
        url:BASE_URL+"api-states/"+$("#country_id").val(),
        headers: {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        success: function (response) {
            var items=response.data;
            $.each(items, function (i, item) {
                $('#state_id').append($('<option>', { 
                    value: item.id,
                    text : item.name 
                }));
            });
           
        },
        error: function (xhr, err) {
            console.log(err);
        }
    });
}
function getCityStateWise()
{
    $.ajax({
        type: "GET",
        url:BASE_URL+"api-cities/"+$("#state_id").val(),
        headers: {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        success: function (response) {
            var items=response.data;
            $.each(items, function (i, item) {
                $('#city_id').append($('<option>', { 
                    value: item.id,
                    text : item.name 
                }));
            });
           
        },
        error: function (xhr, err) {
            console.log(err);
        }
    });
}*/
function Validate(event) {
    var regex = new RegExp(" /^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/");
    var key = String.fromCharCode(event.charCode ? event.which : event.charCode);
    if (!regex.test(key)) {
        event.preventDefault();
        return false;
    }
} 
function enableChangePassword()
{
    var val=$("#enable_chage_password").is(":checked") ? 1 : 0;
    if(val==1)
    {
        $('.check_to_update_password').show();
    }
    else
    {
        $('.check_to_update_password').hide();
    }
}

function removeImage(id)
{
    
    bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to delete profile pic?",
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
                $('.remove-img').attr('disabled',true);
                $.ajax({
                    type: "POST",
                    url: BASE_URL + 'api-users-remove-image',
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
                            $('.imageDiv').remove();
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

function blockSpecialChar(e)
{
  var value = String.fromCharCode(event.which);
   var pattern = new RegExp(/[a-zåäö ]/i);
   return pattern.test(value);
}
$('#first_name').bind('keypress',blockSpecialChar);
$('#last_name').bind('keypress',blockSpecialChar);
$('#emergency_contact_name').bind('keypress',blockSpecialChar);

$(function() {
  var regExp = /[a-z]/i;
  $('#phone_no').on('keydown keyup', function(e) {
    var value = String.fromCharCode(e.which) || e.key;

    // No letters
    if (regExp.test(value)) {
      e.preventDefault();
      return false;
    }
  });
   $('#mobile_no').on('keydown keyup', function(e) {
    var value = String.fromCharCode(e.which) || e.key;

    // No letters
    if (regExp.test(value)) {
      e.preventDefault();
      return false;
    }
  });
  $('#emergency_contact_num').on('keydown keyup', function(e) {
    var value = String.fromCharCode(e.which) || e.key;

    // No letters
    if (regExp.test(value)) {
      e.preventDefault();
      return false;
    }
  });
});