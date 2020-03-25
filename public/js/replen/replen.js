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
    var sortBy=$('#sort_by').val()
    var sortDirection=$('#sort_direction').val();
    var product_search = $("#scan-product-barcode-textbox").val();    
    var selected_priority=$('#selected_priority').val();
    $.ajax({
      url: $("#replen-product-url").val(),
      type: "get",
      data:{product_search:product_search,sort_by:sortBy,sort_direction:sortDirection,selected_priority:selected_priority},
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
          $("#replen-id").html(response.data.data);
        }        
      },
      error: function (xhr, err) 
      {
        $("#page-loader").hide();        
        if(xhr.responseJSON!=undefined &&  xhr.responseJSON.message!=undefined && xhr.responseJSON.message!='')
        {
          PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message);                
        }
        $("#replen-id").html("");
        return false;
      }
    });
  }
  
  $(document).on("keypress","#scan-product-barcode-textbox",function(event)
  {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13')
    {
      productSearch();
    }
  });

  $(document).on("keypress","#select_pick_location",function(event)
  {
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13')
    {
      select_pallet();
    }
  });

  $(document).on("click",".startJob",function(event)
  {
    select_pallet();
  });

  $(document).keypress(
    function(event){
      if (event.which == '13') {
        event.preventDefault();
      }
  });
  
  function select_pallet()
  {
    var selected_location=$('#select_pick_location').val();
    if(selected_location!=undefined && selected_location!='')
    {
      $.ajax({
        url: $("#replen-select-pallet").val(),
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
            location.reload();
          }        
        },
        error: function (xhr, err) 
        {
          $("#page-loader").hide();
          if(xhr.responseJSON!=undefined && xhr.responseJSON.message!=undefined && xhr.responseJSON.message!='')
          {
            PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message);                
          }
          return false;
        }
      });
    }
    else
    {
      return false;
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
            location.reload();
          }        
        },
        error: function (xhr, err) 
        {
          $("#page-loader").hide();
          if(xhr.responseJSON!=undefined && xhr.responseJSON.message!=undefined && xhr.responseJSON.message!='')
          {
            PoundShopApp.commonClass._displayErrorMessage(xhr.responseJSON.message);                
          }  
          return false;              
        }
      });
    }
  });

  $(document).on("click",".refresh",function(event)
  {
    $('#scan-product-barcode-textbox').val('');
    productSearch();
  });         
    
  $(document).on("click",'td.sorting[sort-by],td.sorting_asc[sort-by],td.sorting_desc[sort-by]',function(e)
  {
    $('#sort_by').val($(this).attr("sort-by"))
    $('#sort_direction').val($(this).attr("sort-order"))
    productSearch();
    return false;
  }); 

  $('.cancle_fil').click(function()
  {
    $('.filter_count').html('');
    $('[class=adv_priority]:checked').each(function() {
        $(this).removeAttr('checked');        
    });    
    $('#btnFilter').removeClass('open');
    $('.search-filter-dropdown').removeClass('open'); 
    $('.card-flex-container').removeClass('filter-open'); 
    $('#selected_priority').val('');
    productSearch();      
  });

  $('.apply_fil').click(function()
  {  
    var checkbox_val = [];
    var counter=0;
    $('[class=adv_priority]:checked').each(function() {
        checkbox_val.push($(this).val());
        counter++;
    });    

    if(checkbox_val=='')
    {
        bootbox.alert({
                title: "Alert",
                message: "Please select atleast one filter to search.",
                size: 'small'
        });
        return false;
    }
    else
    {        
      $('.filter_count').html(' ('+counter+')');
      $('#selected_priority').val(checkbox_val);
      productSearch();
      $('#btnFilter').removeClass('open');
      $('.search-filter-dropdown').removeClass('open'); 
      $('.card-flex-container').removeClass('filter-open');     
    }
  });  
  window.PoundShopApp = window.PoundShopApp || {}
  window.PoundShopApp.poundShopTotes = new poundShopTotes();
})(jQuery);

function select_pallet()
{
  $('#locationModal').modal('show');
}

function check_pallet_select()
{
  var selected_pall=$('#selected_pallet').val();
  if(selected_pall=='' || selected_pall==undefined)
  {
    bootbox.alert({
            title: "Alert",
            message: "Please select Pick Pallet to start.",
            size: 'small'
    });
    return false;
  }
}

$("body #set_location_details").scannerDetection(function(e)
{   
    alert('scanner call');
    init_set_location_detatils('1'); 
});

$('body').on('input','#select_pick_location', function(e) {
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

init_typeahead = function(el = $('#select_pick_location'))
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
        'module' : 'replen-pick-pallet'
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
      // productSearch();
    }
  });
}