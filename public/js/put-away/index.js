

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'put_away_table';
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

    if(activeTab=='put-away-dashboard')
    {
        c._listingView = function(){
            var field_coloumns = [
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
            ];
            var columnDefs = [
                   { targets : [1,2,3,4,5,6,7,8,9,10],
                     render : function(data, type, row, targets) {
                            if(data != null)
                            {
                                var rightAlignCol=[1];
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

            var order_coloumns = [[0, "desc"]];
            PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'put_away_dashboard_table','api-put-away-dashboard',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'custom_advance_search', columnDefs);        
        };
    }
    else if(activeTab=='put-away')
    {
        c._listingView = function(){};        
    }
    else
    {
        c._listingView = function(){};        
    }       
    
    $('.refresh').on('click',function()
    {    
        var active_tab = $("ul#myTab li a.active").attr('href');        
        PoundShopApp.commonClass.table.search($('#search_data').val()).draw();          
    });    

    $('#search_data').keyup(function()
    {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13')
        {
            PoundShopApp.commonClass.table.search($(this).val()).draw() ;
        }
        
        if (keycode=='8')
        {
         
            PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
        }
    }) ;  

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
        PoundShopApp.commonClass.table.draw();         
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

    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopTotes = new poundShopTotes();
})(jQuery);