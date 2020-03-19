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
            {"orderable": false, "searchable": false},
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
        
        var order_coloumns = [[0, "desc"]];

        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table, dataTableId,'api-supplier',field_coloumns,order_coloumns,undefined,undefined,'Search by Sup. Name, Sup. Account, Cont. Person, Cont. City', [], 'custom_advance_search');    
        
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

    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);

function advanceSearch(e)
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
            message: "Are you sure, you want to delete? This process cannot be undone.",
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
                        url: BASE_URL+'api-supplier-remove',
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
            message: "Please select atleast one record.",
            size: 'small'
        });
        return false;
    }    
}


function send_email(me)
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
            message: "Are you sure, you want to send welcome mail? This process cannot be undone.",
            buttons: {
                cancel: {
                    label: 'Cancel',
                    className: 'btn-gray'
                },
                confirm: {
                    label: 'Send',
                    className: 'btn-blue'
                }
            },
            callback: function (result) 
            {
                if(result==true)
                {
                    $.ajax({
                        url: BASE_URL+'api-supplier-email',
                        type: "POST",
                        datatype:'JSON',
                        data:{'id':ids},
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
                            if (response.status == 1) {
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                PoundShopApp.commonClass.table.draw();
                            }
                        },
                        error: function (xhr, err) 
                        {
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
            message: "Please select atleast one record.",
            size: 'small'
        });
        return false;
    }    
}

//clear filter
$('.refresh').on('click',function()
{    
    $('#search_data').val('');
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
});

