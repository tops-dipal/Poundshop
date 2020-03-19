/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
     var selecteParentIdArr=[];
     var levelcounter=1;
     var nextaddMoreCat=0;
     var monthArr = [ "January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December" ];
    var poundShopRange = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();
                if($('body #process').val()=='edit')
                {
                    var seasonalStatusVal=$("body input[name='seasonal_status']:checked").val();
                   // console.log(seasonalStatusVal);
                    if(seasonalStatusVal=="1")
                    {
                       
                        $('body .seasonal_show').show();
                    }
                    else
                    {
                        
                        $('body .seasonal_show').hide();
                    }
                 }
                $('.datepicker').datepicker( {
                    changeMonth: false,
                    changeYear: true,
                    showButtonPanel: true,
                    dateFormat: 'dd MM'
                });
                var processVal=$('#process').val();
                
                
                if(processVal=='edit')
                {
                   
                    selectedParent=$("body #parentIds").val().split(",");
                    //getParent(value,selecteParentIdArr,levelcounter);
                    $.each(selectedParent, function( index, value ) {
                        if(value!=$('#editId').val())
                        {
                             getParent(value,selecteParentIdArr,levelcounter);
                            
                        }
                          $("body #child_category div").sort(function (elem1, elem2) {
                       
                        
                        return parseInt(elem1.id) > parseInt(elem2.id);
                            }).each(function () {
                            var element = $(this);
                            element.remove();
                            $(element).appendTo("body #child_category");
                        });
                       
                    });


                }
           
        });
    };
    var c = poundShopRange.prototype;
    
    c._initialize = function ()
    {
        

    };
    $('body').on('change','.seasonal_status',function(){
        var val=this.value;
        
        if($('body #process').val()=='edit')
        {
            if(val==1)
            {
                $('body .seasonal_show').show();
            }
            else
            {
                $('body .seasonal_show').hide();
            }
        }
        
    });
    
   
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".addMore").click(function(){
        nextaddMoreCat=nextaddMoreCat+1;
        var addStr=`<div class="form-group row addMoreCategory`+nextaddMoreCat+`">
            <label for="inputPassword" class="col-lg-4 col-form-label">`+POUNDSHOP_MESSAGES.range_management.cat_name+`<span class="asterisk">*</span></label>
            <div class="col-lg-8">
                <div class="input-btn">
                    <input type="text" class="form-control" id="" placeholder="" name="category_name[]">
                    <div class="btn-container addMoreCategory`+nextaddMoreCat+`">
                        <button type="button" class="btn btn-remove remove">-</button>
                    </div>
                </div>
                <div class="row mt-3 addMoreCategory`+nextaddMoreCat+`">
                    <div class="col-lg-6">
                        <label class="fancy-radio">
                            <input type="radio" name="seasonal_status[`+nextaddMoreCat+`]" value="2" class="seasonal_status" checked="">
                            <span><i></i>`+POUNDSHOP_MESSAGES.range_management.non_seasonal+`</span>
                        </label>        
                    </div>
                    <div class="col-lg-6">
                        <label class="fancy-radio">
                            <input type="radio" name="seasonal_status[`+nextaddMoreCat+`]" value="1" class="seasonal_status">
                            <span><i></i>`+POUNDSHOP_MESSAGES.range_management.seasonal+`</span>
                        </label>
                    </div>    
                </div>
                 <div class="mt-2  seasonal_show`+nextaddMoreCat+` hidden addMoreCategory`+nextaddMoreCat+`">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-lg-4 col-form-label">From</label>
                        <div class="col-lg-8">
                            <div class="d-flex input-select-group">
                                <input type="number" name="seasonal_range_fromdate[]" value="" class="form-control seasonal_show" min="1" max="31">
                                <select name="seasonal_range_frommonth[]" class="form-control seasonal_show">`;
                                   var i;
                                    for (i=0; i<12; i++) {
                                    
                                   addStr+= `<option value="`+i+`">`+monthArr[i]+`</option>`
                                    }
                              addStr+=  `</select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="seasonal_show`+nextaddMoreCat+` hidden addMoreCategory`+nextaddMoreCat+`">
                    <div class="form-group row">
                        <label for="inputPassword" class="col-lg-4 col-form-label">To</label>
                        <div class="col-lg-8">
                            <div class="d-flex input-select-group">
                                <input type="number" name="seasonal_range_todate[]" value="" class="form-control seasonal_show" min="1" max="31">
                                <select name="seasonal_range_tomonth[]" class="form-control seasonal_show">`;
                                   var j;
                                    for (j=0; j<12; j++) {
                                    
                                   addStr+= `<option value="`+j+`">`+monthArr[j]+`</option>`
                                    }
                               addStr+= `</select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;


    $('.addMoreCategory').append(addStr);
    });
     $('body').on('click','.remove',function(){

        var parentClass=$(this).parent().attr('class').split(" ");
        var parentClass=parentClass[1];
       
        
        $(".addMoreCategory").each(function() {
            
            
            var current_element = $(this).children().attr('class');
             
            if($.inArray( parentClass, current_element.split(" ")))
            {
                $('.'+parentClass).remove();
                
            }
         }); 
     
    });
   $('body').on('change','.seasonal_status',function(){
        var name=$(this).attr('name');
        var str=name.split("_").pop();
        var spitStr=str.split('[').pop();
        var splitStrNext=spitStr.split(']');
        var countNum=splitStrNext[0];
        
        if($("input[name='seasonal_status["+countNum+"]']:checked").val()=="1")
        {
           
            $('.seasonal_show'+countNum).removeClass('hidden');
        }
        else
        {
            $('.seasonal_show'+countNum).addClass('hidden');
        }
        
    
   });


    $('body').on('change','.parent_id',function(){
        //
        var parentId=$('option:selected', this).val();
        var childArr=$('.parent_id').attr('attr-child-nodes');
        var option = $('option:selected', this).attr('attr-child-nodes');
        var val=$(this).val();
        $('body #selected_parent').val(val);
        
        if(val!=0)
        {
            if ($.inArray(val, selecteParentIdArr) == -1)
            {
                selecteParentIdArr.push(val);
            }
            else
            {

                selecteParentIdArr= $(selecteParentIdArr).not([val]).get();

                selecteParentIdArr.push(val);
            }
        }
        else
        {
            selecteParentIdArr.pop()
        }
        if(option!='' && option!='undefined' && option!='null')
        {
           
            if(parentId!="")
            {
                $('#child_category #'+$(this).parent().attr('id')).nextAll('div').remove();
            }
            else
            {
                $('#child_category #'+$(this).parent().attr('id')).nextAll('div').remove();
            
            }

            if($(this).parent().parent().attr('id') ==undefined)
           {
           
            $('#child_category').children('div').remove();
            
           }
           
            var childRange=jQuery.parseJSON(option);
            var childDropDownStr='<div class="form-group row" id="'+parentId+'" data-sort="'+parentId+'"><label for="inputPassword" class="col-lg-4 col-form-label">Sub Category</label><div class="col-lg-8"><select class="form-control parent_id" name="parent_id"><option value="">--Select Sub Category--</option>';
            $.each(childRange, function( index, val ) {
              
              if(typeof val.children != 'undefined')
                {   
                    if(val.children.length > 0)
                    {
                        child_nodes = val.children; 
                        var child_nodesJson = JSON.stringify(val.children);
                    }
                    else
                    {
                        var child_nodesJson='';
                    }
                }
              childDropDownStr+="<option value='"+val.id+"' attr-child-nodes='"+child_nodesJson+"'>"+val.category_name+"</option>";
                
            });

            childDropDownStr+="</select></div></div>"
            
            $('#child_category').append(childDropDownStr);
        }
        else
        {
          if($(this).parent().parent().attr('id') ==undefined)
           {
           
            $('#child_category').children('div').remove();
            
           }
           else
           {
                $('#child_category #'+$(this).parent().attr('id')).nextAll('div').remove();
           }
           
           if($('#editId').val()!='' && $('#editId').val()!=undefined)
           {
            $.ajax({
            url: $("#get_child").val(),
            type: "post",
            data: {"id":val,"editId":$('#editId').val(),'_token':$('.token').val(),'selected_parent':val,'process':$('#process').val()},
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            success: function (response) {
              if(response.view=='')
              {
               
                $("div #"+response.parentOfSelectedId).nextAll('.form-group').remove();
                   
              }
              else
              {
                $("div #"+response.parentOfSelectedId).nextAll('.form-group').remove();
                $('body #child_category .form-group').each(function(){
                    if($(this).attr('id')==response.parentOfSelectedId)
                    {
                        $(response.view).insertAfter(this);
                    }
                });
              } 
            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
           }
            
             
         
        }
    })
var valarr = [];
$(document.body).on('focusout', "input[name='category_name[]']", function(){


      var curr = $(this).val();
     
      if (jQuery.inArray(curr, valarr) > -1) {
      } else {
          valarr.push(curr);
      }



  });

 $(document.body).on('click', '.edit-btn', function(){
    var route=$('.'+$(this).attr('id')).val();
    
    $.ajax({
            url: route,
            type: "GET",
            
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
                    $("#page-loader").show();
                },
            success: function (response) {
                 $("#page-loader").hide();
                if(response.view!="")
                {
                   $('.form').replaceWith(response.view);
                   $('#process').val('edit');
                   var selectedParent=[];
                   selectedParent=$("body #parentIds").val().split(",");
                   console.log(selectedParent);
                   console.log(selecteParentIdArr);
                    //getParent("",selecteParentIdArr,levelcounter);
                    $.each(selectedParent, function( index, value ) {
                        if(value!=$('body #editId').val())
                        {
                             getParent(value,selecteParentIdArr,levelcounter);
                            
                        }
                        /*$("body #child_category div").sort(function (elem1, elem2) {
                       
                        
                        return parseInt(elem1.id) > parseInt(elem2.id);
                            }).each(function () {
                            var element = $(this);
                            element.remove();
                            $(element).appendTo("body #child_category");
                        });*/
                       
                    });

                     var seasonalStatusVal=$("body input[name='seasonal_status']:checked").val();
                    
                    if(seasonalStatusVal=="1")
                    {
                       
                        $('body .seasonal_show').show();
                    }
                    else
                    {
                        
                        $('body .seasonal_show').hide();
                    }
                   
                }
                else
                {
                   
                    
                }
            },
            error: function (xhr, err) {
                $('.btn-primary').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
 });
    $(document.body).on('click', '.btn-blue', function(){
        
     $("#create-range-form").validate({
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
            "category_name[]":  {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                },
                maxlength: 40,
                minlength: 3,
            },
            },
            messages: {
                "category_name[].required": "Please select category",
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
             
                 bootbox.confirm({ 
                title: "Confirm",
                message: "Are you sure?.",
                buttons: {
                    cancel: {
                        label: 'No',
                        className: 'btn-gray'
                    },
                    confirm: {
                        label: 'Yes',
                        className: 'btn-green'
                    }
                },
                callback: function (result) 
                {
                    if(result==true)
                    {
                        var dataString = $("body #create-range-form").serialize();
                       
                        $('body .btn-blue').attr('disabled', true);
                        $.ajax({
                            type: "POST",
                            url: $("body #create-range-form").attr("action"),
                            data: dataString,
                            processData: false,

                            headers: {
                                'Authorization': 'Bearer ' + API_TOKEN,
                            },
                            beforeSend: function () {
                                $("body #page-loader").show();
                            },
                            success: function (response) {
                                
                                $('body .btn-blue').attr('disabled', false);
                                $("body #page-loader").hide();
                                if (response.status == 1) {
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    if(response.data.reset=="1")
                                    {
                                    
                                    }
                                    else
                                    {
                                        $("#create-range-form")[0].reset();
                                    }
                                    getListAfterAddEdit();
                                    
                                }
                            },
                            error: function (xhr, err) {
                                $('body .btn-blue').attr('disabled', false);
                                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                            }
                        });
                    }
                }

            });

            }
        });
 });
        $(document).on('click', '.btn-delete', function (event) {
        event.preventDefault();
        var $currentObj = $(this);
         var idStr = $(this).attr("id").split("_"); 
        var childdivClass="expandChildList_"+idStr[1];
        var parentDivId=$(this).parent().parent().closest('div').closest('ul').parent().attr('class');
        var parentCatDivId=$('.'+parentDivId).prev('div').attr('id');
        var parentIdExpand = $(this).parent().parent().closest('li');
         var id=idStr[1];           
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
                if(result==true)
                {
                 $.ajax({
                    url: BASE_URL + 'api-range-remove/'+id,
                    type: "post",
                    processData: false,
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

                               
                                $(parentIdExpand).remove();
                                if($('.'+parentDivId).children('ul').is(':empty'))
                                {
                                     
                                  
                                    $('#expandParent_'+parentIdExpand[1]).remove();
                                }
                                
                                 PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                
                                setTimeout(function () {

                                }, 1000);
                                
                                
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

   $(document).on("click",'.expand',function(){
        event.preventDefault();
       
        var val=this.value;
        var sd=$(this).attr('id').split("_");  
        var parentId=sd[1];
        var expandId=$(this).attr('id');
        var childArr=$(this).attr('attr-child-nodes');
        var haveChild=$(this).attr('attr-child-nodes');
        
        if($('#'+expandId).text()=="+" && haveChild!=undefined)
        {

             $('#'+expandId).text('-');
            var childNodes = jQuery.parseJSON(childArr);
             var str=`<ul class="child-ul">`;
            $.each(childNodes, function( index, val ) {
               if(typeof val.children != 'undefined')
                {   
                    if(val.children.length > 0)
                    {
                        child_nodes = val.children; 
                        var child_nodesJson = JSON.stringify(val.children);
                    }
                    else
                    {
                        var child_nodesJson='';
                    }
                }
                    str+=`<li>
                            <div class="parent-category" id="parentCatId_`+val.parent_id+`">`;
                    if(val.child_status==1){
                        str+=`<a id="expandParent_`+val.id+`" href="javascript:void(0);" class="expand" attr-child-nodes='`+child_nodesJson+`'>+</a>`;
                    }
                    var seasonal=(val.seasonal_status==1) ? 'Seasonal' : '';
                    str+=`<span class="name"><a id="editParent_`+val.id+`"  class="edit-btn" title="Edit Category">`+val.category_name+`</a></span>`;
                    str+=` <div class="category-action">
                        <span class="name">`+seasonal+`</span>
                                <input type="hidden" class="editParent_`+val.id+`" value="`+val.edit_url+`">
                                <a  id="editParent_`+val.id+`"  class="btn btn-blue edit-btn" >
                                    `+POUNDSHOP_MESSAGES.range_management.edit+`
                                </a>
                                <a href="#" id="deleteParent_`+val.id+`" class="btn btn-red btn-delete">
                                `+POUNDSHOP_MESSAGES.range_management.delete+`
                                </a>
                            </div></div>
                                <div class="expandChildList_`+val.id+`">
                                </div>
                            </li>`;

            });
       
            str+=`</ul>`;
             $('.expandChildList_'+parentId).show();
                  $('.expandChildList_'+parentId).html(str);
            
        }
        else
        {
            if(haveChild!=undefined)
            {
                $('#'+expandId).text('+');
             $('.expandChildList_'+parentId).hide();
            }
        }
      
    });
     
window.PoundShopApp = window.PoundShopApp || {}
window.PoundShopApp.poundShopRange = new poundShopRange();

})(jQuery);

function getListAfterAddEdit()
{
    $.ajax({
            url:  BASE_URL + 'api-range',
            type: "GET",
            processData: false,
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
                'Panel': 'web'
            },
            success: function (response) {
              $('.range_data').html(response.view);

               getForm('create');

            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
}
function getForm(formtype)
{
    $.ajax({
        url:  WEB_BASE_URL + '/range-form-type/'+formtype,
        type: "get",
        headers: {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        success: function (response) {
          $('.form').replaceWith(response.view);
           
        },
        error: function (xhr, err) {
            $('.btn-blue').attr('disabled', false);
            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
        }
    });
}

function getParent(val,selecteParentIdArr,levelcounter)
{
   
  
    var val=val;
      
   // console.log(selecteParentIdArr);
       
        var divArr=[];
        $('body #selected_parent').val(val);
      
        if(val!=0)
        {
            if ($.inArray(val, selecteParentIdArr) == -1)
            {
                selecteParentIdArr.push(val);
            }
            else
            {
                selecteParentIdArr= $(selecteParentIdArr).not([val]).get();

                selecteParentIdArr.push(val);
            }
        }
        else
        {
            selecteParentIdArr.pop()
        }
        //console.log(selecteParentIdArr);
        $.ajax({
            url: $("#get_child").val(),
            type: "post",
            data: {"id":val,"editId":$('#editId').val(),'_token':$('.token').val(),'selected_parent':val,'process':$('#process').val()},
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            success: function (response) {
               if(response.view!='' && response.parent_id!=$('#selected_parent').val())
                {
                    $("body #child_category").append(response.view);
                   /* var $wrapper = $('#child_category');

                    $wrapper.find('.row').sort(function (a, b) {
                        
                        return +a.dataset.sort - +b.dataset.sort;
                    })
                    .appendTo( $wrapper );*/

                    
                    
                }
                 
               
            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
        /*var parents=$("body #parentIds").val().split(",");
        let popped = parents.pop();
        
        $.each(parents, function( key, value ) {
           
                if(parents.length<2)
                {
                    $('#child_category .parent_id').trigger('change');
                   
                    $("#child_category .parent_id").val(value);
                }
                else
                {
                    $('.parent_id').trigger('change');
                    console.log(value);
                    $('#child_category .parent_id').val(value);


                }
           
           
        });*/
        //   $('#'+select_id).trigger("onchange");
        // $('#child_category .parent_id').each('change',function(){
        //     $.each(parents, function( key, value ) {
        //         $('#child_category .parent_id').val(value);
        //    });
        // });
/*
        $('#child_category .parent_id').each(function(){
            console.log($(this).val());
            if($(this).val()!=null)
            {
                 $(this).trigger('change');
            }
            else
            {
                $.each(parents, function( key, value ) {
                    console.log("Sdfds");
                    $('#child_category .parent_id').val(value);
                });
            }
             

        });*/


}


