/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'booking_table';

     var dayStockTable;

     var assignLocaionTable;
    
    var poundShopCartons = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
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
        dayStockTable.draw();
        assignLocaionTable.draw();

        $( document ).on( "click", ".assign_location_pro", function() {   
            //display product tags
            var tags=$(this).attr('attr-tags');
            if(tags=='-')
            {
                $('.product-tags').hide();
            }
            else
            {
                $('.product-tags').show();
                var tagsArr=tags.split(', ');
                var tagStr='';

                $.each(tagsArr, function( index, value ) {
                  tagStr+=' <span class="badge badge-primary p-2">'+value+'</span>';
                });  
                $('.tags').html(tagStr); 
            }


            //model title-> product title
            var product_title=$(this).attr('attr-title');
            $('.product-name').html(product_title);

            //set on click event for plus sign
            $('#assign_aisle_btn').attr('onclick','showEmptyLocations('+$(this).attr('attr-product-id')+')')
            
            //set product id to hidden field
            $('#product_id').val($(this).attr('attr-product-id')); 
            
            
            //show existing data in table
              fetchassignedLocationData();



            $('#modal_assigned_location').toggleClass('open');
        })  
        $( document ).on( "click", "#close_assign_location", function() {  
            $('#modal_assigned_location').removeClass('open');
        })  
             
    };
    
    c._listingView = function(){
        var field_coloumns = [
          //  {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            null,
            null,
            null,
            null,
            null,
            null,
             {"orderable": false, "searchable": false},
            /*null,
            null,
           {"orderable": false, "searchable": false},
             {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
             {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},*/
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'location_assign_table','api-location-assignment',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'location-assignment');    
    };

     dayStockTable= $('#dayStockTable').DataTable({
      bFilter: false, 
      bInfo: false,
      processing:true,
    
      columns :[
            {"orderable": false, "searchable": false},
            null,
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
    fetchassignedLocationData=function(){
         if ($.fn.DataTable.isDataTable( '#assigned_location_table' ) ) {
                   $('#assigned_location_table').dataTable().fnDestroy();
                   $('#assigned_location_table > tbody').html('');
                }
        var field_coloumns3 = [
                null,
                null,
                null,
                {"orderable": false, "searchable": false},
               
            ];
            var order_coloumns3 = [[0, "asc"]];
            PoundShopApp.commonClass.table3 = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table3,'assigned_location_table','api-assigned-location',field_coloumns3,order_coloumns3,undefined,undefined,'Search',[],'assigned-locations');    
     }
    assignLocaionTable= $('#assigned_location_table').DataTable({
      bFilter: false, 
      bInfo: false,
      processing:true,
    
      columns :[
            null,
            null,
            null,
            {"orderable": false, "searchable": false},
            
      ],
      bPaginate: false,
      fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
         
        },
        fnDrawCallback: function (oSettings, json) {
         
        },
  });
 
    $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });
     $(".cancle_fil").click(function()
    {
        $('#locationAssignFilterForm').trigger("reset");
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

     $('#search_data_modal').keyup(function(event)
    {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                PoundShopApp.commonClass.table1.search($(this).val()).draw() ;
            }
            if (keycode=='8')
            {
                //$('#search_data').val('');
                PoundShopApp.commonClass.table1.search($('#search_data_modal').val()).draw() ;  
            }
    });
    bindLocationsData = function(data,locationTypes){
        varDataHtml = "";
        //console.log(document.cookie.indexOf('selectedPO='));
        
        for(var i = 0; i< data.length; i++){
            
            varDataHtml+='<tr>';
            varDataHtml+='<td><label class="fancy-checkbox"><input type="checkbox" name="location_id[]" value="'+data[i].id+'"><span><i></i></span></label></td>';
            varDataHtml+='<td>'+data[i].aisle+'</td>';
            varDataHtml+='<td>'+data[i].location+'</td>';
            varDataHtml+='<td>'+locationTypes[data[i].type_of_location]+'</td>';
            varDataHtml+='<td>'+data[i].length+'</td>';
            varDataHtml+='<td>'+data[i].width+'</td>';
            varDataHtml+='<td>'+data[i].height+'</td>';
            varDataHtml+='<td>'+data[i].cbm+'</td>';
            varDataHtml+='<td>0</td>';
            varDataHtml+='</tr>';
        }
        return varDataHtml;
    }

    bindAisleData = function (data)
    {
        varAsileHTML='<option value="" ></option>';
        for(var i = 0; i< data.length; i++){
            var selected="";
            if(i==0)
            {
                selected="selected";
            }
            varAsileHTML+="<option value="+data[i]+" "+selected+">"+data[i]+"</option>";
        }
         return varAsileHTML;
    }

    

   

    //add location in location_assign table
   $('#assignLocationForm').validate({
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
           var allVals = [];  
                $("input[name='location_id[]']:checked").each(function() {  
                    allVals.push($(this).attr('value'));
                });

            if(allVals.length==0){
                bootbox.alert({
                    title: "Alert",
                    message: POUNDSHOP_MESSAGES.buy_by_product.alert_msg.alert_select_atleast_one,
                    size: 'small'
                });
                return false
            }
            var dataString = $("#assignLocationForm").serialize();
            $.ajax({
                type: "POST",
                url: $('#assignLocationForm').attr('action'),
                processData: false,
                data: dataString+'&product_id='+$('body').data('product_id'),
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $('.add_to_existing_po_btn').attr('disabled', false);
                    $("#page-loader").hide();
                    
                        $('#assignLocationModel').modal('hide');
                        //PoundShopApp.commonClass.table.draw();
                        fetchassignedLocationData();
                        //PoundShopApp.commonClass.table2.draw() ;
                       
                    
                     PoundShopApp.commonClass._displaySuccessMessage(response.message);
                },
                error: function (xhr, err) {
                    $('.add_to_existing_po_btn').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }
    });

   deleteLocationAssign=function(locationAssignId) {
        bootbox.confirm({ 
            title: "Confirm",
            message: "Are you sure you want to delete records? This process cannot be undone.",
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
                        url: BASE_URL + 'api-location-assignment/'+locationAssignId,
                        type: "delete",
                        //processData: false,
                        data:{id:locationAssignId},
                        headers: {
                           Authorization: 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                                $("#page-loader").hide();
                                if (response.status == 1) {
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    //PoundShopApp.commonClass.table.draw();
                                    
                                    PoundShopApp.commonClass.table3.draw();
                                }
                        },
                        error: function (xhr, err) {
                           $("#page-loader").hide();
                           PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                        }

                    });
                }
            }

        });
   }

    $(document).on('click', '.saveBulkQtyFitInLocation', function(){

        $("#assignedLocationForm").validate({
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
            "qty_fit_in_location[]": {
                required: true,
            },
        },
        errorPlacement: function (error, element) {
            
           error.insertAfter(element);
        },
        highlight: function (element) { 
            
        // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function (label) {
            
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        submitHandler: function (form) {
            
            var actionUrl=BASE_URL+'api-update-storage-capacity';
            var data=$('#assignedLocationForm').serialize();
            $.ajax({
                type: "POST",
                url:actionUrl,
                data:data,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                  
                    $("#page-loader").hide();
                    //PoundShopApp.commonClass.table.draw();
                       
                    
                     PoundShopApp.commonClass._displaySuccessMessage(response.message);
                },
                error: function (xhr, err) {
                    $('.add_to_existing_po_btn').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }
    });
    });

    //show Empty location for assign location to product
    showEmptyLocations = function(product_id,callfrom='normal') {
        
        var plusCounter=$('body').data('plus_counter');
        $('.location_for_product_id').val(product_id);
        $('#assignLocationForm').trigger("reset");
      //  PoundShopApp.commonClass.table1.destroy();
         if ($.fn.DataTable.isDataTable( '#emptyLocationsTable' ) ) {
           //  $('#emptyLocationsTable').dataTable().fnClearTable();
           $('#emptyLocationsTable').dataTable().fnDestroy();
           $('#emptyLocationsTable > tbody').html('');
        }
        
            $('#assignLocationModel').modal('show'); 
            var field_coloumns1 = [
                {"orderable": false, "searchable": false},
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
               
            ];
            var order_coloumns1 = [[0, "desc"]];
            PoundShopApp.commonClass.table1 = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table1,'emptyLocationsTable','api-empty-locations',field_coloumns1,order_coloumns1,undefined,undefined,'Search',[],'aisle-empty-loactions');    
    }

    advanceSearchAisle = function()
    {
       if(!$('#filter_aisle').val().trim())
        {
            var errorHtml="<span id='filter_aisle-error' class='invalid-feedback' style='display:block'>Please select aisle.</span>"
            $(errorHtml).insertAfter('#filter_aisle');
            return false;
        }
        else
        {
            var errorHtml="<span id='filter_aisle-error' class='invalid-feedback' style='display:block'>Please select aisle.</span>"
            $('#filter_aisle-error').remove();
        }
       PoundShopApp.commonClass.table1.draw() ;     
    }

    cancelAisleFilter= function(){
        $('#assignLocationForm').trigger("reset");
       PoundShopApp.commonClass.table1.draw() ;
    }

    showDayStockModal=function(product_id,ros,day_stock_hold,qty_stock_hold){
        $('.ros').val(ros);
        $('.product_title').html($('#product_title_'+product_id).html());
        $('.product_id_for_stock').val(product_id);
        $('.day_stock_hold').val(day_stock_hold);
        $('.qty_stock_hold').val(qty_stock_hold);
        
        if ($.fn.DataTable.isDataTable( '#dayStockTable' ) ) {
           //  $('#emptyLocationsTable').dataTable().fnClearTable();
           $('#dayStockTable').dataTable().fnDestroy();
           $('#dayStockTable > tbody').html('');
        }
        var field_coloumns2 = [
            {"orderable": false, "searchable": false},
            null,
            null,
            null,
            null,
        ];
        var order_coloumns2 = [[0, "desc"]];
        PoundShopApp.commonClass.table2 = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table2,'dayStockTable','api-inner-outer-barcode-bulk-locations',field_coloumns2,order_coloumns2,undefined,undefined,'Search',[],'day-stock-color');    
                     
           if(!$('.stock_hold_days').val().trim()){
            $('.stock_hold_days').val(1);
           }
        $('#dayStockModel').modal('show');
    }

    $('#close_assign_location').click(function(){
        PoundShopApp.commonClass.table.draw();
    })

    $(document).on('change', 'input[name="day_stock_val"]', function(){

        $('.stock_hold_days').val($(this).val());
    });


    bindDataTodayStockTable=function(data){
        var htmlTable='';
        for(i=0;i<data.length;i++)
        {
            htmlTable+='<tr>';
             htmlTable+='<td> <label class="fancy-radio mr-3"><input type="radio" name="id" value="'+i+'" checked=""/><span><i></i></span></label></td>';
            htmlTable+='<td>'+data[i].qty_per_box+'</td>';
            htmlTable+='<td>'+data[i].total_boxes+'</td>';
            htmlTable+='<td>'+data[i].location+'</td>';
            htmlTable+='<td>'+data[i].min_day_stock_holding+'</td>';
            htmlTable+='</tr>';
        }
        return htmlTable;
    }

    //save day stock hold
    $("#dayStockForm").validate({
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
            "stock_hold_days":{
                required:true,
                number:true,
                 notOnlyZero: '0'
            }
            // "var_barcode[]":{
            //     required: true,
            // },
        },
        messages:{
            "stock_hold_days": {
                notOnlyZero: 'Zero not allowed'
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
            var dataString = new FormData($('#dayStockForm')[0]);
            $('.btn-stock-save').attr('disabled',true);
           // console.log(dataString);return false;
            $.ajax({
                type: "POST",
                url: $("#dayStockForm").attr("action"),
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
                       $('#dayStockModel').modal('hide');
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        PoundShopApp.commonClass.table.draw() ;     
                        $('.btn-stock-save').attr('disabled', false); 
                    }
                },
                error: function (xhr, err) {
                    $('.btn-form').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }
    });
    
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
    var site_filter=0;

    if (document.getElementById('show_product_booked_in').checked) 
    {
       
        is_status_checked++;
        show_booked_in_products=1;
    }
     if (document.getElementById('product_location_not_assign').checked) 
    {
       
        is_status_checked++;
        product_location_not_assign=1;
    }

     if (document.getElementById('product_location_assign').checked) 
    {
       
        is_status_checked++;
        product_location_assign=1;
    }

     if (document.getElementById('new_products').checked) 
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
       
        
        if($('#assigned_aisle').val()=='')
        {
            
            bootbox.alert({
                title: "Alert",
                message: "Please enter aisle.",
                size: 'small'
             });
            return false;
        }
        else
        {
            is_status_checked++;
            assigned_aisle_filter=1;
        }
    }
    if($('#warehose_id').val()!='')
    {
        is_status_checked++;
            site_filter=1;
    }
   
    if((assigned_aisle_filter==0 || box_turn_filter==0 || box_turn_undefined==0 || red_days_stock_holding==0 ||show_booked_in_products==0 || product_location_not_assign==0 || product_location_assign==0 || new_products==0 || site_filter==0) && is_status_checked==0)
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
        if(assigned_aisle_filter!=0 || box_turn_filter!=0 || box_turn_undefined!=0 || red_days_stock_holding!=0 || show_booked_in_products!=0 || product_location_not_assign!=0 || product_location_assign!=0 || new_products!=0 || site_filter!=0)
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

$('#refresh_modal').on('click',function()
{    
    $('#search_data').val('');
    PoundShopApp.commonClass.table1.search($('#search_data_modal').val()).draw() ;  
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


function get_variation(me,productId)
{

    var product_id = productId;
             
    if(typeof product_id != 'undefined')
    {
        if(product_id.length > 0)
        {
            if($(me).hasClass('close'))
            {    
                $(me).removeClass('close').addClass('open');

                if($('#location_assign_table').find('tr[attr-par-id="'+product_id+'"]').length == 0)
                {    
                    $(me).parents('tr').after('<tr attr-par-id = "'+product_id+'"><td colspan="100%">Loading Variations...</td></tr>');

                    $.ajax({
                        url: WEB_BASE_URL+'/product/get-variations-list/'+product_id,
                        type: "GET",
                        datatype:'HTML',
                        headers: {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                        beforeSend: function () {
                            
                        },
                        success: function (response) {
                            $('#location_assign_table').find('tr[attr-par-id="'+product_id+'"]').replaceWith(response);
                        },
                        error: function (xhr, err) {
                           
                        }
                    });
                }
                else
                {
                    $('#location_assign_table').find('tr[attr-par-id="'+product_id+'"]').show();    
                }    
            }
            else
            {
                $(me).removeClass('open').addClass('close');

                $('#location_assign_table').find('tr[attr-par-id="'+product_id+'"]').hide();
            }    
        }    
    }    
}