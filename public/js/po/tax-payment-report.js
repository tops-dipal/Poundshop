



(function ($)
{
    "user strict";
    
    var poundShopTotes = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
            $('#from_date').attr('autocomplete','off');
            $('#to_date').attr('autocomplete','off');
        });
    };
    var c = poundShopTotes.prototype;
    
    c._initialize = function ()
    {
        c._listingView();
        $('.custom-select-search').selectpicker({
          liveSearch:true,
          size:10
        });
        /*$('.reset_search').click(function(){
            PoundShopApp.commonClass._reset_search(dataTableId);
        });*/

    };
    
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
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'data_table','api-tax-payment-report-po',field_coloumns,order_coloumns,undefined,undefined,'Search',[],'tax-payment-report');};
 
   
    $("#reset").click(function()
    {
        $('#tax-report-form').trigger("reset");
        $('#vat_types input:checked').removeAttr('checked');
        $('#po_types input:checked').removeAttr('checked');
       $('.custom-select-search').val('').selectpicker("refresh");
       $('#from_date').val('');
       $('#to_date').val('');
        //$('#supplier_id option:first').prop('selected',true);
        
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open'); 
        PoundShopApp.commonClass.table.draw();
        $('.filter_count').html('');
       
    })

   
    $('#search_data').keyup(function()
    {
         var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
            PoundShopApp.commonClass.table.search($(this).val()).draw() ;
        }
    }) ;  
window.PoundShopApp = window.PoundShopApp || {}
window.PoundShopApp.poundShopTotes = new poundShopTotes();

})(jQuery);


//for checkbox all and none case
 function advanceSearch()
{ 
    var is_status_checked=0;
    var date_filter=0;
    var supplier_filter=0;
    var sku_filter=0;
    var country_filter=0;
    var vat_type_filter=0;
    var po_types_filter=0;
    if ($('#from_date').val()=='') 
    {
       
        is_status_checked=0;
        date_filter=0;
    }
    else
    {
        if ($('#to_date').val()=='') 
        {
           
            bootbox.alert({
                title: "Alert",
                message: "Please enter to date.",
                size: 'small'
             });
            return false;
        }
        else{
            is_status_checked++;
            date_filter=1;

        }
    }
     if ($('#supplier_id').val()!='') 
   {
       
        is_status_checked++;
        supplier_filter=1;
    }

     if ($('#sku').val()!='') 
    {
       
        is_status_checked++;
        sku_filter=1;
    }

     if ($('#country_id').val()!='') 
    {
       
        is_status_checked++;
        country_filter=1;
    }
    

    $("#vat_types").find("input[type='checkbox']").each(function(){
     
        if ($(this).prop('checked')==true){ 
           vat_type_filter=1;
            is_status_checked++;
        }
    });

     $("#po_types").find("input[type='checkbox']").each(function(){
        
        if ($(this).prop('checked')==true){ 
           po_types_filter=1;
            is_status_checked++;
        }
    });
    if(vat_type_filter==0 && country_filter==0 && sku_filter==0 && supplier_filter==0 && date_filter==0 && po_types_filter==0)
    {
        bootbox.alert({
                title: "Alert",
                message: "Please select atleast one filter to search.",
                size: 'small'
        });
          $('.filter_count').html('');
        return false;
    }
    var vat_type=[];
    var po_type=[];
    $('#vat_types input:checked').map(function(){
    vat_type.push($(this).val());
    });
     $('#po_types input:checked').map(function(){
    po_type.push($(this).val());
    });
    console.log(vat_type);
    console.log(vat_type);
    var form_date=$('#from_date').val();
    var to_date=$('#to_date').val();
    var supplied_id=$('#supplier_id').val();
    

    var country_id=$('#country_id').val();
    var sku=$('#sku').val();
    
    console.log(form_date+'-'+to_date+'-'+supplied_id+'-'+vat_type.length+'-'+country_id+'-'+po_type);
     if(sku=='' && form_date=='' && to_date=='' && supplied_id=='' && vat_type.length>0 &&  country_id=='' && po_type.length>0)
    {
        bootbox.alert({
                title: "Alert",
                message: "Please select atleast one filter to search1.",
                size: 'small'
        });
        return false;
    }
    else
    {
        
        var counter=0;
        if(form_date!='' && to_date!='')
        {
            var startDate = new Date(form_date);
            var endDate = new Date(to_date);

            if (startDate < endDate){
            counter++;
            // $('.btn-blue').attr('disabled',false);
            }
            else{
              bootbox.alert({
                        title: "Alert",
                        message: "To date must be greater than from date.",
                        size: 'small'
                });
            //  $('.btn-blue').attr('disabled',true);
                return false;
            }
            
        }

        if(supplied_id!='')
        {
            counter++;
        }

        if(vat_type.length>0)
        {
            counter++;
        }

        if(country_id!='')
        {
            counter++;
        }

        if(po_type.length>0)
        {
            counter++;
        }

         if(sku!='')
        {
            counter++;
        }
        if(counter>0)
            $('.filter_count').html(' ('+counter+')');
         PoundShopApp.commonClass.table.draw() ;      
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open');    
        /*var dataString = $("#tax-report-form").serialize();
         $.ajax({
        type: "POST",
        url: $("#tax-report-form").attr("action"),
        data: dataString,
        processData: false,

            // processData: false,
            // contentType: false,
            // cache: false,
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                //console.log(response);return false;
                $('.btn-blue').attr('disabled', false);
                $("#page-loader").hide();
                  $('.load-data').html(response.view);
                    $('#data_table').dataTable( {
                      "columns": [
                        { "searchable": false },
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,

                      ],
                       "paging":   false,
                        "bFilter": false,
                        "info":     false
                    } );
                     $('#btnFilter').removeClass('open');
                    $('.search-filter-dropdown').removeClass('open'); 
                    $('.card-flex-container').removeClass('filter-open'); 


            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
    });*/

    }
          
    
        
   
}


