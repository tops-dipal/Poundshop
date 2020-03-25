/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
  "user strict";  
  var poundShopTotes = function ()
  {
    $(document).ready(function ()
    {
      productSearch();        
    });
  };

  var c = poundShopTotes.prototype;    
  c._initialize = function ()
  {
    
  };

  function productSearch()
  {
    var product_id=$('#product_id').val()
    var selected_pallet=$('#selected_pallet').val();
    var scan_bulk_location = $("#scan_bulk_location").val();    
    var scan_pro_barcode=$('#scan_pro_barcode').val();
    $.ajax({
      url: $("#replen-product-list-url").val(),
      type: "get",
      data:{product_id:product_id,selected_pallet:selected_pallet,scan_bulk_location:scan_bulk_location,scan_pro_barcode:scan_pro_barcode},
      headers: {
        Authorization: 'Bearer ' + API_TOKEN,
      },
      beforeSend: function () {
        $("#page-loader").show();
      },
      success: function (response) 
      {
        $("#page-loader").hide();      
        if (response.status == 1) 
        {
          $("#product_list_div").html(response.data.data);
        }        
      },
      error: function (xhr, err) 
      {
        $("#page-loader").hide();
        if(xhr.responseJSON.message!=undefined && xhr.responseJSON.message!='')
        {
          PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message);                
        }
        $("#product_list_div").html("");
      }
    });
  }
  
  $(document).on("keypress","#scan_bulk_location",function(event)
  {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13')
    {
      productSearch();
    }
  });

  $(document).on("keypress","#scan_pro_barcode",function(event)
  {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13')
    {
      productSearch();
    }
  });  

  $("body #scan_bulk_location").scannerDetection(function(e)
  {   
      alert('scanner call');
      init_set_location_detatils('1'); 
  });

  $('body').on('input','#scan_bulk_location', function(e) {
    var el = this;
    setTimeout(function() {
        init_set_location_detatils(2,el);
    },300);
  });

  init_set_location_detatils = function(type,el)
  {
    $(el).prev('span.scan_type').remove();

    if(type == '1')
    {
      html = '<span class="scan_type font-10-dark bold d-block mt-1">Scanner</span>';
    }
    else 
    {
      html = '<span class="scan_type font-10-dark bold d-block mt-1">Manual</span>';
      init_typeahead(el);
    }    
  }

  $(document).on("click",".finish_job",function(event)
  {
    var selected_location=$('#selected_pallet').val();
    if(selected_location)
    {
      $.ajax({
        url: $("#replen-finish-pallet").val(),
        type: "get",
        data:{selected_location:selected_location},
        headers: {
          Authorization: 'Bearer ' + API_TOKEN,
        },
        beforeSend: function () {
          $("#page-loader").show();
        },
        success: function (response) 
        {
          $("#page-loader").hide();      
          if (response.status == 1) 
          {
            //$('.redirect_page').trigger( "click" );
            window.location.href = $('#mylist_link').val();
          }        
        },
        error: function (xhr, err) 
        {
          $("#page-loader").hide();
          if(xhr.responseJSON.message!=undefined && xhr.responseJSON.message!='')
          {
            PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message);                
          }
        }
      });
    }
  });
    
  window.PoundShopApp = window.PoundShopApp || {}
  window.PoundShopApp.poundShopTotes = new poundShopTotes();
})(jQuery);


$(document).on("click",".moved_qty",function(event)  
{
  //check if location is selected or not  
  var location_id = $("input:radio[name=barcode]:checked").val();
  var box_picked=$('#box_picked').val();
  var selected_pallet=$('#selected_pallet').val();
  if(location_id=='' || location_id==undefined)
  {
    bootbox.alert({
            title: "Alert",
            message: "Please select one bulk location from which you want to replen data.",
            size: 'small'
    });
    return false;
  }

  if(box_picked=='' || box_picked == undefined || box_picked ==0)
  {
    bootbox.alert({
      title: "Alert",
      message: "Please select box picked quantity to replen.",
      size: 'small'
    });
    return false;
  }  

  if(selected_pallet=='' || selected_pallet == undefined || selected_pallet ==0)
  {
    bootbox.alert({
      title: "Alert",
      message: "Please select pick pallet to replen.",
      size: 'small'
    });
    return false;
  }

  
  if(box_picked!='' && location_id!='')
  { 
    var max_box_pick=$('.no_of_box_'+location_id).html();
    if(parseInt(box_picked)<=parseInt(max_box_pick))
    {      
      var product_id=$('#product_id').val();      
      
      $.ajax({
        url: $("#product-replen-url").val(),
        type: "get",
        data:{product_id:product_id,selected_pallet:selected_pallet,location_id:location_id,box_picked:box_picked},
        headers: {
          Authorization: 'Bearer ' + API_TOKEN,
        },
        beforeSend: function () {
          $("#page-loader").show();
        },
        success: function (response) 
        {  
          $("#page-loader").hide();      
          if (response.status==1 && response.data == 1) 
          {
            location.reload();
          }
          else if(response.status==1 && response.data == 2)
          {            
             //setTimeout(function(){ $('.redirect_page').trigger( "click" ); }, 700);
             window.location.href = $('#mylist_link').val();
          } 
        },
        error: function (xhr, err) 
        {
          $("#page-loader").hide();
          if(xhr.responseJSON.message!=undefined && xhr.responseJSON.message!='')
          {
            PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message);                
          }
        }
      });
    }
    else
    {
      bootbox.alert({
        title: "Alert",
        message: "You can not pick much boxes then exist on location.",
        size: 'small'
      });
      return false;
    }
  }
  
});

$("#box_picked").keydown(function(e)
{
    var key = e.charCode || e.keyCode || 0;    
    // allow backspace, tab, delete, enter, arrows, numbers and keypad numbers ONLY
    // home, end, period, and numpad decimal
    return (
        key == 8 || 
        key == 9 ||
        key == 13 ||
        key == 46 ||
        (key >= 35 && key <= 40) ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
    
});

$("#box_picked").keyup(function(e)
{
  calulate_data();
});

function calulate_data()
{
  var location_id = $("input:radio[name=barcode]:checked").val();
  var box_picked=$('#box_picked').val();
  
  if(location_id!=undefined && location_id!='' && box_picked!=undefined && box_picked!='')
  {
    var qty_per_box=$('.qty_box_'+location_id).html();    
    var total_qty=qty_per_box*box_picked;
    if(total_qty!='' && total_qty!=undefined && total_qty!='0')
    {
      $('.total_qty_sel').html(total_qty);
    }
    else
    {
      $('.total_qty_sel').html('');    
    }
  }
  else
  {
    $('.total_qty_sel').html(''); 
  }
}

$(document).on("click","input[name='barcode']",function(event)  
{
  calulate_data();
});


init_typeahead = function(el = $('#scan_bulk_location'))
{  
    var warehouse_id = $('#warehouse_id').val();        
    $(el).next('span.location_type').remove();    
    $(el).typeahead({
        ajax: {
        url: BASE_URL+'api-location-auto-suggest-on-input',
        method: 'GET',
        extra_data: {
          'warehouse_id': warehouse_id,                
          "keyword" : $(el).val(), 
          'module' : 'replen-bulk'
        },
        headers: 
        {
          'Authorization': 'Bearer ' + API_TOKEN,
        },
      },
      autoSelect: true,
      grepper: false,
      highlighter: false,
      displayField: 'location',
      valueField: 'location_type',
      scrollBar: false,
      on_select_target_blank: false,
      item: '<li><a href="#"></a></li>',
      onSelect: function (value)
      {
        $(el).val(value.text);
        $(el).next('span.location_type').remove();        
        html = '<span class="location_type font-10-dark bold d-block mt-1">'+value.value+'</span>';
        $(el).after(html);
        productSearch();
      }
    });
  }