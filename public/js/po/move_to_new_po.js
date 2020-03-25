function MoveToNewPO() {
	var allVals = [];  
	var tempRow=false;
    $("#po-items-container input[name='ids[]']:checked").each(function(i,v) {  
        if($(this).closest('td').attr("id")!== "")
            allVals.push($(this).closest('td').attr("id"));
    });  
    
    $('#po-items-container tr').each(function(row, tr){
        if($(tr).find('td:eq(0)').find('input[type="checkbox"]:checked')){
            if($(tr).find('td').attr("id") == "" || $(tr).find('td').attr("id") == undefined){
                //remaining
                $(tr).remove();
                tempRow=true;
            }
        }
    }); 
    
    if(allVals<1 && tempRow == false){
        bootbox.alert({
            title: "Alert",
            message: POUNDSHOP_MESSAGES.buy_by_product.alert_msg.alert_select_atleast_one,
            size: 'small'
        });
        return false
    }else{
    	var poProductsId=allVals.join(',');
    	$('.po_products').val(poProductsId);
    	$('#moveToNewPOModal').modal('show');
    }

}
$('.custom-select-search').selectpicker({
  liveSearch:true,
  size:10
});
  getCookie=function(name){
        var dc = document.cookie;
        var prefix = name + "=";
        var begin = dc.indexOf("; " + prefix);
        if (begin == -1) {
            begin = dc.indexOf(prefix);
            if (begin != 0) return null;
        }
        else
        {
            begin += 2;
            var end = document.cookie.indexOf(";", begin);
            if (end == -1) {
            end = dc.length;
            }
        }
    // because unescape has been deprecated, replaced with decodeURI
    //return unescape(dc.substring(begin + prefix.length, end));
        var ans=null;
        var str=decodeURI(dc.substring(begin + prefix.length, end));
        console.log("cookieVal="+str);
        if(str!=null)
        {

            var strRes=str.split(";");
            ans=strRes[0];
        }
        return ans;
    }

bindPoData = function(data){
        varDataHtml = "";
        //console.log(document.cookie.indexOf('selectedPO='));
        var selectedPO=getCookie('selectedExistingPO');
        for(var i = 0; i< data.length; i++){
            var varTotalCost='0.00';
            var expDeliDate='--';
            var checked="";
            if(data[i].total_cost.length!=0)
            {
                varTotalCost=data[i].total_cost;
            }
            if(data[i].exp_deli_date.length!=0)
            {
                expDeliDate=data[i].exp_deli_date;
            }
            if(selectedPO==data[i].id)
            {
                checked="checked";
            }
            varDataHtml+='<tr>';
            varDataHtml+="<td><div class='d-flex'><label class='fancy-radio mr-3'><input type='radio' name='po_id' value='"+data[i].id+"' "+checked+"/><span><i></i></span></label></div></td>";
            varDataHtml+='<td>'+data[i].po_number+'</td>';
            varDataHtml+='<td>'+data[i].date+'</td>';
            varDataHtml+='<td>'+expDeliDate+'</td>';
            varDataHtml+='<td>'+data[i].total_num_items+'</td>';
            varDataHtml+='<td>'+POUNDSHOP_MESSAGES.common.pound_sign+' '+varTotalCost+'</td>';
            varDataHtml+='</tr>';
            $("body").data( "information"+data[i].id, data[i] );
        }
        return varDataHtml;
    }

$('#supplierPosForm').validate({
        focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function(form, validator) {
            console.log(validator);
        if (!validator.numberOfInvalids())
            return;
        var errors = validator.numberOfInvalids();
        if (errors) {                    
            validator.errorList[0].element.focus();
        }
        $('html, body').animate({
            scrollTop: $(validator.errorList[0].element).offset().top-30
        }, 1000);
      },
        errorElement: 'span',
        errorClass: 'invalid-feedback', // default input error message class
        ignore: [],
        rules: {
            "supplier_id": {
                required: true,
            },
        },
        errorPlacement: function (error, element) {
         if(element.attr("id") == "move_supplier_id"){
            error.insertAfter(element.closest(".dropdown"));
            }
            else
            {
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
             var options={
                format: 'dd-M-yyyy',
                todayHighlight: true,
                autoclose: true,
             };
            var btntext=$(".perform_action").val();
            
            if(btntext=="create_po_btn")
            {
                var supplier_country=$("#move_supplier_id").find('option:selected').attr('data-country');
                //console.log(supplier_country);
                if(supplier_country !== '230'){
                    $(".supplier_po_import_type").val(2);
                    $("#supplier_country_id").val(supplier_country)
                }
                else
                {
                    $(".supplier_po_import_type").val(1);
                    $("#supplier_country_id").val(supplier_country)
                }
                var dataString = $("#supplierPosForm").serialize();
                //console.log(dataString);return false;
               // console.log(dataString);return false;
                $('.submit_create_po').attr('disabled', true);
                $.ajax({
                        type: "POST",
                        url: BASE_URL+'api-move-product-to-new-po',
                        data: dataString+'&current_po='+$('#id').val(),
                       // processData: false,
                        headers: {
                            'Authorization': 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                            console.log(response);
                            $('.submit_create_po').attr('disabled', false);
                            $("#page-loader").hide();
                            if (response.status == 1) {
                                $('#moveToNewPOModal').modal('hide');
                                 $("#po-items-container").html(response.data.data);
                                 $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function(e) {
                                        $('.datepicker').hide();
                                        $('.best_before_date').valid();
                                   });
                                   
                                   if($("#po_import_type").val() == 2) {
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
                                   $("#total_margin").text(response.data.total_margin+"%");
                                   if(getTableLength() < 1){
                                        $(".po-item-data").hide();
                                    }
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                              
                            }
                        },
                        error: function (xhr, err) {
                            $('.submit_create_po').attr('disabled', false);
                            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                        }
                    });
            }
            else
            {
                var supplier_id = $("#move_supplier_id").val();
           
                $('.add_to_existing_po_btn').attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: BASE_URL+'api-existing-draft-pos',
                    data: "supplier_id="+supplier_id,
                    processData: false,
                    headers: {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    beforeSend: function () {
                        $("#page-loader").show();
                    },
                    success: function (response) {
                        $('.add_to_existing_po_btn').attr('disabled', false);
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            $('#addToExistingPoModel').modal('show');
                            if(response.data.data.length==0)
                            {
                                $('.submit_add_product_to_po').attr('disabled',true);
                            }
                            else
                            {
                                $('.submit_add_product_to_po').attr('disabled',false);
                            }
                            $("#existingPosTable >tbody").html(bindPoData(response.data.data));

                        }
                         PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    },
                    error: function (xhr, err) {
                        $('.add_to_existing_po_btn').attr('disabled', false);
                        PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                    }
                });

            }
            
        }
    });


function getTableLength(){
      return $('#po-items-table > tbody tr').length;
  }
function addProductToPo()
{
	
    var options={
        format: 'dd-M-yyyy',
        todayHighlight: true,
        autoclose: true,
     };
	if($("#existingPosTable input[name='po_id']:checked").val()==undefined)
	{
		 bootbox.alert({
            title: "Alert",
            message: POUNDSHOP_MESSAGES.buy_by_product.alert_msg.alert_select_po,
            size: 'small'
        });
        return false
	}
   /* else
    {
        if($("#existingPosTable input[name='po_id']:checked").val()==$('#id').val())
        {
            bootbox.alert({
            title: "Alert",
            message: POUNDSHOP_MESSAGES.buy_by_product.product_already_in_po,
            size: 'small'
            });
            return false
        }

    }*/
    var dataString = $("#addProductToPoForm").serialize();
    $('.submit_add_product_to_po').attr('disabled', true);
    $.ajax({
            type: "POST",
            url: BASE_URL+'api-move-product-to-existing-po',
             data: dataString+'&po_products='+$('.po_products').val()+'&current_po='+$('#id').val(),
            processData: false,
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                $('.submit_add_product_to_po').attr('disabled', false);
                $("#page-loader").hide();
                if (response.status == 1) {
                    console.log(response.data);
                    document.cookie="selectedExistingPO="+response.data.selectedExistingPO;
                    $('#addToExistingPoModel').modal('hide');
                    $('#moveToNewPOModal').modal('hide');
                     $("#po-items-container").html(response.data.data);
                     $("#po-items-container .best_before_date").datepicker(options).on("changeDate", function(e) {
                            $('.datepicker').hide();
                            $('.best_before_date').valid();
                       });
                       
                       if($("#po_import_type").val() == 2) {
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
                       $("#total_margin").text(response.data.total_margin+"%");
                       if(getTableLength() < 1){
                            $(".po-item-data").hide();
                        }
                    
                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                }
            },
            error: function (xhr, err) {
                $('.submit_add_product_to_po').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
}


$(".btn-form").click(function(){
     var btntext=$(this).attr('id');
     $('.perform_action').val(btntext);
})