/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'carton_table';
    var poundShopCartons = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
            
            if($( "#select_warehouse option:selected" ).val()=='')
            {
                $("#select_warehouse").val($("#select_warehouse option:first").val());
            }
            $('#warehouse_id').val($( "#select_warehouse option:selected" ).val());

        });
    };
    var c = poundShopCartons.prototype;
    
    c._initialize = function ()
    {
        c._listingView();
    };
    
    c._listingView = function(){
        
    };

    $('#select_warehouse').change(function(){
        $('#warehouse_id').val($(this).val());
        $.ajax({
             url: WEB_BASE_URL + '/count-based-on-site',
             type: "post",
             processData: false,
             data: $('#modalForm').serialize(),
             headers: {
                 'Authorization': 'Bearer ' + API_TOKEN,
             },
             beforeSend: function () {
                 $("#page-loader").show();
             },
             success: function (response) {
                 $("#page-loader").hide();
                 
                    $('.load_count_data').html(response.view);
                  
             },
             error: function (xhr, err) {
                $("#page-loader").hide();
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
             }

        });
    })
 
    showLocationQty=function(productId,qty_count)
    {
        $('#location_qty_count').html(qty_count);
        $('#locationQtyModal').modal('show');
        if ($.fn.DataTable.isDataTable( '#locationQtyInfo' ) ) {
        // $('#emptyLocationsTable').dataTable().fnClearTable();
           $('#locationQtyInfo').dataTable().fnDestroy();
           $('#locationQtyInfo > tbody').html('');
        }
        var field_coloumns = [
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'locationQtyInfo','api-product-location-qty',field_coloumns,order_coloumns,undefined,undefined,'Search by Carton Name',[],'form-warehouse');    
    }

    showOnPONotBookedIn=function(productId,qty_count)
    {
        $('#qty_on_po_but_not_booked_in_count').html(qty_count);
        $('#onPoNotBookedInModal').modal('show');
        if ($.fn.DataTable.isDataTable( '#onPoNotBookedInInfo' ) ) {
        // $('#emptyLocationsTable').dataTable().fnClearTable();
           $('#onPoNotBookedInInfo').dataTable().fnDestroy();
           $('#onPoNotBookedInInfo > tbody').html('');
        }
        var field_coloumns = [
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'onPoNotBookedInInfo','api-product-qty-po-not-booked-in',field_coloumns,order_coloumns,undefined,undefined,'Search by Carton Name',[],'form-warehouse');    
    }

    showBookedInNotArrived=function(productId,qty_count)
    {
        $('#qty_booked_in_not_arrived_count').html(qty_count);
        $('#qtyBookedInNotArrivedModal').modal('show');
        if ($.fn.DataTable.isDataTable( '#qtyBookedbutNotArrivedTable' ) ) {
        // $('#emptyLocationsTable').dataTable().fnClearTable();
           $('#qtyBookedbutNotArrivedTable').dataTable().fnDestroy();
           $('#qtyBookedbutNotArrivedTable > tbody').html('');
        }
        var field_coloumns = [
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'qtyBookedbutNotArrivedTable','api-booked-in-not-arrived',field_coloumns,order_coloumns,undefined,undefined,'Search by Carton Name',[],'form-warehouse');    
    }

    showWaitingToBEPutAway=function(productId,qty_count)
    {
       $('#putaway_qty').html(qty_count);
        $('#waitiongToBePutAwayModal').modal('show');
        if ($.fn.DataTable.isDataTable( '#waitingToBePutawayTable' ) ) {
        // $('#emptyLocationsTable').dataTable().fnClearTable();
           $('#waitingToBePutawayTable').dataTable().fnDestroy();
           $('#waitingToBePutawayTable > tbody').html('');
        }
        var field_coloumns = [
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
           
            
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'waitingToBePutawayTable','api-waiting-to-be-putaway',field_coloumns,order_coloumns,undefined,undefined,'Search by Carton Name',[],'form-warehouse');    
    }
    

   
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);

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
        if('indeterminate' in chkbox_select_all)
        {
             chkbox_select_all.checked = false;   
            chkbox_select_all.indeterminate = true;
        }
    }
}

