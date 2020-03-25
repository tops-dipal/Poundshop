/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    
    var weekNo=0;
    var poundShopBooking = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
        });
    };
    var c = poundShopBooking.prototype;

    c._initialize = function ()
    {
     weekBookingTable = $('#weekly-booking-table').DataTable({
            bFilter: false,
            bInfo: false,
            "processing": true,
            "oLanguage": {
                "sProcessing": '<img src="' + WEB_BASE_URL + '/img/loader.gif" width="40">',
                "sEmptyTable": "No Records Found",
            },
            language: {
                search: "_INPUT_",
                "paginate": {
                  "previous": "<span class='icon-moon icon-Drop-Down-1'></span>",
                  "next": "<span class='icon-moon icon-Drop-Down-1'></span>"
                }
            },
            "dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"i<"blank-left"><"bookin_paging"<"bookin_prev"><"bookin_next">>>',
            "serverSide": true,
            responsive: true,
            columns: [
                null,
                null,
                null,
                null,
                null,
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                null,
                null
              ],
            bPaginate: false,
            fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).find('td:eq(9)').css("text-align","right");      
               
            },
            fnDrawCallback: function (oSettings, json) {
                 var api = this.api()
                 var json = api.ajax.json();
                 
                  $("#weekNo").text('Week No. :'+json.weekNo);
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                
                /*
                 * Calculate the total market share for all browsers in this table (ie inc. outside
                 * the pagination)
                 */
                var iTotalBooking=0;
                var iTotalPallets=0;
                var iTotalSkus = 0;
                var iTotalVariants=0;
                var iEssentialProducts = 0;
                var iTotalSeasonal = 0;
                var iTotalShortDate = 0;
                var iTotalQty = 0;
                var iTotalValue = 0;
                for (var i = 0; i < aaData.length; i++)
                {
                    iTotalBooking += parseInt(aaData[i][1]);
                    iTotalPallets += parseInt(aaData[i][2]);
                    iTotalSkus += parseInt(aaData[i][3]);
                    iTotalVariants += parseInt(aaData[i][4]);
                    iEssentialProducts += parseInt(aaData[i][5]);
                    iTotalSeasonal += parseInt(aaData[i][6]);
                    iTotalShortDate += parseInt(aaData[i][7]);
                    iTotalQty += parseInt(aaData[i][8]);
                    iTotalValue += parseFloat(aaData[i][9].replace('&#163;',''));

                }
                /* Modify the footer row to match what we want */
                var nCells = nRow.getElementsByTagName('th');
                nCells[1].innerHTML = parseInt(iTotalBooking);
                nCells[2].innerHTML = parseInt(iTotalPallets);
                nCells[3].innerHTML = parseInt(iTotalSkus);
                nCells[4].innerHTML = parseInt(iTotalVariants);
                nCells[5].innerHTML = parseInt(iEssentialProducts);
                nCells[6].innerHTML = parseInt(iTotalSeasonal);
                nCells[7].innerHTML = parseInt(iTotalShortDate);
                nCells[8].innerHTML = parseInt(iTotalQty);
                nCells[9].innerHTML = '<span style="float:right">'+'&#163;'+parseFloat(iTotalValue).toFixed(2)+'</span>';
            },
            "ajax": {
                url: $("#weekBookingURL").val(),
                type: "GET", // method  , by default get
                "data": function (d)
                {
                    d.page = (d.start + d.length) / d.length;
                    d.book_date_ur=$('#view_date').val();
                    
                },
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                    'Panel': 'web'
                },
                error: function (xhr, err) {
                    $("#weekly-booking-table_processing").hide();
                    $("#weekly-booking-table tbody").html('<tr class="odd"><td colspan="10" class="dataTables_empty" valign="top">No Records Found</td></tr>');
                    
                }
            }
        });
        
    };
$(document).on('click','.bookin_next',function(e)
    {   
        var current_date=$('#view_date').val();
        var date_data=$('#next_date').val();
        var nextWeekStartDate=PoundShopApp.commonClass.getNextWeekDates(date_data,'start');  
        var nextWeekEndDate=PoundShopApp.commonClass.getNextWeekDates(nextWeekStartDate,'end');
        $('#view_date').val(nextWeekStartDate);
        $('#prev_date').val(nextWeekStartDate);
        $('#next_date').val(nextWeekEndDate);
        weekBookingTable.draw();
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
        weekBookingTable.draw();
    });
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopBooking = new poundShopBooking();

})(jQuery);