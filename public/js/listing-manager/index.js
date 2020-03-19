

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'listing_table';
    var activeTab=$('#active_tab').val();
    var poundShopTotes = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
            $('#search_data').val('');
        });
    };
    var c = poundShopTotes.prototype;
    
    c._initialize = function ()
    {
        c._listingView();
    };
    if(activeTab=='already-listed')
    {
        c._listingView = function(){
            var field_coloumns = [
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                null,
                null,
                null,
                null,
                null,
                {"orderable": false, "searchable": false},
            ];
            var order_coloumns = [[0, "desc"]];
            PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'listing_table','api-listing-manager-already-listed',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'listing-manager');        
        };
    }
    else if(activeTab=='to-be-listed')
    {
        c._listingView = function(){
            var field_coloumns = [
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                null,
                null,
                null,
                null,
                null,
                {"orderable": false, "searchable": false},
            ];
            var order_coloumns = [[0, "desc"]];
            PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'to_be_listed_table','api-listing-manager-to-be-listed',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'listing-manager');        
        };
    }
    else
    {
        c._listingView = function(){
            var field_coloumns = [
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                null,
                null,
                null,
                null,
                null,
                {"orderable": false, "searchable": false},
            ];
            var order_coloumns = [[0, "desc"]];
            PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'inprogress_table','api-listing-manager-inprogress',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'listing-manager');
        };
    }
    
    $('.sort_seasonal').change(function() {
        var count=0;
        if(this.checked) {
           $('#sort_by_season').val(1); 
           count++;
           PoundShopApp.commonClass.table.draw() ;  
            $('.filter_count').html(' ('+count+')');
           $('#btnFilter').removeClass('open');
            $('.search-filter-dropdown').removeClass('open'); 
            $('.card-flex-container').removeClass('filter-open');   
        }
        else
        {
            
            $('#sort_by_season').val(0); 
            $('.filter_count').html('');
            PoundShopApp.commonClass.table.draw() ;  
            $('#btnFilter').removeClass('open');
            $('.search-filter-dropdown').removeClass('open'); 
            $('.card-flex-container').removeClass('filter-open');   
        }
               
    });
    $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });
     $('.refresh').on('click',function()
    {    
        var active_tab = $("ul#myTab li a.active").attr('href');
        
             PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  

        
    });
    
    $(document).on('click', '.delist-btn', function (event) {
        event.preventDefault();
        var $currentObj = $(this);
        var id = $(this).attr("id");    
        bootbox.confirm({ 
            title: "Confirm",
            message: "Are you sure you want to delist record? This process cannot be undone.",
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
                        url: BASE_URL + 'api-listing-manager-product-delist-many',
                        type: "post",
                        //processData: false,
                        data:{'ids':id,'store_id':$('#store_id').val()},
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
                                    PoundShopApp.commonClass.table.draw();
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
    });
   

    $(document).on('click', '.delist-many', function (event) {
        var allVals = [];  
        $("input[name='ids[]']:checked").each(function() {  
            allVals.push($(this).attr('value'));
        }); 

        if (typeof allVals !== 'undefined' && allVals.length > 0) 
        {

           bootbox.confirm({ 
                title: "Confirm",
                message: "Are you sure you want to delist selected records? This process cannot be undone.",
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'Delist',
                        className: 'btn-red'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {
                        var join_selected_values = allVals.join(","); 
                        //console.log(allVals);return false;
                         $.ajax({
                            url: BASE_URL + 'api-listing-manager-product-delist-many',
                            type: "post",
                           /* processData: false,*/
                            data: {'ids':join_selected_values,'store_id':$('#store_id').val()},
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
                                        PoundShopApp.commonClass.table.draw();
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
        else
        {
             bootbox.alert({
                    title: "Alert",
                    message: "Please select atleast one record to delist.",
                    size: 'small'
                });
                return false;
        }
    });
     $(document).on('click', '.list-many', function (event) {
        var allVals = [];  
        $("input[name='ids[]']:checked").each(function() {  
            allVals.push($(this).attr('value'));
        }); 

        if (typeof allVals !== 'undefined' && allVals.length > 0) 
        {

           bootbox.confirm({ 
                title: "Confirm",
                message: "Are you sure you want to listed selected records? This process cannot be undone.",
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'List',
                        className: 'btn-red'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {
                        var join_selected_values = allVals.join(","); 
                        //console.log(allVals);return false;
                         $.ajax({
                            url: BASE_URL + 'api-listing-manager-product-list-many',
                            type: "post",
                           /* processData: false,*/
                            data: {'ids':join_selected_values,'store_id':$('#store_id').val()},
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
                                        PoundShopApp.commonClass.table.draw();
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
        else
        {
             bootbox.alert({
                    title: "Alert",
                    message: "Please select atleast one record to list.",
                    size: 'small'
                });
                return false;
        }
    });

    $(document).on('click', '.enable-magento-product', function (event) {
        var allVals = [];  
        var status=$(this).attr('title');
        var id=$(this).attr('id');
        if(status=='enabled')
        {
            var msg="Are you sure you want to disabled selected record?";
            var btnText="Disabled";
            var make_enabled_status=0;
        }
        else
        {
            var msg="Are you sure you want to enabled selected record?";
            var btnText="Enabled";
            var make_enabled_status=1;
        }
        
        bootbox.confirm({ 
                title: "Confirm",
                message: msg,
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: btnText,
                        className: 'btn-red'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {
                        
                        //console.log(allVals);return false;
                         $.ajax({
                            url: BASE_URL + 'api-magento-product-enable-disabled',
                            type: "post",
                           /* processData: false,*/
                            data: {'ids':id,'store_id':$('#store_id').val(),'make_enabled_status':make_enabled_status},
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
                                        PoundShopApp.commonClass.table.draw();
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
                    

    });
    $('#search_data').keyup(function()
    {
         var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
            PoundShopApp.commonClass.table.search($(this).val()).draw() ;
        }
        if (keycode=='8')
        {
         
            PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
        }
    }) ;  

window.PoundShopApp = window.PoundShopApp || {}
window.PoundShopApp.poundShopTotes = new poundShopTotes();

})(jQuery);
function isNumber(evt, element) {

var charCode = (evt.which) ? evt.which : event.keyCode

if (
    (charCode != 45 || $(element).val().indexOf('-') != -1) &&      // “-” CHECK MINUS, AND ONLY ONE.
    (charCode != 46 || $(element).val().indexOf('.') != -1) &&      // “.” CHECK DOT, AND ONLY ONE.
    (charCode < 48 || charCode > 57))
    return false;

return true;
}    

//for checkbox all and none case
function updateDataTableSelectAllCtrl(table)
{
    var $table             = table.table().node();
    var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
    var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);   
    //var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);
    var chkbox_select_all= $('.dataTables_scrollHead .master').get(0);
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
         if('indeterminate' in chkbox_select_all)
        {
             chkbox_select_all.checked = false;   
            chkbox_select_all.indeterminate = true;
        }
    }
}

$(document).ready(function ()
{   
    var rows_selected = [];   
    var table = PoundShopApp.commonClass.table; 
    $('#listing_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
    $('#inprogress_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
    $('#to_be_listed_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
});

function storeMagentoQtyLog(magentoId,btndivId)
{

    var qty=$('#magentoQty_'+magentoId).val();
    if(qty!='')
    {
        $('#error_magentoQty_'+magentoId).hide();
        $.ajax({
            url: BASE_URL + 'api-magento-qty-log-store',
            type: "post",
           /* processData: false,*/
            data: {'quantity':qty,'magento_id':magentoId},
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
                        $('#'+btndivId).addClass('hidden');
                        PoundShopApp.commonClass.table.draw();
                    }
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
    }
    else
    {
        $('#error_magentoQty_'+magentoId).show();
         bootbox.alert({
                title: "Alert",
                message: "Please enter quantity.",
                size: 'small'
            });
            return false;
    }
     
}
$(document).on("keydown",".qty",function(e){

    if (e.shiftKey || e.ctrlKey || e.altKey) {
    e.preventDefault();
    } else {
    var key = e.keyCode;
    if (!((key == 8) || (key == 46) || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) || (key >= 96 && key <= 105))) {
    e.preventDefault();
    }
    }
   
   
    return true
});
 $(document).on("keypress",".price",function(e){
    return isNumber(event, this)
});

function storeMagentoPriceLog(magentoId,btndivId)
{
    
    var price=$('#magentoPrice_'+magentoId).val();
    if(price!='')
    {
         $.ajax({
            url: BASE_URL + 'api-magento-price-log-store',
            type: "post",
           /* processData: false,*/
            data: {'selling_price':price,'magento_id':magentoId},
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
                        $('#'+btndivId).addClass('hidden');
                        PoundShopApp.commonClass.table.draw();
                    }
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
               PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
    }
    else
    {
         bootbox.alert({
                title: "Alert",
                message: "Please enter price.",
                size: 'small'
            });
            return false;
    }
    
}
function showSaveQtyBtn(id,btndivId)
{

    var oldval=parseFloat($('#'+id).attr('attr-val'));
    var newval=$('#'+id).val();
    if(oldval!=newval && newval!='')
    {
        $('#'+btndivId).removeClass('hidden');
    }
    else
    {
        $('#'+btndivId).addClass('hidden');
    }
}
function showSavePriceBtn(id,btndivId)
{
        
    var oldval=parseFloat($('#'+id).attr('attr-val'));
    var newval=$('#'+id).val();
    if(oldval!=newval && newval!='')
    {
        $('#'+btndivId).removeClass('hidden');
    }
    else
    {
        $('#'+btndivId).addClass('hidden');
    }
}
function expandPostingResult(id)
{
    var textData=JSON.parse($('#'+id).attr('attr-data'));
     
    var str='';
     $.each(textData, function (index, value) {
      str+="<span><strong style='font-weight: 600;'>"+ucwords(value.error_type)+'</strong> : '+value.long_msg+'</span><br>'
    });
   
     if($('#'+id).attr('title')!='Expand')
     {
        
        $('#'+id).attr('title','Expand');
        $('#addedRow_'+id).remove();
     }
     else
     {
        $('#'+id).attr('title','Contract');
        var parentTR =  $('#'+id).closest('tr');
        parentTR.after("<tr style='background: #fff3f3' id='addedRow_"+id+"'><td colspan='8' style='padding: 10px;'>"+str+"</td></tr>");
        
     }
    
}
function ucwords (str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}