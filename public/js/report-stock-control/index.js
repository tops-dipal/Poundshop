/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function reportstockModal(record_id)
{
    $('#reportstockModal').modal('show');
    // if(not_allowed_edit_delete_status==1)
    // {

    //     bootbox.alert({
    //             title: "Alert",
    //             message: "This location is in use , so you can't edit this location.",
    //             size: 'small'
    //     });
       
    //     return false;
    // }
    // else
    // {
    //     //get values
    //     var location_type_val=$('#hid_location_type_'+record_id).val();
    //     var case_pack_val=$('#hid_case_pack_'+record_id).val();
    //     var length_val=parseFloat($('#hid_length_'+record_id).val());
    //     var width_val=parseFloat($('#hid_width_'+record_id).val());
    //     var height_val=parseFloat($('#hid_height_'+record_id).val());
    //     var cbm_val=parseFloat($('#hid_cbm_'+record_id).val());
    //     var sto_weight_val=parseFloat($('#hid_stor_weight_'+record_id).val());
    //     var carton_val=$('#hid_carton_id_'+record_id).val(); 
    //     //put values in the fields        
    //     $('#edit_record_id').val(record_id);
    //     $('#edi_location_type').val(location_type_val);
    //     $('#edi_case_pack').val(case_pack_val);

    //     if(isNaN(length_val))
    //     {
    //         length_val=0;
    //     }

    //     if(isNaN(width_val))
    //     {
    //         width_val=0;
    //     }
        
    //     if(isNaN(height_val))
    //     {
    //         height_val=0;
    //     }
        
    //     if(isNaN(cbm_val))
    //     {
    //         cbm_val=0;
    //     }

    //     if(isNaN(sto_weight_val))
    //     {
    //         sto_weight_val=0;
    //     }

    //     $('#edi_length').val(length_val);
    //     $('#edi_width').val(width_val);
    //     $('#edi_height').val(height_val);
    //     $('#edi_cbm').val(cbm_val);
    //     $('#edi_stor_weight').val(sto_weight_val);
    //     $('#edit_carton_id').val(carton_val);
    //     $('#locationModal').modal('show');
    //     if(location_type_val==11)
    //     {
    //         document.getElementById("edi_location_type").disabled = true;
    //     }
    //     else
    //     {
    //         document.getElementById("edi_location_type").disabled = false;
    //     }
    // }
}

function saveLocation()
{
    if($('#edit_carton_id').val()=='')
    {
        bootbox.alert({
                title: "Alert",
                message: "Please select box.",
                size: 'small'
        });
        return false;
    }
    $.ajax({
        url: BASE_URL + 'api-locations-row-update',
        type: "post",
        processData: true,
        data: $('#locationsEditForm').serialize(),
        headers: 
        {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        beforeSend: function () 
        {
            $("#page-loader").show();
        },
        success: function (response) 
        {
            $("#page-loader").hide();
            if (response.status == 1) 
            {
                PoundShopApp.commonClass._displaySuccessMessage(response.message);
                PoundShopApp.commonClass.table.draw();
            }
            
            PoundShopApp.commonClass.table.draw();  
            $('#locationModal').modal('hide');
        },
        error: function (xhr, err) 
        {
           $("#page-loader").hide();
           PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
        }
    });    
}

function fun_AllowOnlyAmountAndDot(txt)
{
  if(event.keyCode > 47 && event.keyCode < 58 || event.keyCode == 46)
  {
    var txtbx=document.getElementById(txt);
    var amount = document.getElementById(txt).value;
    var present=0;
    var count=0;

    if(amount.indexOf(".",present)||amount.indexOf(".",present+1));
    {    
    }
    
    do
    {
        present=amount.indexOf(".",present);
        if(present!=-1)
        {
            count++;
            present++;
        }
    }
    while(present!=-1);
    if(present==-1 && amount.length==0 && event.keyCode == 46)
    {
        event.keyCode=0;        
        return false;
    }

    if(count>=1 && event.keyCode == 46)
    {
      event.keyCode=0;      
      return false;
    }

    if(count==1)
    {
      var lastdigits=amount.substring(amount.indexOf(".")+1,amount.length);
      if(lastdigits.length>=2)
      {
          event.keyCode=0;
          return false;
      }
    }
    return true;
  }
  else
  {
    event.keyCode=0;    
    return false;
  }
}


$("#edit_carton_id").change(function () 
  {                            
    var multiple_volume = 0;
    var length=0;
    var width=0
    var heigth=0;
      
    if($("#edi_length").val() !== undefined && $("#edi_length").val() !==""){
        length= parseFloat($("#edi_length").val());
    }
    if($("#edi_width").val() !== undefined && $("#edi_width").val() !==""){
        width= parseFloat($("#edi_width").val());
    }
    if($("#edi_height").val() !== undefined && $("#edi_height").val() !=="" ){
        heigth= parseFloat($("#edi_height").val());
    }
    var max_volume='';

    if(length!='' && width!='' && heigth!='')
    {
        max_volume=length*width*heigth;
    }
    else if(length!='' && width!='' && heigth=='')
    {
        max_volume=length*width;
    }
    else if(length!='' && width=='' && heigth!='')
    {
        max_volume=length*heigth;
    }
    else if(length=='' && width!='' && heigth!='')
    {
        max_volume=width*heigth;
    }
    else if(length!='' && width=='' && heigth=='')
    {
        max_volume=length;   
    }
    else if(length=='' && width!='' && heigth=='')
    {
        max_volume=width;   
    }
    else if(length=='' && width=='' && heigth!='')
    {
        max_volume=heigth;   
    }
    max_volume=max_volume/1000000;
    $("#edi_cbm").val(max_volume.toFixed(2));
  });

$("#edi_length,#edi_width,#edi_height,#box").keydown(function(e)
{
    var key = e.charCode || e.keyCode || 0;        
    return (key == 8 || key == 9 || key == 13 || key == 46 || (key >= 35 && key <= 40) || (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
});