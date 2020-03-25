

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function ($)
{
    "user strict";
    var dataTableId = 'totes_table';
    var poundShopTotes = function ()
    {
        $(document).ready(function ()
        {
            var previously_range_selected=[];
            c._initialize();
            set_category_data();
            var selectedParent=[];
            var selecteParentIdArr=$("body #parentIds").val().split(",");
               selectedParent=$("body #parentIds").val().split(",");
                //getParent("",selecteParentIdArr,levelcounter);
                $.each(selectedParent, function( index, value ) {
                    if(value!=$('body #range_id').val())
                    {
                        getParent(value,selecteParentIdArr,1);
                    }

                });
                

                var status=$('#status').val();
                if(status=='mapped'){

                //magento Categories
                var selectedParentMagento=[];
                var selecteParentIdMagentoArr=$("body #parentIdsMagento").val().split(",");
                   selectedParentMagento=$("body #parentIdsMagento").val().split(",");
                    //getParent("",selecteParentIdArr,levelcounter);
                    $.each(selectedParentMagento, function( index, value ) {
                        if(value!=$('body #magenot_category_id').val())
                        {
                            getParentMagento(value,selecteParentIdMagentoArr,1);
                           
                            
                        }
                    });
                }
        });
    };
     $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    var c = poundShopTotes.prototype;
    
    c._initialize = function ()
    {
        c._listingView();
    };
    
    c._listingView = function(){
        var field_coloumns = [
            {"orderable": false, "searchable": false},
            null,
            null,
            {"orderable": false, "searchable": false},
        ];
        var order_coloumns = [[0, "desc"]];
        PoundShopApp.commonClass.table = PoundShopApp.commonClass._generateDataTable(PoundShopApp.commonClass.table,'totes_table','api-category-mapping',field_coloumns,order_coloumns,undefined,undefined,'Search by Tote Name');        };
 
    $(".master").click(function () {
        $("input[name='ids[]']").prop('checked', $(this).prop('checked'));
    });

    isEmpty=  function(value) {
      return typeof value == 'string' && !value.trim() || typeof value == 'undefined' || value === null;
    }
  
    getParent= function(val,selecteParentIdArr,levelcounter)
    {

       /* console.log(selecteParentIdArr);
        var encodedChildCat=$(".parent_id option:selected").attr('attr-child-nodes');
        console.log(encodedChildCat);
        var decodedChildCat=jQuery.parseJSON(encodedChildCat);
     
        if(encodedChildCat!=null && encodedChildCat!='null')
        {
          //  console.log("1");
         //   console.log(decodedChildCat);
            var dropdownString=` <div class="form-group" id="`+val+`" data-sort="`+val+`">                                        
                <select class="form-control parent_id" name="range_cat_id">
                <option value="0">--Select Sub Category--</option>`;
                $.each(decodedChildCat, function( index, value ) {
                  //  console.log(jQuery.inArray(value.id, selecteParentIdArr));
                //  console.log(isEmpty(value.children));
                    if(!isEmpty(value.children))
                    {
                        child=JSON.stringify(value.children);
                    }
                    else
                    {
                        child=null;
                    }
                    console.log(value.id);
                    console.log($.inArray(value.id.toString(), selecteParentIdArr));
                   
                    if( $.inArray(value.id.toString(), selecteParentIdArr) >= 0)
                    {
                        selectedStr='selected="selected"';
                    }
                    else
                    {
                        selectedStr='';
                    }
                    dropdownString+=`<option value="`+value.id+`" attr-child-nodes='`+child+`' `+selectedStr+`>`+value.category_name+`</option>`;
                });
                
               dropdownString+=`</select>                                        
                </div>`;
               


            $('#child_category').append(dropdownString);
            $( "#child_category .parent_id" ).trigger( "change" );
        }
        return 1;*/
   // console.log(val);
        var val=val;
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
        $.ajax({
            url: WEB_BASE_URL+'/mapping-relation-range-list',
            type: "post",
            data: {"type":'range',"id":val,"editId":$('#range_id').val(),'_token':$('.token').val(),'selected_parent':val,'process':'edit'},
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            success: function (response) {
               
                $("body #child_category").append(response.view);
                 var $wrapper = $('#child_category');

                $wrapper.find('.form-group').sort(function (a, b) {
                    return +a.dataset.sort - +b.dataset.sort;
                })
                .appendTo( $wrapper );
             
            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });

        return ;
      
    }


      getParentMagento= function(val,selecteParentIdMagentoArr,levelcounter)
    {

       /* console.log(selecteParentIdArr);
        var encodedChildCat=$(".parent_id option:selected").attr('attr-child-nodes');
        console.log(encodedChildCat);
        var decodedChildCat=jQuery.parseJSON(encodedChildCat);
     
        if(encodedChildCat!=null && encodedChildCat!='null')
        {
          //  console.log("1");
         //   console.log(decodedChildCat);
            var dropdownString=` <div class="form-group" id="`+val+`" data-sort="`+val+`">                                        
                <select class="form-control parent_id" name="range_cat_id">
                <option value="0">--Select Sub Category--</option>`;
                $.each(decodedChildCat, function( index, value ) {
                  //  console.log(jQuery.inArray(value.id, selecteParentIdArr));
                //  console.log(isEmpty(value.children));
                    if(!isEmpty(value.children))
                    {
                        child=JSON.stringify(value.children);
                    }
                    else
                    {
                        child=null;
                    }
                    console.log(value.id);
                    console.log($.inArray(value.id.toString(), selecteParentIdArr));
                   
                    if( $.inArray(value.id.toString(), selecteParentIdArr) >= 0)
                    {
                        selectedStr='selected="selected"';
                    }
                    else
                    {
                        selectedStr='';
                    }
                    dropdownString+=`<option value="`+value.id+`" attr-child-nodes='`+child+`' `+selectedStr+`>`+value.category_name+`</option>`;
                });
                
               dropdownString+=`</select>                                        
                </div>`;
               


            $('#child_category').append(dropdownString);
            $( "#child_category .parent_id" ).trigger( "change" );
        }
        return 1;*/
   // console.log(val);
        var val=val;
        var divArr=[];
        $('body #selected_parent_magento').val(val);
      
        if(val!=0)
        {
            if ($.inArray(val, selecteParentIdMagentoArr) == -1)
            {
                selecteParentIdMagentoArr.push(val);
            }
            else
            {
                selecteParentIdMagentoArr= $(selecteParentIdMagentoArr).not([val]).get();

                selecteParentIdMagentoArr.push(val);
            }
        }
        else
        {
            selecteParentIdMagentoArr.pop()
        }
        $.ajax({
            url: WEB_BASE_URL+'/mapping-relation-range-list',
            type: "post",
            data: {'type':'magento',"id":val,"editId":$('#magento_category_id').val(),'_token':$('.token').val(),'selected_parent':val,'process':'edit'},
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            success: function (response) {
               
                $("body #magentochild_category").append(response.view);
                 var $wrapper = $('#magentochild_category');

                $wrapper.find('.form-group').sort(function (a, b) {
                    return +a.dataset.sort - +b.dataset.sort;
                })
                .appendTo( $wrapper );
             
            },
            error: function (xhr, err) {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });

        return ;
      
    }
    $('body').on('click','.submitBtn',function(){
     $("#create-mapping-form").validate({
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
                'range_id': {
                        required: true,
                    },
                'magento_category_id': {
                        required: true,
                    },
            },
            errorPlacement: function (error, element) {
                console.log(error[0].id);
                if(error[0].id=="range_id-error")
                {
                    error.insertAfter('.parent_id');
                }
                else
                {
                    error.insertAfter('.magentoparent_id');
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
                 $('.magentoparent_id option:selected').each(function(i, obj) {
                   
                        var element = $(this);
                        if(element.val()==$('#magento_category_id').val())
                        {
                            $('#magento_category_id').val(element.attr('attr-table-id'));
                        }
                });
                var dataString = $("#create-mapping-form").serialize();
                $('.btn-blue').attr('disabled', true);
                $.ajax({
                    type: "POST",
                    url: $("#create-mapping-form").attr("action"),
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
                        $('.btn-blue').attr('disabled', false);
                        $("body  #page-loader").hide();
                        if (response.status == 1) {
                           
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            PoundShopApp.commonClass.table.draw();
                            getForm('create');
                             
                             //$("#create-mapping-form")[0].reset();
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
    $(document).on('click', '.btn-delete', function (event) {
        event.preventDefault();
        var $currentObj = $(this);
        var id = $(this).attr("id");            
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
                        url: BASE_URL + 'api-category-mapping-remove/'+id,
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
                                    PoundShopApp.commonClass._displaySuccessMessage(response.message);
                                    PoundShopApp.commonClass.table.draw();
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
        $("input[name='ids[]']:checked").each(function() {  
            allVals.push($(this).attr('value'));
        });          
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
                    if(result==true)
                    {
                        var join_selected_values = allVals.join(","); 
                         $.ajax({
                            url: BASE_URL + 'api-category-mapping-remove-multiple',
                            type: "post",
                            processData: false,
                             data: 'ids='+join_selected_values,
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
                                        PoundShopApp.commonClass.table.draw();
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
             bootbox.alert({
                    title: "Alert",
                    message: "Please select atleast one record to delete.",
                    size: 'small'
                });
                return false;
        }
    });
    $('#search_data').keyup(function()
    {
         var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13'){
            PoundShopApp.commonClass.table.search($(this).val()).draw() ;
        }
        if (keycode=='8')
        {
            $('#search_data').val('');
            PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
        }
    }) ;  
window.PoundShopApp = window.PoundShopApp || {}
window.PoundShopApp.poundShopTotes = new poundShopTotes();

})(jQuery);


//for checkbox all and none case
function updateDataTableSelectAllCtrl(table)
{
    var $table             = table.table().node();
    var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
    var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);   

    // If none of the checkboxes are checked
    if($chkbox_checked.length === 0)
    {
        chkbox_select_all.checked = false;
        if('indeterminate' in chkbox_select_all){
            chkbox_select_all.indeterminate = false;
        }

        // If all of the checkboxes are checked
    } 
    else if ($chkbox_checked.length === $chkbox_all.length)
    {
        chkbox_select_all.checked = true;
        if('indeterminate' in chkbox_select_all)
        {
            chkbox_select_all.indeterminate = false;
        }
        // If some of the checkboxes are checked
    } 
    else 
    {
        chkbox_select_all.checked = true;
        if('indeterminate' in chkbox_select_all)
        {
            chkbox_select_all.indeterminate = true;
        }
    }
}

var IDs=[];
var previously_selected=[];
var previously_magento_selected=[];

$('body').on('change','.parent_id',function(){
    //
    var parentId=$('option:selected', this).val();
    var childArr=$('.parent_id').attr('attr-child-nodes');
    var option = $('option:selected', this).attr('attr-child-nodes');
    var dropdownIndex=$(this).parent().children('.parent_id').index(this); 
    if(parentId!='')
    {
         $('#range_id').val(parentId);
    }
    else
    {
        //var y = $("body #parentIds").val().split(",");
        if(previously_selected.length==0)
        {
            previously_selected=$("body #parentIds").val().split(",");
        }
        var removeItem =previously_selected[previously_selected.length-1];

        previously_selected = jQuery.grep(previously_selected, function(value) {
          return value != removeItem;
        });
       // console.log(y);
        $('#range_id').val(previously_selected[previously_selected.length-1]);
    }
   

    

    if(option!='' && option!='undefined' && option!='null' && option!=null && option!='""')
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
        var childDropDownStr='<div class="form-group" id="'+parentId+'" data-sort="'+parentId+'"><select class="form-control parent_id" name="range_cat_id"><option value="">--Select Sub Category--</option>';
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
          childDropDownStr+="<option value='"+val.id+"' attr-child-nodes='"+child_nodesJson+"''>"+val.category_name+"</option>";
            
        });

        childDropDownStr+="</select></div>"
        
        $('#child_category').append(childDropDownStr);
    }
    else
    {
        console.log($(this).parent().parent().attr('id'));
       
       if($(this).parent().parent().attr('id') ==undefined)
       {
       
        $('#child_category').children('div').remove();
        
       }
       else
       {
            $('#child_category #'+$(this).parent().attr('id')).nextAll('div').remove();
       }
    }
});


function set_category_data()
{
    $('#categoryLevelDiv .magentoparent_id option[attr-child-nodes]').each(function(){
        id = $(this).attr('value');
        data = $(this).attr('attr-child-nodes');
        $('body').data(id, data);
    });
}
  function set_category_data_child()
{
    $('#magentochild_category .form-group .magentoparent_id option[attr-child-nodes]').each(function(){
        id = $(this).attr('value');
        data = $(this).attr('attr-child-nodes');
        $('body').data(id, data);
    });
}
    
$('body').on('change','.magentoparent_id',function(){
    set_category_data_child();
    var parentId=$('option:selected', this).val();
    var childArr=$('.magentoparent_id').attr('attr-child-nodes');
    var option = $('option:selected', this).attr('attr-child-nodes');
    if(parentId!='')
    {
         $('#magento_category_id').val(parentId);
    }
    else
    {
        //var y = $("body #parentIds").val().split(",");
        if(previously_magento_selected.length==0)
        {
            previously_magento_selected=$("body #parentIdsMagento").val().split(",");
        }
        var removeItem =previously_magento_selected[previously_magento_selected.length-1];

        previously_magento_selected = jQuery.grep(previously_magento_selected, function(value) {
          return value != removeItem;
        });
       // console.log(y);
        $('#magento_category_id').val(previously_magento_selected[previously_magento_selected.length-1]);
    }
    
    var nodesJson = $('body').data(parentId); 
    console.log(option);
    if(option!='' && option!='undefined' && option!='null' && option!=null && option!='""')
    {
       if(parentId!="")
        {
              console.log($('#magentochild_category #'+$(this).parent().attr('id')).nextAll('div'));
            $('#magentochild_category #'+$(this).parent().attr('id')).nextAll('div').remove();
        }
        else
        {
            console.log($('#magentochild_category #'+$(this).parent().attr('id')).nextAll('div'));
            $('#magentochild_category #'+$(this).parent().attr('id')).nextAll('div').remove();
        
        }

        if($(this).parent().parent().attr('id') ==undefined)
       {
       
        $('#magentochild_category').children('div').remove();
        
       }
      // var childRange=jQuery.parseJSON(option);
       //console.log(childRange);
        var childDropDownStr='<div class="form-group" id="'+parentId+'" data-sort="'+parentId+'"><select class="form-control magentoparent_id" name="magento_cat_id"><option value="">--Select Sub Category--</option>';
        if(typeof nodesJson != 'undefined')
            {   
                if(nodesJson.length > 0)
                {
                     var nodes = jQuery.parseJSON(nodesJson);
                    $.each(nodes, function( i, val ) {
                      if(typeof val.children != 'undefined')
                        {   
                            if(val.children.length > 0)
                            {
                                child_nodes = val.children;        

                                
                                var child_nodesJson = JSON.stringify(val.children);
                                
                                var category_id = val.id;
                                
                                category_id = category_id.toString();

                                $('body').data(category_id, child_nodesJson);
                            }    
                        } 
                        childDropDownStr+="<option value='"+val.id+"' attr-table-id='"+val.table_id+"' attr-child-nodes='"+child_nodesJson+"'>"+val.name+"</option>";  

                    });
                }
          
            
      

        childDropDownStr+="</select></div>";
        
        $('#magentochild_category').append(childDropDownStr);
    }
    else
    {
       if($(this).parent().parent().attr('id') ==undefined)
       {
       
        $('#magentochild_category').children('div').remove();
        
       }
       else
       {
            $('#magentochild_category #'+$(this).parent().attr('id')).nextAll('div').remove();
       }
    }
}
else
{
     if($(this).parent().parent().attr('id') ==undefined)
       {
       
        $('#magentochild_category').children('div').remove();
        
       }
       else
       {
            $('#magentochild_category #'+$(this).parent().attr('id')).nextAll('div').remove();
       }
}
})

$(document).ready(function ()
{   
    var rows_selected = [];   
    var table = PoundShopApp.commonClass.table; 
    $('#totes_table tbody').on('click', 'input[type="checkbox"]', function(e)
    {
        updateDataTableSelectAllCtrl(table);        
        e.stopPropagation();
    });
});

$('.refresh').on('click',function()
{    
    $('#search_data').val('');
    PoundShopApp.commonClass.table.search($('#search_data').val()).draw() ;  
});
function getForm(formtype)
{
    $.ajax({
        url:  WEB_BASE_URL + '/mapping-form-type/'+formtype,
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


