/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var poundShopCodes = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
            $('body').data('remove_points_id',new Array());
        });
    };
    var c = poundShopCodes.prototype;    
    c._initialize = function ()
    {        

    };
    $(document).on('click', '.addMore', function (event) {
        var pointCount=parseInt($("input[name='total_points']").val())+1;
        var addStr=`<div class="point_`+pointCount+`">
                <div class="form-group row">
                    <label for="inputPassword" class="col-lg-2 col-form-label">Checklist Points<span class="asterisk">*</span></label>
                    <div class="col-lg-6">                       
                        <input type="text" name="checklist_points[]" class="form-control">
                    </div>
                    <div class="col-lg-2">
                       <a class="btn-delete bg-light-red" href="javascript:void(0);" id="point_`+pointCount+`"><span class="icon-moon icon-Delete"></span></a>
                    </div>
                </div>
            </div>`
                   
        $('#add_more_points').append(addStr);
        $("input[name='total_points']").val(pointCount);
    });
   $(document).on('click', '.btn-delete', function (event) {
       
        var currebtId=$(this).attr('id');
        //alert(currebtId);
        var attrVal=$(this).attr('attr-val');
        var total_points=$("input[name='total_points']").val();
        
        if(total_points=="1")
        {
            bootbox.alert({
                title: "Alert",
                message: "You can't delete checklist point, QC Checklist must have atleast one checklist point.",
                size: 'small'
            });
            return false;
        }
        if(attrVal!=undefined)
        {
            bootbox.confirm({ 
            title: "Confirm",
            message: "Are you sure you want to delete records? This process cannot be undone.",
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
                    var removePointIds=$('body').data('remove_points_id');
                    removePointIds.push(attrVal);
                    $('body').data('remove_points_id',removePointIds);
                    $("input[name='total_points']").val(total_points-1);
                    $('.'+currebtId).remove();
                    /*$.ajax({
                        url: BASE_URL + 'api-checklist-point-remove/'+attrVal,
                        type: "post",
                        processData: false,
                        data:{id:attrVal},
                        headers: {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                            $("#page-loader").hide();
                            if (response.status == 1) {
                                $("input[name='total_points']").val(total_points-1);
                                //$('.add-category-form').load(document.URL +  ' .add-category-form');
                                $('.'+currebtId).remove();
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            }
                        },
                        error: function (xhr, err) {
                           $("#page-loader").hide();
                           PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                        }

                    });*/
                }               
            }
        });                          
        }
        else
        {

            var points=parseInt($("input[name='total_points']").val());
            
            $("input[name='total_points']").val(--points);
            $('.'+currebtId).remove();
            if($('#add_more_points').children().length==0)
           {
                //$('.add-category-form').load(document.URL +  ' .add-category-form');
           }

        }
       
    });

   //To check any point's of qc checklist existes or not after deleting all points
   $(".btn-cancel").click(function(){
    var pointArr=[];
    $('input[name="checklist_points[]"]').each(function(){
          pointArr.push($(this).val());
        });
    
    if(pointArr.length<=0)
    {
        bootbox.alert({
            title: "Alert",
            message: "Please add atleast one checklist point.",
            size: 'small'
        });
        return false;
    }
    else
    {
        window.location.href = WEB_BASE_URL+"/qc-checklist/";
    }

   });
    $("#qc-checklist-form").validate({
        focusInvalid: false, // do not focus the last invalid input
        invalidHandler: function(form, validator) {

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
            "name": {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                maxlength: 40,
                minlength: 3,

            },
            "checklist_points[]": {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                maxlength: 40,
                minlength: 3,
            },
        },
         messages: {
                "checklist_points[].required": "This field is required.",
            },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        },
        highlight: function (element) { // hightlight error inputs
             $('.btn-blue').attr('disabled', true);
            $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
        },
        success: function (label) {
             $('.btn-blue').attr('disabled', false);
            label.closest('.form-group').removeClass('has-error');
            label.remove();
        },
        submitHandler: function (form) {
            $('#remove_points_id').val($('body').data('remove_points_id'));
            var dataString = $("#qc-checklist-form").serialize();
           // console.log(dataString);return false;
            $('.btn-blue').attr('disabled', true);
            $.ajax({
                type: "POST",
                url: $("#qc-checklist-form").attr("action"),
                data: dataString,
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                success: function (response) {
                    $('.btn-blue').attr('disabled', false);
                    $("#page-loader").hide();
                    if (response.status == 1) {
                        //$("#create-totes-form")[0].reset();
                        PoundShopApp.commonClass._displaySuccessMessage(response.message);
                        setTimeout(function () {
                            window.location.href = WEB_BASE_URL + '/qc-checklist';
                        }, 1000);
                    }
                },
                error: function (xhr, err) {
                    $('.btn-blue').attr('disabled', false);
                    PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });
        }
    });
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCodes = new poundShopCodes();

})(jQuery);





