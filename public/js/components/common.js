/* 
 * @author : Hitesh Tank
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function toFixedDigit(value){
    return parseFloat(value).toFixed(2);
}
 function digitOnly(e) {
     var key = e.charCode || e.keyCode || 0;
            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
            // home, end, period, and numpad decimal
            if (!(key == 65 || key == 8 || 
                key == 9 ||
                key == 13 ||
                key == 46 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105))){
                    e.preventDefault();
            }
 }

(function($)
{
    'use strict'
    var commonClass = function ()
    {
        var c = this;       
        $(document).ready(function ()
        {          
            c._initialize();
        });
    };    
    
    var c = commonClass.prototype;   
    
    c._initialize=function(){
        c._initilizeToaster();
    };
    
    //Loader to be show
    c._showLoader = function ()
    {
        $("#page-loader").show();
    };

    //Loader to be hide
    c._hideLoader = function ()
    {
        $("#page-loader").hide();
    };
    
    c._initilizeToaster = function(){
        toastr.options = {
            "debug": false,
            "positionClass": "toast-bottom-right",
            "onclick": null,
            "fadeIn": 300,
            "fadeOut": 1000,
            "timeOut": 5000,
            "extendedTimeOut": 1000,
            "preventDuplicates": true
        };        
    };
    
    c._displaySuccessMessage = function (message) {
        c._hideLoader();
        toastr.success(message)
    };

    c._displayErrorMessage = function (message) {
        c._hideLoader();
        toastr.error(message)
    };
    
    c._commonFormErrorShow = function (obj, err) 
    {
        PoundShopApp.commonClass._hideLoader();
        if (obj.status == 422) {
           var message = "";
            var errorJson = JSON.parse(obj.responseText);
            $.each(errorJson.errors, function (key, value)
            {
                message += value + "<br>";
            });
            toastr.error(message);
        } else if (obj.status == 400) {
            toastr.error(obj.responseText);
        } else {
            toastr.error(formatErrorMessage(obj, err));
        }
    }
    
    if (window.matchMedia("(max-width: 767px)").matches) 
    {        
        //Generate data table MOBILE
        c._generateDataTable = function (table, element_id_name, ajax_URL, field_coloumns, order_coloumns, need_pagination, need_search,search_text,filters=[],advanceSearch='') 
        {
            var bPaginate = true;
            var bInfo = true;
            var bSearching = true;
            var pageLength=10;
            if (field_coloumns === undefined) {
                field_coloumns = [];
            }
            if (order_coloumns === undefined) {
                order_coloumns = [[0, "desc"]];
            }
            if (need_pagination !== undefined) {
                bPaginate = need_pagination;
                bInfo = need_pagination;
            }
            if (need_search !== undefined) {
                bSearching = need_search;
            }
            var intial_url = 'http://';
            var intial_url2 = 'https://';        
            var final_ajax_url = '';

            if (ajax_URL.indexOf(intial_url) != -1) {
                final_ajax_url = ajax_URL;
            } else if (ajax_URL.indexOf(intial_url2) != -1) {
                final_ajax_url = ajax_URL;
            } else {
                final_ajax_url = BASE_URL + ajax_URL;
            }

            //add for api booking
            var book_date_ur='';
            if (ajax_URL == 'api-booking-day-list') 
            {
                book_date_ur=$('#view_date').val();  
                bPaginate=false;          
            } 
            if(ajax_URL=='api-booking-week-list')  
            {
                book_date_ur=$('#view_date').val();  
                bPaginate=false;  
            }
            if(ajax_URL=='api-empty-locations')  
            {
                
                pageLength=25;
            }
            
            table = $('#' + element_id_name).DataTable({
                "processing": true,
                "order": order_coloumns,
                "oLanguage": {
                    "sProcessing": '<img src="' + WEB_BASE_URL + '/img/loader.gif" width="40">',
                    "sEmptyTable": "No Records Found",
                },
                "lengthMenu": [10, 25, 50, 75, 100],
                "serverSide": true,
                "pageLength":pageLength,
                    // rowReorder: {
                    // selector: 'td:nth-child(2)'
                    // },
                // responsive: true,
                 responsive: {
                    details: {
                        type: 'column',
                        target: 1
                    }
                },
                columnDefs: [ {
                    className: 'control',
                    orderable: false,
                    targets:   1
                } ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: search_text,
                    "paginate": {
                      "previous": "<span class='icon-moon icon-Drop-Down-1'></span>",
                      "next": "<span class='icon-moon icon-Drop-Down-1'></span>"
                    }
                },
                // "dom": '<"custom-table-header"lf<"refresh reset_search">><"custom-table-body"rt><"custom-table-footer"ip>',
                "dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"ipl>',
                "bInfo": bInfo,
                "autoWidth": false,
                "searching": bSearching,
                //"orderCellsTop": true,
                //"fixedHeader": true,
                "stateSave": false,
                "columns": field_coloumns,
                "bPaginate": bPaginate,
                fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if(advanceSearch !== 'purchase-order-revision')
                    $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                },
                fnDrawCallback: function (oSettings, json) {
                    $('.master-checkbox').prop('indeterminate', false);
                    // $('a[data-rel^=lightcase]').lightcase();
                    if(advanceSearch !== 'purchase-order-revision')
                    $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                    if(advanceSearch == 'tax-payment-report')
                    {
                        $('.total_import_duty').text(oSettings.json.total_import_duty);
                        $('.total_vat').text(oSettings.json.total_vat);
                        $('.total_tax').text(oSettings.json.total_tax);
                        $('.total_vat_on_uk').text(oSettings.json.total_vat_on_uk);
                        $('.total_vat_on_import').text(oSettings.json.total_vat_on_import);
                    }
                     if(advanceSearch == 'form-warehouse')
                    {
                        if(typeof oSettings.json.labelData!== 'undefined')
                        {
                            $('.total_pick_qty').text(oSettings.json.labelData.totalInPickLocationQty);
                            $('.total_bulk_qty').text(oSettings.json.labelData.totalInBulkLocationQty);
                            $('.total_return_qty').text(oSettings.json.labelData.totalInReturnLocationQty);
                            $('.total_num_pick_location').text(oSettings.json.labelData.totalInPickLocationCount);
                            $('.total_num_bulk_location').text(oSettings.json.labelData.totalInBulkLocationCount);
                            $('.scanned_datetime').text(oSettings.json.labelData.scanned_datetime);
                            $('.scanned_user').text(oSettings.json.labelData.scanned_user);
                        }
                    }
                    
                    if(advanceSearch == 'day-stock-color')
                    {
                        $("input:radio[name=day_stock_val]:first").attr('checked', true);
                        $('.stock_hold_days').val($("input:radio[name=day_stock_val]:first").val());
                    }

                    // JUST ADD "mangeAjaxTableResponse()" TO YOUR MODULE.JS FILE AND MANIPULTAE AJAX REPONSE FROM THERE. REFER:EXCESS QTY RECEIVED REPORT.
                    if(typeof mangeAjaxTableResponse !== 'undefined' && jQuery.isFunction(mangeAjaxTableResponse))
                    {
                        mangeAjaxTableResponse(oSettings, json);
                    }   

                    $('[data-toggle="popover"]').popover();
                },
                initComplete: function () {

                },
                "ajax": {
                    url: final_ajax_url,
                    type: "GET", // method  , by default get
                    "data": function (d) 
                    {
                        d.page = (d.start + d.length) / d.length;
                        if(advanceSearch == 'location')
                        {    
                            var form_data=$('#location_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'purchase-order-revision'){
                            d.purchase_order_id = $("#po_id").val();
                        }
                        if(advanceSearch == 'purchase-order'){
                            var form_data=$('#po-search-form').serialize();                        
                            d.advanceSearch=form_data;
                            d.search=$("#search_data").val();
                        }
                        if(advanceSearch == 'custom_advance_search')
                        {    
                            var form_data=$('#custom_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'tax-payment-report')
                        {    
                            var form_data=$('#tax-report-form').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'listing-manager')
                        {    
                            var form_data=$('#listing-manager-form').serialize();
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'form-warehouse')
                        {    
                            var form_data=$('#modalForm').serialize();                        
                            d.advanceSearch=form_data;
                        }
                         if(advanceSearch=="assigned-locations")
                        {
                            var form_data=$('#assignedLocationForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                         if(advanceSearch == 'location-assignment')
                        {    
                            var form_data=$('#locationAssignFilterForm').serialize();
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'aisle-empty-loactions')
                        {
                           var form_data=$('#assignLocationForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                         if(advanceSearch == 'replen-request')
                        {
                           var form_data=$('#replenRequestFilterForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                        if(advanceSearch == 'day-stock-color')
                        {
                           var form_data=$('#dayStockForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                        d.book_date_ur=book_date_ur;
                    },
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                        'Panel': 'web'
                    },
                    error: function (xhr, err) {
                       $("#page-loader").hide();
                    }
                }
            });
            return table;
        };

        c._generateDataTableBookin = function (table, element_id_name, ajax_URL, field_coloumns, order_coloumns, need_pagination, need_search,search_text,filters=[],advanceSearch='') 
        {
            var bPaginate = true;
            var bInfo = true;
            var bSearching = true;
            if (field_coloumns === undefined) {
                field_coloumns = [];
            }
            if (order_coloumns === undefined) {
                order_coloumns = [[0, "desc"]];
            }
            if (need_pagination !== undefined) {
                bPaginate = need_pagination;
                bInfo = need_pagination;
            }
            if (need_search !== undefined) {
                bSearching = need_search;
            }
            var intial_url = 'http://';
            var intial_url2 = 'https://';        
            var final_ajax_url = '';

            if (ajax_URL.indexOf(intial_url) != -1) {
                final_ajax_url = ajax_URL;
            } else if (ajax_URL.indexOf(intial_url2) != -1) {
                final_ajax_url = ajax_URL;
            } else {
                final_ajax_url = BASE_URL + ajax_URL;
            }

            //add for api booking
            var book_date_ur='';
            if (ajax_URL == 'api-booking-day-list' || ajax_URL=='api-booking-week-list') 
            {
                bPaginate=false;          
            }        
            
            table = $('#' + element_id_name).DataTable({
                "processing": true,
                "order": order_coloumns,
                "oLanguage": {
                    "sProcessing": '<img src="' + WEB_BASE_URL + '/img/loader.gif" width="40">',
                    "sEmptyTable": "No Records Found",
                },
                "lengthMenu": [10, 25, 50, 75, 100],
                "serverSide": true,
                    // rowReorder: {
                    // selector: 'td:nth-child(2)'
                    // },
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: search_text,
                    "paginate": {
                      "previous": "<span class='icon-moon icon-Drop-Down-1'></span>",
                      "next": "<span class='icon-moon icon-Drop-Down-1'></span>"
                    }
                },
                // "dom": '<"custom-table-header"lf<"refresh reset_search">><"custom-table-body"rt><"custom-table-footer"ip>',
                //"dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"ipl>',
                "dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"i<"bookin_paging"<"bookin_prev"><"bookin_next">>>',
                "bInfo": bInfo,
                "autoWidth": false,
                "searching": bSearching,
                //"orderCellsTop": true,
                //"fixedHeader": true,
                "stateSave": false,
                "columns": field_coloumns,
                "bPaginate": bPaginate,
                fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if(advanceSearch !== 'purchase-order-revision')
                    $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                },
                fnDrawCallback: function (oSettings, json) {
                    $('a[data-rel^=lightcase]').lightcase();
                    if(advanceSearch !== 'purchase-order-revision')
                    $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                    $('.master-checkbox').prop('indeterminate', false);
                    $('.master-checkbox').prop('checked', false);
                    if(advanceSearch !== 'purchase-order-revision'){
                        $(this).closest(".dataTables_scrollBody").siblings(".dataTables_scrollHead").find("th:first").removeClass('sorting_asc').removeClass('sorting_desc');
                        //$(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                    }
                    if(advanceSearch == 'tax-payment-report')
                    {
                        $('.total_import_duty').text(oSettings.json.total_import_duty);
                        $('.total_vat').text(oSettings.json.total_vat);
                        $('.total_tax').text(oSettings.json.total_tax);
                        $('.total_vat_on_uk').text(oSettings.json.total_vat_on_uk);
                        $('.total_vat_on_import').text(oSettings.json.total_vat_on_import);
                    }
                    
                     if(advanceSearch == 'day-stock-color')
                    {
                        $("input:radio[name=day_stock_val]:first").attr('checked', true);
                    }
                    if (ajax_URL == 'api-booking-day-list') 
                    {
                        var new_date_data=PoundShopApp.commonClass.get_new_date_string($('#view_date').val())
                        $('.date_wise_data_class').html(new_date_data);
                    }
                    $('[data-toggle="popover"]').popover();
                },
                initComplete: function () {

                },
                "ajax": {
                    url: final_ajax_url,
                    type: "GET", // method  , by default get
                    "data": function (d) 
                    {
                        d.page = (d.start + d.length) / d.length;
                        if(advanceSearch == 'location')
                        {    
                            var form_data=$('#location_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'purchase-order-revision'){
                            d.purchase_order_id = $("#po_id").val();
                        }
                        if(advanceSearch == 'purchase-order'){
                            var form_data=$('#po-search-form').serialize();                        
                            d.advanceSearch=form_data;
                            d.search=$("#search_data").val();
                        }
                        if(advanceSearch == 'custom_advance_search')
                        {    
                            var form_data=$('#custom_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'tax-payment-report')
                        {    
                            var form_data=$('#tax-report-form').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'listing-manager')
                        {    
                            var form_data=$('#listing-manager-form').serialize();
                            d.advanceSearch=form_data;
                        }
                         if(advanceSearch == 'form-warehouse')
                        {    
                            var form_data=$('#modalForm').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        d.book_date_ur=book_date_ur;

                        //add for api booking                    
                        if (advanceSearch == 'api-booking-day-list') 
                        {
                            d.book_date_ur=$('#view_date').val();
                            var form_data=$('#booking_day_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if (advanceSearch == 'api-booking-week-list') 
                        {
                            d.book_date_ur=$('#view_date').val();
                            var form_data=$('#week-booking-form').serialize();                        
                            d.advanceSearch=form_data;
                        }
                    },
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                        'Panel': 'web'
                    },
                    error: function (xhr, err) {
                       $("#page-loader").hide();
                    }
                }
            });
            return table;
        };        
    } 
    else 
    {
        c._generateDataTable = function (table, element_id_name, ajax_URL, field_coloumns, order_coloumns, need_pagination, need_search,search_text,filters=[],advanceSearch='', column_def_param = []) 
        {
            var bPaginate = true;
            var bInfo = true;
            var bSearching = true;
            var pageLength=10;
            if (field_coloumns === undefined) {
                field_coloumns = [];
            }
            if (order_coloumns === undefined) {
                order_coloumns = [[0, "desc"]];
            }
            if (need_pagination !== undefined) {
                bPaginate = need_pagination;
                bInfo = need_pagination;
            }
            if (need_search !== undefined) {
                bSearching = need_search;
            }
            var intial_url = 'http://';
            var intial_url2 = 'https://';
            var final_ajax_url = '';
            if (ajax_URL.indexOf(intial_url) != -1) {
                final_ajax_url = ajax_URL;
            } else if (ajax_URL.indexOf(intial_url2) != -1) {
                final_ajax_url = ajax_URL;
            } else {
                final_ajax_url = BASE_URL + ajax_URL;
            }

            //add for api booking
            var book_date_ur='';
            if (ajax_URL == 'api-booking-day-list') 
            {
                book_date_ur=$('#view_date').val();  
                bPaginate=false;          
            } 
            if(ajax_URL=='api-booking-week-list')  
            {
                book_date_ur=$('#view_date').val();  
                bPaginate=false;  
            }
             if(ajax_URL=='api-empty-locations')  
            {
                pageLength=25;
            }
             if(ajax_URL=='api-assigned-location' || ajax_URL=='api-tax-payment-report-po')  
            {
                bPaginate=false;
            }

            //for add pl-12 class in booking module
            if(ajax_URL=='api-cartons')
            {
                var columnDefs = [
                   { targets : [2,3,4,5,6,7],
                     render : function(data, type, row, targets) {
                        var rightAlignCol=[2,3,4,5];
                        if($.inArray(targets.col,rightAlignCol)!=-1)
                        {
                            return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                        }
                        else
                        {
                            return '<span class="pl-12">'+data+'</span>'
                        }
                    }     
                   }
                ]
            }  
            else if(ajax_URL=='api-category-mapping')
            {
                var columnDefs = [
                   { targets : [1,2],
                     render : function(data, type, row) {
                        return '<span class="pl-12">'+data+'</span>'
                     }     
                   }
                ]
            }
            else if(ajax_URL=='api-commodity-codes')
            {
                var columnDefs = [
                   { targets : [2,3],
                     render : function(data, type, row) {
                        return '<span class="pl-12">'+data+'</span>'
                     }     
                   }
                ]
            }  
            else if(ajax_URL=='api-import-duty')
            {
                var columnDefs = [
                   { 
                        targets : [2,3,4],
                        render : function(data, type, row, targets) {
                            if(data != null)
                            {
                                var rightAlignCol=[3];
                                if($.inArray(targets.col,rightAlignCol)!=-1)
                                {
                                    return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                                }
                                else
                                {
                                    return '<span class="pl-12">'+data+'</span>'
                                }
                            }
                            else
                            {
                                return "";
                            }    
                        }        
                   }
                ]
            }  
            else if(ajax_URL=='api-warehouse')
            {
                var columnDefs = [
                   { targets : [2,3,4],
                     render : function(data, type, row) {
                        return '<span class="pl-12">'+data+'</span>'
                     }     
                   }
                ]
            }  
            else if(ajax_URL=='api-locations' || ajax_URL=='api-replen-request')
            {
                var columnDefs = [
                   { targets : [1,2,3,4,5,6,7,8,9,10,11,12,13],
                     render : function(data, type, row, targets) {
                    if(data != null)
                    {
                        var rightAlignCol=[9,10,11,12];
                        
                        if($.inArray(targets.col,rightAlignCol)!=-1)
                        {
                            return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                        }
                        else
                        {
                            return '<span class="pl-12">'+data+'</span>'
                        }
                    }
                    else
                    {
                        return "";
                    }    
                }         
                   }
                ]
            }  
            else if(ajax_URL=='api-pallets')
            {
                var columnDefs = [
                   { targets : [2,3,4,5,6,7],
                     render : function(data, type, row, targets) {
                        if(data != null)
                        {
                            var rightAlignCol=[2,3,4];
                            if($.inArray(targets.col,rightAlignCol)!=-1)
                            {
                                return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                            }
                            else
                            {
                                return '<span class="pl-12">'+data+'</span>'
                            }
                        }
                        else
                        {
                            return "";
                        }    
                    }         
                   }
                ]
            }  
            else if(ajax_URL=='api-totes')
            {
                var columnDefs = [
                   { targets : [2,3,4,5,6,7],
                     render : function(data, type, row, targets) {
                        if(data != null)
                        {
                            var rightAlignCol=[3,4,5];
                            if($.inArray(targets.col,rightAlignCol)!=-1)
                            {
                                return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                            }
                            else
                            {
                                return '<span class="pl-12">'+data+'</span>'
                            }
                        }
                        else
                        {
                            return "";
                        }    
                    }         
                   }
                ]
            }  
             else if(ajax_URL=='api-qc-checklist')
            {
                var columnDefs = [
                   { targets : [2],
                     render : function(data, type, row) {
                        return '<span class="pl-12">'+data+'</span>'
                     }     
                   }
                ]
            }  
             else if(ajax_URL=='api-users')
            {
                var columnDefs = [
                   { targets : [2,3,4,5,6,7],
                     render : function(data, type, row) {
                        return '<span class="pl-12">'+data+'</span>'
                     }     
                   }
                ]
            }  
             else if(ajax_URL=='api-location-assignment')
            {
                var columnDefs = [
                   { targets : [1,2,3,4,5,6],
                     render : function(data, type, row) {
                        return '<span class="pl-12">'+data+'</span>'
                     }     
                   }
                ]
            }  
             else if(ajax_URL=='api-supplier')
            {
                var columnDefs = [
                   { targets : [1,2,3,4,5,6,7,8],
                     render : function(data, type, row, targets) {
                        if(data != null)
                        {
                            var rightAlignCol=[3];

                            if($.inArray(targets.col,rightAlignCol)!=-1)
                            {
                                return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                            }
                            else
                            {
                                return '<span class="pl-13">'+data+'</span>'
                            }
                        }
                        else
                        {
                            return "";
                        }    
                    }            
                   }
                ]
            }  
            else if(ajax_URL=='api-purchase-orders')
            {
                var columnDefs = [
                   { targets : [2,3,5,6],
                     render : function(data, type, row) {
                        return '<span class="pl-12">'+data+'</span>'
                     }     
                   }
                ]
            }  
            else if(ajax_URL=='api-listing-manager-already-listed' || ajax_URL=='api-listing-manager-inprogress' || ajax_URL=='api-listing-manager-to-be-listed' )
            {
                var columnDefs = [
                   { targets : [3,4,5,6],
                     render : function(data, type, row, targets) {
                        if(data != null)
                        {
                            var rightAlignCol=[5];
                            if($.inArray(targets.col,rightAlignCol)!=-1)
                            {
                                return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                            }
                            else
                            {
                                return '<span class="pl-12">'+data+'</span>'
                            }
                        }
                        else
                        {
                            return "";
                        }    
                    }          
                   }
                ]
            }
            else if(column_def_param.length > 0){
                var columnDefs = column_def_param;
            }  
             else if(ajax_URL=='api-tax-payment-report-po')
            {
                var columnDefs = [
                   { targets : [1,2,3,4,5,6,7,8],
                     render : function(data, type, row, targets) {
                        if(data != null)
                        {
                            var rightAlignCol=[4,5,6,7,8];
                            if($.inArray(targets.col,rightAlignCol)!=-1)
                            {
                                return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                            }
                            else
                            {
                                return '<span class="pl-12">'+data+'</span>'
                            }
                        }
                        else
                        {
                            return "";
                        }    
                    }         
                   }
                ]
            }  
            else if(ajax_URL=='api-product-location-qty')
            {
                var columnDefs = [
                   { targets : [0,1,2,3,4,5,6],
                     render : function(data, type, row, targets) {
                        
                        return '<span class="pl-12">'+data+'</span>'
                          
                    }         
                   }
                ]
            }
            else{
                var columnDefs =[];
            } 
            
            table = $('#' + element_id_name).DataTable({            
                scrollX:        true,
                scrollCollapse: true, 
                fixedColumns: {
                    leftColumns: 0,
                    rightColumns: 0
                },
                pageLength:pageLength,
                columnDefs:columnDefs,
                "processing": true,
                "order": order_coloumns,
                "oLanguage": {
                    "sProcessing": '<img src="'+WEB_BASE_URL+'/img/loader.gif" width="40">',
                    "sEmptyTable": "No Records Found",
                },
                "lengthMenu": [10, 25, 50, 75, 100],
                "serverSide": true,           
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: search_text,
                    "paginate": {
                      "previous": "<span class='icon-moon icon-Drop-Down-1'></span>",
                      "next": "<span class='icon-moon icon-Drop-Down-1'></span>"
                    }
                },
                // "dom": '<"custom-table-header"lf<"refresh reset_search">><"custom-table-body"rt><"custom-table-footer"ip>',
                "dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"ipl>',
                "bInfo": bInfo,
                "autoWidth": false,
                "searching": bSearching,
                //"orderCellsTop": true,
                //"fixedHeader": true,
                "stateSave": false,
                "columns": field_coloumns,
                "bPaginate": bPaginate,
                fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {

                    if(advanceSearch !== 'purchase-order-revision' || advanceSearch !== 'tax-payment-report' || advanceSearch !=='assigned-locations'){
                        if(advanceSearch =='assigned-locations' || advanceSearch!=='tax-payment-report'){
                            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                        }else if(advanceSearch !== 'purchase-order-revision'){
                            $(this).closest(".dataTables_scrollBody").siblings(".dataTables_scrollHead").find("th:first").removeClass('sorting_asc').removeClass('sorting_desc');    
                        }
                        
                        
                    }
                    $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                },
                fnDrawCallback: function (oSettings, json) {
                    $('.master-checkbox').prop('indeterminate', false);
                    $('.master-checkbox').prop('checked', false);
                    if(advanceSearch !== 'purchase-order-revision' || advanceSearch!=='tax-payment-report' || advanceSearch !=='assigned-locations'){
                        if(advanceSearch =='assigned-locations' || advanceSearch=='tax-payment-report'){
                            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                        }else if(advanceSearch !== 'purchase-order-revision'){
                        $(this).closest(".dataTables_scrollBody").siblings(".dataTables_scrollHead").find("th:first").removeClass('sorting_asc').removeClass('sorting_desc');    
                        }
                        
                        
                        
                    }
                    if(advanceSearch == 'tax-payment-report')
                    {
                        $('.total_import_duty').text(oSettings.json.total_import_duty);
                        $('.total_vat').text(oSettings.json.total_vat);
                        $('.total_tax').text(oSettings.json.total_tax);
                        $('.total_vat_on_uk').text(oSettings.json.total_vat_on_uk);
                        $('.total_vat_on_import').text(oSettings.json.total_vat_on_import);
                    }  

                     if(advanceSearch == 'form-warehouse')
                    {
                        if(typeof oSettings.json.labelData!== 'undefined')
                        {
                            $('.total_pick_qty').text(oSettings.json.labelData.totalInPickLocationQty);
                            $('.total_bulk_qty').text(oSettings.json.labelData.totalInBulkLocationQty);
                            $('.total_return_qty').text(oSettings.json.labelData.totalInReturnLocationQty);
                            $('.total_num_pick_location').text(oSettings.json.labelData.totalInPickLocationCount);
                            $('.total_num_bulk_location').text(oSettings.json.labelData.totalInBulkLocationCount);
                            $('.scanned_datetime').text(oSettings.json.labelData.scanned_datetime);
                            $('.scanned_user').text(oSettings.json.labelData.scanned_user);
                        }
                    }
                    
                    if(advanceSearch == 'day-stock-color')
                    {
                        $("input:radio[name=day_stock_val]:first").attr('checked', true);
                        $('.stock_hold_days').val($("input:radio[name=day_stock_val]:first").val());
                    }

                    // JUST ADD "mangeAjaxTableResponse()" TO YOUR MODULE.JS FILE AND MANIPULTAE AJAX REPONSE FROM THERE. REFER:EXCESS QTY RECEIVED REPORT.
                    if(typeof mangeAjaxTableResponse !== 'undefined' && jQuery.isFunction(mangeAjaxTableResponse))
                    {
                        mangeAjaxTableResponse(oSettings, json);
                    }   

                    $('[data-toggle="popover"]').popover();                
                },
                initComplete: function () {

                },
                "ajax": {
                    url: final_ajax_url,
                    type: "GET", // method  , by default get
                    "data": function (d) 
                    {
                        d.page = (d.start + d.length) / d.length;
                        if(advanceSearch == 'location')
                        {    
                            var form_data=$('#location_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'purchase-order-revision'){
                            d.purchase_order_id = $("#po_id").val();
                        }
                        if(advanceSearch == 'purchase-order'){
                            var form_data=$('#po-search-form').serialize();                        
                            d.advanceSearch=form_data;
                            d.search=$("#search_data").val();
                        }
                        if(advanceSearch == 'custom_advance_search')
                        {    
                            var form_data=$('#custom_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'tax-payment-report')
                        {    
                            var form_data=$('#tax-report-form').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'listing-manager')
                        {    
                            var form_data=$('#listing-manager-form').serialize();
                            d.advanceSearch=form_data;
                        }
                         if(advanceSearch == 'location-assignment')
                        {    
                            var form_data=$('#locationAssignFilterForm').serialize();
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'aisle-empty-loactions')
                        {
                           var form_data=$('#assignLocationForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                        if(advanceSearch == 'replen-request')
                        {
                           var form_data=$('#replenRequestFilterForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                        if(advanceSearch == 'day-stock-color')
                        {
                           var form_data=$('#dayStockForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                         if(advanceSearch == 'form-warehouse')
                        {    
                            var form_data=$('#modalForm').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch=="assigned-locations")
                        {
                            var form_data=$('#assignedLocationForm').serialize();
                            d.advanceSearch=form_data; 
                        }
                        
                         d.book_date_ur=book_date_ur;
                    },
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                        'Panel': 'web'
                    },
                    error: function (xhr, err) {
                       $("#page-loader").hide();
                    }
                }
            });
            return table;
        };

        c._generateDataTableBookin = function (table, element_id_name, ajax_URL, field_coloumns, order_coloumns, need_pagination, need_search,search_text,filters=[],advanceSearch='') 
        {
            var bPaginate = true;
            var bInfo = true;
            var bSearching = true;
            if (field_coloumns === undefined) {
                field_coloumns = [];
            }
            if (order_coloumns === undefined) {
                order_coloumns = [[0, "desc"]];
            }
            if (need_pagination !== undefined) {
                bPaginate = need_pagination;
                bInfo = need_pagination;
            }
            if (need_search !== undefined) {
                bSearching = need_search;
            }
            var intial_url = 'http://';
            var intial_url2 = 'https://';        
            var final_ajax_url = '';

            if (ajax_URL.indexOf(intial_url) != -1) {
                final_ajax_url = ajax_URL;
            } else if (ajax_URL.indexOf(intial_url2) != -1) {
                final_ajax_url = ajax_URL;
            } else {
                final_ajax_url = BASE_URL + ajax_URL;
            }

            //add for api booking
            var book_date_ur='';
            if (ajax_URL == 'api-booking-day-list' || ajax_URL=='api-booking-week-list') 
            {
                bPaginate=false;          
            } 

            //for add pl-12 class in booking module
            if(ajax_URL=='api-booking')
            {
                var columnDefs = [
                   { targets : [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15],
                     render : function(data, type, row,targets) {
                        var ammountCol=[12,15];
                        if($.inArray(targets.col,ammountCol)!=-1)
                        {
                            return '<p class="text-right"><span class="pl-12">'+data+'</span></p>'
                        }
                        else
                        {
                            return '<span class="pl-12">'+data+'</span>'
                        }
                     }     
                   }
                ]
            }
            else if(ajax_URL=='api-booking-day-list')
            {
                var columnDefs = [
                   { targets : [13,12],
                     render : function(data, type, row, targets) {
                        if(data != null)
                        {
                            var rightAlignCol=[13,12];
                            if($.inArray(targets.col,rightAlignCol)!=-1)
                            {
                                return '<p class="mb-0 pr-3 text-right">'+data+'</p>'
                            }
                            else
                            {
                                return '<span class="pl-12">'+data+'</span>'
                            }
                        }
                        else
                        {
                            return "";
                        }    
                    }     
                   }
                ]
            }   
            else{
                var columnDefs =[];
            } 
            table = $('#' + element_id_name).DataTable({
                scrollX:        true,
                scrollCollapse: true, 
                fixedColumns: {
                    leftColumns: 0,
                    rightColumns: 0
                },
                columnDefs:columnDefs,
                "processing": true,
                "order": order_coloumns,
                "oLanguage": {
                    "sProcessing": '<img src="' + WEB_BASE_URL + '/img/loader.gif" width="40">',
                    "sEmptyTable": "No Records Found",
                },
                "lengthMenu": [10, 25, 50, 75, 100],
                "serverSide": true,
                    // rowReorder: {
                    // selector: 'td:nth-child(2)'
                    // },
                //responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: search_text,
                    "paginate": {
                      "previous": "<span class='icon-moon icon-Drop-Down-1'></span>",
                      "next": "<span class='icon-moon icon-Drop-Down-1'></span>"
                    }
                },
                // "dom": '<"custom-table-header"lf<"refresh reset_search">><"custom-table-body"rt><"custom-table-footer"ip>',
                //"dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"ipl>',
                "dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"i<"bookin_paging"<"bookin_prev"><"bookin_next">>>',
                "bInfo": bInfo,
                "autoWidth": false,
                "searching": bSearching,
                //"orderCellsTop": true,
                //"fixedHeader": true,
                "stateSave": false,
                "columns": field_coloumns,
                "bPaginate": bPaginate,
                fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                    if(advanceSearch !== 'purchase-order-revision')
                    $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                },
                fnDrawCallback: function (oSettings, json) {
                    $('a[data-rel^=lightcase]').lightcase();
                    if(advanceSearch !== 'purchase-order-revision')
                    $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                    if(advanceSearch == 'tax-payment-report')
                    {
                        $('.total_import_duty').text(oSettings.json.total_import_duty);
                        $('.total_vat').text(oSettings.json.total_vat);
                        $('.total_tax').text(oSettings.json.total_tax);
                        $('.total_vat_on_uk').text(oSettings.json.total_vat_on_uk);
                        $('.total_vat_on_import').text(oSettings.json.total_vat_on_import);
                    }
                    
                     if(advanceSearch == 'day-stock-color')
                    {
                        $("input:radio[name=day_stock_val]:first").attr('checked', true);
                    }
                    $('[data-toggle="popover"]').popover();

                    if (ajax_URL == 'api-booking-day-list') 
                    {
                        var new_date_data=PoundShopApp.commonClass.get_new_date_string($('#view_date').val())
                        $('.date_wise_data_class').html(new_date_data);
                    }
                },
                initComplete: function () {

                },
                "ajax": {
                    url: final_ajax_url,
                    type: "GET", // method  , by default get
                    "data": function (d) 
                    {
                        d.page = (d.start + d.length) / d.length;
                        if(advanceSearch == 'location')
                        {    
                            var form_data=$('#location_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'purchase-order-revision'){
                            d.purchase_order_id = $("#po_id").val();
                        }
                        if(advanceSearch == 'purchase-order'){
                            var form_data=$('#po-search-form').serialize();                        
                            d.advanceSearch=form_data;
                            d.search=$("#search_data").val();
                        }
                        if(advanceSearch == 'custom_advance_search')
                        {    
                            var form_data=$('#custom_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'tax-payment-report')
                        {    
                            var form_data=$('#tax-report-form').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if(advanceSearch == 'listing-manager')
                        {    
                            var form_data=$('#listing-manager-form').serialize();
                            d.advanceSearch=form_data;
                        }
                         if(advanceSearch == 'form-warehouse')
                        {    
                            var form_data=$('#modalForm').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        d.book_date_ur=book_date_ur;

                        //add for api booking                    
                        if (advanceSearch == 'api-booking-day-list') 
                        {
                            d.book_date_ur=$('#view_date').val();
                            var form_data=$('#booking_day_advance_search').serialize();                        
                            d.advanceSearch=form_data;
                        }
                        if (advanceSearch == 'api-booking-week-list') 
                        {
                            d.book_date_ur=$('#view_date').val();
                            var form_data=$('#week-booking-form').serialize();                        
                            d.advanceSearch=form_data;
                        }
                    },
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                        'Panel': 'web'
                    },
                    error: function (xhr, err) {
                       $("#page-loader").hide();
                    }
                }
            });
            return table;
        }; 
    }
    

    // reset datatable search
    c._reset_search = function(element_id_name){
        $('#'+element_id_name).DataTable().search('').draw();
    };


    c.get_new_date = function(dateString,incr_dec,days)
    {    
        var myDate = new Date(dateString);

        //add a day to the date
        if(incr_dec==1)
        {
            myDate.setDate(myDate.getDate() + days);
        }
        else
        {
            myDate.setDate(myDate.getDate() - days);   
        }        

        var y = myDate.getFullYear(),
        m = myDate.getMonth() + 1, // january is month 0 in javascript
        d = myDate.getDate();
        var pad = function(val) { var str = val.toString(); return (str.length < 2) ? "0" + str : str};
        dateString = [y, pad(m), pad(d)].join("-");
        return dateString;
    }

    c.get_new_date_string = function(dateString,incr_dec,days)
    {    
        var myDate = new Date(dateString);        
        myDate.setDate(myDate.getDate());
        const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
          "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"
        ];

        var y = myDate.getFullYear(),
        m = myDate.getMonth(), // january is month 0 in javascript
        d = myDate.getDate();        
        var pad = function(val) { var str = val.toString(); return (str.length < 2) ? "0" + str : str};
        dateString = [pad(d),monthNames[m], y].join("-");        
        return dateString;
    }
    
    c.get_new_week_date = function(dateString,incr_dec,days)
    {    
        var myDate = new Date(dateString);
        var updateDate=null;
        //add a day to the date
        if(incr_dec== 'start') //next date
        {
            myDate.setDate(myDate.getDate() + 1);
            updateDate = new Date(myDate.setDate(myDate.getDate() - myDate.getDay()));
        }
        else //previous date
        {
            myDate.setDate(myDate.getDate());   
            updateDate = new Date(myDate.setDate(myDate.getDate() - myDate.getDay()+6));
        }        

        var y = updateDate.getFullYear(),
        m = updateDate.getMonth() + 1, // january is month 0 in javascript
        d = updateDate.getDate();
        var pad = function(val) { var str = val.toString(); return (str.length < 2) ? "0" + str : str};
        dateString = [y, pad(m), pad(d)].join("-");
        return dateString;
    }

    /**
     * @author Hitesh Tank
     * @param {type} dateString
     * @param {type} type
     * @returns {.commonClass.prototype.getPreviousWeekDates.dateString|String}
     */
    c.getPreviousWeekDates=function(dateString,type){
        var myDate = new Date(dateString);
        var updateDate=null;
        if(type=='start'){ // week start date
            myDate.setDate(myDate.getDate() - 1);
            updateDate = new Date(myDate.setDate(myDate.getDate() - myDate.getDay()));
        }else{ //week end date
            myDate.setDate(myDate.getDate());   
            updateDate = new Date(myDate.setDate(myDate.getDate() - myDate.getDay()+6));
        }
        var y = updateDate.getFullYear(),
        m = updateDate.getMonth() + 1, // january is month 0 in javascript
        d = updateDate.getDate();
        var pad = function(val) { var str = val.toString(); return (str.length < 2) ? "0" + str : str};
        dateString = [y, pad(m), pad(d)].join("-");
        return dateString;
    }
    /**
     * @author Hitesh Tank
     * @param {type} dateString
     * @param {type} type
     * @returns {.commonClass.prototype.getNextWeekDates.dateString|String}
     */
    c.getNextWeekDates=function(dateString,type){
        var myDate = new Date(dateString);
        var updateDate=null;
        if(type=='start'){ // week start date
            myDate.setDate(myDate.getDate() +1);
            updateDate = new Date(myDate.setDate(myDate.getDate() - myDate.getDay()));
        }else{ //week end date
            myDate.setDate(myDate.getDate());   
            updateDate = new Date(myDate.setDate(myDate.getDate() - myDate.getDay()+6));
        }
        var y = updateDate.getFullYear(),
        m = updateDate.getMonth() + 1, // january is month 0 in javascript
        d = updateDate.getDate();
        var pad = function(val) { var str = val.toString(); return (str.length < 2) ? "0" + str : str};
        dateString = [y, pad(m), pad(d)].join("-");
        return dateString;
    }
    c.get_new_date_from_string_date = function(dateString)
    {    
        var new_d=dateString.replace(/-/g," ");
        var news=new Date(new_d);
        var month=news.getMonth()+1;
        var updated_booking_date=news.getFullYear()+"-"+month+"-"+news.getDate();   
        return updated_booking_date;
    }  
      
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.commonClass = new commonClass();

    // get state
    c.getState = function(me, state_el_id = 'stateDropdown', city_el_id = 'cityDropdown')
    {
        $('#'+state_el_id).find('option').not(':first').remove();
        $('#'+city_el_id).find('option').not(':first').remove();

        if($(me).val() != "")
        {
            $.ajax({
                url: BASE_URL+'api-states/'+$(me).val(),
                type: "GET",
                datatype: 'JSON',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $('#'+state_el_id).addClass('control-loading');
                },
                success: function (response) {
                  $('#'+state_el_id).removeClass('control-loading');                   

                    if(response.status == 'success')
                    {
                        var state_html = '';
                        $.each(response.data, function (index, value){
                            state_html += '<option value="'+value.id+'">'+value.name+'</option>'
                        });
                        $('#'+state_el_id).removeAttr('disabled');
                                                   
                        $('#'+state_el_id).append(state_html);
                        
                    }    
                },
                error: function (xhr, err) {
                    $('#'+state_el_id).removeClass('control-loading');
                    
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        } 
        else
        {
            $('#'+state_el_id).removeClass('control-loading');
            $('#'+state_el_id).attr('disabled', "disabled");   

             $('#'+city_el_id).removeClass('control-loading');
            $('#'+city_el_id).attr('disabled', "disabled");    
        }   
    }
    
    // get City
    c.getCity = function(me, city_el_id = 'cityDropdown')
    {
        $('#'+city_el_id).find('option').not(':first').remove();
        if($(me).val() != "")
        {
            $.ajax({
                url: BASE_URL+'api-cities/'+$(me).val(),
                type: "GET",
                datatype: 'JSON',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {

                    $('#'+city_el_id).addClass('control-loading');
                },
                success: function (response) 
                {
                    $('#'+city_el_id).removeClass('control-loading');
                    if(response.status == 'success')
                    {
                        var state_html = '';
                        $.each(response.data, function (index, value){
                            state_html += '<option value="'+value.id+'">'+value.name+'</option>'
                        });

                        $('#'+city_el_id).removeAttr('disabled');                            
                        $('#'+city_el_id).append(state_html);
                    }    
                },
                error: function (xhr, err) {
                    $('#'+city_el_id).removeClass('control-loading');
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        } 
        else
        {
            $('#'+city_el_id).attr('disabled', "disabled");    
        }   
    }

    //get City List for Users
     c.getCityList = function(me, city_el_id = 'cityDropdown', city_textbox='city_id')
    {
        $('#'+city_el_id).find('option').not(':first').remove();
        if($(me).val() != "")
        {
            $.ajax({
                url: BASE_URL+'api-cities-list/'+$(me).val(),
                type: "POST",
                datatype: 'JSON',
                data:{'country_id':$('.country_id').val(),'state_name':$('.state_id').val()},
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {

                    $('#'+city_el_id).addClass('control-loading');
                },
                success: function (response) 
                {
                    $('#'+city_el_id).removeClass('control-loading');
                    if(response.status == 'success')
                    {
                        var state_html = '';
                        $.each(response.data, function (index, value){
                            state_html += '<option value="'+value.name+'">'+value.name+'</option>'
                        });
                        $('#'+city_textbox).val("");
                        $('#'+city_textbox).attr('autocomplete','off');
                        $('#'+city_textbox).removeAttr('disabled');                            
                        $('#'+city_el_id).html(state_html);
                        
                        $('#'+city_textbox).val($('body').data("city_id"));
                    }    
                },
                error: function (xhr, err) {
                    $('#'+city_el_id).removeClass('control-loading');
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        } 
        else
        {
            $('#'+city_el_id).attr('disabled', "disabled");    
        }   
    }

    //get State List for Users
     c.getStateList = function(me, state_el_id = 'stateDropdown', city_el_id = 'cityDropdown', state_textbox= 'state_id')
    {
        $('#'+state_el_id).find('option').not(':first').remove();
        $('#'+city_el_id).find('option').not(':first').remove();

        if($(me).val() != "")
        {
            $.ajax({
                url: BASE_URL+'api-states/'+$(me).val(),
                type: "GET",
                datatype: 'JSON',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $('#'+state_el_id).addClass('control-loading');
                },
                success: function (response) {
                  $('#'+state_el_id).removeClass('control-loading');                   

                    if(response.status == 'success')
                    {
                        var state_html = '';
                        $.each(response.data, function (index, value){
                            state_html += '<option value="'+value.name+'">'+value.name+'</option>'
                        });
                        $('#'+state_textbox).val("");
                        $('#'+state_textbox).attr('autocomplete','off');
                        $('#'+state_textbox).removeAttr('disabled');
                        $('#'+state_el_id).html(state_html);
                        
                        if($('body').data("state_id_uk")!=undefined && $('body').data("country_type")=="uk")
                        {
                            $('#state_id').val($('body').data("state_id_uk"));
                        }
                        else
                        {
                            $('#state_id').val($('body').data("state_id"));
                        }
                        
                    }    
                },
                error: function (xhr, err) {
                    $('#'+state_el_id).removeClass('control-loading');
                    
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        } 
        else
        {
            $('#'+state_el_id).removeClass('control-loading');
            $('#'+state_el_id).attr('disabled', "disabled");   

             $('#'+city_el_id).removeClass('control-loading');
            $('#'+city_el_id).attr('disabled', "disabled");    
        }   
    }

   
})(jQuery);
