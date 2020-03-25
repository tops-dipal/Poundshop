/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var searchTable;
    var currentRequest = null;
    var typingTimer;                //timer identifier
    var doneTypingInterval = 5000;  //time in ms, 5 second for example
    var xhr;
    
    
    var options={
            format: 'dd-M-yyyy',
            todayHighlight: true,
            autoclose: true,
            startDate: '-0m'
         };


    var poundShopCartons = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();

            $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function (e) {
                $('.datepicker').hide();
                $('.best_before_date').valid();
            });
        });
    };
    var c = poundShopCartons.prototype;

    function randomNumberFromRange(min, max)
    {
        return Math.floor(Math.random() * (max - min + 1) + min);
    }

    c._initialize = function ()
    {
        $("#po_date,#po_cancel_date,#exp_deli_date").datepicker(options);

        CKEDITOR.replace('term_condition', {
            toolbar: [
                {name: 'document', items: ['Source']},
                ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo'], // Defines toolbar group without name.
                //'/', // Line break - next group will be placed in new line.
                {name: 'basicstyles', items: ['Bold', 'Italic']}
            ]
        });
        CKEDITOR.replace('term_supplier_condition', {
            toolbar: [
                {name: 'document', items: ['Source']},
                ['Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo'], // Defines toolbar group without name.
                //'/', // Line break - next group will be placed in new line.
                {name: 'basicstyles', items: ['Bold', 'Italic']}
            ]
        });

    };

    //send email po
    $("#sendEmailBtn").click(function (event) {
        event.preventDefault();

        $.ajax({
            url: BASE_URL + 'api-purchase-order-send-email-pdf',
            type: "post",
            dataType: "json",
            data: {po_id: $("#po_id").val()},
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
                } else {
                    PoundShopApp.commonClass._displayErrorMessage(response.message);
                }
            },
            error: function (xhr, err) {
                $("#page-loader").hide();
                $('#save-po-btn').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
    });

    //recalculate import po item content
    $("#country_id").on('focus', function () {
        // Store the current value on focus and on change
        previous = this.value;
    }).change(function (event) {

        event.preventDefault();
        var currentObj = $(this);
        if ($("#po_import_type").val() == 1) {
            return false;
        }

        bootbox.confirm({
            title: "Confirm",
            message: "Are you sure to change country? Item's data will recalculate.",
            buttons: {
                cancel: {
                    label: 'No',
                    className: 'btn-gray'
                },
                confirm: {
                    label: 'Yes',
                    className: 'btn-red'
                }
            },
            callback: function (result)
            {
                if (result) {
                    $.ajax({
                        url: BASE_URL + 'api-purchase-order-recalculate-items',
                        type: "post",
                        dataType: "json",
                        data: {po_id: $("#po_id").val(), 'country_id': currentObj.val()},
                        headers: {
                            Authorization: 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                            currentObj.prop("disabled", true);
                        },
                        success: function (response) {
                            $("#page-loader").hide();
                            currentObj.prop("disabled", false);
                            if (response.status == 1) {
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                $("#overall_total_no_cubes").text(response.data.total_no_of_cubes)
                                $("#remaining_space").text(response.data.remaining_space)
                                $("#total_cost").text(response.data.total_cost)
                                $("#overall_import_duty").text(response.data.total_import_duty)
                                $("#total_space").text(response.data.total_space)
                                $("#cost_per_cube").text(response.data.cost_per_cube)
                                $("#overall_total_delivery").text(response.data.total_delivery_charge)
                                $("#total_delivery").val(response.data.total_delivery_charge)
                                $("#sub_total").text(response.data.sub_total);
                                $("#total_margin").text(response.data.total_margin.toFixed(2) + "%");
                                $("#po-items-container").html(response.data.data);
                            } else {
                                PoundShopApp.commonClass._displayErrorMessage(response.message);
                            }
                        },
                        error: function (xhr, err) {
                            currentObj.prop("disabled", false);
                            $('#country_id option:eq("' + previous + '")').prop('selected', true)
                            $("#page-loader").hide();
                            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                        }

                    });
                } else {
                    currentObj.prop("disabled", false);
                    $('#country_id option:eq("' + previous + '")').prop('selected', true)
                }
            }
        });


    })

    //revise po
    $("#revise-po-btn").click(function (event) {
        event.preventDefault();

        $.ajax({
            url: BASE_URL + 'api-purchase-orders-revise',
            type: "post",
            dataType: "json",
            data: {po_id: $("#po_id").val()},
            headers: {
                Authorization: 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
                $('#save-po-btn').attr('disabled', false);
            },
            success: function (response) {
                $("#page-loader").hide();

                if (response.status == 1) {
                    $("#po_status").prop("disabled", false);
                    $('#po_status option:eq(0)').prop('selected', true)
                    $("#po_cancel_date").val("");
                    $("#field-set").prop('disabled', false)
                    $("#country_id").prop('disabled', false)
                    $(".tab-click").each(function () {
                        if ($(this).hasClass("active") && $(this).attr("href") == "#items") {
                            $("#save-po-btn").show();
                            $("#show-modal-btn").show()
                        }
                    })
                    $("#delete-many").show();
                    $("#po-items-container").find(".removeRow").show();
                    $("#hidden_po_status").val(1);
                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    PoundShopApp.commonClass.table.draw();


                } else {
                    PoundShopApp.commonClass._displayErrorMessage(response.message);
                }
            },
            error: function (xhr, err) {
                $("#page-loader").hide();
                $('#save-po-btn').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }

        });
    })

    // general po update
    $("#create-po-form").validate({
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
                required: true,
            },
            "supplier_contact": {
                required: true,
            },
            "po_status": {
                required: true,
            },
            "recev_warehouse": {
                required: true,
            },
            "po_import_type": {
                required: true
            },
//                "supplier_order_number":{
//                     required:true
//                }
        },
        messages: {
            "supplier": {
                required: "Please select supplier.",
            },
            "supplier_contact": {
                required: "Please select supplier contact.",
            },
            "po_status": {
                required: "Please select po status.",
            },
            "recev_warehouse": {
                required: "Please select receiving warehouse.",
            },
            "po_import_type": {
                required: "Please select U.K PO/Import PO.",
            },
//                "supplier_order_number":{
//                     required:"Please enter supplier order number."
//                }


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
            var dataString = $("#create-po-form").serialize();
            $('#create-po-button').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#create-po-form").attr("action"),
                data: dataString,
                processData: false,
//                    contentType: false,
//                    cache: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $('#create-po-button').attr('disabled', false);
                    $("#page-loader").hide();
                    $("#po-items-table > tbody .best_before_date").datepicker(options);
                    $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function (e) {
                        $('.datepicker').hide();
                        $('.best_before_date').valid();
                    });
                    if (response.status == 1) {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        $("#hidden_po_status").val(response.data.po_status);
                        if (response.data.po_status == 10)
                        {

                            $("#po_cancel_date").val(response.data.po_cancel_date);
                            $("#po_cancel_date").attr("disabled", true)
                            $("#po_status").attr("disabled", true)
                            if ($("#country_id").val() != 230)
                                $("#country_id").prop('disabled', true)

                        }
                        if (response.data.po_status > 5) {
                            $("#field-set").prop('disabled', true)
                            $("#save-po-btn").hide();
                            $("#show-modal-btn").hide()
                            $("#delete-many").hide();
                            $("#po-items-container").find(".removeRow").hide();
                            if ($("#country_id").val() != 230)
                                $("#country_id").prop('disabled', true)
                        } else {
                            $("#field-set").prop('disabled', false)
                            if ($("#country_id").val() != 230)
                                $("#country_id").prop('disabled', false)
//                                $("#save-po-btn").show();
//                                $("#show-modal-btn").show()
                            $("#delete-many").show();
                            $("#po-items-container").find(".removeRow").show();
                        }
//                            setTimeout(function () {
//                                location.reload(1);
//                            }, 1000);
                    }
                },
                error: function (xhr, err) {
                    $('#create-po-button').attr('disabled', false);
                    $("#page-loader").hide();
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });

        }
    });
    $("#create-po-button").click(function () {
        $("#create-po-form").submit();
    });
    $("#update-term-button").click(function () {
        $("#po-terms-form").submit();
    });
    //save po content
    $('form#save-po-form').on('submit', function (event) {

        //Add validation rule for dynamically generated name fields
        $('#po-items-container .po_textbox').each(function () {

            if ($(this).hasClass('qty_per_box') || $(this).hasClass('total_box') || $(this).hasClass('total_quantity')) {
                $(this).rules("add",
                        {
                            required: true,
                            digits: true,
                            min: 1,
                            messages: {
                                required: "Required",
                                digits: "Digit only",
                                min: '> 1',
                                maxlength: '< {0} char',
                            }
                        });
            } else if ($(this).hasClass('unit_price') || $(this).hasClass('expected_mros') || $(this).hasClass('sel_qty') || $(this).hasClass('sel_price')) {
                $(this).rules("add",
                        {
                            required: true,
                            min: 0.01,
                            messages: {
                                required: "Required", min: 'signed value'
                            }
                        });
            } else if (!($(this).hasClass('supplier_sku') || $(this).hasClass('barcode') || $(this).hasClass('best_before_date'))) {
                $(this).rules("add",
                        {
                            required: true,
                            messages: {
                                required: "Required",
                            }
                        });
            }

        });

        $('#po-items-container .po_textbox').each(function () {
            $(this).rules("remove", "maxlength");
        });



    });

    $("#save-po-form").validate({
        focusInvalid: true, // do not focus the last invalid input
        submitHandler: function (form) {
//                for (instance in CKEDITOR.instances) {
//                    CKEDITOR.instances[instance].updateElement();
//                }
            //  var formData = $("#po-terms-form").serialize();
            //  $('#update-term-button').attr('disabled', true);
            var TableData = new Array();
            if ($("#po_import_type").val() == 1) {
                $('#po-items-container tr').each(function (row, tr) {
                    var variantVal = $(tr).find(".variant").is(":checked") ? 1 : 0;
                    TableData[row] = {
                        "id": $(tr).find('td:eq(0)').attr("id"),
                        "product_id": $.trim($(tr).find('input[type="checkbox"]').val())
                        , "supplier_sku": $.trim($(tr).find('.supplier_sku').val())
                        , "is_listed_on_magento" :$.trim($(tr).find('.is_listed_on_magento').val())
                        , "new_barcode": $.trim($(tr).find('.barcode').val())
                        , "bar_code": $.trim($(tr).find('.barcode').attr("data-barcode"))
                        , "variant": $.trim(variantVal)
                        , "qty_per_box": $.trim($(tr).find('.qty_per_box').val())
                        , "total_box": $.trim($(tr).find('.total_box').val())
                        , "total_quantity": $.trim($(tr).find('.total_quantity').val())
                        , "unit_price": $.trim($(tr).find('.unit_price').val())
                        , "total_product_cost": $.trim($(tr).find('.total_product_cost').text())
                        , "vat": $.trim($(tr).find('.vat').text())
                        , "standard_rate": $.trim($("#hidden_standard_rate").val())
                        , "zero_rate": $.trim($("#hidden_standard_rate").val())
                        , "standard_rate_value": $.trim($(tr).find('.standard_rate').val())
                        , "zero_rate_value": $.trim($(tr).find('.zero_rate').val())
                        , "best_before_date": $.trim($(tr).find('.best_before_date').val())
                        , "expected_mros": $.trim($(tr).find('.expected_mros').val())
                        , "sel_qty": $.trim($(tr).find('.sel_qty').val())
                        , "sel_price": $.trim($(tr).find('.sel_price').val())
                        , "mros": 20 //$(tr).find(".mros").val()
                        , "landed_product_cost": $.trim($(tr).find('.landed_product_cost').val())
                        , "vat_type": $.trim($(tr).find('.vat_type').val())
                        , "net_selling_price_excluding_vat": $.trim($(tr).find('.net_selling_price_excluding_vat').val())
                        , "total_net_selling_price": $.trim($(tr).find('.total_net_selling_price').val())
                        , "total_net_profit": $.trim($(tr).find('.total_net_profit').val())
                        , "total_net_margin": $.trim($(tr).find('.total_net_margin').val())
                        , "po_import_type": $.trim($("#po_import_type").val())
                    }
                });
            } else {
                $('#po-items-container tr').each(function (row, tr) {
                    var variantVal = $(tr).find(".variant").is(":checked") ? 1 : 0;
                    TableData[row] = {
                        "id": $.trim($(tr).find('td:eq(0)').attr("id")),
                        "product_id": $.trim($(tr).find('input[type="checkbox"]').val())
                        , "supplier_sku": $.trim($(tr).find('.supplier_sku').val())
                        , "is_listed_on_magento" :$.trim($(tr).find('.is_listed_on_magento').val())
                        , "new_barcode": $.trim($(tr).find('.barcode').val())
                        , "bar_code": $.trim($(tr).find('.barcode').attr("data-barcode"))
                        , "variant": $.trim(variantVal)
                        , "qty_per_box": $.trim($(tr).find('.qty_per_box').val())
                        , "total_box": $.trim($(tr).find('.total_box').val())
                        , "total_quantity": $.trim($(tr).find('.total_quantity').val())
                        , "cube_per_box": $.trim($(tr).find('.cube_per_box').val())
                        , "total_num_cubes": $.trim($(tr).find('.total_num_cubes').val())
                        , "unit_price": $.trim($(tr).find('.unit_price').val())
                        , "total_product_cost": $.trim($(tr).find('.total_product_cost').text())
                        , "import_duty": $.trim($(tr).find('.import_duty').attr("data-value"))
                        , "vat": $.trim($(tr).find('.vat').attr("data-value"))
                        , "vat_type": $.trim($(tr).find('.vat_type').val())
                        , "standard_rate": $.trim($("#hidden_standard_rate").val())
                        , "zero_rate": $.trim($("#hidden_standard_rate").val())
                        , "standard_rate_value": $.trim($(tr).find('.standard_rate').val())
                        , "zero_rate_value": $.trim($(tr).find('.zero_rate').val())
                        , "delivery_charge": $.trim($(tr).find('.total_delivery_charge').text())
                        , "landed_product_cost": $.trim($(tr).find('.landed_product_cost').text())
                        , "landed_price_in_pound": $.trim($(tr).find('.landed_price_in_pound').val())
                        , "best_before_date": $.trim($(tr).find('.best_before_date').val())
                        , "expected_mros": $.trim($(tr).find('.expected_mros').val())
                        , "sel_qty": $.trim($(tr).find('.sel_qty').val())
                        , "sel_price": $.trim($(tr).find('.sel_price').val())
                        , "mros": 20 //$(tr).find(".mros").val()
                        , "vat_in_amount": $.trim($(tr).find('.vat_in_amount').val())
                        , "import_duty_in_cost": $.trim($(tr).find('.import_duty_in_cost').val())
                        , "itd_vat": $.trim($(tr).find('.totalProductCostImportDutyDeliveryCharge').val())
                        , "total_vat": $.trim($(tr).find('.total_vat').val())
                        , "currency_exchange_rate": $.trim($(tr).find('.currency_exchange_rate').val())
                        , "total_net_selling_price": $.trim($(tr).find('.total_net_selling_price').val())
                        , "net_selling_price_excluding_vat": $.trim($(tr).find('.gross_sel_price_exc_vat').val())
                        , "total_net_profit": $.trim($(tr).find('.total_net_profit').val())
                        , "total_net_margin": $.trim($(tr).find('.total_net_margin').val())
                        , "po_import_type": $.trim($("#po_import_type").val())
                    }
                });
            }

            if (TableData.length < 1) {
                return false;
            }

            if (parseFloat(undefinedValue($("#hidden_min_po_amount").val())) > parseFloat(undefinedValue($("#sub_total").text()))) {
                bootbox.alert({
                    title: "Alert",
                    message: "PO amount is less than supplier minimum po amount.",
                    size: 'small'
                });
            }
            $.ajax({
                url: BASE_URL + 'api-purchase-orders-item-save',
                type: "post",
                dataType: "json",
                data: {data: JSON.stringify(TableData),
                    supplier_id: $("#supplier_id").val(),
                    po_id: $("#po_id").val(),
                    po_import_type: $("#po_import_type").val(),
                    'sub_total': $("#sub_total").text(),
                    'total_margin': $("#total_margin").text(),
                    'total_import_duty': $("#overall_import_duty").text(),
                    'total_delivery_charge': $("#overall_total_delivery").text(),
                    'total_space': $("#total_space").val(),
                    'cost_per_cube': $("#cost_per_cube").text(),
                    'total_number_of_cubes': $("#overall_total_no_cubes").text(),
                    'remaining_space': $("#remaining_space").text(),
                    'total_cost': $("#total_cost").text()
                },
                headers: {
                    Authorization: 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                    $('#save-po-btn').attr('disabled', false);
                },
                success: function (response) {
                    $("#page-loader").hide();
                    $('#save-po-btn').attr('disabled', false);

                    if (response.status == 1) {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        $("#po-items-container").html(response.data);
                        $("#po-items-table > tbody .best_before_date").datepicker(options);
                        $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function (e) {
                            $('.datepicker').hide();
                            $('.best_before_date').valid();
                        });
//                                     setTimeout(function () {
//                                       location.reload(1);
//                                    }, 1000);
                    } else {
                        PoundShopApp.commonClass._displayErrorMessage(response.message);
                    }
                },
                error: function (xhr, err) {
                    $("#page-loader").hide();
                    $('#save-po-btn').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }

            });

        }
    });
    $("#save-po-btn").click(function () {
        $("#save-po-form").submit();
    });


    //terms update
    $("#po-terms-form").validate({
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
        },
        messages: {
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        highlight: function (element) { // hightlight error inputs
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function (label) {
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        submitHandler: function (form) {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
            var formData = $("#po-terms-form").serialize();
            $('#update-term-button').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#po-terms-form").attr("action"),
                data: formData,
                cache: false,
//                    contentType: false,
//                    processData: false,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $('#update-term-button').attr('disabled', false);
                    $("#page-loader").hide();
                    if (response.status == 1) {
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    }
                },
                error: function (xhr, err) {
                    $('#update-term-button').attr('disabled', false);
                    $("#page-loader").hide();
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });

        }
    });
    //Supplier Selection
    $("#supplier").change(function (event) {
        //var options="";
        //options += "<option value=''>Select Supplier Contact</option>";
//         if($(this).find('option:selected').attr('data-supplier') !== undefined && $(this).find('option:selected').attr('data-supplier') !== ""){
//             var supplier_contact=$.parseJSON($(this).find('option:selected').attr('data-supplier')); 
//            if(supplier_contact.length > 0){
//                for(var i=0;i<supplier_contact.length;i++){
//                    if(supplier_contact[i]['is_primary'] == 1){
//                        options += "<option selected='selected' value='"+supplier_contact[i]['id']+"'>"+supplier_contact[i]['name']+"</option>";
//                    }else{
//                        options += "<option value='"+supplier_contact[i]['id']+"'>"+supplier_contact[i]['name']+"</option>";
//                    }
//                }
//            }
//         }


//         var supplier_country=$(this).find('option:selected').attr('data-country');
//         if(supplier_country !== '230'){
//             $("#po_import_type").val(2);
//         }else{
//             $("#po_import_type").val(1);
//         }

        var supplierId = $(this).val();

        $.ajax({
            url: BASE_URL + 'api-supplier-contacts?supplier_id=' + supplierId,
            type: "GET",
            datatype: 'JSON',
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response)
            {
                $("#page-loader").hide();

                if (response.status == true)
                {
                    $("#supplier_contact").html(response.data);
                }
            },
            error: function (xhr, err) {
                $("#page-loader").hdie();
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });



        //  $("#supplier_contact").html(options);
    });

    //warehouse change
    $("#recev_warehouse").change(function (event) {
        var warehouse = $(this).find('option:selected');
        if (warehouse.val() !== undefined && warehouse.val() !== "") {
            var warehouse_detail = $.parseJSON(warehouse.attr('data-info'));
            $("#address1").val(warehouse_detail['address_line1']);
            $("#address2").val(warehouse_detail['address_line2']);
            $("#pincode").val(warehouse_detail['zipcode']);
            $("#country").val($.parseJSON(warehouse.attr('data-country'))['name']);
            $("#state").val($.parseJSON(warehouse.attr('data-state'))['name']);
            $("#city").val($.parseJSON(warehouse.attr('data-city'))['name']);
        } else {
            $("#address1").val('');
            $("#address2").val('');
            $("#pincode").val('');
            $("#country").val('');
            $("#state").val('');
            $("#city").val('');
        }




    });
    //supplier order number
    $(document).on('keydown', '#supplier_order_number', function (e) {
        var a = e.key;
        if (a.length == 1)
            return /[a-z]|[0-9]|&/i.test(a);
        return true;
    })

    $("#show-modal-btn").click(function () {
        $('#search-product-textbox').val('');
        $(".master").attr("checked", false);
        $('#item-modal').modal('show');
        $("#example > tbody").html('<td colspan="6" class="dataTables_empty py-4" valign="top">No Records Found</td>');

        setTimeout(function () {
            $('#search-product-textbox').focus();
        }, 500);
    });

    $.fn.extend({
        donetyping: function (callback, timeout) {
            timeout = timeout || 200; // 1 second default timeout
            var timeoutReference,
                    doneTyping = function (el) {
                        if (!timeoutReference)
                            return;
                        timeoutReference = null;
                        callback.call(el);
                    };
            return this.each(function (i, el) {
                var $el = $(el);
                // Chrome Fix (Use keyup over keypress to detect backspace)
                // thank you @palerdot
                $el.is(':input') && $el.on('keyup keypress paste', function (e) {
                    // This catches the backspace button in chrome, but also prevents
                    // the event from triggering too preemptively. Without this line,
                    // using tab/shift+tab will make the focused element fire the callback.
                    if (e.type == 'keyup' && e.keyCode != 8)
                        return;

                    // Check if timeout has been set. If it has, "reset" the clock and
                    // start over again.
                    if (timeoutReference)
                        clearTimeout(timeoutReference);
                    timeoutReference = setTimeout(function () {
                        // if we made it here, our timeout has elapsed. Fire the
                        // callback
                        doneTyping(el);
                    }, timeout);
                }).on('blur', function () {
                    // If we can, fire the event since we're leaving the field
                    doneTyping(el);
                });
            });
        }
    });

    $('#search-product-textbox').donetyping(function (event) {
        var URL = $("#search_product_url").val();
        var search_data = $.trim($(this).val());
        var supplier_id = $("#supplier_id").val();

        if (search_data == "") {
            $("#example_processing").hide();
            $("#example > tbody").html('<td colspan="6" class="dataTables_empty" valign="top">No Records Found</td>');
            return false;
        }
        if (xhr && xhr.readystate != 4) {
            xhr.abort();
        }
        $("#example_processing").show();
        xhr = $.ajax({
            type: "GET",
            url: URL,
            data: {'search-keyword': search_data, 'supplier-id': supplier_id},
            //cache: false,
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#example_processing").show();
            },
            success: function (response) {
                $("#example_processing").hide();
                if (response.status == 1) {
                    $.fn.dataTable.tables({visible: true, api: true}).columns.adjust();
                    $("#example >tbody").html(bindProductData(response.data));
                } else {
                    $("#example >tbody").html('<tr><td colspan="6" align="center"><a id="addNewBarcodeBtn" class="btn btn-blue mt-3" data-string="' + search_data + '" href="javascript:;">Add New Barcode Product</a></td></tr>');
                }
            },
            error: function (xhr, err) {
                $("#example_processing").hide();
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });


    });


    $(document).on('click', '#addNewBarcodeBtn', function (event) {
        var currentObj = $(this);
        bootbox.confirm({
            title: "Confirm",
            message: "Would you like to add Mix Vat Rate?",
            buttons: {
                cancel: {
                    label: 'No',
                    className: 'btn-gray'
                },
                confirm: {
                    label: 'Yes',
                    className: 'btn-red'
                }
            },
            callback: function (result)
            {
                if (result) {
                    var row = addBlankPO(currentObj.attr("data-string"), true);
                    $("#po-items-table > tbody").prepend(row);
                    $("#po-items-table > tbody .best_before_date").datepicker(options);
                    $('#item-modal').modal('hide');
                    $("#search-product-textbox").val('');
                    searchTable.draw();
                    $(".po-item-data").show();
                    $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function (e) {
                        $('.datepicker').hide();
                        $('.best_before_date').valid();
                    });
                } else {
                    var row = addBlankPO(currentObj.attr("data-string"), false);
                    $("#po-items-table > tbody").prepend(row);
                    $("#po-items-table > tbody .best_before_date").datepicker(options);
                    $('#item-modal').modal('hide');
                    $("#search-product-textbox").val('');
                    searchTable.draw();
                    $(".po-item-data").show();
                    $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function (e) {
                        $('.datepicker').hide();
                        $('.best_before_date').valid();
                    });
                }
            }
        });


    });

    addBlankPO = function (barcode, isVatRateAdded) {
        var randId = randomNumberFromRange(99999999, 999999999);
        var DataHtml = "";
        if ($("#po_import_type").val() == 1) { //UK PO
            DataHtml += '<tr>';
            DataHtml += '<td id=""><div class="d-flex"><label class="fancy-checkbox"><input name="ids[]" type="checkbox" class="child-checkbox" value=""/><span><i></i></span></label></div></td>';
            DataHtml += '<td><div class="d-flex group-item"><span class="title color-light w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.sku + '</span><span class="desc color-blue">-</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.title + '</span><span class="desc color-light po-product-title">-</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.status + '</span><span class="desc color-blue">New</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.supplier_sku + '</span><span class="desc"><input type="text" class="color-blue supplier_sku po_textbox w-120" name="supplier_sku_' + randId + '" maxlength="15" /></span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.best_before + '</span><span class="desc color-blue"><input type="text" name="best_before_date_' + randId + '" class="best_before_date po_textbox w-120" autocomplete="off" /></span></div></td>';
            DataHtml += '<td><input data-barcode=""   maxlength="20"  type="text" name="barcode_' + randId + '" class="barcode po_textbox input-barcode" value="' + barcode + '" /><div class="d-flex group-item mt-3"><span class="desc"><label class="fancy-checkbox"><input type="checkbox" name="variant" class="variant"><span><i></i>' + POUNDSHOP_MESSAGES.purchase_order.items.tables.variant + '</span></label></span></div></td>';
            DataHtml += '<td>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.qty_per_box + '</span><span class="desc"><input maxlength="8"  type="text" name="qty_per_box_' + randId + '" class="qty_per_box po_textbox" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_box + '</span><span class="desc"><input value=""  maxlength="8"  type="text" name="total_box_' + randId + '" class="total_box po_textbox"  /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_qty + '</span><span class="desc"><input  maxlength="9"  type="text" name="total_quantity_' + randId + '" class="total_quantity po_textbox"  /></span></div>';
            DataHtml += '</td>';
            DataHtml += '<td><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" maxlength="9" name="unit_price_' + randId + '" class="unit_price po_textbox" /></div></td>';
            DataHtml += '<td><span class="title">&#163;</span><span  name="total_product_cost_' + randId + '" class="title total_product_cost po_textbox" >0.00</span></div></td>';
            DataHtml += '<td>';
            var textRateValue = 1;
            if (isVatRateAdded == true)
            {
                textRateValue = 2;
                DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.std_rate + '</span> <span class="desc"><input type="text" class="standard_rate po_textbox" value="0" oninput="calculateMixRate(this)" /></span></div>';
                DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.zero_rate + '</span><span class="desc"><input type="text" class="zero_rate" value="0" /></span></div>';
                DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat desc" data-value="0">0%</span></div>';
            } else {
                textRateValue = 1;
                DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat desc" data-value="0">0%</span></div>';
            }
            DataHtml += '</td>';



            DataHtml += '<td><div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.expected + '</span><span class="desc"><input type="text" name="expected_mros_' + randId + '" class="expected_mros po_textbox"  /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_qty + '</span><span class="desc"><input type="text" name="sel_qty_' + randId + '" class="sel_qty po_textbox" maxlenth="5" value="1"/></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_price + '</span><span class="desc"><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" name="sel_price_' + randId + '" class="sel_price po_textbox" maxlength="9" value="1" /></div></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.mros + '</span><span class="desc"><input value="20" readonly="readonly" type="text" name="mros_' + randId + '" class="mros po_textbox" maxlength="9" /></span></div></td>';
            DataHtml += '<td align="center"><input type="hidden" class="vat_type" value="' + textRateValue + '" /><input type="hidden" class="landed_product_cost" /><input type="hidden" class="net_selling_price_excluding_vat" /><input type="hidden" class="total_net_selling_price " /><input type="hidden" class="total_net_profit" /><input type="hidden" class="total_net_margin" /> <a href="javascript:;" class="removeRow"><span class="icon-moon icon-Delete"></span></a></td>';
            DataHtml += '</tr>';
        } else { // IMPORT PO
            DataHtml += '<tr>';
            DataHtml += '<td id=""><div class="d-flex"><label class="fancy-checkbox"><input name="ids[]" type="checkbox" class="child-checkbox" value="" /><span><i></i></span></label></div></td>';
            DataHtml += '<td><div class="d-flex group-item"><span class="title color-light w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.sku + '</span><span class="desc color-blue">--</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.title + '</span><span class="desc color-light po-product-title">-</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.status + '</span><span class="desc color-blue">New</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.supplier_sku + '</span><span class="desc"><input type="text" class="color-blue supplier_sku po_textbox w-120" name="supplier_sku_' + randId + '" maxlength="15" /></span></div><div class="d-flex group-item"><span class="title  w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.best_before + '</span><span class="desc"><input type="text" name="best_before_date_' + randId + '" class="best_before_date po_textbox w-120" autocomplete="off" /></span></div></td>';
            DataHtml += '<td><input data-barcode=""   maxlength="20"  type="text" name="barcode_' + randId + '" class="barcode po_textbox input-barcode" value="' + barcode + '" /><div class="d-flex group-item mt-3"><span class="desc"><label class="fancy-checkbox"><input type="checkbox" name="variant" class="variant"><span><i></i>' + POUNDSHOP_MESSAGES.purchase_order.items.tables.variant + '</span></label></span></div></td>';
            DataHtml += '<td>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.qty_per_box + '</span><span class="desc"><input maxlength="8"  type="text" name="qty_per_box_' + randId + '" class="qty_per_box po_textbox" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_box + '</span><span class="desc"><input value=""  maxlength="8"  type="text" name="total_box_' + randId + '" class="total_box po_textbox"  /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_qty + '</span><span class="desc"><input  maxlength="9"   type="text" name="total_quantity_' + randId + '" class="total_quantity po_textbox"  /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.cube_per_box + '</span><span class="desc"><input value=""  maxlength="8"  type="text" name="cube_per_box_' + randId + '" class="cube_per_box po_textbox" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_cubes + '</span><span class="desc"><input value=""  maxlength="9"  type="text" name="total_num_cubes_' + randId + '" readonly="readonly" class="total_num_cubes po_textbox" /></span></div>';
            DataHtml += '</td>';

            DataHtml += '<td><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" maxlength="9" name="unit_price_' + randId + '" class="unit_price po_textbox"/></div></td>';
            DataHtml += '<td><span class="title">&#163;</span><span  name="total_product_cost_' + randId + '" class="title total_product_cost po_textbox">0.00</span></td>';
            DataHtml += '<td>';
            var textRateValue = 1;
            if (isVatRateAdded == true)
            {
                textRateValue = 2;
                DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.std_rate + '</span> <span class="desc"><input type="text" class="standard_rate po_textbox" value="0" oninput="calculateMixRate(this)" /></span></div>';
                DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.zero_rate + '</span><span class="desc"><input type="text" class="zero_rate" value="0" /></span></div>';
                DataHtml += '<div class="d-flex group-item"><span class="title w-80" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat desc" data-value="0">0%</span></div>';
            } else {
                textRateValue = 1;
                DataHtml += '<div class="d-flex group-item"><span class="title w-80" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat desc" data-value="0">0%</span></div>';
            }

            var isDefault = false;
            var importDuty = 0;
            var countryCommodityCodes = $.parseJSON($("#countries_commodities").val());
            if (countryCommodityCodes.length > 0) {
                for (var i = 0; i < countryCommodityCodes.length; i++) {
                    if (countryCommodityCodes[i].is_default == 1) { // if found then take is default
                        importDuty = countryCommodityCodes[i].pivot.rate;
                        isDefault = true;
                        break;
                    }
                }
                if (!isDefault) { //else store default as a 0
                    importDuty = 0;
                }
            } else {
                importDuty = 0;
            }

            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.import_duty + '</span><span class="desc">';
            DataHtml += '<span class="import_duty" data-value="' + importDuty + '">' + importDuty + '%</span>';
            DataHtml += '</span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.tot_del_charge + '</span><span class="desc"><div class="d-flex align-items-center">&#163;<span class="total_delivery_charge"></span></div></span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.landed_product_cost + '</span><span class="desc"><span class="landed_product_cost"  ></span></span></div>';
            DataHtml += '</td>';
            DataHtml += '<td><div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.expected + '</span><span class="desc"><input type="text" name="expected_mros_' + randId + '" class="expected_mros po_textbox w-60"  /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_qty + '</span><span class="desc"><input type="text" name="sel_qty_' + randId + '" class="sel_qty po_textbox w-60" maxlenth="5" value="1" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_price + '</span><span class="desc"><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" name="sel_price_' + randId + '" class="sel_price po_textbox w-60" maxlength="9" value="1" /></div></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.mros + '</span><span class="desc"><input type="text" value="20" readonly="readonly"  name="mros_' + randId + '" class="mros po_textbox w-60" maxlength="9" /></span></div>';
            DataHtml += '</td>';
            DataHtml += '<td align="center"><input type="hidden" class="vat_type" value="' + textRateValue + '" /> <input type="hidden" class="vat_in_amount" /> <input type="hidden" class="import_duty_in_cost" /><input type="hidden" class="totalProductCostImportDutyDeliveryCharge" /><input type="hidden" class="total_vat" /><input type="hidden" class="currency_exchange_rate" /><input type="hidden" class="landed_price_in_pound" /><input type="hidden" class="total_net_selling_price" /><input type="hidden" class="gross_sel_price_exc_vat" /><input type="hidden" class="total_net_profit" /><input type="hidden" class="total_net_margin" /><a href="javascript:;" class="removeRow"><span class="icon-moon icon-Delete"></span></a></td>';
            DataHtml += '</tr>';
        }

        return DataHtml;
    }
    addToPO = function () {
        var searchPOTableData = new Array();
        var allVals = [];
        $("#example > tbody input[name='ids[]']:checked").each(function () {
            allVals.push($("body").data("information" + $(this).val()));
        });
        if (allVals < 1) {
            bootbox.alert({
                title: "Alert",
                message: "Please select atleast one record to add.",
                size: 'small'
            });
            return false
        } else {
            bindtoItems(allVals);
            $('#item-modal').modal('hide');
            $("#search-product-textbox").val('');
            searchTable.draw();
            $(".po-item-data").show();
            $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function (e) {
                $('.datepicker').hide();
                $('.best_before_date').valid();
            });
        }
    }

    bindtoItems = function (datas) {
        var rows = "";
        $.each(datas, function (key, value) {
            if (checkProduct(value)) {
                if ($("#po_import_type").val() == 2) {
                    rows += bindImportItemsData(value);
                } else {
                    rows += bindItemsData(value);
                }
            }
        });
        $("#po-items-table > tbody").prepend(rows);
        $("#po-items-table > tbody .best_before_date").datepicker(options);
    }

    function checkProduct(value) {
        var productIds = [];
        $("#po-items-table > tbody tr").each(function (row, tr) {
            productIds.push(parseInt($(tr).find('td:eq(0)').find('input[type="checkbox"]').val()));
        });
        if ($.inArray(value.id, productIds) !== -1) {
            return false;
        } else {
            return true;
        }
    }
    //calculate Qty
    calculateQty = function (obj, field_name) {
        var currentValue = obj.val();

        if (currentValue !== undefined && currentValue !== "") {
            currentValue = parseInt(currentValue);
        } else {
            currentValue = 0;
        }
        if ($("#po_import_type").val() == 1) // u.k po
        {
            if (obj.hasClass('qty_per_box'))
            {
                var totalBox = obj.closest('tr').find('.total_box').val();
                var totalQty = obj.closest('tr').find('.total_quantity').val();
                var calculatedBox = 0, calculatedQty = 0;

                if (totalBox !== undefined && totalBox !== "") {
                    calculatedBox = parseInt(totalBox);
                }
                if (totalQty !== undefined && totalQty !== "") {
                    calculatedQty = parseInt(totalQty);
                }

                if (calculatedBox !== 0 || calculatedQty !== 0) {
                    if (calculatedBox !== 0) {  //calculate qty
                        obj.closest('tr').find('.total_quantity').val(currentValue * calculatedBox);
                    } else { //calculate total box
                        if (Number.isInteger(calculatedQty / currentValue)) {
                            obj.closest('tr').find('.total_box').val(calculatedQty / currentValue);
                        } else {
                            obj.closest('tr').find('.total_box').val((calculatedQty / currentValue).toFixed(1));
                        }

                    }

                }


            } else if (obj.hasClass('total_box')) {
                var perBox = obj.closest('tr').find('.qty_per_box').val();
                var totalQty = obj.closest('tr').find('.total_quantity').val();
                var calculatedPerBox = 0, calculatedQty = 0;
                if (perBox !== undefined && perBox !== "") {
                    calculatedPerBox = parseInt(perBox);
                }
                if (totalQty !== undefined && totalQty !== "") {
                    calculatedQty = parseInt(totalQty);
                }
                if (calculatedPerBox !== 0 || calculatedQty !== 0) {
                    if (calculatedPerBox !== 0) {//calculate total qty
                        obj.closest('tr').find('.total_quantity').val(currentValue * calculatedPerBox);
                    } else { // calculate per box
                        if (Number.isInteger(calculatedQty / currentValue)) {
                            obj.closest('tr').find('.qty_per_box').val(calculatedQty / currentValue);
                        } else {
                            obj.closest('tr').find('.qty_per_box').val((calculatedQty / currentValue).toFixed(1));
                        }

                    }
                }

            } else if (obj.hasClass('total_quantity')) {
                var perBox = obj.closest('tr').find('.qty_per_box').val();
                var totalBox = obj.closest('tr').find('.total_box').val();

                var calculatedPerBox = 0, calculatedBox = 0;

                if (perBox !== undefined && perBox !== "") {
                    calculatedPerBox = parseInt(perBox);
                }
                if (totalBox !== undefined && totalBox !== "") {
                    calculatedBox = parseInt(totalBox);
                }

                if (calculatedPerBox !== 0 || calculatedBox !== 0) {
                    if (calculatedPerBox !== 0) {//calculate per box
                        if (Number.isInteger(currentValue / calculatedPerBox)) {
                            obj.closest('tr').find('.total_box').val(currentValue / calculatedPerBox);
                        } else {
                            obj.closest('tr').find('.total_box').val((currentValue / calculatedPerBox).toFixed(1));
                        }

                    } else { // calculate per box
                        if (Number.isInteger(currentValue / calculatedBox)) {
                            obj.closest('tr').find('.qty_per_box').val(currentValue / calculatedBox);
                        } else {
                            obj.closest('tr').find('.qty_per_box').val((currentValue / calculatedBox).toFixed(1));
                        }

                    }
                }

                //calculate total price
            }
        } else { // import P.O
            if (obj.hasClass('qty_per_box'))
            {
                var totalBox = obj.closest('tr').find('.total_box').val();
                var totalQty = obj.closest('tr').find('.total_quantity').val();
                var calculatedBox = 0, calculatedQty = 0;

                if (totalBox !== undefined && totalBox !== "") {
                    calculatedBox = parseInt(totalBox);
                }
                if (totalQty !== undefined && totalQty !== "") {
                    calculatedQty = parseInt(totalQty);
                }

                if (calculatedBox !== 0 || calculatedQty !== 0) {
                    if (calculatedBox !== 0) {  //calculate qty
                        obj.closest('tr').find('.total_quantity').val(currentValue * calculatedBox);
                    } else { //calculate total box
                        if (Number.isInteger(calculatedQty / currentValue)) {
                            obj.closest('tr').find('.total_box').val(calculatedQty / currentValue);
                        } else {
                            obj.closest('tr').find('.total_box').val((calculatedQty / currentValue).toFixed(1));
                        }

                    }

                }


            } else if (obj.hasClass('total_box')) {
                var perBox = obj.closest('tr').find('.qty_per_box').val();
                var totalQty = obj.closest('tr').find('.total_quantity').val();
                var calculatedPerBox = 0, calculatedQty = 0;
                if (perBox !== undefined && perBox !== "") {
                    calculatedPerBox = parseInt(perBox);
                }
                if (totalQty !== undefined && totalQty !== "") {
                    calculatedQty = parseInt(totalQty);
                }
                if (calculatedPerBox !== 0 || calculatedQty !== 0) {
                    if (calculatedPerBox !== 0) {//calculate total qty
                        obj.closest('tr').find('.total_quantity').val(currentValue * calculatedPerBox);
                    } else { // calculate per box
                        if (Number.isInteger(calculatedQty / currentValue)) {
                            obj.closest('tr').find('.qty_per_box').val(calculatedQty / currentValue);
                        } else {
                            obj.closest('tr').find('.qty_per_box').val((calculatedQty / currentValue).toFixed(1));
                        }

                    }
                }

            } else if (obj.hasClass('total_quantity')) {
                var perBox = obj.closest('tr').find('.qty_per_box').val();
                var totalBox = obj.closest('tr').find('.total_box').val();

                var calculatedPerBox = 0, calculatedBox = 0;

                if (perBox !== undefined && perBox !== "") {
                    calculatedPerBox = parseInt(perBox);
                }
                if (totalBox !== undefined && totalBox !== "") {
                    calculatedBox = parseInt(totalBox);
                }

                if (calculatedPerBox !== 0 || calculatedBox !== 0) {
                    if (calculatedPerBox !== 0) {//calculate per box
                        if (Number.isInteger(currentValue / calculatedPerBox)) {
                            obj.closest('tr').find('.total_box').val(currentValue / calculatedPerBox);
                        } else {
                            obj.closest('tr').find('.total_box').val((currentValue / calculatedPerBox).toFixed(1));
                        }

                    } else { // calculate per box
                        if (Number.isInteger(currentValue / calculatedBox)) {
                            obj.closest('tr').find('.qty_per_box').val(currentValue / calculatedBox);
                        } else {
                            obj.closest('tr').find('.qty_per_box').val((currentValue / calculatedBox).toFixed(1));
                        }
                    }
                }
            }

        }

    }

    //calculate Total Price
    function calculateTotalProductCost(obj) {
        var sum = 0.00;
        var unit_price = 0.00;
        var total_qty = 0;

        if (obj.hasClass('total_quantity')) { //total qty
            var price = obj.closest('tr').find('.unit_price').val();
            if ((price !== undefined && price !== 0 && price !== "") && (obj.val() !== undefined && obj.val() !== "")) {
                obj.closest('tr').find('.total_product_cost').text(toFixedDigit(parseFloat(parseFloat(obj.val()) * parseFloat(price))))
            } else {
                obj.closest('tr').find('.total_product_cost').text(0.00);
            }

        } else { //unit price
            var total_qty = obj.closest('tr').find('.total_quantity').val();
            if ((total_qty !== undefined && total_qty !== 0) && (obj.val() !== undefined && obj.val() !== "")) {
                obj.closest('tr').find('.total_product_cost').text(toFixedDigit(parseFloat(parseFloat(obj.val()) * parseFloat(total_qty))))
            } else {
                obj.closest('tr').find('.total_product_cost').text(0.00);
            }
        }
        calculateSubTotal();
        calculateGlobalPriceMargin();
    }

    //calculate importduty
    function calculateImportDutyInCost($obj) {
        var tr = $obj.closest('tr');
        var totalProductCost = parseFloat(undefinedValue(tr.find('.total_product_cost').text()));
        var duty = parseInt(undefinedValue(tr.find('.import_duty').attr("data-value")));
        var ImportDutyInAmount = parseFloat((totalProductCost * duty) / 100);
        tr.find('.import_duty_in_cost').val(ImportDutyInAmount);
    }

    function calculateVatInAmount($obj) {
        var tr = $obj.closest('tr');
        var totalProductCost = parseFloat(undefinedValue(tr.find('.total_product_cost').text()));
        var dutyInAmount = parseFloat(undefinedValue(tr.find('.import_duty_in_cost').val()));
        var vat = parseInt(undefinedValue(tr.find('.vat').attr("data-value")));

        var vatInAmount = parseFloat(((totalProductCost + dutyInAmount) * vat) / 100);
        tr.find('.vat_in_amount').val(vatInAmount);
    }
    calculateCostPerCube = function ($obj) {
        var totalDelivery = parseFloat(undefinedValue($('#total_delivery').val()));
        var totalSpace = parseInt(undefinedValue($('#total_space').val()));
        var costPerCube = 0;
        if (totalSpace !== 0 && totalSpace !== undefined) {
            costPerCube = parseFloat(totalDelivery / totalSpace).toFixed(2);
        }


        $("#cost_per_cube").text(costPerCube);
        if ($($obj).attr("name") == 'total_delivery')
            $("#overall_total_delivery").text($($obj).val());


        $("#po-items-container tr").each(function (row, tr) {
            if ($("#po_import_type").val() == 2) {
                var cubes = parseFloat(undefinedValue($(tr).find('.total_num_cubes').val()));
                $(tr).find('.total_delivery_charge').text(parseFloat(cubes * costPerCube).toFixed(2));
            }
        });

    }
    //calculate total cost
    function calculateTotalCost() {}

    //CalculateTotal No of Cubes
    function calculateTotalNoofCubes($obj) {
        var tr = $obj.closest('tr');
        var totalBox = parseInt(undefinedValue(tr.find('.total_box').val()));
        var cubePerBox = parseFloat(undefinedValue(tr.find('.cube_per_box').val()));
        tr.find(".total_num_cubes").val(parseFloat(totalBox * cubePerBox).toFixed(2));
    }

    function calculateOverAllNoOfCubes() {
        var totalCubes = 0;
        $("#po-items-container tr").each(function (row, tr) {
            if ($("#po_import_type").val() == 2) {
                var cubes = parseFloat(undefinedValue($(tr).find('.total_num_cubes').val()));
                totalCubes += cubes;
            }
        });
        $("#overall_total_no_cubes").text(totalCubes);
        var totalSpace = parseFloat(undefinedValue($('#total_space').val()));
        $("#remaining_space").text(totalSpace - totalCubes);
    }
    //calculate total delivery charge
    function calculateDeliveryCharge($obj) {
        var tr = $obj.closest('tr');
        var totalBox = parseFloat(undefinedValue(tr.find('.total_num_cubes').val()));
        var costPerCubes = parseFloat(undefinedValue($('#cost_per_cube').text()));
        tr.find(".total_delivery_charge").text(parseFloat(totalBox * costPerCubes).toFixed(2));
    }

    //calculate Delivery Charges
    function importDutyCharges() {
        var totalCharge = 0.00
        $("#po-items-container tr").each(function (row, tr) {
            if ($("#po_import_type").val() == 2) {
                var charge = parseInt(undefinedValue($(tr).find('.import_duty_in_cost').val()));
                totalCharge += charge;
            }
        });

        $("#overall_import_duty").text(parseFloat(totalCharge).toFixed(2));
    }

    function totalCost() {
        var subTotal = parseFloat(undefinedValue($("#sub_total").text())).toFixed(2);
        var importDuty = parseFloat(undefinedValue($("#overall_import_duty").text())).toFixed(2);
        var deliveryCharge = parseFloat(undefinedValue($("#total_delivery").val())).toFixed(2);
        $("#total_cost").text(parseFloat(parseFloat(subTotal) + parseFloat(importDuty) + parseFloat(deliveryCharge)).toFixed(2));

    }
    //calculate Total VAT
    function calculateTotalVAT($obj) {
        var tr = $obj.closest('tr');
        var productCost = parseFloat(undefinedValue(tr.find('.total_product_cost').text()));
        var importDuty = parseFloat(undefinedValue(tr.find('.import_duty_in_cost').val()));
        var deliveryCharge = parseFloat(undefinedValue(tr.find('.total_delivery_charge').text()));
        var vat = parseFloat(undefinedValue(tr.find('.vat').attr('data-value')));
        var totalQty = parseFloat(undefinedValue(tr.find('.total_quantity').val()));
        var totalProductCostImportDutyDeliveryCharge = parseFloat(productCost + importDuty + deliveryCharge).toFixed(2);

        var totalVat = parseFloat((totalProductCostImportDutyDeliveryCharge * vat) / 100).toFixed(2);
        var landedProductCost = 0.00;

        tr.find('.totalProductCostImportDutyDeliveryCharge').val(totalProductCostImportDutyDeliveryCharge);

        tr.find('.total_vat').val(totalVat);
        if (totalQty !== 0 && totalQty !== undefined)
            landedProductCost = parseFloat(totalProductCostImportDutyDeliveryCharge / totalQty);


        tr.find(".landed_product_cost").text(landedProductCost.toFixed(2))
        tr.find('.landed_price_in_pound').val(parseFloat(landedProductCost / 1.5));
    }

    function calculateNetSellingExculudingVAT($obj) {
        var tr = $obj.closest('tr');
        var price = parseFloat(undefinedValue(tr.find('.sel_price').val()));
        var vat = parseInt(undefinedValue(tr.find('.vat').attr('data-value')));
        var totalSellingPriceExtVAT = parseFloat((price / (100 + vat)) * 100).toFixed(2);
        tr.find(".gross_sel_price_exc_vat").val(totalSellingPriceExtVAT);
    }

    function calculateTotalNetSellingPrice($obj) {
        var tr = $obj.closest('tr');
        var totalSellingPriceExtVAT = parseFloat(undefinedValue(tr.find('.gross_sel_price_exc_vat').val()));
        var totalQty = parseInt(undefinedValue(tr.find('.total_quantity').val()));
        var selQty = parseInt(undefinedValue(tr.find('.sel_qty').val()));
        var totalSelPrice = 0.00;
        if (selQty !== 0 && selQty !== undefined && selQty !== "")
            totalSelPrice = parseFloat((totalSellingPriceExtVAT * totalQty) / selQty).toFixed(2);
        tr.find(".total_net_selling_price").val(totalSelPrice);
    }


    //calculate sub total
    function calculateSubTotal() {

        var subTotal = 0;
        $("#po-items-container tr").each(function (row, tr) {
            var rowTotal = $(tr).find('.total_product_cost').text();
            if (rowTotal !== undefined && rowTotal != "") {
                subTotal += parseFloat(rowTotal);
            } else {
                subTotal += 0;
            }
        });
        $("#sub_total").text(toFixedDigit(parseFloat(subTotal)));
        var supplierAmount = parseFloat(undefinedValue($("#supplier_min_amount").text()));
        if (subTotal > supplierAmount) {
            $(".remainingDiv").hide();
            $("#remaining_amount").text(0.00)
        } else {
            $(".remainingDiv").show();
            $("#remaining_amount").text(toFixedDigit(parseFloat(supplierAmount - subTotal)));
        }

    }

    function undefinedValue(value) {
        if (value !== undefined && value !== "") {
            return value;
        } else {
            return 0;
        }
    }

    function getTableLength() {
        return $('#po-items-table > tbody tr').length;
    }


    function calculateGlobalPriceMargin() {
        var overAllTotalNetProfit = 0.00;
        var overAllTotalNetSellingPrice = 0.00;
        var totalOverAllMargin = 0.00;
        $("#po-items-container tr").each(function (row, tr) {

            if ($("#po_import_type").val() == 1) {
                var totalMargin = 0.00
                var totalNetSellingPrice = 0.00;

                var totalQty = parseInt(undefinedValue($(tr).find('.total_quantity').val()));
                var price = parseFloat(undefinedValue($(tr).find('.unit_price').val()));
                var totalProductCost = parseFloat(undefinedValue($(tr).find('.total_product_cost').text()));
                var sellingQty = parseInt(undefinedValue($(tr).find('.sel_qty').val()));
                var sellingPrice = parseFloat(undefinedValue($(tr).find('.sel_price').val()));
                var VAT = parseFloat(undefinedValue($(tr).find('.vat').attr('data-value')));

                //console.log('total Qty:'+totalQty+" price : "+ price + "total Prod cost:"+totalProductCost+" total sel qty :"+sellingQty + " total sel prioc"+sellingPrice)

                var landedPrice = price;
                var netSellingPriceExcludingVAT = parseFloat(sellingPrice / (100 + VAT)) * 100;

                //console.log("Sel Price Ex Vat : "+netSellingPriceExcludingVAT);
                if (sellingQty !== 0) {
                    totalNetSellingPrice = parseFloat((netSellingPriceExcludingVAT * totalQty) / sellingQty);
                }
                //console.log("Sel Price : "+totalNetSellingPrice);
                var totalNetProfit = parseFloat(totalNetSellingPrice - totalProductCost);

                //console.log("Profit : "+totalNetProfit);
                if (totalNetSellingPrice !== 0) {
                    totalMargin = parseFloat((totalNetProfit / totalNetSellingPrice) * 100).toFixed(2);
                }


                $(tr).find(".net_selling_price_excluding_vat").val(netSellingPriceExcludingVAT);
                $(tr).find(".total_net_selling_price").val(totalNetSellingPrice);
                $(tr).find(".total_net_profit").val(totalNetProfit);
                $(tr).find(".total_net_margin").val(totalMargin);

                overAllTotalNetSellingPrice += totalNetSellingPrice;
                overAllTotalNetProfit += totalNetProfit;
            } else {
                var totalMargin = 0.00;
                var totalNetSellingPrice = 0.00;
                // total_profite = total_net_selling_price-totalProductCostImportDutyDeliveryCharge
                totalNetSellingPrice = parseFloat(undefinedValue($(tr).find('.total_net_selling_price').val()));
                var totalCostImpotDelivery = parseFloat(undefinedValue($(tr).find('.totalProductCostImportDutyDeliveryCharge').val()));
                var totalNetProfite = parseFloat(totalNetSellingPrice - totalCostImpotDelivery).toFixed(2);


                if (totalNetSellingPrice !== 0 && totalNetSellingPrice !== undefined && totalNetSellingPrice !== "") {
                    totalMargin = parseFloat((totalNetProfite / totalNetSellingPrice) * 100).toFixed(2);
                } else {
                    totalNetSellingPrice = 0.00;
                }
                $(tr).find('.total_net_profit').val(totalNetProfite);
                $(tr).find('.total_net_margin').val(totalMargin);
                overAllTotalNetProfit += parseFloat(undefinedValue($(tr).find('.total_net_profit').val()));
                overAllTotalNetSellingPrice += parseFloat(undefinedValue($(tr).find('.total_net_selling_price').val()));
            }

        });



        if (overAllTotalNetSellingPrice !== 0) {
            totalOverAllMargin = parseFloat((overAllTotalNetProfit / overAllTotalNetSellingPrice) * 100);
        }

        $("#total_margin").text(parseFloat(totalOverAllMargin).toFixed(2) + "%");
    }

    bindItemsData = function (data) {
        
        var barcode = '';
        if (data.bar_codes.length > 0)
        {
            for (var i = 0; i < data.bar_codes.length; i++) {
                if (data.bar_codes[i].barcode_type == 1) {
                    barcode = data.bar_codes[i].barcode;
                    break;
                }
            }
        }
        var DataHtml = "";
        var supplierSku = "";
        if (data.supplier.supplier_sku !== "" && data.supplier.supplier_sku !== undefined) {
            supplierSku = data.supplier.supplier_sku;
        }
        DataHtml += '<tr>';
        DataHtml += '<td id=""><div class="d-flex"><label class="fancy-checkbox"><input name="ids[]" type="checkbox" class="child-checkbox" value="' + data.id + '"/><span><i></i></span></label></div></td>';
        DataHtml += '<td>';
        var productStatus = "New";
        if (data.title == "")
            data.title = "--"

        if (data.sku == "")
            data.sku = "--"

        if (data.is_listed_on_magento == 1)
            productStatus = "Listed";
        else if (data.is_listed_on_magento == 2)
            productStatus = "Delisted";

        DataHtml += '<div class="d-flex group-item"><span class="title color-light w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.sku + '</span><span class="desc color-blue">' + data.sku + '</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.title + '</span><span class="desc color-light po-product-title" title="' + data.title + '">' + data.title + '</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.status + '</span><span class="desc color-blue">' + productStatus + '</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.supplier_sku + '</span><span class="desc"><input value="' + supplierSku + '" type="text" class="color-blue supplier_sku po_textbox w-120" name="supplier_sku_' + data.id + '" maxlength="15" /></span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.best_before + '</span><span class="desc"><input type="text" name="best_before_date_' + data.id + '" class="best_before_date po_textbox w-120" autocomplete="off" /></span></div></td>';
        DataHtml += '<td><input data-barcode="' + barcode + '"   maxlength="20"  type="text" name="barcode_' + data.id + '" class="barcode po_textbox input-barcode" value="' + barcode + '" />';
        if (data.product_type == 'parent')
            DataHtml += '<div class="d-flex group-item mt-3"><span class="desc"><label class="fancy-checkbox"><input type="checkbox" checked="checked" name="variant" class="variant"><span><i></i>' + POUNDSHOP_MESSAGES.purchase_order.items.tables.variant + '</span></label></span></div>';
        else
            DataHtml += '<div class="d-flex group-item mt-3"><span class="desc"><label class="fancy-checkbox"><input type="checkbox" name="variant" class="variant"><span><i></i>' + POUNDSHOP_MESSAGES.purchase_order.items.tables.variant + '</span></label></span></div>';
        DataHtml += '<td>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.qty_per_box + '</span><span class="desc"><input maxlength="8"  type="text" name="qty_per_box_' + data.id + '" class="qty_per_box po_textbox" /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_box + '</span><span class="desc"><input value=""  maxlength="8"  type="text" name="total_box_' + data.id + '" class="total_box po_textbox"  /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_qty + '</span><span class="desc"><input  maxlength="9"   type="text" name="total_quantity_' + data.id + '" class="total_quantity po_textbox"  /></span></div>';
        DataHtml += '</td>';
        DataHtml += '<td><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" maxlength="9" name="unit_price_' + data.id + '" class="unit_price po_textbox" /></div></td>';
        DataHtml += '<td><span class="title">&#163;</span><span  name="total_product_cost_' + data.id + '" class="title total_product_cost po_textbox">0.00</span></td>';
        DataHtml += '<td>';

        if (data.vat_type == 0) { //standard
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="desc vat" data-value="' + $("#hidden_standard_rate").val() + '">' + $("#hidden_standard_rate").val() + '%</span></div>';
        } else if (data.vat_type == 1) //Zero rated
        {
            DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat desc" data-value="' + $("#hidden_zero_rate").val() + '">' + $("#hidden_zero_rate").val() + '%</span></div>';
        } else { // mixed rate

            DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.std_rate + '</span> <span class="desc"><input type="text" class="standard_rate po_textbox" value="0" oninput="calculateMixRate(this)" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.zero_rate + '</span><span class="desc"><input type="text" class="zero_rate" value="0" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-60" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat desc" data-value="0">0%</span></div>';
        }
        DataHtml += '</td>';


        if (data.ros !== 0 && data.ros !== undefined) {
            DataHtml += '<td><div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.expected + '</span><span class="desc"><input type="text" name="expected_mros_' + data.id + '" class="expected_mros po_textbox" value="' + data.ros + '"  /></span></div>';
        } else {
            DataHtml += '<td><div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.expected + '</span><span class="desc"><input type="text" name="expected_mros_' + data.id + '" class="expected_mros po_textbox" /></span></div>';
        }

        DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_qty + '</span><span class="desc"><input type="text" name="sel_qty_' + data.id + '" class="sel_qty po_textbox" maxlenth="5" value="1" /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_price + '</span><span class="desc"><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" name="sel_price_' + data.id + '" class="sel_price po_textbox" maxlength="9" value="1" /></div></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.mros + '</span><span class="desc"><input value="20" readonly="readonly" type="text" name="mros_' + data.id + '" class="mros po_textbox" maxlength="9" /></span></div></td>';
        DataHtml += '<td  align="center"><input type="hidden" class="is_listed_on_magento" value="' + data.is_listed_on_magento + '" /> <input type="hidden" class="vat_type" value="' + data.vat_type + '" /><input type="hidden" class="landed_product_cost" /><input type="hidden" class="net_selling_price_excluding_vat" /><input type="hidden" class="total_net_selling_price " /><input type="hidden" class="total_net_profit" /><input type="hidden" class="total_net_margin" /> <a href="javascript:;" class="removeRow"><span class="icon-moon icon-Delete"></span></a></td>';
        DataHtml += '</tr>';
        return DataHtml;

    }
    calculateMixRate = function (obj) {
        var $obj = $(obj);
        var standardRate = parseFloat(undefinedValue($("#hidden_standard_rate").val()));
        var unitPrice = parseFloat(undefinedValue($obj.closest('tr').find('.unit_price').val()));
        var customStandardRate = parseFloat(undefinedValue($obj.closest('tr').find('.standard_rate').val()));
        var finalVat = 0;

        if (unitPrice !== 0 && unitPrice !== undefined)
            finalVat = (customStandardRate / unitPrice) * standardRate;

        var vatObj = $obj.closest('tr').find('.vat');
        vatObj.attr("data-value", finalVat.toFixed(2));
        vatObj.text(finalVat.toFixed(2) + "%");


        calculateImportDutyInCost($obj);
        calculateVatInAmount($obj);
        calculateDeliveryCharge($obj);
        calculateTotalVAT($obj);
        calculateNetSellingExculudingVAT($obj);
        calculateTotalNetSellingPrice($obj);
        calculateSubTotal();
        calculateOverAllNoOfCubes();
        importDutyCharges();
        totalCost();
        calculateGlobalPriceMargin();


    }

    bindImportItemsData = function (data) {
        
        var barcode = '';

        if (data.bar_codes.length > 0)
        {

            for (var i = 0; i < data.bar_codes.length; i++) {
                if (data.bar_codes[i].barcode_type == 1) {
                    barcode = data.bar_codes[i].barcode;
                    break;
                }
            }
        }
        var supplierSku = "";
        if (data.supplier.supplier_sku !== "" && data.supplier.supplier_sku !== undefined) {
            supplierSku = data.supplier.supplier_sku;
        }
        var DataHtml = '';
        DataHtml += '<tr>';
        var productStatus = "New";
        if (data.title == "")
            data.title = "--"

        if (data.sku == "")
            data.sku = "--"


        if (data.is_listed_on_magento == 1)
            productStatus = "Listed";
        else if (data.is_listed_on_magento == 2)
            productStatus = "Delisted";

        DataHtml += '<td id=""><div class="d-flex"><label class="fancy-checkbox"><input name="ids[]" type="checkbox" class="child-checkbox" value="' + data.id + '"/><span><i></i></span></label></div></td>';
        DataHtml += '<td><div class="d-flex group-item"><span class="title color-light w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.sku + '</span><span class="desc color-blue">' + data.sku + '</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.title + '</span><span class="desc color-light po-product-title" title="' + data.title + '">' + data.title + '</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.status + '</span><span class="desc color-blue">' + productStatus + '</span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.supplier_sku + '</span><span class="desc"><input type="text" value="' + supplierSku + '" class="color-blue supplier_sku po_textbox w-120" name="supplier_sku_' + data.id + '" maxlength="15" /></span></div> <div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.best_before + '</span><span class="desc"><input type="text" name="best_before_date_' + data.id + '" class="best_before_date po_textbox w-120"  autocomplete="off"/></span></div></td>';
        DataHtml += '<td><input data-barcode="' + barcode + '"   maxlength="20"  type="text" name="barcode_' + data.id + '" class="barcode po_textbox input-barcode" value="' + barcode + '" />';

        if (data.product_type == 'parent')
            DataHtml += '<div class="d-flex group-item mt-3"><span class="desc"><label class="fancy-checkbox"><input type="checkbox" name="variant" checked="checked" class="variant"><span><i></i>' + POUNDSHOP_MESSAGES.purchase_order.items.tables.variant + '</span></label></span></div>';
        else
            DataHtml += '<div class="d-flex group-item mt-3"><span class="desc"><label class="fancy-checkbox"><input type="checkbox" name="variant" class="variant"><span><i></i>' + POUNDSHOP_MESSAGES.purchase_order.items.tables.variant + '</span></label></span></div>';
        DataHtml += '<td>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.qty_per_box + '</span><span class="desc"><input maxlength="8"  type="text" name="qty_per_box_' + data.id + '" class="qty_per_box po_textbox" /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_box + '</span><span class="desc"><input value=""  maxlength="8"  type="text" name="total_box_' + data.id + '" class="total_box po_textbox"  /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_qty + '</span><span class="desc"><input  maxlength="9"   type="text" name="total_quantity_' + data.id + '" class="total_quantity po_textbox"  /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.cube_per_box + '</span><span class="desc"><input value=""  maxlength="8"  type="text" name="cube_per_box_' + data.id + '" class="cube_per_box po_textbox" /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.total_cubes + '</span><span class="desc"><input value=""  maxlength="9"  type="text" name="total_num_cubes_' + data.id + '" readonly="readonly" class="total_num_cubes po_textbox" /></span></div>';
        DataHtml += '</td>';
        DataHtml += '<td><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" maxlength="9" name="unit_price_' + data.id + '" class="unit_price po_textbox"/></div></td>';
        DataHtml += '<td><span class="title">&#163;</span><span  name="total_product_cost_' + data.id + '" class="title total_product_cost po_textbox">0.00</span></td>';
        var importDuty = 0;
        var countryCommodityCodes = $.parseJSON($("#countries_commodities").val());
        var isDefault = false;
        if (data.commodity_code_id !== "" && data.commodity_code_id !== undefined && data.commodity_code_id !== null) { //If product has commodity code
            if (countryCommodityCodes.length > 0) { // if exists take it from same country commodity else 0
                for (var i = 0; i < countryCommodityCodes.length; i++) {
                    if (countryCommodityCodes[i].pivot.commodity_code_id == data.commodity_code_id) { // if found then take is default
                        importDuty = countryCommodityCodes[i].pivot.rate;
                        isDefault = true;
                        break;
                    }
                }
                if (!isDefault) {
                    importDuty = 0;
                }
            } else {
                importDuty = 0;
            }
        } else { // else take country default commodity code value
            if (countryCommodityCodes.length > 0) {
                for (var i = 0; i < countryCommodityCodes.length; i++) {
                    if (countryCommodityCodes[i].is_default == 1) { // if found then take is default
                        importDuty = countryCommodityCodes[i].pivot.rate;
                        isDefault = true;
                        break;
                    }
                }
                if (!isDefault) { //else store default as a 0
                    importDuty = 0;
                }
            } else {
                importDuty = 0;
            }
        }

        DataHtml += '<td>'
        if (data.vat_type == 0) { //standard
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="desc vat" data-value="' + $("#hidden_standard_rate").val() + '">' + $("#hidden_standard_rate").val() + '%</span></div>';
        } else if (data.vat_type == 1) //Zero rated
        {
            DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat desc" data-value="' + $("#hidden_zero_rate").val() + '">' + $("#hidden_zero_rate").val() + '%</span></div>';
        } else { // mixed rate

            DataHtml += '<div class="d-flex group-item"><span class="title w-80" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.std_rate + '</span><span class="desc"><input type="text" class="standard_rate po_textbox" value="0" oninput="calculateMixRate(this)" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.zero_rate + '</span><span class="desc"><input type="text" class="zero_rate" value="0" /></span></div>';
            DataHtml += '<div class="d-flex group-item"><span class="title w-80" >' + POUNDSHOP_MESSAGES.purchase_order.items.tables.vat + '</span><span class="vat" data-value="0">0%</span></div>';
        }

        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.import_duty + '</span><span class="desc">';
        if (importDuty !== 0)
            DataHtml += '<span class="import_duty" data-value="' + importDuty + '">' + importDuty + '%</span>';
        else
            DataHtml += '<span class="import_duty" data-value="0">0%</span>';
        DataHtml += '</span></div>';

        DataHtml += '<div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.tot_del_charge + '</span><span class="desc"><div class="d-flex align-items-center">&#163;<span class="total_delivery_charge"></span></div></span></div><div class="d-flex group-item"><span class="title w-80">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.landed_product_cost + '</span><span class="desc"><span class="landed_product_cost"  ></span></span></div>';
        DataHtml += '</td>'


        if (data.ros !== 0 && data.ros !== undefined) {
            DataHtml += '<td><div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.expected + '</span><span class="desc"><input type="text" name="expected_mros_' + data.id + '" class="expected_mros po_textbox w-60" value="' + data.ros + '"  /></span></div>';
        } else {
            DataHtml += '<td><div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.expected + '</span><span class="desc"><input type="text" name="expected_mros_' + data.id + '" class="expected_mros po_textbox w-60" /></span></div>';
        }
        DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_qty + '</span><span class="desc"><input type="text" name="sel_qty_' + data.id + '" class="sel_qty po_textbox w-60" maxlenth="5" value="1" /></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.selling_price + '</span><span class="desc"><div class="position-relative"><span class="pound-sign">&#163;</span><input type="text" name="sel_price_' + data.id + '" class="sel_price po_textbox w-60" maxlength="9" value="1" /></div></span></div>';
        DataHtml += '<div class="d-flex group-item"><span class="title w-60">' + POUNDSHOP_MESSAGES.purchase_order.items.tables.mros + '</span><span class="desc"><input type="text" name="mros_' + data.id + '" readonly="readonly" class="mros po_textbox valid w-60" maxlength="9" aria-invalid="false" value="20"></span></div>';
        DataHtml += '</td>';

        DataHtml += '<td align="center"><input type="hidden" class="is_listed_on_magento" value="' + data.is_listed_on_magento + '" /> <input type="hidden" class="vat_type" value="' + data.vat_type + '" /> <input type="hidden" class="vat_in_amount" /> <input type="hidden" class="import_duty_in_cost" /><input type="hidden" class="totalProductCostImportDutyDeliveryCharge" /><input type="hidden" class="total_vat" /><input type="hidden" class="currency_exchange_rate" /><input type="hidden" class="landed_price_in_pound" /><input type="hidden" class="total_net_selling_price" /><input type="hidden" class="gross_sel_price_exc_vat" /><input type="hidden" class="total_net_profit" /><input type="hidden" class="total_net_margin" /><a href="javascript:;" class="removeRow"><span class="icon-moon icon-Delete"></span></a></td>';
        DataHtml += '</tr>';
        return DataHtml;
    }

    $(document).on("keypress", ".unit_price,.sel_price,#total_delivery,.standard_rate,.zero_rate,.cube_per_box", function (e) {
        if (((e.which != 46 || (e.which == 46 && $(this).val() == '')) ||
                $(this).val().indexOf('.') != -1) && (e.which < 48 || e.which > 57)) {
            e.preventDefault();
        }

    });
    $(document).on("keydown", ".expected_mros,.qty_per_box,.total_box,.total_quantity,.sel_qty,#total_space", function (e) {
        var key = e.charCode || e.keyCode || 0;
        // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
        // home, end, period, and numpad decimal

        if (!(key == 65 || key == 8 ||
                key == 9 ||
                key == 13 ||
                key == 46 ||
                (key >= 35 && key <= 40) ||
                (key >= 48 && key <= 57) ||
                (key >= 96 && key <= 105))) {
            e.preventDefault();
        }
    });
    $("#po-items-container").on("input", ".qty_per_box,.total_box,.total_quantity,.cube_per_box", function (e) {
        if ($("#po_import_type") == 1) {
            calculateQty($(this), $(this).attr('class'));
            calculateTotalProductCost($(this).closest('tr').find('.total_quantity'));
            calculateSubTotal();
            calculateGlobalPriceMargin();
        } else {
            calculateQty($(this), $(this).attr('class'));
            calculateTotalNoofCubes($(this));
            calculateTotalProductCost($(this).closest('tr').find('.total_quantity'));
            calculateImportDutyInCost($(this));
            calculateVatInAmount($(this));
            calculateDeliveryCharge($(this));
            calculateTotalVAT($(this));
            calculateNetSellingExculudingVAT($(this));
            calculateTotalNetSellingPrice($(this));
            calculateSubTotal();
            calculateOverAllNoOfCubes();
            importDutyCharges();
            totalCost();
            calculateGlobalPriceMargin();
        }


    });

    $(document).on("input", "#total_space", function (event) {
        calculateOverAllNoOfCubes();
    });
    $('#po-items-container').on('input', ".total_quantity,.unit_price", function (e) {
        calculateTotalProductCost($(this));
        if ($(this).closest("tr").find(".standard_rate").length > 0)
            calculateMixRate(this);
        calculateImportDutyInCost($(this));
        calculateVatInAmount($(this));
        calculateTotalVAT($(this));
        calculateTotalNetSellingPrice($(this));
        calculateOverAllNoOfCubes();
        importDutyCharges();
        totalCost();
        calculateGlobalPriceMargin();
        
    });
    $('#po-items-container').on('input', ".sel_qty,.sel_price", function (e) {
        calculateNetSellingExculudingVAT($(this));
        calculateTotalNetSellingPrice($(this));
        calculateOverAllNoOfCubes();
        importDutyCharges();
        totalCost();
        calculateGlobalPriceMargin();

    });
    bindProductData = function (data) {
        varDataHtml = "";
        for (var i = 0; i < data.length; i++) {

            var sku = "--";
            var supplierSku = "--";
            var productIdentifier = "--";
            var productTitle = "--";
            if (data[i].sku !== "" && data[i].sku !== undefined && data[i].sku !== null) {
                sku = data[i].sku;
            }

            if (data[i].supplier.supplier_sku !== "" && data[i].supplier.supplier_sku !== undefined && data[i].supplier.supplier_sku !== null) {
                supplierSku = data[i].supplier.supplier_sku;
            }
            if (data[i].product_identifier !== "" && data[i].product_identifier !== undefined && data[i].product_identifier !== null) {
                productIdentifier = data[i].product_identifier;
            }
            if (data[i].title !== "" && data[i].title !== undefined && data[i].title !== null) {
                productTitle = data[i].title;
            }
            varDataHtml += '<tr>';
            varDataHtml += "<td><div class='d-flex'><label class='fancy-checkbox'><input name='ids[]' class='ids' type='checkbox' value='" + data[i].id + "' class='child-checkbox'><span><i></i></span></label></div></td>"
            varDataHtml += '<td><img style="width:70px;height:70px;" src="' + data[i].main_image_internal + '" /></td>';
            varDataHtml += '<td>' + sku + '</td>';
            varDataHtml += '<td>' + supplierSku + '</td>';
            varDataHtml += '<td>' + productIdentifier + '</td>';
            varDataHtml += '<td>' + productTitle + '</td>';
            varDataHtml += '</tr>';
            $("body").data("information" + data[i].id, data[i]);

        }
        return varDataHtml;
    }

    searchTable = $('#example').DataTable({
        bFilter: false,
        bInfo: false,
        processing: true,
        "oLanguage": {
            "sProcessing": '<img src="' + WEB_BASE_URL + '/img/loader.gif" width="40">',
            "sEmptyTable": "No Records Found",
        },
        columns: [
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false},
            {"orderable": false, "searchable": false}
        ],
        bPaginate: false,
        fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
        },
        fnDrawCallback: function (oSettings, json) {
            $(this).find('tr:first th:first').removeClass('sorting_asc').removeClass('sorting_desc');
        },
    });
    $(".master").click(function () {
        $("#example input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });
    $(".po_item_master").click(function () {
        $("#po-items-container  input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });



    $("#po-items-container").on('click', '.removeRow', function (event) {


        event.preventDefault();
        var $currentObj = $(this);
        var id = $(this).attr("data-id");
        var temp = false;

        if (id == undefined)
            temp = true;



        bootbox.confirm({
            title: "Confirm",
            message: "Are you sure you want to delete record? This process cannot be undone.",
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

                if (result) {

                    if (temp == true) {
                        $currentObj.closest('tr').remove();
                        calculateSubTotal();
                        calculateGlobalPriceMargin();
                        if (getTableLength() < 1) {
                            $(".po-item-data").hide();
                        }
                        return true;

                    }



                    $.ajax({
                        url: BASE_URL + 'api-purchase-orders-item-remove',
                        type: "post",
                        // processData: false,
                        data: {id: id},
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
                                //$("#po-items-container").html(response.data.data);
                                $currentObj.closest('tr').remove();
                                calculateSubTotal();
                                calculateGlobalPriceMargin();
//                                           $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function(e) {
//                                                $('.datepicker').hide();
//                                                $('.best_before_date').valid();
//                                           });
//                                           
//                                           if($("#po_import_type") == 2) {
//                                               $("#overall_total_no_cubes").text(response.data.total_no_of_cubes)
//                                               $("#remaining_space").text(response.data.remaining_space)
//                                               $("#total_cost").text(response.data.total_cost)
//                                               $("#overall_import_duty").text(response.data.total_import_duty)
//
//                                               $("#total_space").text(response.data.total_space)
//                                               $("#cost_per_cube").text(response.data.cost_per_cube)
//                                               $("#overall_total_delivery").text(response.data.total_delivery_charge)
//                                               $("#total_delivery").val(response.data.total_delivery_charge)
//
//                                           }
//                                           $("#sub_total").text(response.data.sub_total);
//                                           $("#supplier_min_amount").text(response.data.supplier_min_amount);
//                                           $("#remaining_amount").text(response.data.remaining_amount);
//                                           $("#total_margin").text(response.data.total_margin+"%");
                                if (getTableLength() < 1) {
                                    $(".po-item-data").hide();
                                }
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

    $(document).on('click', '.delete-many', function (event) {
        var allVals = [];
        var tempRow = false;
        $("#po-items-container input[name='ids[]']:checked").each(function (i, v) {
            if ($(this).closest('td').attr("id") !== "")
                allVals.push($(this).closest('td').attr("id"));
        });

        $('#po-items-container tr').each(function (row, tr) {
            if ($(tr).find('td:eq(0)').find('input[type="checkbox"]').is(":checked")) {
                if ($(tr).find('td').attr("id") == "" || $(tr).find('td').attr("id") == undefined) {
                    //remaining
                    $(tr).remove();
                    tempRow = true;
                }
            }
        });

        if (allVals < 1 && tempRow == false) {
            bootbox.alert({
                title: "Alert",
                message: "Please select atleast one record to delete.",
                size: 'small'
            });
            return false;
        }
        if (allVals < 1 && tempRow == true) {
            if (getTableLength() < 1) {
                $(".po-item-data").hide();
            }
            return false;
        }
        if (typeof allVals !== 'undefined' && allVals.length > 0)
        {
            bootbox.confirm({
                title: "Confirm",
                message: "Are you sure you want to delete selected records? This process cannot be undone.",
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
                    if (result) {
                        var join_selected_values = allVals.join(",");
                        $.ajax({
                            url: BASE_URL + 'api-purchase-orders-item-remove-multiple',
                            type: "post",
                            processData: false,
                            data: 'ids=' + join_selected_values + "&purchase_order_id=" + $("#po_id").val(),
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
                                    $("#po-items-container").html(response.data.data);
                                    $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function (e) {
                                        $('.datepicker').hide();
                                        $('.best_before_date').valid();
                                    });
                                    if ($("#po_import_type") == 2) {
                                        $("#overall_total_no_cubes").text(response.data.total_no_of_cubes)
                                        $("#remaining_space").text(response.data.remaining_space)
                                        $("#total_cost").text(response.data.total_cost)
                                        $("#overall_import_duty").text(response.data.total_import_duty)

                                        $("#total_space").text(response.data.total_space)
                                        $("#cost_per_cube").text(response.data.cost_per_cube)
                                        $("#overall_total_delivery").text(response.data.total_delivery_charge)
                                        $("#total_delivery").val(response.data.total_delivery_charge)

                                    }
                                    $("#sub_total").text(response.data.sub_total);
                                    $("#supplier_min_amount").text(response.data.supplier_min_amount);
                                    $("#remaining_amount").text(response.data.remaining_amount);
                                    $("#total_margin").text(response.data.total_margin + "%");
                                    if (getTableLength() < 1) {
                                        $(".po-item-data").hide();
                                    }

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
        } else
        {
            bootbox.alert({
                title: "Alert",
                message: "Please select atleast one record to delete.",
                size: 'small'
            });
            return false;
        }
    });

    $(window).on('load', function () {
        // calculateSubTotal();
        // calculateGlobalPriceMargin();
        $("#po-items-table > tbody .best_before_date").datepicker(options);

    });
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);
var current_tab = "";
$(document).ready(function () {


    $(".tab-click").click(function (e) {
        if (!$(this).parent().hasClass('responsivetabs-more')) {
            $(".tab-click").each(function () {
                var link = $(this);
                if (link.attr("id") == "general-tab" && link.hasClass('active')) {
                    $("#show-modal-btn").hide();
                    if (!$("#create-po-form").valid())
                    {
                        e.stopImmediatePropagation();
                    } else {
                        $("#create-po-form").submit();
                    }
                } else if (link.attr("id") == "item-tab" && link.hasClass('active')) {

                    if (!$("#field-set").prop('disabled')) {
                        $("#save-po-btn").trigger('click');
                        if (!$("#save-po-form").valid()) {
                            e.stopImmediatePropagation();
                            return false;
                        }
                    }
                } else if (link.attr("id") == "delivery-tab" && link.hasClass('active')) {
                    $("#show-modal-btn").hide();
                } else if (link.attr("id") == "revision-tab" && link.hasClass('active')) {
                    $("#show-modal-btn").hide();
                } else if (link.attr("id") == "terms-tab" && link.hasClass('active')) {
                    $("#show-modal-btn").hide();
                    if (!$("#po-terms-form").valid())
                    {
                        e.stopImmediatePropagation();
                    } else {
                        $("#po-terms-form").submit();
                    }
                }
            });
        }



        //after click
        setTimeout(function () {
            if (!$(this).parent().hasClass('responsivetabs-more')) {
                $(".tab-click").each(function () {
                    var link = $(this);
                    if (link.attr("id") == "general-tab" && link.hasClass('active')) {
                        console.log('general tab content')
                        $("#save-po-btn").hide();
                        $("#update-term-button").hide();
                        $("#create-po-button").show();
                        $("#show-modal-btn").hide();
                    } else if (link.attr("id") == "item-tab" && link.hasClass('active')) {
                        $("#create-po-button").hide();
                        $("#update-term-button").hide();
                        if ($("#hidden_po_status").val() > 5) {
                            $("#show-modal-btn").hide();
                            $("#save-po-btn").hide();
                        } else {
                            $("#show-modal-btn").show();
                            $("#save-po-btn").show();
                        }

                        //  $(".item-actions").show();
                    } else if (link.attr("id") == "revision-tab" && link.hasClass('active')) {
                        //  console.log(link.attr("id"))
                        $("#revision").DataTable().clear().destroy();
                        var field_coloumns = [
                            null,
                            null,
                            {"orderable": false, "searchable": false},
                        ];
                        var order_coloumns = [[0, "desc"]];
                        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table, 'revision', 'api-purchase-orders-revise', field_coloumns, order_coloumns, undefined, undefined, '', [], 'purchase-order-revision');
                        $("#create-po-button").hide();
                        $("#update-term-button").hide();
                        $("#save-po-btn").hide();
                        $("#show-modal-btn").hide();
                    } else if (link.attr("id") == "delivery-tab" && link.hasClass('active')) {
                        $("#create-po-button").hide();
                        $("#update-term-button").hide();
                        $("#save-po-btn").hide();
                        $("#show-modal-btn").hide();
                    } else if (link.attr("id") == "terms-tab" && link.hasClass('active')) {
                        console.log('test');
                        $("#show-modal-btn").hide();
                        $("#create-po-button").hide();
                        $("#save-po-btn").hide();
                        $("#update-term-button").show();
                        //  $(".item-actions").hide();
                    }
                });
            }

        }, 100);


    });

});
//
$(document).ready(() => {
    let url = location.href.replace(/\/$/, "");
    if (location.hash) {
        const hash = url.split("#");
        $('#myTab a[href="#' + hash[1] + '"]').tab("show");
        url = location.href.replace(/\/#/, "#");
        history.replaceState(null, null, url);

        if (hash[1] == 'general')
        {
            current_tab = hash[1];
            $("#save-po-btn").hide();
            $("#update-term-button").hide();
            $("#create-po-button").show();
            $("#show-modal-btn").hide();
        } else if (hash[1] == 'items') {
            current_tab = hash[1];
            if ($("#hidden_po_status").val() < 6) {
                $("#create-po-button").hide();
                $("#update-term-button").hide();
                $("#save-po-btn").show();
                $("#show-modal-btn").show();
            } else {
                $("#create-po-button").hide();
                $("#show-modal-btn").hide();
                $("#save-po-btn").hide();
            }
        } else if (hash[1] == 'revise') {
            $("#create-po-button").hide();
            $("#update-term-button").hide();
            $("#save-po-btn").hide();
            $("#show-modal-btn").hide();
        } else if (hash[1] == 'terms') {
            //$("#show-modal-btn").hide();
            $("#create-po-button").hide();
            $("#save-po-btn").hide();
            $("#update-term-button").show();
            $("#show-modal-btn").hide();
        } else if (hash[1] == 'deliveries') {
            $("#create-po-button").hide();
            $("#save-po-btn").hide();
            $("#update-term-button").hide();
            $("#show-modal-btn").hide();
        }
        setTimeout(() => {
            $(window).scrollTop(0);
        }, 400);
    }
    $('a[data-toggle="tab"]').on("click", function () {
        let newUrl;
        const hash = $(this).attr("href");
        if (hash == "#home") {
            newUrl = url.split("#")[0];
        } else {
            newUrl = url.split("#")[0] + hash;
        }
        newUrl += "/";
        history.replaceState(null, null, newUrl);
    });
});


