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
    var bookingSlotContainer = $("#booking-slot-container");
    var searchTableFilterContainer = $("#search-table-container");
    var addProductBtn=$("#add-product-link");
    var bookingStatus = $(".status").val();
    var createBookingBtn = $("#create-boooking-button");
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
                {"orderable": false, "searchable": false}
            ],
            bPaginate: false,
            fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                        //  $(nRow).find('td:eq(8)').css("text-align","right");      
                $(nRow).find('td:eq(9)').css("text-align","right");      
            },
            fnDrawCallback: function (oSettings, json) {
               //  console.log(oSettings);
            },
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                /*
                 * Calculate the total market share for all browsers in this table (ie inc. outside
                 * the pagination)
                 */
                var iTotalSkus = 0
                var iTotalEstimatedVariants = 0;
                var iEssentialProducts = 0
                var iTotalSeasonal = 0
                var iTotalShortDate = 0;
                var iTotalQty = 0;
                var iTotalValue = 0;
                for (var i = 0; i < aaData.length; i++)
                {
                    iTotalSkus += parseInt(aaData[i][3]);
                    iTotalEstimatedVariants += parseInt(aaData[i][4]);
                    iEssentialProducts += parseInt(aaData[i][5]);
                    iTotalSeasonal += parseInt(aaData[i][6]);
                    iTotalShortDate += parseInt(aaData[i][7]);
                    iTotalQty += parseInt(aaData[i][8]);
                    iTotalValue += parseFloat(aaData[i][9].replace('&#163;',''));

                }
                /* Modify the footer row to match what we want */
                var nCells = nRow.getElementsByTagName('th');
                nCells[3].innerHTML = parseInt(iTotalSkus);
                nCells[4].innerHTML = parseInt(iTotalEstimatedVariants);
                nCells[5].innerHTML = parseInt(iEssentialProducts);
                nCells[6].innerHTML = parseInt(iTotalSeasonal);
                nCells[7].innerHTML = parseInt(iTotalShortDate);
                nCells[8].innerHTML = parseInt(iTotalQty);
                nCells[9].innerHTML = '<span style="float:right">'+'&#163;'+parseFloat(iTotalValue).toFixed(2)+'</span>';
            },
            "ajax": {
                url: $("#bookingPOUrl").val(),
                type: "GET", // method  , by default get
                "data": function (d)
                {
                    d.page = (d.start + d.length) / d.length;
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
        
        var options={
            format: 'dd-M-yyyy',
            todayHighlight: true,
            autoclose: true,
            startDate: '-0m'
         };
         $("#book_date").datepicker(options).on("changeDate", function(e) {
            $('.datepicker').hide();
            $('#book_date').valid();
            $('#view_date').val($(this).val());
            weekBookingTable.draw();
        });
         
         if(bookingStatus == 1){ // with
             addProductBtn.show();
             createBookingBtn.text("Reserve Selected PO");
         }else if(bookingStatus == 2){ //without
             addProductBtn.hide();
             createBookingBtn.text("Save");
         }else{ // 3 confirm
             addProductBtn.show();
             createBookingBtn.text("Book Selected PO");
         }
    };

    $("#supplier").change(function () {
        if ($(this).val() == "") {
            $("#po_listing_table tbody").html('<tr class="odd"><td colspan="12" class="dataTables_empty" valign="top">No Records Found</td></tr>');
        } else {
            poTable.draw();
        }

    });
    $('#search').keyup(function (event)
    {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            event.preventDefault();
            poTable.draw()
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
            addProductBtn.hide();
            createBookingBtn.text("Save");
        } else if (checkBoxValue == 1) //with
        {
            bookingSlotContainer.show();
            poContainer.show();
            searchTableFilterContainer.show();
            addProductBtn.show();
            createBookingBtn.text("Reserve Selected PO");
        } else { //confirm
           // bookingSlotContainer.hide();
            poContainer.show();
            searchTableFilterContainer.show();
            addProductBtn.show();
            createBookingBtn.text("Book Selected PO");
        }
        
        
        var url = new URL(addProductBtn.attr("href"));
        url.searchParams.set("selected_option", checkBoxValue); // setting your param
        var newUrl = url.href; 
        addProductBtn.attr("href",newUrl);
    
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
//            "supplier": {
//                required: true,
//            },
            "warehouse": {
                required:true
            },
            "book_date": {
                required: true
            },
            "slot": {
                required: true,                
            },
            "pallet": {
                required: true,
                digits:true,
                min:0.01,
               
            },
            "estimated_value": {
                required : true,
                min:0.01,
            }
        },
        messages: {
//            "supplier": {
//                required: "Please select supplier",
//            },
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
                min:"Please enter pallet.",
                
            },
            "estimated_value": {
                required: "Please enter estimated value.",
                min:"Please enter estimated value",
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

            $(".edit-po-items").each(function(){
                allVals.push($(this).attr('id'));
            });
            
            
            var dataString = $("#create-boooking-form").serialize();
            if (bookingStatus == 2 || bookingStatus == 1) { // without
                if (bookingStatus == 1) {
                    if (allVals < 1) {
                        bootbox.alert({
                            title: "Alert",
                            message: "Please add Atleast One Purchase Order.",
                            size: 'small'
                        });
                        return false;

                    }
                }
            } else { //confirm
                if (allVals < 1) {
                    bootbox.alert({
                        title: "Alert",
                        message: "Please Add Atleast One Purchase Order.",
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
                        setTimeout(function () {
                            window.location.href = WEB_BASE_URL + '/booking-in';
                        }, 1000);
                    } else {
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
    
    /**
     * @author Hitesh TAnk
     * @desc remove po from booking
     * 
     */
     $(document).on('click', '.btn-delete', function (event) {
            event.preventDefault();
            var $currentObj = $(this);
            var id = $(this).attr("id");
            bootbox.confirm({ 
                        title: "Confirm",
                        message: "Are You Sure You Want To Delete Selected Records? This Process Cannot Be Undone.",
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
                            if(result){
                                $.ajax({
                                    url: BASE_URL + 'api-booking-po-delete',
                                    type: "post",
                                    //processData: false,
                                    data:{id:id},
                                    headers: {
                                       Authorization: 'Bearer ' + API_TOKEN,
                                    },
                                    beforeSend: function () {
                                        $("#page-loader").show();
                                    },
                                    success: function (response) {
                                            $("#page-loader").hide();
                                            if (response.status == 1) {
                                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                                poTable.draw();
                                            }else{
                                                PoundShopApp.commonClass._displayErrorMessage(response.message);
                                                poTable.draw();
                                            }

                                    },
                                    error: function (xhr, err) {
                                       $("#page-loader").hide();
                                       PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                                    }

                                });
                            }
                        }
             });
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