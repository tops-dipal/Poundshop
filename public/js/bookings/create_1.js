/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function ($)
{
    "user strict";
    var poTable;
    var poContainer = $("#po-booking-container");
    var weekTableContainer = $("#week-booking-container");
    var bookingSlotContainer = $("#booking-slot-container");
    var searchTableFilterContainer = $("#search-table-container");
    var bookingStatus = 0;
    var createBookingBtn = $("#create-boooking-button");
    var viewbookingIns = $(".view-bookingIns");
    
    
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
        var options={
            format: 'dd-M-yyyy',
            todayHighlight: true,
            autoclose: true,
            startDate: '-0m',
         };
         $("#book_date").datepicker(options);
        $('.custom-select-search').selectpicker({
            liveSearch: true,
            size: 10
        });
        $("#book_date").datepicker(options).on("changeDate", function(e) {
            $('.datepicker').hide();
            $('#book_date').valid();
            $('#view_date').val($(this).val());
            weekBookingTable.draw();
        });
        poTable = $('#po_listing_table').DataTable({
            bFilter: false,
            bInfo: false,
            "processing": true,
            "oLanguage": {
                "sProcessing": '<img src="' + WEB_BASE_URL + '/img/loader.gif" width="40">',
                "sEmptyTable": "No Records Found",
            },
            "serverSide": true,
            responsive: true,
            columns: [
                {"orderable": false, "searchable": false},
                null,
                null,
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
                {"orderable": false, "searchable": false},
            ],
            bPaginate: false,
            fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
                $("#po_listing_table > tbody").html('');
            },
            fnDrawCallback: function (oSettings, json) {
                $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
            },
            "ajax": {
                url: $("#searchUrl").val(),
                type: "GET", // method  , by default get
                "data": function (d)
                {
                    d.page = (d.start + d.length) / d.length;
                    d.supplier_id = $("#supplier").val();
                    d.search = $('#search').val();
                    d.cancelled_po = $('#cancelled').is(":checked") ? 1 : 0;
                    d.booking_id = $("#booking_id").val();
                },
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                    'Panel': 'web'
                },
                error: function (xhr, err) {
                    $("#po_listing_table_processing").hide();
                    $("#po_listing_table tbody").html('<tr class="odd"><td colspan="12" class="dataTables_empty" valign="top">No Records Found</td></tr>');
                }
            }
        });


        
    };

    $("#supplier").change(function () {
        if ($(this).val() == "") {
            $("#po_listing_table tbody").html('<tr class="odd"><td colspan="12" class="dataTables_empty" valign="top">No Records Found</td></tr>');
        } else {
            poTable.draw();
        }

    });
    $('#search').keydown(function (event)
    {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            poTable.draw()
            event.stopImmediatePropagation();
            return false;
        }
    })
    $('#cancelled').change(function (event)
    {
        poTable.draw()
    })


    /**
     * @author Hitesh Tank
     * @desc : visible add slot detail while change the ration button
     */
    $(".status").change(function (e) {
        var checkBoxValue = $(this).val();
        if (checkBoxValue == 2) //without
        {
            bookingSlotContainer.show();
            poContainer.hide();
            searchTableFilterContainer.hide();
            weekTableContainer.show();
            viewbookingIns.show();
            createBookingBtn.text("Save");
        } else if (checkBoxValue == 1) //with
        {
            bookingSlotContainer.show();
            poContainer.show();
            searchTableFilterContainer.show();
            createBookingBtn.text("Reserve Selected PO");
            weekTableContainer.show();
            viewbookingIns.show();
        } else { //confirm
        //    bookingSlotContainer.hide();
            poContainer.show();
            searchTableFilterContainer.show();
            createBookingBtn.text("Book Selected PO");
            weekTableContainer.hide();
            viewbookingIns.hide();
        }
    });


    /**
     * @author Hitesh Tank
     * @desc : create a booking
     */
    $("form#create-boooking-form").validate({
        focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function (form, validator) {

            if (!validator.numberOfInvalids())
                return;

            $('html, body').animate({
                scrollTop: $(validator.errorList[0].element).offset().top - 30
            }, 1000);
        },
        errorElement: 'span',
        errorClass: 'invalid-feedback', // default input error message class
        ignore: [],
        rules: {
            "supplier": {
                required: function(element){
                    if($("booking_id").val()!==""){
                        return false;
                    }else{
                        return true;
                    }
                },
            },
            "warehouse": {
                required: true,
            },
            "book_date": {
                required:true,
            },
            "slot": {
                required:true,
            },
            "pallet": {
                required:true,
                digits: true,
            },
            "estimated_value": {
                required: true,
            }
        },
        messages: {
            "supplier": {
                required: "Please select supplier",
            },
            "warehouse": {
                required: "Please select warehouse.",
            },
            "book_date": {
                required: "Please select book date.",
            },
            "slot": {
                required: "Please select slot.",
            },
            "pallet": {
                required: "Please enter pallet.",
            },
            "estimated_value": {
                required: "Please enter estimated value.",
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "supplier") {
                error.insertAfter(element.closest(".dropdown"));
            } else {
                error.insertAfter(element);
            }
        },
        highlight: function (element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        submitHandler: function (form) {
            var allVals = [];
            if($("booking_id").val() !== ""){
                $("input[name='ids[]']:checked").each(function () {
                    allVals.push($(this).attr('value'));
                });
                $("input[name='added_ids[]']:checked").each(function () {
                    allVals.push($(this).attr('value'));
                });
            }else{
                $("input[name='ids[]']:checked").each(function () {
                    allVals.push($(this).attr('value'));
                });
            }
            
            var dataString = $("#create-boooking-form").serialize();
            if (bookingStatus == 2 || bookingStatus == 1) { // without
                if(bookingStatus == 1){
                   if (allVals < 1) {
                        bootbox.alert({
                            title: "Alert",
                            message: "Please Select Atleast One Purchase Order.",
                            size: 'small'
                        });
                        return false;
                        
                    } 
                }
            } else { //confirm
                if (allVals < 1) {
                    bootbox.alert({
                        title: "Alert",
                        message: "Please Select Atleast One Purchase Order.",
                        size: 'small'
                    });
                    return false;
                }
                
            }
            
            
            $('#create-boooking-buttonn').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#create-boooking-form").attr("action"),
                data: dataString,
                processData: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $('#create-boooking-button').attr('disabled', false);
                    $("#page-loader").hide();
                    if (response.status == 1) {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        if(response.data.status == 2)
                        {
                            setTimeout(function () {
                                window.location.href = WEB_BASE_URL + '/booking-in/';
                            }, 1000);
                        }else{
                            setTimeout(function () {
                            window.location.href = WEB_BASE_URL + '/booking-in/' + response.data.id + '/edit';
                        }, 1000);
                        }
                        
                    }else{
                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }
                    
                },
                error: function (xhr, err) {
                    $('#create-boooking-button').attr('disabled', false);
                    $("#page-loader").hide();
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });

        }
    });
    $("#create-boooking-button").click(function (event) {
        bookingStatus = $("input[name='status']:checked").val()
        event.preventDefault();
        $("form#create-boooking-form").submit();
    });
//pallet keypress
    $(document).on("keydown", "#pallet", function (e) {
        var key = e.charCode || e.keyCode || 0;            // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
        if (!((e.ctrlKey && e.key == 'a') || key == 8 ||
                key == 9 ||
                key == 13 ||
                key == 46 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105))) {
            e.stopImmediatePropagation();
            return false;
        }
    });
    //estimated value
    $(document).on("keypress", "#estimated_value", function (e) {
        if (((e.ctrlKey && e.key == 'a') || (e.which != 46 || (e.which == 46 && $(this).val() == '')) ||
                $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
            e.stopImmediatePropagation();
            return false;
        }

    });
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopBooking = new poundShopBooking();

})(jQuery);