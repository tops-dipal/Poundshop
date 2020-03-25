

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'users_table';
    var poundShopUsers = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopUsers.prototype;
    
    c._initialize = function ()
    {
        c._listingView();
      

    };
   $('.refresh').on('click',function()
    {    
        $('#search_data').val('');
        PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
    });
    
    c._listingView = function(){
        var field_coloumns = [
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            null,
            null,
            null,
            null,
            null,
            null,
            {"orderable": false, "searchable": false},
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'users_table','api-users',field_coloumns,order_coloumns,undefined,undefined,'Search');    
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
                        url: BASE_URL + 'api-users-remove/'+id,
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
                            url: BASE_URL + 'api-users-remove-multiple',
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
window.PoundShopApp.poundShopUsers = new poundShopUsers();

})(jQuery);
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
    
    $('#users_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
});