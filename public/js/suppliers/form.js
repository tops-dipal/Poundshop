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
        if($('input[name="id"]').val()=='')
          {
            $('.country_id').select2().trigger('change');
          }
          else
          {
            $('.country_id').select2();
          }

       
        getGoogleAddress();
        initalize_tab_switch_save();
        
        $('input[name="primary_contact"]').on('change', function(){
            setPrimaryContact(this);
        });
        
        $('input[name="name"]').on('change', function(){
            supplier_name = $('input[name="name"]').val();
            
            if($('input[name="beneficiary_name"]').val().length > 0)
            {    
                $('input[name="beneficiary_name"]').val(supplier_name);
            }
        });
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
    $('form').on('submit', function(e){
        e.preventDefault();

        if($(this).valid())
        {    
            var form = this;

            var dataString = $(this).serialize();
            
            var form_id = $(form).attr("id");
            
            $('#form_submit, #contact_submit').attr('disabled', true);
            
            $.ajax({
                type: "POST",
                url: $(form).attr("action"),
                data: dataString,
                datatype: 'JSON',
                processData: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $('#form_submit, #contact_submit').attr('disabled', false);
                    if (response.status == 1) 
                    {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        
                        if(form_id == 'form-general' && typeof response.data.id != 'undefined')
                        {
                            setTimeout(function () {
                                window.location.href = WEB_BASE_URL + '/supplier/form'+response.data.id+'#contact';
                            }, 1000);  
                        }

                        if(form_id == 'contactForm')
                        {
                            $('#contactModal').modal('hide');
                        }    

                        refresh_tab(form_id);  
                    }
                },
                error: function (xhr, err) {
                    $('#form_submit, #contact_submit').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }    
    })
    
    $("#form-payment").validate({
        //focusInvalid: false, // do not focus the last invalid input
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
        messages: {
            payment_days: {
                required: "Required",
            },
            overall_percent_discount: {
                required: "Required",
            },
            period_discount_days: {
                required: "Both days and percent field is required.",
            },
            period_percent_discount: {
                required: "Both days and percent field is required.",
            },
            retro_amount: {
                required: "All retro discount fields are required.",
            },
            retro_percent_discount: {
                required: "All retro discount fields are required.",
            },
            retro_from_date: {
                required: "All retro discount fields are required.",
            },
            retro_to_date: {
                required: "All retro discount fields are required.",
            },
        },
        errorPlacement: function (error, element) {

            if(element.attr('name') == 'payment_days' ||
                    element.attr('name') == 'overall_percent_discount'
                )
            {
                error.insertAfter(element.parents('label'));
            }
            else if(  element.attr('name') == 'period_discount_days' ||
                    element.attr('name') == 'period_percent_discount'
            )
            {
                if($('#discountDaysGet').find('.invalid-feedback').length > 0)
                {
                    $('#discountDaysGet').find('.invalid-feedback').text('Both days and percent field is required.');
                }
                else
                {
                    error.insertAfter(element.parents('label'));
                }
            }
            else if(   
                    element.attr('name') == 'retro_amount' ||
                    element.attr('name') == 'retro_percent_discount' ||
                    element.attr('name') == 'retro_from_date' ||
                    element.attr('name') == 'retro_to_date'
            )
            {
                if($('#retroOptions').find('.invalid-feedback').length > 0)
                {
                   $('#retroOptions').find('.invalid-feedback').text('All retro discount fields are required.'); 
                }   
                else
                {
                    error.insertAfter(element.parents('label'));
                } 
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
        }
    });


     $("#form-general").validate({
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
            
            "name": {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                maxlength: 40,
                minlength: 3,
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
        errorPlacement: function (error, element) {
            
            
            if(element.attr('name') == 'payment_days' ||
                    element.attr('name') == 'period_discount_days' ||
                    element.attr('name') == 'period_percent_discount' ||
                    element.attr('name') == 'overall_percent_discount' ||
                    element.attr('name') == 'retro_amount' ||
                    element.attr('name') == 'retro_percent_discount' 
                )
            {
                error.insertAfter(element.parents('label'));
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
        }
    });
    
    $.validator.addMethod( "unique_email", function( value, element, params ) {
        var valid = true;
        
        var exclude_el = "";

        if($('#contactForm input').hasClass('edit_contact'))
        {
            temp_id = $('#contactForm .edit_contact').val();
            exclude_el = 'supplier_contacts['+temp_id+']';
        }

        $('input[name^="supplier_contacts"]').each(function(){
            formData = $(this).val();
            
            formarray =  getGetQueryStringParameter(formData);    
            
            if(formarray['email'] == value && exclude_el != $(this).attr('name'))
            {
                valid = false;
            }    
        });
        return valid;
    }, $.validator.format( "Please enter unique email for this supplier." ) );

    $("#contactForm").validate({
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
            
            "name": {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                maxlength: 40,
                minlength: 3,
            },
            "email": {
                required: true,
                unique_email:true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                minlength: 3,
                email: true,
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
    
    refresh_tab = function (form_id)
    {
        if(typeof $('#'+form_id).attr('refresh') != 'undefined')
        {
            if($('#'+form_id).attr('refresh').length > 0)
            {
                var refresh_url = $('#'+form_id).attr('refresh');
                var html_dev_id = $('#'+form_id).attr('html_id');

                $.ajax({
                    type: "GET",
                    url: refresh_url,
                    datatype: 'html',
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                      $("#page-loader").hide();
                       $('#'+html_dev_id).html(response);
                       c._initialize()
                    },
                    error: function (xhr, err) {
                        $("#page-loader").hide();
                    }
                });
            }    
        }    
    }
    // $('#supplier_contact_person').DataTable({
    //     paging: false,
    //     searching: false,    
    // });      
        
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);

// Contact Info JS
contactForm = function(me)
{   
    $('#exampleModalLabel').text('Create Contact');
    
    $('span.invalid-feedback').remove();
    
    $('#contactForm .edit_contact').remove();
    
    $('#contactForm').trigger('reset');
    
    $('#contactModal').modal('show');
}

saveContact = function(me)
{
    $('#contactForm').submit();
}



// saveContact = function(me)
// {
//     if($('#contactForm').valid())
//     {   
//         if($('#contactForm input').hasClass('edit_contact'))
//         {
//             var temp_id = $('#contactForm .edit_contact').val();

//             $('input[name="supplier_contacts['+temp_id+']"]').remove();
//         }   
//         else
//         { 
//             var temp_id = $('tr[tr-temp-id]').length + 1; 
//         }

//         var formData = $('#contactForm').serialize();
        
//         var formDataInput =  '<input type="hidden" name="supplier_contacts['+temp_id+']" value="'+formData+'" form="supplier_form">'

//         var radio_check = ($('#contactForm input[name="is_primary"]').prop('checked')) ? 'checked="checked"' : ''  ;

//         if(temp_id == 1)
//         {
//             radio_check = 'checked="checked"';
//         }    

//         var master_radio = '<label class="fancy-radio"><input type="radio" name="primary_contact" value="'+$('#contactForm input[name="email"]').val()+'" '+radio_check+'><span><i></i></span></label>';

//         var row_html = '<tr tr-temp-id="'+temp_id+'">'+
//                             '<td>'+
//                             '<div class="d-flex">'+
//                                 '<label class="fancy-checkbox">'+
//                                     '<input type="checkbox" value="'+temp_id+'" class="child-checkbox">'+
//                                     '<span><i></i></span>'+
//                                 '</label>'+
//                             '</div>'+
//                             '</td>'+
//                             '<td>'+$('#contactForm input[name="name"]').val()+'</td>'+
//                             '<td>'+$('#contactForm input[name="email"]').val()+'</td>'+
//                             '<td>'+$('#contactForm input[name="phone"]').val()+'</td>'+
//                             '<td>'+$('#contactForm input[name="mobile"]').val()+'</td>'+
//                             '<td>'+$('#contactForm input[name="designation"]').val()+'</td>'+
//                             '<td>'+master_radio+'</td>'+
//                             '<td>'+
//                                 '<ul class="action-btns">'+
//                                     '<li>'+
//                                         '<a class="btn-edit" href="javascript:;" temp-id= "'+temp_id+'" onclick="editContact(this)"> <span class="icon-moon icon-Edit"></span></a>'+
//                                     '</li>'+
//                                     '<li>'+
//                                         '<a class="btn-delete" href="javascript:;" temp-id= "'+temp_id+'" onclick="deleteContact(this)"><span class="icon-moon icon-Delete"></span></a>'+
//                                     '</li>'+
//                                 '</ul>'+
//                             '</td>'+
//                             formDataInput+
//                         '</tr>';

//         if($('#contactForm input').hasClass('edit_contact'))
//         {
//             $('tr[tr-temp-id="'+temp_id+'"]').replaceWith(row_html);
//         }
//         else
//         {
//             $('#contact_persons').append(row_html);
//         }    
        
//         $('#contactModal').modal('hide');
//     }
// }

editContact = function(me)
{
    $('#exampleModalLabel').text('Edit Contact');

    $('#contactForm .edit_contact').remove();

    $('span.invalid-feedback').remove();

    $('#contactForm').trigger('reset');

    var temp_id = $(me).attr('temp-id');   
    
    var formData = $('input[name="supplier_contacts['+temp_id+']"]').val();
    
    var formarray =  getGetQueryStringParameter(formData);
    
    var primary_cont = $('input[name="primary_contact"]:checked').val();

    if(formarray['email'] == primary_cont)
    {
        $('#contactForm input[name="is_primary"]').prop('checked', 'checked');
    }    
    
    $('#contactForm input').each(function(i, value)
    {
        el_name = $(this).attr('name');
        
        if(typeof formarray[el_name] != 'undefined' && el_name != 'is_primary')
        {
            $('#contactForm input[name="'+el_name+'"]').val(formarray[el_name])
        }
    });

    var extra_fields = '<input type="hidden" class="edit_contact" value="'+temp_id+'">';

    if(typeof formarray['id'] != 'undefined')
    {
        extra_fields += '<input type="hidden" class="edit_contact" name="id" value="'+formarray['id']+'">';
    }    

    $('#contactForm input:last').append(extra_fields);

    $('#contactModal').modal('show');
}

getGetQueryStringParameter = function(url)
{
    if(url.length > 0)
    {   
        let queryParams = [];
        
        let params = url.split('&');

        for (var i = 0; i < params.length; i++) 
        {
            var pair = params[i].split('=');
            
            queryParams[pair[0]] = decodeURIComponent(pair[1]);
        }
        
        return queryParams;  
    }    
}

deleteContact = function(me)
{
    var ids = [];

    if(typeof $(me).attr('temp-id') != 'undefined')
    {
        ids.push($(me).attr('temp-id'));
    }   
    else
    {
        ids = getListingCheckboxIds();
    } 
    
    if(ids.length > 0)
    {
        var supplier_id = $(me).parents('form').find('input[name="id"]').val();

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
                if(result)
                {
                    $.ajax({
                        type: "POST",
                        url: BASE_URL+'api-supplier-destory-contacts',
                        data: {
                                ids:ids,
                                supplier_id:supplier_id,
                            },
                        datatype: 'JSON',
                        headers: {
                            'Authorization': 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                            refresh_tab('contactForm');
                        },
                        error: function (xhr, err) {
                            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
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

// Payment Info JS
$('input[name="payment_days"]').on('keyup',function(){
  if($('input[name="payment_days"]').val() != "")
  {
    $('input[name="payment_term"][value="3"]').trigger('click');
  }
});

$('input[name="overall_percent_discount"]').on('keyup',function(){
    if($(this).val() != "")
    {
        $('input[name="allow_overall_discount"]').prop('checked', true);    
        $('input[name="allow_overall_discount"]').trigger('change');    
    } 
});

$('input[name="period_discount_days"], input[name="period_percent_discount"]').on('keyup',function(){
    if($(this).val() != "")
    {
        $('input[name="allow_period_discount"]').prop('checked', true);    
        $('input[name="allow_period_discount"]').trigger('change');    
    } 
});


$('input[name="payment_term"]').on('change', function(){
  if($(this).val() == '3'){
    $('input[name="payment_days"]').attr('required', 'required');
    $('#discountDaysGet').show();
  }
  else
  {
    $('input[name="payment_days"]').removeAttr('required');
    $('#discountDaysGet').hide();
    $('input[name="allow_period_discount"]').prop('checked', false);
    $('input[name="allow_period_discount"]').trigger('change');
  }
});

$('input[name="allow_overall_discount"]').on('change', function(){
    if($(this).prop('checked'))
    {
        $('input[name="overall_percent_discount"]').attr('required', 'required');   
    }   
    else
    {
        $('input[name="overall_percent_discount"]').removeAttr('required');
    } 
});

$('input[name="allow_period_discount"]').on('change', function(){
    if($(this).prop('checked'))
    {
        $('input[name="period_discount_days"]').attr('required', 'required');
        $('input[name="period_percent_discount"]').attr('required', 'required');
    }   
    else
    {
        $('input[name="period_discount_days"]').removeAttr('required');
        $('input[name="period_percent_discount"]').removeAttr('required');
    } 
});


$('input[name="allow_retro_discount"]').on('change', function(){
    if($(this).prop('checked') == true)
    {
        $('input[name="retro_amount"], input[name="retro_from_date"], input[name="retro_to_date"], input[name="retro_percent_discount"]').attr('required', 'required');

        $('#retroOptions').show();
    }
    else
    {
        $('input[name="retro_amount"], input[name="retro_from_date"], input[name="retro_to_date"], input[name="retro_percent_discount"]').removeAttr('required', 'required');

        $('#retroOptions').hide();
    }
});


$(document).ready(() => {
  
  let url = location.href.replace(/\/$/, "");
  if (location.hash) {
    const hash = url.split("#");
    $('#myTab a[href="#'+hash[1]+'"]').tab("show");
    url = location.href.replace(/\/#/, "#");
    history.replaceState(null, null, url);
    setTimeout(() => {
      $(window).scrollTop(0);
    }, 400);
  } 

  $('a[data-toggle="tab"]').on("click", function(e) {

    let active_tab = $('a[data-toggle="tab"].active').attr('href');
            
    let active_form_id = active_tab.replace('#','form-');
    
    if(!$('#'+active_form_id).valid())
    {
        e.stopImmediatePropagation();
    }
    else
    {
        let newUrl;
        
        const hash = $(this).attr("href");
        
        if(hash == "#home") 
        {
          newUrl = url.split("#")[0];
        } 
        else 
        {
          newUrl = url.split("#")[0] + hash;
        }
        
        newUrl += "/";
        
        history.replaceState(null, null, newUrl);
        
        let active_tab = $(this).attr('id');
        
        save_tab_data(active_form_id);
        
        set_active_tab_action_buttons(active_tab);
    }

    
  });
  set_active_tab_action_buttons(); 
});



function set_active_tab_action_buttons(tab_id = "")
{
    $('.tab_actions').hide();
    
    if(tab_id.length <= 0)
    {   
        tab_id = $('a[data-toggle="tab"].active').attr('id'); 
    }   

    if(tab_id == 'contact-tab')
    {
        $('.contact_tab_actions').show();
    }

    if(tab_id == 'general-tab')
    {
        $('.general_tab_actions').show();
    }

    if(tab_id == 'payment-tab')
    {
        $('.payment_tab_actions').show();
    }

    if(tab_id == 'terms-tab')
    {
        $('.terms_condition_tab_actions').show();
    }

    let form_id = $('#'+tab_id).attr('href');
    
    form_id = form_id.replace('#', 'form-');
    
    $('#form_submit').attr('form', form_id);
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
    
    if(typeof on_load_form_data[form_id] != 'undefined')
    {    
        if(on_load_form_data[form_id].length > 0)
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

setPrimaryContact = function (me)
{
    if($(me).val().length > 0)
    {
        var supplier_id = $(me).parents('form').find('input[name="id"]').val();
        
        $.ajax({
            url: BASE_URL+'api-supplier-set-default-contact',
            type: "POST",
            datatype:'JSON',
            data:{
                    'contact_id':$(me).val(),
                    'supplier_id':supplier_id,
                },
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
                    }
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
            }
        });
    }    
}