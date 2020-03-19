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

            //Search outside the datatables
            $('#search_data').keyup(function()
            {
                var keycode = (event.keyCode ? event.keyCode : event.which);
                
                if(keycode == '13')
                {
                    PoundShopApp.commonClass.table.search($(this).val()).draw();
                }
            });

            //Search outside the datatables
            $('.cancle_fil').click(function()
            {
                $('.filter_count').html('');
                
                $('#seasonal_range').hide();

                $('#custom_advance_search_fields input,select,textarea').each(function()
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
                        else if($(this).attr('type') == 'button' || $(this).attr('name') == 'search_type')
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
                PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;         
            }) ;
            
            var rows_selected = [];   
            var table = PoundShopApp.commonClass.table; 
        });
    };

    var c = poundShopCartons.prototype;
    
    c._initialize = function ()
    {
        c._listingView();

        $('.reset_search').click(function(){
            PoundShopApp.commonClass._reset_search(dataTableId);
        });

        $(".select2-tag").select2({
            tags: true,
            dropdownParent: $('#select_2_dropdown')
            // tokenSeparators: [',', ' ']
        })
    };
    
    c._listingView = function(){
        var field_coloumns = [
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            null,
            null,
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            null,
            null,
            null,
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
        ];
        
        var order_coloumns = [[0, "desc"]];

        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table, dataTableId,'api-product',field_coloumns,order_coloumns,undefined,undefined,'Search by SKU, Title, Barcode' , [], 'custom_advance_search');    
    };
        
    $('.datepicker_month_and_date').datepicker({
        inline              : true,
        format              : 'dd-M',
        autoclose           : true,
        enableOnReadonly    : true,
        disableTouchKeyboard: true,
        leftArrow           : '<i class="fa fa-long-arrow-left"></i>',
        rightArrow          : '<i class="fa fa-long-arrow-right"></i>',
        todayHighlight      : true,
        maxViewMode         : 'months',
        beforeShowDay       : function (date) {
        }
    });     

    $('input[name="filter_show_seasonal_products_only"]').on('change', function (){
       if($('input[name="filter_show_seasonal_products_only"]').is(':checked'))
       {
            $('#seasonal_range').show();
       }
       else
       {
            $('#seasonal_range').hide();
       } 
    });

    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);

function delete_record(me)
{
    var ids = [];

    if(typeof $(me).attr('attr-id') != 'undefined')
    {
        ids.push($(me).attr('attr-id'));
    }
    else
    {
        ids = getListingCheckboxIds();   
    }    

    if(ids.length > 0)
    {    
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
                        url: BASE_URL+'api-product-remove',
                        type: "POST",
                        datatype:'JSON',
                        data:{'id':ids},
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
}

function draw_table()
{
    PoundShopApp.commonClass.table.draw() ;
}

$('.refresh').on('click',function()
{    
    $('#search_data').val('');
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw();  
});

function advanceSearch()
{   
    var counter=0;

    if($('#custom_advance_search_fields').length > 0)
    {    
        $('#custom_advance_search_fields input,select,textarea').each(function()
        {
            if(typeof $(this).val() != undefined && $(this).val() != null && !$(this).hasClass('clear_except'))
            {
                if($(this).val().length > 0)
                {    
                    if($(this).attr('name') == 'filter_custom_tags[]')
                    {
                        if($('.select2-tag').val() != "" && $('.select2-tag').val() != null)
                        {    
                            counter++;
                        }
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

function get_variation(me)
{
    var product_id = $(me).parents('td').find('.child-checkbox').val();
        
    if(typeof product_id != 'undefined')
    {
        if(product_id.length > 0)
        {
            if($(me).hasClass('close'))
            {    
                $(me).removeClass('close').addClass('open');

                if($('#listing_table').find('tr[attr-par-id="'+product_id+'"]').length == 0)
                {    
                    $(me).parents('tr').after('<tr attr-par-id = "'+product_id+'"><td colspan="100%">Loading Variations...</td></tr>');

                    $.ajax({
                        url: WEB_BASE_URL+'/product/get-variations-list/'+product_id,
                        type: "GET",
                        datatype:'HTML',
                        headers: {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                        beforeSend: function () {
                            
                        },
                        success: function (response) {
                            $('#listing_table').find('tr[attr-par-id="'+product_id+'"]').replaceWith(response);
                        },
                        error: function (xhr, err) {
                           
                        }
                    });
                }
                else
                {
                    $('#listing_table').find('tr[attr-par-id="'+product_id+'"]').show();    
                }    
            }
            else
            {
                $(me).removeClass('open').addClass('close');

                $('#listing_table').find('tr[attr-par-id="'+product_id+'"]').hide();
            }    
        }    
    }    
}
