/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'booking_day_table';
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
        c._listingView();

        setTimeout(function(){ 

            var default_val = document.getElementsByClassName('default_entry').length;
            var total_record = document.getElementsByClassName('live_entry').length;
            console.log(total_record);
            if(total_record!=undefined && total_record!=0)
            {                
                $('#booking_day_table_info').html('Showing 1 to '+total_record+' of '+ total_record +' entries');
            }
            

        }, 500);        
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
            null,
            null,
            {"orderable": false, "searchable": false},
        ];
        var order_coloumns = [[2, "asc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTableBookin(PoundShopApp.commonClass.table,'booking_day_table','api-booking-day-list',field_coloumns,order_coloumns,undefined,undefined,'Search by Carton Name','','api-booking-day-list');    
    };
 
    $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });
    $(document).on('click', '.btn-delete', function (event) {
        event.preventDefault();
        var $currentObj = $(this);
        var id = $(this).attr("id");
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
                        url: BASE_URL + 'api-booking/'+id,
                        type: "delete",
                        processData: false,
                        data:{id:id},
                        headers: {
                           Authorization: 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
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
    });
    
    $(document).on('click', '.delete-many', function (event) {
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
                             url: BASE_URL + 'api-booking-remove-multiple',
                             type: "post",
                             processData: false,
                             data: 'ids='+join_selected_values,
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
                    message: "Please select atleast one record to delete.",
                    size: 'small'
                });
                return false;
        }
    });
    //Search outside the datatables
    $('#search_data').keyup(function(event)
    {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                PoundShopApp.commonClass.table.search($(this).val()).draw() ;
            }
            if (keycode=='8')
            {
                $('#search_data').val('');
                PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
            }
    })
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();    
})(jQuery);

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

$(document).on('click','.bookin_next',function(e)
{   
    var current_date=$('#view_date').val();
    var date_data=$('#next_date').val();
    var next_date=PoundShopApp.commonClass.get_new_date(date_data,1,1);      
    $('#view_date').val(date_data);
    $('#prev_date').val(current_date);
    $('#next_date').val(next_date);
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
});

$(document).on('click','.bookin_prev',function(e)
{
    var current_date=$('#view_date').val();    
    var date_data=$('#prev_date').val();
    var prev_date=PoundShopApp.commonClass.get_new_date(date_data,2,1);    
    $('#view_date').val(date_data);
    $('#next_date').val(current_date);
    $('#prev_date').val(prev_date);
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
});

function advanceSearch()
{   
    var booking_date=$('#booking_date').val();
    var is_status_checked=0;
    var completed=0;
    var not_completed=0;
    if (document.getElementById('booking_status_comp').checked) 
    {
        is_status_checked++;
        completed=1;
    }

    if(document.getElementById('booking_status_not_comp').checked)
    {
        is_status_checked++;
        not_completed=1;
    }

    if(booking_date=='' && is_status_checked==0)
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
        if(booking_date!='')
        {
            counter++;
        }

        if(completed!=0)
        {
            counter++;
        }

        if(not_completed!=0)
        {
            counter++;
        }                
        
        if(booking_date!='' && booking_date!= undefined && booking_date!='0')
        {
            var date_data=PoundShopApp.commonClass.get_new_date_from_string_date(booking_date);//current day in format
        }
        else
        {
            var date_data=PoundShopApp.commonClass.get_new_date_from_string_date($('#url_date').val());//current day in format
        }

        var next_date=PoundShopApp.commonClass.get_new_date(date_data,1,1);//next day in format
        var prev_date=PoundShopApp.commonClass.get_new_date(date_data,2,1);//prev day in format
            
        $('#view_date').val(date_data);
        $('#next_date').val(next_date);
        $('#prev_date').val(prev_date);
        
        $('.filter_count').html(' ('+counter+')');
        PoundShopApp.commonClass.table.draw() ;      
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open');     
    }
}


//Search outside the datatables
$('.cancle_fil').click(function()
{
    $('.filter_count').html('');
    document.getElementById("booking_date").value = "";
    document.getElementById("booking_status_comp").checked = false;
    document.getElementById("booking_status_not_comp").checked = false;
    //back to previous thing
    var date_data=$('#url_date').val();
    var next_date=PoundShopApp.commonClass.get_new_date(date_data,1,1);//next day in format
    var prev_date=PoundShopApp.commonClass.get_new_date(date_data,2,1);//prev day in format

    $('#view_date').val(date_data);
    $('#next_date').val(next_date);
    $('#prev_date').val(prev_date);

    $('#btnFilter').removeClass('open');
    $('.search-filter-dropdown').removeClass('open'); 
    $('.card-flex-container').removeClass('filter-open'); 
    $('#search_data').val('');
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;         
}) ;