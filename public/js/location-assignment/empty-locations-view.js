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
     weekBookingTable = $('#emptyLocationsTable').DataTable({
            bPaginate : true,
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
             "dom": '<"custom-table-header"><"custom-table-body"rt><"custom-table-footer"ipl>',
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
              
            },
            "ajax": {
                url: BASE_URL+'api-empty-locations',
                type: "GET", // method  , by default get
                "data": function (d)
                {
                    d.page = (d.start + d.length) / d.length;
                  
                },
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                    'Panel': 'web'
                },
                error: function (xhr, err) {
                    $("#weekly-booking-table_processing").hide();
                    $("#emptyLocationsTable >tbody").html('<tr class="odd"><td colspan="9" class="dataTables_empty" valign="top">No Records Found</td></tr>');
                    
                }
            }
        });
        
    };

     window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopBooking = new poundShopBooking();

})(jQuery);
