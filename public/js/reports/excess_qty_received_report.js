/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";

    var dataTableId = 'listing_table';

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
            {className: "hide_ajaxDatable_column" },
            null,
            null,
            null,
            null,
            null,
            null,
            null,
            null,
        ];
        
        var order_coloumns = [[1, "desc"]];

        var columnDefs = [
                   { targets : [4,5,6,7,8],
                     render : function(data, type, row, targets) {
                        if(data != null)
                        {
                            var rightAlignCol=[7];
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
                ];

        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table, dataTableId,'api-excess-qty-received-report',field_coloumns,order_coloumns,undefined,undefined,'Search by Sup. Name, Sup. Account, Cont. Person, Cont. City', [], 'custom_advance_search',columnDefs);    
        
        var rows_selected = [];   

        var table = PoundShopApp.commonClass.table; 
    };
 
    //Search outside the datatables
    $('#search_data').keyup(function()
    {
        var keycode = (event.keyCode ? event.keyCode : event.which);
                
        if(keycode == '13')
        {
            PoundShopApp.commonClass.table.search($(this).val()).draw() ;
        }
    });     

    //Search outside the datatables
    $('.cancle_fil').click(function()
    {
        $('.filter_count').html('');
        
        $('#custom_advance_search_fields').find('input,select,textarea').each(function()
        {
            if(typeof $(this).val() != undefined && !$(this).hasClass('clear_except'))
            {
                if($(this).attr('type') == 'checkbox')
                {
                    $(this).prop('checked', false); 
                }
                else if($(this).attr('type') == 'radio')
                {
                    $(this).prop('checked', false); 
                }
                else if($(this).attr('type') == 'button')
                {

                }    
                else
                {
                    $(this).val('');    
                }    
            }    
        });
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open'); 
        $('#search_data').val('');
        PoundShopApp.commonClass.table.search($('#search_data').val()).draw();         
    }) ;

    advanceSearch = function(e)
    {   
        e.preventDefault();

        var counter=0;

        if($('#custom_advance_search_fields').length > 0)
        {    
            $('#custom_advance_search_fields').find('input,select,textarea').each(function()
            {
                if(typeof $(this).val() != 'undefined' && $(this).val() != null)
                {
                    if($(this).val().length > 0)
                    {    
                        if(this.nodeName.toLowerCase() === 'select') {
                            counter++;    
                        }    

                        if($(this).attr('type') == 'text' || $(this).attr('type') == 'textarea')
                        {
                            counter++;    
                        }   
                        
                        if($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio')
                        {
                            if($(this).prop('checked') == true)
                            {
                                counter++;    
                            }    
                        }
                    }    
                }    
            });

            if(counter == 0)
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
                $('.filter_count').html(' ('+counter+')');
                PoundShopApp.commonClass.table.draw() ;      
                $('#btnFilter').removeClass('open');
                $('.search-filter-dropdown').removeClass('open'); 
                $('.card-flex-container').removeClass('filter-open'); 
            }    
        }    
    }

    mangeAjaxTableResponse =  function(oSettings, json)
    {
        if(typeof oSettings.json.global_result !== 'undefined')
        {    
            let global_result = oSettings.json.global_result;
            
            if(typeof global_result.sku_count !== 'undefined')
            {    
                $('.total_extra_products').text(global_result.sku_count);
            }

            if(typeof global_result.sku_count !== 'undefined')
            {
                $('.total_extra_quantity').text(global_result.quantity);
            }
            
            if(typeof global_result.value !== 'undefined')
            {    
                $('.total_value').html('<span class="font-12-dark mr-1">&#163;</span>'+global_result.value);
            }    
        }
    }

    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();


})(jQuery);

