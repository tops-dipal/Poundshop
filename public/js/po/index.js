/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'po_table';

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
    };
    c._listingView = function(){
        var field_coloumns = [
            {"orderable": false, "searchable": false},
            null,
            null,
            null,
            {"orderable": false, "searchable": false},
            null,
            null,
            null,
            {"orderable": false, "searchable": false},
        ];
        var order_coloumns = [[7, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'po_table','api-purchase-orders',field_coloumns,order_coloumns,undefined,undefined,'',[],'purchase-order');    
        
    };
     $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });
    
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
            if('indeterminate' in chkbox_select_all)
            {
                 chkbox_select_all.checked = false;   
                chkbox_select_all.indeterminate = true;
            }
        }
    }
    $(document).on('click', '.btn-delete', function (event) {
        event.preventDefault();
        var $currentObj = $(this);
        var id = $(this).attr("id");
        bootbox.confirm({ 
                    title: "Confirm",
                    message: "Are You Sure You Want To Delete Selected Records? This Process Cannot Be Undone.",
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
                        if(result){
                            $.ajax({
                                url: BASE_URL + 'api-purchase-orders/'+id,
                                type: "delete",
                                processData: false,
                                data:{id:id},
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
                                        }else{
                                            PoundShopApp.commonClass._displayErrorMessage(response.message);
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
            if(allVals<1){
                bootbox.alert({
                    title: "Alert",
                    message: "Please Select Atleast One Record To Delete.",
                    size: 'small'
                });
                return false;
            }
            
            if (typeof allVals !== 'undefined' && allVals.length > 0) 
            {
                 bootbox.confirm({ 
                    title: "Confirm",
                    message: "Are You Sure You Want To Delete Selected Records? This Process Cannot Be Undone.",
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
                        if(result){
                                var join_selected_values = allVals.join(","); 
                             $.ajax({
                                url: BASE_URL + 'api-purchase-orders-remove-multiple',
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
                                        }else{
                                            PoundShopApp.commonClass._displayErrorMessage(response.message);
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
                    message: "Please Select Atleast One Record To Delete.",
                    size: 'small'
                });
                return false;
            }
           
        

    });
    
//    $(document).on('change','#po_status,#supplier_category',function(event){
//         PoundShopApp.commonClass.table.draw();
//    });
//    $(document).on('keyup','#supplier_name',function(e){
//          PoundShopApp.commonClass.table.draw();
//    });
    $(document).on('change','#pending_descripancy,#uk_po,#import_po,#missing_photo,#missing_information,#outstanding_po',function(e){
        if ($(this).is(':checked')) {
                $(this).val(1);
              } else {
                $(this).val(0);
              }
    });
    
    $(document).ready(function ()
    {   
        var rows_selected = [];   
        $('#po_table tbody').on('click', 'input[type="checkbox"]', function(e)
        {
            var table = PoundShopApp.commonClass.table; 
            updateDataTableSelectAllCtrl(table);        
            e.stopPropagation();
        });
});
    
    
    //clear filter
window.PoundShopApp = window.PoundShopApp || {}
window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);


function advanceSearch()
{
    var po_status=$('#po_status').val();
    var supplier_category=$('#supplier_category').val();
    var supplier_name=$('#supplier_name').val();
    var uk_po=$('#uk_po').val();
    var import_po=$('#import_po').val();
    var missing_photo=$('#missing_photo').val();
    var missing_information=$('#missing_information').val();
    var outstanding_po = $("#outstanding_po").val();
    var pending_descripancy = $("#pending_descripancy").val();
    var counter=0;
    if(po_status !==""){
        counter++;
    }
       
    
    
    if(supplier_category !==""){
        counter++;
    }
        
    
    if(supplier_name !==""){
        counter++;
    }
        
    
    if(uk_po !=='0'){
        counter++;
    }
        
    
    if(import_po !== '0'){
        counter++;
    }
        
    
    if(missing_photo !=='0'){
            counter++;
    }
    
    
    if(missing_information !=='0'){
        counter++;
    }
        
    if(outstanding_po !== '0'){
        counter++;
    }
    
    if(pending_descripancy !== '0'){
        counter++;
    }
    
        
     PoundShopApp.commonClass.table.draw() ; 
     if(counter > 0){
         $('.filter_count').html(' ('+counter+')');
     }else{
         $('.filter_count').html(' ');
     }
     
 
     $('#btnFilter').removeClass('open');
     $('.search-filter-dropdown').removeClass('open'); 
     $('.card-flex-container').removeClass('filter-open');    
}

//Search outside the datatables
    $('.cancle_fil').click(function()
    {
        $("#po-search-form")[0].reset();
        PoundShopApp.commonClass.table.draw() ; 
        $('.filter_count').html(' ');
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open'); 
    });
    
$('#search_data').keyup(function(event)
{
   
    var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            PoundShopApp.commonClass.table.draw() ;
        }
})

//clear filter
$('.refresh').on('click',function()
{    
    $('#search_data').val('');
    $("#po-search-form")[0].reset();
    PoundShopApp.commonClass.table.clear().draw();
    PoundShopApp.commonClass.table.draw();  
    window.location.reload();
    
});