/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'booking_table';
    var productInfoTable;
    var poundShopCartons = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();


            jQuery.validator.addMethod('le', function(value, element, param) {
                  return this.optional(element) || parseInt(value) <= $(param).val();
            });
            jQuery.validator.addMethod('ge', function(value, element, param) {
                  return this.optional(element) || parseInt(value) >= $(param).val();
            });
            jQuery.validator.addMethod("notOnlyZero", function (value, element, param) {
                return this.optional(element) || parseInt(value) > 0;
            });
            var counterForPlus=0;
            $('body').data('plus_counter',counterForPlus);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    };
    var c = poundShopCartons.prototype;
    
    c._initialize = function ()
    {
        c._listingView();
        productInfoTable.draw();
         
    };
    
    c._listingView = function(){
        var field_coloumns = [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            {"orderable": false, "searchable": false},
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'replen_request_table','api-replen-request',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'replen-request');    
    };

    productInfoTable= $('#productInfoTable').DataTable({
      bFilter: false, 
      bInfo: false,
      processing:true,
    
      columns :[
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
      ],
      bPaginate: false,
      fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
        },
        fnDrawCallback: function (oSettings, json) {
            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
        },
  });
    
     $(".select2-tag").select2({
            tags: true,
            dropdownParent: $('#select_2_dropdown')
            // tokenSeparators: [',', ' ']
        })
    $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });
     $(".cancle_fil").click(function()
    {
        $('#replenRequestFilterForm').trigger("reset");
         $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open'); 
        $('.filter_count').html('');
        PoundShopApp.commonClass.table.draw() ;
       
       
    })
    //Search outside the datatables
    $('#search_data').keyup(function(event)
    {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                PoundShopApp.commonClass.table.search($(this).val()).draw() ;
            }
            if (keycode=='8')
            {
                //$('#search_data').val('');
                PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
            }
    });

    bindProductInfo=function(productInfo,stockTotal){
        var priorityArr={'2':'Emergency','4':'Priority-1','6':'Priority-2','8':'Priority-3','10':'Priority-4','12':'Priority-5'};
        varDataHtml = "";
         priorityOption='';
          if(productInfo.length!=0)  
        {
             for (var key in priorityArr) {
                 var selected='';
                if(productInfo.priority==key)
                {
                     selected="selected='selected'";
                }
                priorityOption+='<option value="'+key+'" '+selected+'>'+priorityArr[key]+'</option>';
            }
            infoStr=`<div class="d-flex"> 
    <div><a href="`+productInfo.main_image_internal+`" data-rel="lightcase"><img src="`+productInfo.main_image_internal+`"  width="75" height="75" /></a></div><div class="pl-2"><p class="mb-2 mt-4">SKU: `+productInfo.sku+`</p><p>Barcode: `+productInfo.product_identifier+`</p></div></div>`;
            varDataHtml+='<tr>';
            varDataHtml+='<td><p class="p-name">'+productInfo.title+'</p>'+infoStr+'</td>';
            varDataHtml+='<td>'+productInfo.cron_replan_qty+'</td>';
            varDataHtml+='<td>'+priorityArr[productInfo.cron_replan_priority]+'</td>';
            varDataHtml+='<td><div class="err-absolute"><input type="hidden" name="stockTotal" id="stockTotal" value="'+stockTotal+'"><input type="hidden" id="cron_replan_qty" value="'+productInfo.cron_replan_qty+'"><input type="hidden" id="override_qty_old" name="prev_qty" value="'+productInfo.replan_qty+'"><input  class="form-control override_qty" name="override_qty" id="override_qty_'+productInfo.id+'" type="text" value="'+productInfo.replan_qty+'"></div></td>';
            
            varDataHtml+='<td><div class="err-absolute"><input type="hidden" id="cron_replan_priority" name="cron_replan_priority" value="'+productInfo. cron_replan_priority+'"><input type="hidden" id="override_priority_old" name="prev_priority" value="'+productInfo.priority+'"><select class="form-control override_priority" name="override_priority" id="override_priority_'+productInfo.id+'" >'+priorityOption+'</select></div></td>';
            varDataHtml+='</tr>';
        
            return varDataHtml;
        }
        else
        {
            varDataHtml+='<td colspan="5" class="dataTables_empty py-4" valign="top">No Records Found</td>';
            return varDataHtml;
        }
    }

    //Edit Override Pop up
    showOverrideModel = function(object, stockTotal){
       var  productInfo=jQuery.parseJSON(object);
       $('.product_id').val(productInfo.id);
       $('.replen_id').val(productInfo.replen_id);
        $("#productInfoTable >tbody").html(bindProductInfo(productInfo,stockTotal));
         $('.submit').attr('disabled', true);
         $('#search_edit_override').val(productInfo.title);
        $('#overrideModel').modal('show');
    }

    searchProductFromModel = function(){
      var searchText=$('#search_edit_override').val();
      if(searchText!='')
      {
        $('#err_search_edit_override').remove();
         $.ajax({
                type: "GET",
                url: BASE_URL+'api-product-replen-info?search='+searchText,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                   // console.log(response);return false;
                    //$('.submit').attr('disabled', false);
                    $("#page-loader").hide();
                   
                    if (response.status == 1) {
                       
                            $("#productInfoTable >tbody").html(bindProductInfo(response.data));
                        
                       
                    }
                },
                error: function (xhr, err) {
                    $('.submit_create_po').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
      }
      else
      {
        var errTxt="<span id='err_search_edit_override' class='invalid-feedback' style='display:inline'>Please enter search</span>";
        $(errTxt).insertAfter('#search_edit_override');
      }
    }
    $(document.body).on('keyup', ".override_qty", function(){

    //$('.override_qty').on('input change', function () {
        if ($(this).val() != ''|| $(this).val()!=$('#override_qty_old').val()) {
             $('.submit').attr('disabled', false);
        }
        else {
           // console.log("Hii");
              $('.submit').attr('disabled', true);
        }
    });
    $(document.body).on('change', ".override_priority", function(){
    // $('.override_priority').on('input change', function () {
            if ($(this).val() != '' || $(this).val()!=$('#override_priority_old').val()) {
                 $('.submit').attr('disabled', false);
            }
            else {
                console.log("Hello");
                  $('.submit').attr('disabled', true);
            }
        });

     $('#editOverideForm').validate({
        focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function(form, validator) {
            console.log(validator);
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
            "override_qty": {
                required: true,
                ge: '#cron_replan_qty',
                le:'#stockTotal',
            },
            "override_priority": {
                required: true,
                le: '#cron_replan_priority'
            },
           
        },
         messages: {
            override_qty: {ge: 'Must be greater than or equal to in-progress replen quanity',le: 'Must be less than or equal to quanity in warehouse'},
            override_priority: {le: 'Must be greater than or equal to in-progress replen priority'},
           
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
            $('.submit').attr('disabled', true);
             $.ajax({
                    type: "POST",
                    url: BASE_URL+'api-edit-override',
                    data: $('#editOverideForm').serialize(),
                   // processData: false,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                       // console.log(response);return false;
                        $('.submit').attr('disabled', false);
                        $("#page-loader").hide();
                        //console.log(response);return false;
                        if (response.status == 1) {
                            $('#overrideModel').modal('hide');
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            PoundShopApp.commonClass.table.draw() ;
                        }
                    },
                    error: function (xhr, err) {
                        $('.submit_create_po').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });
        }
    });

     callCron=function(){
         $('.update_live_data').attr('disabled', true);
          $.ajax({
            type: "GET",
            url: BASE_URL+'api-call-cron-start',
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
               // console.log(response);return false;
                $('.update_live_data').attr('disabled', false);
                $("#page-loader").hide();
                //console.log(response);return false;
                if (response.status == 1) {
                    
                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    
                }
            },
            error: function (xhr, err) {
                $('.update_live_data').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
     }

     updateCronData=function(){
        $.ajax({
            type: "GET",
            url: WEB_BASE_URL+'/replen-request',
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
               // $("#page-loader").show();
            },
            success: function (response) {
               // console.log(response);return false;
               console.log(response);
               $('.update_live_data').attr('disabled',response.btnDisabledStatus);
               $('#livedata_last_update').html(response.date);
            },
            error: function (xhr, err) {
                $('.update_live_data').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
     }
    setInterval(function(){ 
       updateCronData();
    }, 15 * 60 * 1000);
    
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);


function advanceSearch()
{   
    var show_booked_in_products=0;
    var product_location_not_assign=0;
    var product_location_assign=0;
    var new_products=0;
    var is_status_checked=0;
    var completed=0;
    var not_completed=0;
    var red_days_stock_holding=0;
    var box_turn_undefined=0;
    var box_turn_filter=0;
    var assigned_aisle_filter=0;
    var product_with_day_stock_filter=0;
    var tag_filter=0;
   if($('#custom_advance_search_fields').length > 0)
    {
         $('#custom_advance_search_fields input,select,textarea').each(function()
        {
            if(typeof $(this).val() != undefined && $(this).val() != null && !$(this).hasClass('clear_except'))
            {
                if($(this).val().length > 0)
                {    
                    if($(this).attr('name') == 'filter_custom_tags[]')
                    {
                        if($('.select2-tag').val() != "" && $('.select2-tag').val() != null)
                        {    
                            tag_filter++;
                            is_status_checked++
                        }
                    }    

                    if($(this).attr('type') == 'text' || $(this).attr('type') == 'textarea')
                    {
                        tag_filter++;    
                    }   
                    
                    if($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio')
                    {
                        if($(this).prop('checked') == true)
                        {
                            tag_filter++;    
                        }    
                    }
                }    
            }    
        });
    }
    if ($('#warehouse_id').val()!='') 
    {
       
        is_status_checked++;

        
    }
    if ($('#pick_aisle').val()!='') 
    {
       
        is_status_checked++;

        
    }
     if ($('#bulk_aisle').val()!='') 
    {
       
        is_status_checked++;
    }

     if ($('#status').val()!='') 
    {
       
        is_status_checked++;
    }

     if ($('#priority').val()!='') 
    {
       
        is_status_checked++;
    }
     if ($('#priority').val()!='') 
    {
       
        is_status_checked++;
    }

      if (document.getElementById('product_with_day_stock_filter').checked) 
    {
       
       if($('#days').val()=='')
       {
              bootbox.alert({
                    title: "Alert",
                    message: "Please enter days for Products With Days Stock Holding .",
                    size: 'small'
            });
            return false;
       }
        else
        {
            is_status_checked++;
            product_with_day_stock_filter=1;
        }
    }
   /*  if (document.getElementById('new_products').checked) 
    {
       
        is_status_checked++;
        new_products=1;
    }

      if (document.getElementById('red_days_stock_holding').checked) 
    {
       
        is_status_checked++;
        red_days_stock_holding=1;
    }

     if (document.getElementById('box_turn_undefined').checked) 
    {
       
        is_status_checked++;
        box_turn_undefined=1;
    }

     if (document.getElementById('box_turn_filter').checked) 
    {
       
        is_status_checked++;
        box_turn_filter=1;
        if($('#box_turn_from').val()=='' && $('#box_turn_to').val()=='')
        {
            alert('please enter from and to val');
            return false;
        }
    }

     if (document.getElementById('assigned_aisle_filter').checked) 
    {
       
        is_status_checked++;
        assigned_aisle_filter=1;
        if($('#assigned_aisle').val()=='')
        {
            alert('please enter aisle');
            return false;
        }
    }*/
   
    if(($('#warehouse_id').val()=='' && product_with_day_stock_filter==0 &&  $('#priority').val()=='' &&  $('#pick_aisle').val()=='' && $('#bulk_aisle').val()=='' && $('#status').val()=='' )&& is_status_checked==0)
    {
        bootbox.alert({
                title: "Alert",
                message: "Please select atleast one filter to search.",
                size: 'small'
        });
        
        $('.filter_count').html('');
        return false;
    }
    else
    {
        var counter=0;
        if($('#warehouse_id').val()!='' || product_with_day_stock_filter!=0 || $('#days').val()!='' || $('#priority').val()!='' ||  $('#pick_aisle').val()!='' || $('#bulk_aisle').val()!='' || $('#status').val()!='')
        {

            counter++;
        }
        $('.filter_count').html(' ('+is_status_checked+')');
        PoundShopApp.commonClass.table.draw() ;      
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open');     
    }
}
;



//for checkbox all and none case
function updateDataTableSelectAllCtrl(table)
{
    var $table             = table.table().node();
    var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
    var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);   

    // If none of the checkboxes are checked
    if($chkbox_checked.length === 0)
    {
        chkbox_select_all.checked = false;
        if('indeterminate' in chkbox_select_all){
            chkbox_select_all.indeterminate = false;
        }

        // If all of the checkboxes are checked
    } 
    else if ($chkbox_checked.length === $chkbox_all.length)
    {
        chkbox_select_all.checked = true;
        if('indeterminate' in chkbox_select_all)
        {
            chkbox_select_all.indeterminate = false;
        }
        // If some of the checkboxes are checked
    } 
    else 
    {
        chkbox_select_all.checked = true;
        if('indeterminate' in chkbox_select_all)
        {
            chkbox_select_all.indeterminate = true;
        }
    }
}

$(document).ready(function ()
{   
    var rows_selected = [];   
    var table = PoundShopApp.commonClass.table; 
    $('#carton_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
});

//clear filter
$('.refresh').on('click',function()
{    
    $('#search_data').val('');
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
});
$(document).on("keypress",".qty_fit_in_location",function(e){
    return isNumber(event, this)
});

function isNumber(evt, element) {

var charCode = (evt.which) ? evt.which : event.keyCode

if ((charCode < 48 || charCode > 57))
    return false;

return true;
}    


$(function() {
 // var regExp = /[a-z]/i;
  var regExp = /^[a-zA-Z!@#$%\^&*)(+=._-]*$/;
  $(document.body).on('keydown keyup', ".override_qty", function(e){
  
    var value = String.fromCharCode(e.which) || e.key;

    // No letters
    if (regExp.test(value)) {
      e.preventDefault();
      return false;
    }
    else
    {
      return   blockSpecialChar(e);
    }
  });

  function blockSpecialChar(e){
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
        }
   
});