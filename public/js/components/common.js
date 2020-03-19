/* 
 * @author : Hitesh Tank
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function toFixedDigit(value){
    return parseFloat(value).toFixed(2);
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
            "extendedTimeOut": 1000
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
            table = $('#' + element_id_name).DataTable({            
                scrollX:        true,
                scrollCollapse: true, 
                fixedColumns: {
                    leftColumns: 0,
                    rightColumns: 0
                },
                "processing": true,
                "order": order_coloumns,
                "oLanguage": {
                    "sProcessing": '<img src="'+WEB_BASE_URL+'/img/loader.gif" width="40">',
                    "sEmptyTable": "No Records Found",
                },
                "lengthMenu": [10, 25, 50, 75, 100],
                "serverSide": true,
                    // rowReorder: {
                    // selector: 'td:nth-child(2)'
                    // },
                //responsive: true,
                responsive: {
                    details: {
                        type: 'column',
                        target: -1
                    }
                },
                columnDefs: [ {
                    className: 'control',
                    orderable: false,
                    targets:   -1
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
                    if(advanceSearch !== 'purchase-order-revision'){
                        $(this).closest(".dataTables_scrollBody").siblings(".dataTables_scrollHead").find("th:first").removeClass('sorting_asc').removeClass('sorting_desc');
                        //$(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                    }
                },
                fnDrawCallback: function (oSettings, json) {
                    $('.master-checkbox').prop('indeterminate', false);
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
        
    } else {
          //Generate data table WEB
        c._generateDataTable = function (table, element_id_name, ajax_URL, field_coloumns, order_coloumns, need_pagination, need_search,search_text,filters=[],advanceSearch='') 
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
            table = $('#' + element_id_name).DataTable({            
                scrollX:        true,
                scrollCollapse: true, 
                fixedColumns: {
                    leftColumns: 0,
                    rightColumns: 0
                },
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
                    if(advanceSearch !== 'purchase-order-revision'){
                        $(this).closest(".dataTables_scrollBody").siblings(".dataTables_scrollHead").find("th:first").removeClass('sorting_asc').removeClass('sorting_desc');
                        //$(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                    }
                    $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
                },
                fnDrawCallback: function (oSettings, json) {
                    $('.master-checkbox').prop('indeterminate', false);
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
                      $.fn.dataTable.tables( {visible: true, api: true} ).columns.adjust();
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
                        console.log(state_el_id);                                
                        $('#'+state_el_id).append(state_html);
                        // console.log($('#'+state_el_id).text());
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
                        console.log($('body').data("city_id"));
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
                        // console.log($('#'+state_el_id).text());
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