

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
            c._initialize();
            set_category_data();
        });
    };
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
                        $("#page-loader").hide();
                        if (response.status == 1) {
                            //$("#create-carton-form")[0].reset();
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            setTimeout(function () {
                                location.reload();
                               // window.location.href = WEB_BASE_URL + '/cartons';
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
$('body').on('change','.parent_id',function(){
    //
    var parentId=$('option:selected', this).val();
    var childArr=$('.parent_id').attr('attr-child-nodes');
    var option = $('option:selected', this).attr('attr-child-nodes');
    $('#range_id').val(parentId);
    if(option!='' && option!='undefined')
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
})

function set_category_data()
{
    $('#categoryLevelDiv .magentoparent_id option[attr-child-nodes]').each(function(){
        id = $(this).attr('value');
        data = $(this).attr('attr-child-nodes');
        $('body').data(id, data);
    });
}
    
$('body').on('change','.magentoparent_id',function(){
    
    var parentId=$('option:selected', this).val();
    var childArr=$('.magentoparent_id').attr('attr-child-nodes');
    var option = $('option:selected', this).attr('attr-child-nodes');
    $('#magento_category_id').val(parentId);
    var nodesJson = $('body').data(parentId); 
    if(option!='' && option!='undefined')
    {
       if(parentId!="")
        {
            
            $('#magentochild_category #'+$(this).parent().attr('id')).nextAll('div').remove();
        }
        else
        {
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
                        childDropDownStr+="<option value='"+val.id+"'>"+val.name+"</option>";  

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


