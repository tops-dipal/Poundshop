
(function ($)
{
    "user strict";

    var poundShopCartons = function ()
    {
        var timeList = [];
        $(document).ready(function ()
        {
            var i=0;
             $("body").data('timeList',[]);
            var endtimes = $("input[name='to_time[]']")
            
            $("input[name='from_time[]']").each(function(index){
                timeList.push({
                  startTime: +getDate($(this).val()),
                  endTime : +getDate($(endtimes[index]).val())
                });
               
               
            });
            
             $('body .timepicker').datetimepicker({
                    format: 'h:mm a',
                    stepping: 1,
                
            });
             $("body").data('timeList',timeList);
             console.log($("body").data('timeList'));
            c._initialize();
        });
    };
    var c = poundShopCartons.prototype;
    
    c._initialize = function ()
    {
        

    };
   
    $(document).on('click', '.addMore', function (event) {

        var slotCount=parseInt($("input[name='slot_num']").val())+1;

        var addStr=`<div class="mt-2 slot_`+slotCount+`">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-2 col-form-label">`+POUNDSHOP_MESSAGES.modules.slot+` `+slotCount+`</label>
                            <div class="col-lg-6">
                                <div class="d-flex" id="slotGroup_`+slotCount+`">
                                    <input type="text" name="from[`+slotCount+`]" value="" class="form-control mr-2 timepicker" placeholder="From">
                                    <input type="text" name="to[`+slotCount+`]" value="" class="form-control timepicker" placeholder="To">
                                </div>
                            </div>
                            <div class="col-lg-2">
                                <a class="btn-delete bg-light-red" href="javascript:void(0);" id="slot_`+slotCount+`"><span class="icon-moon icon-Delete"></span></a>
                            </div>
                        </div>
                    </div>`;
                   
        $('#add_more_slote').append(addStr);
         $('body .timepicker').datetimepicker({
                    format: 'h:mm a',
                    stepping: 1,

                
            });
        $("input[name='slot_num']").val(slotCount);
    });
    $(document).on('click', '.btn-delete', function (event) {
        var currebtId=$(this).attr('id');
        var attrVal=$(this).attr('attr-val');
        var slotCount=parseInt($("input[name='slot_num']").val())-1;
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
                    $.ajax({
                        url: BASE_URL + 'api-slot-remove/'+attrVal,
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
                                $('.add-category-form').load(document.URL +  ' .add-category-form');
                                $('.'+currebtId).remove();
                                 $("input[name='slot_num']").val(slotCount);
                                 var idArr=currebtId.split("_");
                                var num=parseInt(idArr[1])-1;
                                var timeList=$("body").data('timeList');
                                if(typeof timeList[num] === 'undefined') {
                                        // does not exist
                                    }
                                    else {
                                        console.log("fdf");
                                        timeList.splice(num,1);
                                        $("body").data('timeList',timeList);
                                        console.log(timeList);
                                        
                                        }
                                PoundShopApp.commonClass._displaySuccessMessage(response.message);
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
            var idArr=currebtId.split("_");
            var num=parseInt(idArr[1])-1;
            var timeList=$("body").data('timeList');
            if(typeof timeList[num] === 'undefined') {
                    // does not exist
                }
                else {
                    timeList.splice(num,1);
                    $("body").data('timeList',timeList);
                     $("input[name='slot_num']").val(slotCount);
                    
                    // does exist
                }

            $('.'+currebtId).remove();
             
            if($('#add_more_slote').children().length==0)
           {
                $('.add-category-form').load(document.URL +  ' .add-category-form');
           }

        }
       
    });
  
    
 $(document.body).on('click', '.btn-blue', function(){

    var startTimeArr=[];
    var endTimeArr=[];
    var   errorCount=0;
    $("body .timepicker").each(function () {  
        var val=$(this).val();
        var convertedVal=(+getDate($(this).val()));
        var name=$(this).attr('name');
        var parentDivId=$(this).parent().attr('id');
        if($(this).val()=='')
        {
            var errStr="";
            errStr='<span id="'+parentDivId+'-error" id=""class="invalid-feedback" style="display: inline;">Please Enter Slot</span>';
           
            if($('#'+parentDivId+'-error').length==0)
            {
                errorCount++;
                $(errStr).insertAfter($(this).parent());
            }
        }
        else
        {
            if($('#'+parentDivId+'-error').length>0)
            {
                 if(errorCount!=0)
                        errorCount--;
                $('#'+parentDivId+'-error').remove();
            }
            var Startfound = jQuery.inArray(convertedVal, startTimeArr);
            if (Startfound >= 0) {
                var errStr="";
                errStr='<span id="'+parentDivId+'-error" id=""class="invalid-feedback" style="display: inline;">Slot already exists</span>';
               
                if($('#'+parentDivId+'-error').length==0)
                {
                    errorCount++;
                    $(errStr).insertAfter($(this).parent());
                    return false;
                }
                startTimeArr.splice(found, 1);
            } else {
                if(errorCount!=0)
                        errorCount--;
                 $('#'+parentDivId+'-error').remove();
                startTimeArr.push(convertedVal);
            }

        }
    }) 
    
    var Contain = "";
    
    var overLapCount=0;
    var totalTimes=$("input[name='slot_num']").val();
    var timeListArr=$("body").data('timeList');
    
     for(var i=1;i<=totalTimes;i++)
    {
        var attr = $("input[name='from["+i+"]']").attr('disabled');
        
        if (typeof attr!="string") {
            
            var startTime=(+getDate($("input[name='from["+i+"]']").val()));
           
            var endTime=(+getDate($("input[name='to["+i+"]']").val()));
            
            if(startTime==endTime)
            {
                $('.error_'+i).remove();
                $("<span class='error_"+i+" invalid-feedback' style='display:inline'>End Time should be greater than start time</span>").insertAfter("#slotGroup_"+i);
                overLapCount++;
                return false;
            }
            else if(startTime>endTime)
            {
                $('.error_'+i).remove();
                $("<span class='error_"+i+" invalid-feedback' style='display:inline'>End Time should be greater than start time</span>").insertAfter("#slotGroup_"+i);
                overLapCount++;
                return false;
            }
           else if(timeListArr.filter(x => x.startTime <= endTime).length>0 && timeListArr.filter(x => x.endTime >= startTime).length>0){
                 var  startIndex = timeListArr.findIndex(x => x.startTime ===startTime);
                var endIndex =timeListArr.findIndex(x => x.endTime ===endTime);
                console.log(timeListArr);
                $.each(timeListArr, function( index1, value1 ) {
                    var start=value1.startTime;
                    var end=value1.endTime;
                    if (startTime > start && startTime < end) {
                      $('.error_'+i).remove();
                        $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot overlaps other slot.</span>").insertAfter("#slotGroup_"+i);
                        overLapCount++;
                        return false;
                    }
                    else if (startTime == value1.startTime && endTime == value1.endTime) {
                       
                          $('.error_'+i).remove();
                            $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot already exists.</span>").insertAfter("#slotGroup_"+i);
                            overLapCount++;
                            return false;
                    }
                    else if(endTime > value1.startTime && endTime < value1.endTime) {
                        $('.error_'+i).remove();
                        $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot overlaps other slot.</span>").insertAfter("#slotGroup_"+i);
                        overLapCount++;
                        return false;
                    }
                 
                });
              
            }
            else if(timeListArr.filter(x => x.startTime === startTime).length>0 && timeListArr.filter(x => x.endTime === endTime).length>0){
               var  startIndex = timeListArr.findIndex(x => x.startTime ===startTime);
                var endIndex =timeListArr.findIndex(x => x.endTime ===endTime);
                console.log("Here Now");
                $.each(timeListArr, function( index, value ) {
                     if (startTime == value.startTime && startTime == value.endTime) {
                          $('.error_'+i).remove();
                            $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot already exists.</span>").insertAfter("#slotGroup_"+i);
                            overLapCount++;
                            return false;
                        }
                        else  if(startIndex!=endIndex)
                        {
                            $('.error_'+i).remove();
                            $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot already exists</span>").insertAfter("#slotGroup_"+i);
                            overLapCount++;
                            return false;
                        }
                        else
                        {
                            if(startIndex==endIndex)
                            {
                                $('.error_'+i).remove();
                                overLapCount++;
                                console.log("dfdf111");
                                timeListArr.splice(startIndex,1)
                                $("body").data('timeList',timeListArr);
                            }
                            else
                            {
                                $('.error_'+i).remove();
                                $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot already exists</span>").insertAfter("#slotGroup_"+i);
                                overLapCount++;
                                return false;
                            }
                        }
                      });
                
            } 
            else{
                $('.error_'+i).remove();
                
                timeListArr.push({
                  startTime: startTime,
                  endTime :endTime,
                });
                $("body").data('timeList',timeListArr);
            }
        }
    }
    console.log($("body").data('timeList'));
   if(overLapCount>0)
    {

       return false;
    }
   
   
   
    if(errorCount>0)
    {/**/
        
       // $('.btn-blue').attr('disabled', true);
        return false;
    }
    else
    {
        //$('.btn-blue').attr('disabled', false);
    }
   
    $("#create-slot-form").validate({
     focusInvalid: false, // do not focus the last invalid input
    invalidHandler: function(form, validator) {

    if (!validator.numberOfInvalids())
        return;

    $('html, body').animate({
        scrollTop: $(validator.errorList[0].element).offset().top-30
    }, 1000);
                       },
    errorElement: 'span',
    errorClass: 'invalid-feedback', // default input error message class
    ignore: [],
    rules: {
       
        
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
        var dataString = $("#create-slot-form").serialize();
        
        $('.btn-blue').attr('disabled', true);
        $.ajax({
            type: "POST",
            url: $("#create-slot-form").attr("action"),
            data: dataString,
            processData: false,
          //contentType: false,
         // cache: false,
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
                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    $('.add-category-form').load(document.URL +  ' .add-category-form',function() {
                            $("body").data('timeList',[]);
                            var endtimes = $("input[name='to_time[]']")
                            var timeList = [];
                            $("input[name='from_time[]']").each(function(index){
                                timeList.push({
                                  startTime: +getDate($(this).val()),
                                  endTime : +getDate($(endtimes[index]).val())
                                });
                            });
                            $("body").data('timeList',timeList);
                            console.log($("body").data('timeList'));
                    });
                    $('body .timepicker').datetimepicker({
                        format: 'h:mm a',
                        stepping: 1,
                        });
                    
                }
            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });

    }
    });
});
window.PoundShopApp = window.PoundShopApp || {}
window.PoundShopApp.poundShopCartons = new poundShopCartons();

})(jQuery);

function validateFromTo(currObj) {
    
   /* var errorCount=0;
    var timeData=$("body").data('timeList')
    var val=$(currObj).val();
    var name=$(currObj).attr('name');
    var parentDivId=$(currObj).parent().attr('id');
    
    if($(currObj).val()=='')
    {
        var errStr="";
        errStr='<span id="'+parentDivId+'-error" id=""class="invalid-feedback" style="display: inline;">Please Enter Slot</span>';
       
        if($('#'+parentDivId+'-error').length==0)
        {
            errorCount++;
            $(errStr).insertAfter($(this).parent());
        }
    }
    else
    {
        if($('#'+parentDivId+'-error').length>0)
        {
            if(errorCount!=0)
                errorCount--;
            $('#'+parentDivId+'-error').remove();
        }
    }
    if(errorCount==0)
    {
        $('.btn-blue').attr('disabled', false);
    }
    else
    {
        $('.btn-blue').attr('disabled', true);
    }*/

     var overLapCount=0;
    var totalTimes=$("input[name='slot_num']").val();
    var timeListArr=$("body").data('timeList');
    
     for(var i=1;i<=totalTimes;i++)
    {
        var attr = $("input[name='from["+i+"]']").attr('disabled');
      
                   
        if (typeof attr!="string") {
            
            var startTime=(+getDate($("input[name='from["+i+"]']").val()));
           
            var endTime=(+getDate($("input[name='to["+i+"]']").val()));
             
            console.log(startTime+'----'+endTime);
            if(!(isNaN(startTime) || isNaN(endTime))){
                if(startTime>=endTime)
                {
                    console.log("if part"+i);
                    $('.error_'+i).remove();
                    $("<span class='error_"+i+" invalid-feedback' style='display:inline'>End Time should be greater than start time</span>").insertAfter("#slotGroup_"+i);
                    overLapCount++;
                    $('.btn-blue').attr('disabled',true);
                    return false;
                }
                 else if(timeListArr.filter(x => x.startTime === startTime).length>0 && timeListArr.filter(x => x.endTime === endTime).length>0){
                    console.log("here");
                    $('.error_'+i).remove();
                    $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot already exists</span>").insertAfter("#slotGroup_"+i);
                    overLapCount++;
                    $('.btn-blue').attr('disabled',true);
                    return false;
                } 
                else {
                   console.log("here1");
                    if(timeListArr.length>0)
                   {
                    console.log(timeListArr);
                    
                     $.each(timeListArr, function( index, value ) {
                        console.log(startTime +'+++++++++++'+ value.startTime)
                            if(startTime == value.startTime && endTime == value.endTime) {
                               
                                  $('.error_'+i).remove();
                                    $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot already exists</span>").insertAfter("#slotGroup_"+i);
                                    overLapCount++;
                                    $('.btn-blue').attr('disabled',true);
                                    return false;
                               
                            }
                            else if (startTime >= value.startTime && startTime <= value.endTime) {
                              $('.error_'+i).remove();
                                $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot overlaps other slot.</span>").insertAfter("#slotGroup_"+i);
                                overLapCount++;
                                $('.btn-blue').attr('disabled',true);
                                 return false;
                               
                            }
                            else if (startTime == value.startTime || endTime <= value.endTime) {
                              $('.error_'+i).remove();
                                $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot overlaps other slot.</span>").insertAfter("#slotGroup_"+i);
                                overLapCount++;
                                $('.btn-blue').attr('disabled',true);
                                 return false;
                               
                            }
                            else if(endTime >= value.startTime && endTime <= value.endTime) {
                                $('.error_'+i).remove();
                                $("<span class='error_"+i+" invalid-feedback' style='display:inline'>Slot overlaps other slot.</span>").insertAfter("#slotGroup_"+i);
                                overLapCount++;
                                $('.btn-blue').attr('disabled',true);
                                return false;
                            }
                            else
                            {
                                //changeTime(i-1,startTime,endTime);
                                if (typeof timeListArr[i-1] !== 'undefined') {
                                    changeTime(i-1,startTime,endTime);
                                    return false;
                                }else
                                {
                                     $('.btn-blue').attr('disabled',false);
                                    $('.error_'+i).remove();
                                    timeListArr.push({
                                      startTime: startTime,
                                      endTime :endTime,
                                    });
                                    var result = timeListArr.reduce(function(memo, e1){
                                      var matches = memo.filter(function(e2){
                                        return e1.startTime == e2.startTime && e1.endTime == e2.endTime
                                      })
                                      if (matches.length == 0)
                                        memo.push(e1)
                                        return memo;
                                    }, [])
                                    $("body").data('timeList',result);
                                    
                                    if(overLapCount!=0)
                                    {
                                         overLapCount--;
                                    }  
                                    console.log($("body").data('timeList'));
                                }
                               
                            }
                        });
                   }
                   else
                   {
                       $('.btn-blue').attr('disabled',false);
                            $('.error_'+i).remove();
                            var newList=[];
                            newList.push({
                              startTime: startTime,
                              endTime :endTime,
                            });
                            var result = newList.reduce(function(memo, e1){
                              var matches = memo.filter(function(e2){
                                return e1.startTime == e2.startTime && e1.endTime == e2.endTime
                              })
                              if (matches.length == 0)
                                memo.push(e1)
                                return memo;
                            }, []);
                            
                            $("body").data('timeList',result);
                            
                            if(overLapCount!=0)
                            {
                                 overLapCount--;
                            }  
                            console.log($("body").data('timeList'));
                   }
                  
                }
                /*else{
                    $('.error_'+i).remove();
                    
                    timeListArr.push({
                      startTime: startTime,
                      endTime :endTime,
                    });
                    $("body").data('timeList',timeListArr);
                    if(overLapCount!=0)
                    {
                         overLapCount--;
                    }
                   
                }*/
            }
          
        }
        //return overLapCount;
    }
    console.log($("body").data('timeList'));
    if(overLapCount>0)
    {

        $('.btn-blue').attr('disabled',true);
        return false;
    }
    else
    {
        $('.btn-blue').attr('disabled',false);
        
    }
    return overLapCount;
}
 function addTime(from,to) {
  var startTime = from;
  var endTime = to;
  var timeList=$("body").data('timeList');
  
  if (validate(startTime, endTime,timeList)==1){
        timeList.push({
          startTime: startTime,
          endTime: endTime
        });
    $("body").data('timeList',timeList);
   
    $('.btn-blue').attr('disabled',false);
    
    return "1";
    }
    else if(validate(startTime, endTime,timeList)==2)
    {
        return "2";
    }
  else
    {
        $('.btn-blue').attr('disabled',true);
        
        return "0";
  }

}

function validate(sTime, eTime,timeList) {
    
    if (+getDate(sTime) < +getDate(eTime)) {
        var len = timeList.length;
        var timeList1=$("body").data('timeList');
        
        
        if(len>0)
        {
            //var ans=(+getDate(timeList[len - 1].endTime) < +getDate(sTime) );
            
            timeList1.push({
              startTime: sTime,
              endTime: eTime
            });
            
               for (var i = 0; i < timeList1.length; i++) {
                    for (var j = i + 1; j < timeList1.length; j++) {
                       
                        
                        if ((+getDate(timeList1[i].startTime) < +getDate(timeList1[j].endTime)) && (+getDate(timeList1[j].startTime) < +getDate(timeList1[i].endTime)) ){                      
                            var r="valid";
                          //return true;
                        }
                        else
                        {
                            var r="invalid";
                            //return false;
                        }
                    }
               }
           
           return false;
        }
        else
        {
            var ans=false;
        }
        
        return ans;
        //return len>0 ? (+getDate(timeList[len - 1].endTime) < +getDate(sTime) ):true;
    } else {
        //console.log("End Time Less then Start Time");
        return 2;
    }
}

function getDate(time) {
  var today = new Date();
  if(time)
    {
        var timeStr=convert12Hto24Hour(time);
      var _t = timeStr.split(":");
      today.setHours(_t[0], _t[1], 0, 0);
      
      return today;
    }
}

function convert12Hto24Hour(time12h)
{
     const [time, modifier] = time12h.split(' ');

      let [hours, minutes] = time.split(':');

      if (hours === '12') {
        hours = '00';
      }

      if (modifier === 'PM' || modifier=='pm') {
        hours = parseInt(hours, 10) + 12;
      }

      return `${hours}:${minutes}`;
    

}

function changeTime( objIndex, startTime,endTime ) {
   var lists=$('body').data('timeList');
   lists[objIndex].startTime = startTime;
   lists[objIndex].endTime=endTime;
   console.log(lists);
   $('body').data('timeList',lists);
}



