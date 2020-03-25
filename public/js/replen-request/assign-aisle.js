(function ($)
{
    "user strict";
    var dataTableId = 'booking_table';
    var productInfoTable;
    var bulkAisleForWarehouseHtml;
    var countAisleAssign=1;
    var bulkAisleForWarehouse='';
    var assignAisleExistingData;
    var siteUserHtml;
    var prorityHtml='';
    var aisleUseData=[];
    var poundShopCartons = function ()
    {
        $(document).ready(function ()
        {
            c._initialize();

           // $('#warehouse_id').trigger('change');
             $("body").data('aisleUseData',[]);
             
            
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    };
    var c = poundShopCartons.prototype;
    
    c._initialize = function ()
    {
      
       
         
    };
    
   
    $('#warehouse_id').change(function(){
        var prioritySection=$('option:selected', this).attr('attr-priority-data');

         var sitePriorityData = jQuery.parseJSON(prioritySection);


        bindHTMLForPriorityData(sitePriorityData);
        refreshDiv();
        /*$('.assign_aisle_div').remove();
        countAisleAssign=1;

        //bulk aisle data
        var attrAisle=$('option:selected', this).attr('attr-bulk-aisle');

        var bulkAisleData=attrAisle.split(",");
        

        $.each(bulkAisleData, function( index, value ) {
         bulkAisleForWarehouseHtml+="<option value='"+value+"'>"+value+"</option>";
        });
        

        //user Data
        var siteUsers=$('option:selected', this).attr('attr-site-users');

        
        var siteUsersData = jQuery.parseJSON(siteUsers);
       
        if(siteUsersData.length>0)
        {
            $.each(siteUsersData, function( index, value ) {
              siteUserHtml+="<option value='"+value.id+"'>"+value.first_name+" "+value.last_name+"</option>";
            });
        }
        else
        {
            siteUserHtml='';
        }

        //existing assign ailse data
        var attrExistingData=$('option:selected', this).attr('attr-assign-aisle-data');
        assignAisleExistingData = jQuery.parseJSON( attrExistingData );

         $('#refreshDiv').nextAll('div').remove();
        bindHTMLForAlreadySavedData(bulkAisleForWarehouseHtml,siteUserHtml,assignAisleExistingData,bulkAisleData,siteUsersData);

        var prioritySection=$('option:selected', this).attr('attr-priority-data');

         var sitePriorityData = jQuery.parseJSON(prioritySection);


        bindHTMLForPriorityData(sitePriorityData);*/
    }); 

    bindHTMLForPriorityData=function(priorityData){
        var priorityHtml='';
        if(priorityData.length>0)
        {
            $.each(priorityData, function( index, value ) {
                if(value.aisle!=null)
                {
                    priorityHtml+=' <button type="button" class="btn btn-blue mb-1">Aisle '+value.aisle+' <span class="badge badge-light aisles-cnt"> '+value.count_product+'</span></button>';
                }
            });
            if(priorityHtml=='')
            {
                $('.priorityAisle').hide();
            }
            else
            {
                $('.priorityAisle').show();
            }
            $('.priority_load').html(priorityHtml);
        }
        else
        {
            priorityHtml+'-';
            $('.priorityAisle').hide();
        }
    }
    
    bindHTMLForAlreadySavedData = function(bulkAisleForWarehouseHtml,siteUserHtml,assignAisleExistingData,bulkAisleData,siteUsersData)
    {

        var numOfData=assignAisleExistingData.length;
     // console.log(bulkAisleForWarehouseHtml);
        var htmlContent='';
        if(numOfData>0)
        {
            
            var plusPosition=numOfData-1;
            for (var i = 0; i < numOfData; i++) {
                htmlContent+=  ` <input type="hidden" name="total_existing_record" value="`+numOfData+`" id="total_existing_record">
                <div class="col-lg-12 assign_aisle_div assign_aisle_div_`+countAisleAssign+`" id="assign_aisle_div_`+countAisleAssign+`">
                                <input type="hidden" name="update_id[]" id="update_id_`+i+`"  value="`+assignAisleExistingData[i].id+`">
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                         <select class="form-control" id="aisle_`+i+`" name="aisle[]">
                                               `;
                $.each(bulkAisleData, function( index, value ) {
                    if(value==assignAisleExistingData[i].aisle)
                    {
                        var selected="selected='selected'";
                    }
                    else
                    {
                        var selected='';
                    }
                    htmlContent+="<option value='"+value+"'"+selected+">"+value+"</option>";
                   // bulkAisleForWarehouseHtml+="<option value='"+value+"'"+selected+">"+value+"</option>";
                });
                                               
                htmlContent+=`</select>
                                </div>
                                <div class="col-lg-4">
                                 <select class="form-control" id="user_id" name="user_id[]">`;
                $.each(siteUsersData, function( index1, value1 ) {
                    if(value1.id==assignAisleExistingData[i].user_id)
                    {
                        var selected1="selected='selected'";
                    }
                    else
                    {
                        var selected1='';
                    }
                    htmlContent+="<option value='"+value1.id+"'"+selected1+">"+value1.first_name+" "+value1.last_name+"</option>";
                });
                htmlContent+=` </select>
                                </div>
                                <div class="col-lg-4 btnDiv`+countAisleAssign+`">
                                    <a  attr-div="assign_aisle_div_`+countAisleAssign+`" class="btn-delete bg-light-red  deleteBtn deleteBtn_`+countAisleAssign+`" href="javascript:void(0);" id="" attr-val="`+assignAisleExistingData[i].id+`"><span class="icon-moon icon-Delete"></span></a>`;
                if(plusPosition==i)
                {
                   // htmlContent+=`<a class="btn btn-add btn-light-green btn-header addBtn_`+countAisleAssign+`" id="addMoreAisleAssign" href="javascript:void(0);" id="" attr-val=""><span class="icon-moon icon-Add"></span></a>`;
                }
                htmlContent+=`</div></div></div>`;
                 countAisleAssign++;

            }
        }
        else
        {
             htmlContent+=  ` <input type="hidden" name="total_existing_record" value="1" id="total_existing_record">
             <div class="col-lg-12 assign_aisle_div assign_aisle_div_`+countAisleAssign+`" id="assign_aisle_div_`+countAisleAssign+`">
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                         <select class="form-control" id="aisle_`+countAisleAssign+`" name="aisle[]">
                                               `+bulkAisleForWarehouseHtml+`
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                         <select class="form-control" id="user_id_`+countAisleAssign+`" name="user_id[]">
                                               `+siteUserHtml+`
                                            </select>
                                        </div>
                                        <div class="col-lg-4  btnDiv`+countAisleAssign+`">
                                            <a class="btn-delete bg-light-red deleteBtn deleteBtn_`+countAisleAssign+`" href="javascript:void(0);" id="" attr-val="" attr-div="assign_aisle_div_`+countAisleAssign+`"><span class="icon-moon icon-Delete"></span></a>`;
                
               // htmlContent+=`<a class="btn btn-add btn-light-green btn-header addBtn_`+countAisleAssign+`" id="addMoreAisleAssign" href="javascript:void(0);" id="" attr-val=""><span class="icon-moon icon-Add"></span></a>`;
               
                htmlContent+=`</div></div></div>`;
                countAisleAssign++;
        }
       
       $(htmlContent).append("#refreshDiv");
    }

    $(document).on('click', '#addMoreAisleAssign', function(){
        var htmlContent='';
         var lastDivId=$( "#refreshDiv" ).children().last().attr('id');
         console.log(lastDivId+"= last div id Add");
        countAisleAssign=$('#total_existing_record').val();
          var plusBtnPos=countAisleAssign;
          countAisleAssign=parseInt(countAisleAssign)+1;
          //console.log("position:add="+plusBtnPos);
        //$('.addBtn_'+plusBtnPos).remove();
        $('#total_existing_record').val(countAisleAssign);

        var attrAisle=$('#warehouse_id option:selected').attr('attr-bulk-aisle');
        console.log(attrAisle);
        var bulkAisleData=attrAisle.split(",");
        
        var  bulkAisleForWarehouseHtml='';
        $.each(bulkAisleData, function( index, value ) {
            bulkAisleForWarehouseHtml+="<option value='"+value+"'>"+value+"</option>";
        });
        console.log(bulkAisleForWarehouseHtml);

        var siteUsers=$('#warehouse_id option:selected').attr('attr-site-users');

        
        var siteUsersData = jQuery.parseJSON(siteUsers);
       console.log(siteUsersData.length);
       var siteUserHtml='';
        /*if(siteUsersData.length>0)
        {*/
            $.each(siteUsersData, function( index, value ) {
                console.log(value);
              siteUserHtml+="<option value='"+value.id+"'>"+value.first_name+" "+value.last_name+"</option>";
            });
       /* }
        else
        {
            siteUserHtml='';
        }*/
         htmlContent+=  `<div class="col-lg-12 assign_aisle_div assign_aisle_div_`+countAisleAssign+`" id="assign_aisle_div_`+countAisleAssign+`">
                                    <div class="form-group row">
                                        <div class="col-lg-2">
                                         <select class="form-control" id="aisle_`+countAisleAssign+`" name="aisle[]">
                                               `+bulkAisleForWarehouseHtml+`
                                            </select>
                                        </div>
                                        <div class="col-lg-4">
                                         <select class="form-control" id="user_id_`+countAisleAssign+`" name="user_id[]">
                                               `+siteUserHtml+`
                                            </select>
                                        </div>
                                        <div class="col-lg-4 btnDiv`+countAisleAssign+`">
                                            <a class="btn-delete bg-light-red deleteBtn deleteBtn_`+countAisleAssign+`" attr-div="assign_aisle_div_`+countAisleAssign+`" href="javascript:void(0);" id="" attr-val=""><span class="icon-moon icon-Delete"></span></a>`;
                
                //htmlContent+=`<a class="btn btn-add btn-light-green btn-header addBtn_`+countAisleAssign+`" id="addMoreAisleAssign" href="javascript:void(0);" id="" attr-val=""><span class="icon-moon icon-Add"></span></a>`;
               
                htmlContent+=`</div></div></div>`;
      
        
      //  $(htmlContent).insertAfter('.assign_aisle_div_'+plusBtnPos);
      $('#refreshDiv').append(htmlContent);
        countAisleAssign++;
        
    });

    $(document).on('click', '.deleteBtn', function(){
        var lastDivId=$( "#refreshDiv" ).children().last().attr('id');
       // console.log(lastDivId);return false;
        var firstDivId=$( "#refreshDiv:eq(1)" ).children().attr('id');
        var last = $('body .formFields').length;
        var deleteDiv=$(this).attr('attr-div');
      //  console.log(deleteDiv);return false;
        var plusBtnAddIn=($('#total_existing_record').val()!=1) ? ($('#total_existing_record').val()-1) :$('#total_existing_record').val();
       //  var btnContent=`<a class="btn btn-add btn-light-green btn-header addBtn_`+countAisleAssign+`" id="addMoreAisleAssign" href="javascript:void(0);" id="" attr-val=""><span class="icon-moon icon-Add"></span></a>`;
     //   console.log(deleteDiv);
        
        console.log('.btnDiv'+plusBtnAddIn);
        
        var deleteId=$(this).attr('attr-val');

        if(deleteId!=undefined && deleteId!='')
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
                        $('.'+deleteDiv).remove();
                        $.ajax({
                        url: BASE_URL + 'api-assign-aisle-delete/'+deleteId,
                        type: "get",
                        headers: {
                           Authorization: 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                            $("#page-loader").hide();
                            if (response.status == 1) {
                              //  refreshDiv();
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                console.log(deleteDiv+"=="+lastDivId);
                                $('#total_existing_record').val($('#total_existing_record').val()-1);
                                if(deleteDiv==lastDivId)
                                {
                                    //$('.btnDiv'+plusBtnAddIn).append(btnContent);
                                     countAisleAssign--;
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
        }
        else
        {
           
            
            if(plusBtnAddIn!=0 && deleteDiv==lastDivId)
            {
                
                //$('.btnDiv'+plusBtnAddIn).append(btnContent);
                 countAisleAssign--;
            }
            console.log(lastDivId);
            /*if('assign_aisle_div_1'==lastDivId)
            {
                bootbox.alert({
                    title: "Alert",
                    message: "Atleast One record required",
                    size: 'small'
                });
                return false;
               
            }
            else
            {
                 $('#total_existing_record').val($('#total_existing_record').val()-1);*/
                $('.'+deleteDiv).remove();
            //}

        }
    });

    addInaisleUserArr=function(arr, aisle,user_id) {
          const { length } = arr;
          const id = length + 1;
          const found = arr.some(el => el.aisle === aisle && el.user_id==user_id);
          console.log(found);
          //const found1 = arr.some(el => el.username === name);
          if (!found) arr.push({ aisle:aisle, user_id: user_id });
          return arr;
        }
    $(document.body).on('click', '.btn-blue', function(){
        $('.invalid-feedback').remove();
        /* $("input[name='user_id[]']").each(function () {  
           
         });*/
       var arr = $('select[name="user_id[]"]').map(function () {
            return this.value; // $(this).val()
        }).get();
        var aisleArr = $('select[name="aisle[]"]').map(function () {
            return this.value; // $(this).val()
        }).get();
       var aisleUseData=[];
        var aisleData = $("select[name='aisle[]']");
        $("select[name='user_id[]']").each(function(index){
            aisleUseData=addInaisleUserArr(aisleUseData,$(aisleData[index]).val(),$(this).val());
        });
         console.log(aisleUseData);
        var positionArr=[];
        var errorStatus=0;
        $('select[name="user_id[]"').each(function (index) {  
            var user_id=$(this).val();
            var aisle=aisleArr[index];
          
            

            var aiseUserIndex = aisleUseData.findIndex(function(person) {
                return (person.user_id == user_id && person.aisle==aisle)
            });
          //  var aisleUseData=[];
            if(jQuery.inArray(aiseUserIndex, positionArr) == -1)
            {

                positionArr.push(aiseUserIndex);

                //aisleUseData=addInaisleUserArr(aisleUseData,aisle,user_id);
            }
            else
            {
                var positionError=index+1;
                $('#'+'aisle_'+positionError+'-error').remove();
                var erorText="<span id='aisle_"+positionError+"-error' class='invalid-feedback' style='display: inline;'>Aisle already assign to this user, please choose another one...</span>";
                
                console.log('#aisle_'+positionError);
                $(erorText).insertAfter('#aisle_'+positionError);
                $('#aisle_'+positionError).focus();
                  errorStatus=1;
                 return false;
              
                 
            }
          
            

        });
        //  console.log(errorStatus);return false;
       
        if(errorStatus==1)
        {
             console.log(errorStatus);
            return false;
        }
        else
        {
          //  console.log(errorStatus+'in');return false;
            //store assign aisle
        $("#assign-aisle-form").validate({
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
                    'aisle[]':{
                        required:true
                    },
                    'user_id[]':{
                         required:true
                    }
                },
                messages:{
                        
                },
                errorPlacement: function (error, element) {
                    if(error[0].id=="profile_pic-error")
                    {
                          error.insertAfter(element.closest('div'));
                    }
                    if(error[0].id=="country_id-error")
                    {
                        console.log(element.closest('div'));
                        error.insertAfter('.select2-selection__arrow');
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
                   // var dataString = $("#create-user-form").serialize();
                    var dataString = new FormData($("#assign-aisle-form")[0]);
                    $('.btn-blue').attr('disabled', true);
                    $.ajax({
                        type: "POST",
                        url: $("#assign-aisle-form").attr("action"),
                        data: dataString,
                        processData: false,
                        contentType: false,
                        cache: false,
                        headers: {
                            'Authorization': 'Bearer ' + API_TOKEN,
                        },
                        beforeSend: function () {
                            $("#page-loader").show();
                        },
                        success: function (response) {
                            $('.btn-blue').attr('disabled', false);
                            $("#page-loader").hide();
                           // console.log(response);return false;
                            if (response.status == 1) {
                               // $(".loadFormData").load(location.href + " .loadFormData");
                               refreshDiv();
            
                               // $('.warehouse_id').trigger('change');
                                // $('.loadFormData').load(document.URL +  ' .loadFormData');
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                              
                            }
                        },
                        error: function (xhr, err) {
                            
                            $('.btn-blue').attr('disabled', false);
                            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                        }
                    });

                }
            });
        }

    });
refreshDiv=function()
{
    $.ajax({
            url: WEB_BASE_URL+'/assign-aisle',
            data:{id:$('#warehouse_id').val()},
            success: 
            function(result){
                //console.log(result);return false;
                if(result.showAddbtnStatus=='hide')
                {
                    $('#addBtn').hide();
                }
                else
                {
                    $('#addBtn').show();
                }
                $('#refreshDiv').remove();
                $(result.view).insertAfter('.label_title .row');
                //$('#refreshDiv').html(result.view); //insert text of test.php into your div
               
            }
        });
}

    refreshPage=function(){
          $.ajax({
                type: "GET",
                url: refreash_url,
                data:data,
                datatype: 'html',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                success: function (response) {
                    console.log(response);
                }
            });
    }
    window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);