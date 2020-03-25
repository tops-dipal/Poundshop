/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'booking_table';
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
            null,
            null,
            null,
            null,
            null,
        ];
        var order_coloumns = [[0, "asc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTableBookin(PoundShopApp.commonClass.table,'booking_table','api-booking',field_coloumns,order_coloumns,undefined,undefined,'Search by Carton Name','','api-booking-week-list');    
    };
 
    $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });
    $(document).on('click','.bookin_next',function(e)
    {   
        var current_date=$('#view_date').val();
        var date_data=$('#next_date').val();
        var nextWeekStartDate=PoundShopApp.commonClass.get_new_week_date(date_data,'start',7);  
        var nextWeekEndDate=PoundShopApp.commonClass.get_new_week_date(nextWeekStartDate,'end',7);
        $('#view_date').val(nextWeekStartDate);
        $('#prev_date').val(nextWeekStartDate);
        $('#next_date').val(nextWeekEndDate);
        getStartEndDate($('#prev_date').val(),$('#next_date').val());
        PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
    });

    $(document).on('click','.bookin_prev',function(e)
    {
        var current_date=$('#view_date').val();    
        var date_data=$('#prev_date').val();
        var prevWeekStartDate=PoundShopApp.commonClass.getPreviousWeekDates(date_data,'start');    
        var prevWeekEndDate=PoundShopApp.commonClass.getPreviousWeekDates(prevWeekStartDate,'end');    
        $('#view_date').val(prevWeekStartDate);
        $('#prev_date').val(prevWeekStartDate);
        $('#next_date').val(prevWeekEndDate);
        getStartEndDate($('#prev_date').val(),$('#next_date').val());
        PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
    });
   
    //Search outside the datatables
    $('#search_data').keyup(function(event)
    {
            var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
                PoundShopApp.commonClass.table.search($(this).val()).draw() ;
            }
            if (keycode=='8')
            {
                //$('#search_data').val('');
                PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
            }
    })
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);

function getStartEndDate(start,end)
{
    var months=["Jan","Feb","Mar","Apr","May","Jun","Jul",
    "Aug","Sep","Oct","NOv","Dec"];
    var startDate    = new Date(start);
    var startDate=startDate.getDate()+"-"+months[startDate.getMonth()]+"-"+startDate.getFullYear();
    $('#start_date').text(startDate);
    var endDate    = new Date(end);
    var endDate=endDate.getDate()+"-"+months[endDate.getMonth()]+"-"+endDate.getFullYear();
    $('#end_date').text(endDate);
}
function advanceSearch()
{   
    var booking_date=$('#booking_date').val();
    var is_status_checked=0;
    var completed=0;
    var not_completed=0;
    if (document.getElementById('booking_status_comp').checked) 
    {
        is_status_checked++;
        completed=1;
    }

    if(document.getElementById('booking_status_not_comp').checked)
    {
        is_status_checked++;
        not_completed=1;
    }
    
    if(booking_date=='' && is_status_checked==0)
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
        var counter=0;
        if(booking_date!='')
        {

            counter++;
        }
        if(booking_date=='')
        {
            booking_date=$('#view_date').val();
        }

        if(completed!=0)
        {
            counter++;
        }

        if(not_completed!=0)
        {
            counter++;
        }                
        
        if(booking_date!='' && booking_date!= undefined && booking_date!='0')
        {
            var date_data=PoundShopApp.commonClass.get_new_date_from_string_date(booking_date);//current day in format
        }
        else
        {
            var date_data=PoundShopApp.commonClass.get_new_date_from_string_date($('#url_date').val());//current day in format
        }

        var next_date=PoundShopApp.commonClass.get_new_week_date(date_data,"end");//next day in format
        var prev_date=PoundShopApp.commonClass.get_new_week_date(date_data,"start");//prev day in format
        if($('#booking_date').val()!='')
        {
            $('#view_date').val(date_data);
            $('#next_date').val(next_date);
            $('#prev_date').val(prev_date);
            getStartEndDate($('#prev_date').val(),$('#next_date').val());
        }
        
        
        $('.filter_count').html(' ('+counter+')');
        PoundShopApp.commonClass.table.draw() ;      
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open');     
    }
}
$('.cancle_fil').click(function()
{
    $('.filter_count').html('');
    document.getElementById("booking_date").value = "";
    document.getElementById("booking_status_comp").checked = false;
    document.getElementById("booking_status_not_comp").checked = false;
    //back to previous thing
    var d = new Date();
    var strDate = d.getFullYear() + "-" + (d.getMonth()+1) + "-" + d.getDate();
   var date_data=strDate;
   var next_date=PoundShopApp.commonClass.get_new_week_date(date_data,"end");//next day in format
    var prev_date=PoundShopApp.commonClass.get_new_week_date(date_data,"start");//prev day in format
    $('#view_date').val(date_data);
    $('#next_date').val(next_date);
    $('#prev_date').val(prev_date);
    getStartEndDate($('#prev_date').val(),$('#next_date').val());
   /* $('#view_date').val(date_data);
    $('#next_date').val(next_date);
    $('#prev_date').val(prev_date);*/

    $('#btnFilter').removeClass('open');
    $('.search-filter-dropdown').removeClass('open'); 
    $('.card-flex-container').removeClass('filter-open'); 
    $('#search_data').val('');
    PoundShopApp.commonClass.table.draw() ;      
}) ;

//for checkbox all and none case
function updateDataTableSelectAllCtrl(table)
{
    var $table             = table.table().node();
    var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
    var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);   

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
        chkbox_select_all.checked = true;
        if('indeterminate' in chkbox_select_all)
        {
            chkbox_select_all.indeterminate = true;
        }
    }
}

$(document).ready(function ()
{   
    var rows_selected = [];   
    var table = PoundShopApp.commonClass.table; 
    $('#carton_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
});

//clear filter
$('.refresh').on('click',function()
{    
    $('#search_data').val('');
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
});