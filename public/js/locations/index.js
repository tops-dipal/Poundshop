

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
var editor;
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
        c._listingView();
    };
    
    c._listingView = function(){
        var field_coloumns = [
            {"orderable": false, "searchable": false},
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
            {"orderable": false, "searchable": false},
        ];
        var order_coloumns = [[5, "asc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'locations_table','api-locations',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'location');    
    };     

    $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });

    $(document).on('change', '.location_type', function (event) 
    {
        event.preventDefault();
        var $currentObj = $(this);
        var location_type = $(this).val(); 
        var record_id =$(this).attr('data-id');
        update_field(record_id,location_type,'');        
    });

    $(document).on('change', '.case_pack', function (event) 
    {
        event.preventDefault();
        var $currentObj = $(this);
        var case_pack = $(this).val(); 
        var record_id =$(this).attr('data-id'); 
        update_field(record_id,'',case_pack);        
    });   

    $(document).on('click', '.btn-delete', function (event) 
    {
        event.preventDefault();
        var $currentObj = $(this);
        var id = $(this).attr("id");            
        bootbox.confirm({ 
            title: "Confirm",
            message: "Are you sure you want to delete record? This process cannot be undone.",
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
                        url: BASE_URL + 'api-locations-remove/'+id,
                        type: "post",
                        processData: false,
                        data:{id:id},
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

    $(document).on('click', '.delete-many', function (event) 
    {
        var allVals = [];  
        $("input[name='ids[]']:checked").each(function() {  
            allVals.push($(this).attr('value'));
        });  
        
        if (typeof allVals !== 'undefined' && allVals.length > 0) 
        {
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
                callback: function (result) 
                {
                    if(result==true)
                    {
                        var join_selected_values = allVals.join(","); 
                        $.ajax({
                            url: BASE_URL + 'api-locations-remove-multiple',
                            type: "post",
                            processData: false,
                            data: 'ids='+join_selected_values,
                            headers: 
                            {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                            beforeSend: function () {
                                $("#page-loader").show();
                            },
                            success: function (response) {
                                $("#page-loader").hide();
                                if (response.status == 1) 
                                {
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    PoundShopApp.commonClass.table.draw();
                                }
                                
                                //unchecked checkbox
                                // var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);
                                // chkbox_select_all.checked = false;

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
                message: "Please select atleast one record to delete.",
                size: 'small'
            });
            return false;
        }       
    });

    $(document).on('click', '.active-many', function (event) 
    {
        var allVals = [];  
        $("input[name='ids[]']:checked").each(function() {  
            allVals.push($(this).attr('value'));
        });  
        
        if (typeof allVals !== 'undefined' && allVals.length > 0) 
        {
            bootbox.confirm({ 
                title: "Confirm",
                message: "Are you sure, you want to active selected record's?",
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'Active',
                        className: 'btn-blue'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {            
                        var join_selected_values = allVals.join(","); 
                        $.ajax({
                            url: BASE_URL + 'api-locations-active-multiple',
                            type: "post",
                            processData: false,
                            data: 'ids='+join_selected_values,
                            headers: 
                            {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                            beforeSend: function () 
                            {
                                $("#page-loader").show();
                            },
                            success: function (response) 
                            {
                                $("#page-loader").hide();
                                if (response.status == 1) 
                                {
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    PoundShopApp.commonClass.table.draw();
                                }
                                
                                //unchecked checkbox
                                // var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);
                                // chkbox_select_all.checked = false;

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
                message: "Please select atleast one record to active.",
                size: 'small'
            });
            return false;
        }       
    });

    $(document).on('click', '.incative-many', function (event) 
    {
        var allVals = [];  
        $("input[name='ids[]']:checked").each(function() {  
            allVals.push($(this).attr('value'));
        });  
        
        if (typeof allVals !== 'undefined' && allVals.length > 0) 
        {            
            bootbox.confirm({ 
                title: "Confirm",
                message: "Are you sure, you want to inactive selected record's?",
                buttons: {
                    cancel: {
                        label: 'Cancel',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'Inactive',
                        className: 'btn-red'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {            
                        var join_selected_values = allVals.join(","); 
                        $.ajax({
                            url: BASE_URL + 'api-locations-inactive-multiple',
                            type: "post",
                            processData: false,
                            data: 'ids='+join_selected_values,
                            headers: 
                            {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                            beforeSend: function () 
                            {
                                $("#page-loader").show();
                            },
                            success: function (response) 
                            {
                                $("#page-loader").hide();
                                if (response.status == 1) 
                                {
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    PoundShopApp.commonClass.table.draw();
                                }
                                
                                //unchecked checkbox
                                // var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);
                                // chkbox_select_all.checked = false;

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
                message: "Please select atleast one record to inactive.",
                size: 'small'
            });
            return false;
        }       
    });

    //Search outside the datatables
    $('#search_data').keyup(function()
    {
        PoundShopApp.commonClass.table.search($(this).val()).draw() ;
    }) ;

    //Search outside the datatables
    $('.cancle_fil').click(function()
    {
        $('.filter_count').html('');
        document.getElementById("fil_aisle").value = "";
        document.getElementById("fil_rack").value = "";
        document.getElementById("fil_floor").value = "";
        document.getElementById("fil_box").value = "";
        document.getElementById("fil_location").value = "";
        document.getElementById("fil_site_id").value = "";
        document.getElementById("fil_location_type").value = "";
        document.getElementById("fil_status").value = "";
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open'); 
        $('#search_data').val('');
        PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;         
    }) ;
    
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopLocations = new poundShopLocations();

})(jQuery);

function update_field(record_id,location_type,case_pack)
{
    $.ajax({
        url: BASE_URL + 'api-locations-inline-update',
        type: "post",
        processData: true,
        data: {'record_id':record_id,'location_type':location_type,'case_pack':case_pack},
        headers: 
        {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        beforeSend: function () 
        {
            $("#page-loader").show();
        },
        success: function (response) 
        {
            $("#page-loader").hide();
            if (response.status == 1) 
            {
                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                PoundShopApp.commonClass.table.draw();
            }
        },
        error: function (xhr, err) 
        {
           $("#page-loader").hide();
           PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
        }
    });
}

//for checkbox all and none case
function updateDataTableSelectAllCtrl(table)
{
    var $table             = table.table().node();
    var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
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
        //console.log(chkbox_select_all);
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
    $('#locations_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
});

//clear filter
$('.refresh').on('click',function()
{    
    $('#search_data').val('');
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw();  
});

function advanceSearch()
{   
    var fil_aisle=$('#fil_aisle').val();
    var fil_rack=$('#fil_rack').val();
    var fil_floor=$('#fil_floor').val();
    var fil_box=$('#fil_box').val();
    var fil_location=$('#fil_location').val();
    var fil_site_id=$('#fil_site_id').val();
    var fil_location_type=$('#fil_location_type').val();
    var fil_status=$('#fil_status').val();

    if(fil_aisle=='' && fil_rack=='' && fil_floor=='' && fil_box=='' && fil_location=='' && fil_site_id=='' && fil_location_type=='' && fil_status=='')
    {
        bootbox.alert({
                title: "Alert",
                message: "Please select atleast one filter to search.",
                size: 'small'
        });
        return false;
    }
    else
    {
        var counter=0;
        if(fil_aisle!='')
        {
            counter++;
        }

        if(fil_rack!='')
        {
            counter++;
        }

        if(fil_floor!='')
        {
            counter++;
        }

        if(fil_box!='')
        {
            counter++;
        }

        if(fil_location!='')
        {
            counter++;
        }

        if(fil_site_id!='')
        {
            counter++;
        }

        if(fil_location_type!='')
        {
            counter++;
        }

        if(fil_status!='')
        {
            counter++;
        }

        $('.filter_count').html(' ('+counter+')');
        PoundShopApp.commonClass.table.draw() ;      
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open');     
    }
}

function edit_location(record_id)
{
    //get values
    var location_type_val=$('#hid_location_type_'+record_id).val();
    var case_pack_val=$('#hid_case_pack_'+record_id).val();
    var length_val=$('#hid_length_'+record_id).val();
    var width_val=$('#hid_width_'+record_id).val();
    var height_val=$('#hid_height_'+record_id).val();
    var cbm_val=$('#hid_cbm_'+record_id).val();
    var sto_weight_val=$('#hid_stor_weight_'+record_id).val(); 
    //put values in the fields
    $('#edit_record_id').val(record_id);
    $('#edi_location_type').val(location_type_val);
    $('#edi_case_pack').val(case_pack_val);
    $('#edi_length').val(length_val);
    $('#edi_width').val(width_val);
    $('#edi_height').val(height_val);
    $('#edi_cbm').val(cbm_val);
    $('#edi_stor_weight').val(sto_weight_val);
    $('#locationModal').modal('show');
}

function saveLocation()
{
    $.ajax({
        url: BASE_URL + 'api-locations-row-update',
        type: "post",
        processData: true,
        data: $('#locationsEditForm').serialize(),
        headers: 
        {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        beforeSend: function () 
        {
            $("#page-loader").show();
        },
        success: function (response) 
        {
            $("#page-loader").hide();
            if (response.status == 1) 
            {
                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                PoundShopApp.commonClass.table.draw();
            }
            
            PoundShopApp.commonClass.table.draw();  
            $('#locationModal').modal('hide');
        },
        error: function (xhr, err) 
        {
           $("#page-loader").hide();
           PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
        }
    });    
}

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
    }
    
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
        return false;
    }

    if(count>=1 && event.keyCode == 46)
    {
      event.keyCode=0;      
      return false;
    }

    if(count==1)
    {
      var lastdigits=amount.substring(amount.indexOf(".")+1,amount.length);
      if(lastdigits.length>=2)
      {
          event.keyCode=0;
          return false;
      }
    }
    return true;
  }
  else
  {
    event.keyCode=0;    
    return false;
  }
}


$("#edi_length,#edi_width,#edi_height").keyup(function()
  {                            
    var multiple_volume = 0;
    var length=0;
    var width=0
    var heigth=0;
      
    if($("#edi_length").val() !== undefined && $("#edi_length").val() !==""){
        length= parseFloat($("#edi_length").val());
    }
    if($("#edi_width").val() !== undefined && $("#edi_width").val() !==""){
        width= parseFloat($("#edi_width").val());
    }
    if($("#edi_height").val() !== undefined && $("#edi_height").val() !=="" ){
        heigth= parseFloat($("#edi_height").val());
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
    $("#edi_cbm").val(max_volume.toFixed(2));
  });

$("#edi_length,#edi_width,#edi_height,#box").keydown(function(e)
{
    var key = e.charCode || e.keyCode || 0;        
    return (key == 8 || key == 9 || key == 13 || key == 46 || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
});

function copy_location(record_id)
{
    var copy_order_column=$('#copy_order_column').val();
    var copy_order_dir=$('#copy_order_dir').val();
    var copy_search=$('#copy_search').val();
    var copy_advance_search=$('#copy_advance_search').val();

    //get data to be update
    var location_type_val=$('#hid_location_type_'+record_id).val();
    var case_pack_val=$('#hid_case_pack_'+record_id).val();
    var length_val=$('#hid_length_'+record_id).val();
    var width_val=$('#hid_width_'+record_id).val();
    var height_val=$('#hid_height_'+record_id).val();
    var cbm_val=$('#hid_cbm_'+record_id).val();
    var sto_weight_val=$('#hid_stor_weight_'+record_id).val(); 

    bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to copy record? This process cannot be undone.",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Copy',
                className: 'btn-blue'
            }
        },
        callback: function (result) 
        {
            if(result==true)
            { 
                $.ajax({
                    url: BASE_URL + 'api-locations-row-copy',
                    type: "post",
                    processData: true,
                    data: {'record_id':record_id,'copy_order_column':copy_order_column,'copy_order_dir':copy_order_dir,'copy_search':copy_search,'copy_advance_search':copy_advance_search,'location_type_val':location_type_val,'case_pack_val':case_pack_val,'length_val':length_val,'width_val':width_val,'height_val':height_val,'cbm_val':cbm_val,'sto_weight_val':sto_weight_val},
                    headers: 
                    {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () 
                    {
                        $("#page-loader").show();
                    },
                    success: function (response) 
                    {
                        $("#page-loader").hide();
                        if (response.status == 1) 
                        {
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            PoundShopApp.commonClass.table.draw();
                        }
                    },
                    error: function (xhr, err) 
                    {
                       $("#page-loader").hide();
                       PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });   
            }
        }
    });  
}