var on_load_form_data = {};

// saving data to textarea before submit
window.onload = function()
{

    if($('.ckeditor').length > 0)
    {	
    	$.each(CKEDITOR.instances, function(i, val){
			val.on('change', function() {
		        val.updateElement();
		     });
		});
	}
}

$( document ).ready(function() {
 
	$('[data-toggle="tooltip"]').tooltip()
 	$("img[data-original]").lazyload();
 	
	
 	const demo = document.querySelector('.custom-scroll');
    
    if(demo !== null)
    {	
    	const ps = new PerfectScrollbar(demo);
    }

    $('#myInputTextField').keypress(function(){
	    oTable.search($(this).val()).draw();
	})
	$('.btn-profile-toggle').click(function () {
		$('.profile-dropdown ul').toggleClass('open');
	})
	$('#toggle_container_info').click(function () {		
		$(this).parent('.container-info').toggleClass('open');
	})

	
	$('.btn-checklist-toggle').click(function () {
		$(this).parent('.checklist-container').toggleClass('open');
	})
	
	$('#toggle_sidebar').click(function () {
		$('.sidebar-area').toggleClass('sidebar-collapsed');
		$('body').toggleClass('sidebar-collapsed');
	})	
	$('#toggle_sidebar_mobile').click(function () {
		$('.sidebar-area').toggleClass('sidebar-open-mobile');
		$('body').toggleClass('sidebar-open-mobile-body');
	})
	$('#close_sidebar_mobile').click(function () {
		$('.sidebar-area').removeClass('sidebar-open-mobile');
		$('body').removeClass('sidebar-open-mobile-body');
	})
	
	$('.has-submenu').click(function () {
		$(this).find('.open-submenu').toggleClass('open');
		$(this).next().toggleClass('open');
	})
	
	$('a[data-rel^=lightcase]').lightcase();

	$( ".custom_fix_header" ).scroll(function() {
	  $(this).find('table.table_fix_header').find('thead td').css('top',$(this).scrollTop());
	});

	$('.datepicker').datepicker({
        inline              : true,
        format              : 'dd-M-yyyy',
        clearBtn            : true,
        autoclose           : true,
        enableOnReadonly    : true,
        disableTouchKeyboard: true,
        leftArrow           : '<i class="fa fa-long-arrow-left"></i>',
        rightArrow          : '<i class="fa fa-long-arrow-right"></i>',
        todayHighlight      : true,
        beforeShowDay       : function (date) {
        }
	});	

	var date = new Date();
	date.setDate(date.getDate());
	$('.datepicker_disbale_past').datepicker({
        inline              : true,
        format              : 'dd-M-yyyy',
        autoclose           : true,
        enableOnReadonly    : true,
        disableTouchKeyboard: true,
        leftArrow           : '<i class="fa fa-long-arrow-left"></i>',
        rightArrow          : '<i class="fa fa-long-arrow-right"></i>',
        todayHighlight      : true,
        startDate			: date,
        beforeShowDay       : function (date) {
        }
	});

	$("#startdate").datepicker({
       	nline              : true,
        format              : 'dd-M-yyyy',
        clearBtn            : true,
        autoclose           : true,
        enableOnReadonly    : true,
        disableTouchKeyboard: true,
        leftArrow           : '<i class="fa fa-long-arrow-left"></i>',
        rightArrow          : '<i class="fa fa-long-arrow-right"></i>',
        todayHighlight      : true,
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#enddate').datepicker('setStartDate', minDate);
    });

    $("#enddate").datepicker({
    	nline              : true,
        format              : 'dd-M-yyyy',
        clearBtn            : true,
        autoclose           : true,
        enableOnReadonly    : true,
        disableTouchKeyboard: true,
        leftArrow           : '<i class="fa fa-long-arrow-left"></i>',
        rightArrow          : '<i class="fa fa-long-arrow-right"></i>',
        todayHighlight      : true,
    }).on('changeDate', function (selected) {
        var maxDate = new Date(selected.date.valueOf());
        $('#startdate').datepicker('setEndDate', maxDate);
    });

	$('#btnFilter').click(function () {
		$('.card-flex-container').toggleClass('filter-open');
		$('.search-filter-dropdown').toggleClass('open');	
		$(this).toggleClass('open');	
	});
	
	// /*-- outside click to remove hide---*/
	// $(document).on("click", function (e) {		
	// 	if (!$(e.target).closest(".search-filter-dropdown, .btn-filter").length){
	// 		$('.card-flex-container').removeClass('filter-open');
	// 		$('.search-filter-dropdown').removeClass('open');	
	// 		$('#btnFilter').removeClass('open');	
	// 	}		
	// });
	$(".dropdown").on("show.bs.dropdown", function(event){
	  		$('.card-flex-container').removeClass('filter-open');
			$('.search-filter-dropdown').removeClass('open');	
	});
	$('.nav-tabs').responsiveTabs();

	$('.category-breadcrumbs a').on('click', function(e) {
		e.preventDefault();
    	var liIndex =  $(this).parent('li').index();	    	
		$( '.category-list-holder' ).slick('slickGoTo', parseInt(liIndex) -1 );
		// if (window.matchMedia("(max-width: 1600px)").matches) {				
		// 	$( '.category-list-holder' ).slick('slickGoTo', parseInt(liIndex) -1 );
		// }	

	});

	$(".category-list-holder").slick({  
	  	infinite: false,	  
		slidesToShow: 5,
		arrows: true,

		responsive: [{

	      breakpoint: 1600,
	      settings: {
	        slidesToShow: 4		        
	      }

	    }, 
	    {

	      breakpoint: 1025,
	      settings: {
	        slidesToShow: 3		        
	      }

	    }, {

	      breakpoint: 769,
	      settings: {
	        slidesToShow: 2
	      }

	    }, {

	      breakpoint: 580,
	      settings: {
	        slidesToShow: 1
	      }

	    }, {

	      breakpoint: 300,
	      settings: "unslick" // destroys slick

	    }],
		onAfterChange: function(){
	    	console.log($('.slides').slickCurrentSlide()+1);
	  	}	
	});


	$( document ).ajaxComplete(function(){
		
		$('a[data-rel^=lightcase]').lightcase();
		
		$("img[data-original]").lazyload();
		
		$( ".custom_fix_header" ).scroll(function() {
		  $(this).find('table.table_fix_header').find('thead td').css('top',$(this).scrollTop());
		});
    });
});

$("body").on("keypress","input[only_numeric]",function(e)
{
	if($(this).val().indexOf(".")>-1 && e.which=="46")
  	{
		return false;
  	}

	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
	{

	  if((e.which == '17' && e.which == '86') || (e.which == '17' && e.which == '67') || e.which=="46")
	  {

		return true;
		
	  }
	  else
	  {
		   return false;
	  } 
	}
});  

$("body").on("keypress","input[only_digit]",function(e)
{
	// Allow: backspace, delete, tab, escape, enter and .
	if(e.ctrlKey === true || e.metaKey === true)
	{
	  return true;
	}


  	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
  	{
		if((e.which == '17' && e.which == '86') || (e.which == '17' && e.which == '67'))
		{
		  return true;
		}
		else
		{
	    	return false;
		} 
	}
});

$("body").on("keypress","input[only_phone]",function(e)
{   
	// Allow: backspace, delete, tab, escape, enter, space, - , ( , ) and .
	if(e.ctrlKey === true || e.metaKey === true)
	{
		return true;
	}

	if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
	{
		if((e.which == '17' && e.which == '86') || (e.which == '17' && e.which == '67') || (e.which == '40') || (e.which == '41') || (e.which == '45') || (e.which == '32') || (e.which == '16' && e.which == '57')  || (e.which == '16' && e.which == '48'))
		{
			return true;
		}
		else
		{
	 		return false;
		} 
	}
});

$("body").on("keypress","input[only_numeric_dimension]",function(e)
{  
    if($(this).val().indexOf(".")>-1 && e.which=="46")
    {
        return false;
    }

    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
    {
        if((e.which == '17' && e.which == '86') || (e.which == '17' && e.which == '67') || e.which=="46")
        {
            return true;
        }
        else
        {
            return false;
        } 
    }
});

$('body').on('change', '.master-checkbox', function()
{
	var childClass = 'child-checkbox';

	if(typeof $(this).attr('child-checkbox-class') != 'undefined')
	{
		childClass = $(this).attr('child-checkbox-class');
	}	

	var table = $(this).closest('thead').parent('table');

	if($(this).closest('thead').parent('table').parent('div').hasClass('dataTables_scrollHeadInner'))
	{
		var table = $('.master-checkbox').parents('table').parents('div').closest('.dataTables_scrollBody');
	}	
	
	$(table).find('.'+childClass).prop('checked', $(this).prop('checked'));
});

$('body').on('click', 'tbody input[type="checkbox"]', function(e)
{
    e.stopPropagation();
    
    var child_class = 'child-checkbox';

	var chkbox_select_all  = $('.dataTables_scrollHead .master-checkbox').get(0); 
    
    if(typeof chkbox_select_all == 'undefined')
    {
    	chkbox_master  = $(this).parents('tbody').prev('thead').find('input[type="checkbox"].master-checkbox');
    	
    	chkbox_select_all  = $(chkbox_master).get(0);

    	if(typeof chkbox_select_all != 'undefined')
    	{	
	    	if(typeof $(chkbox_master).attr('child-checkbox-class') != 'undefined')
	    	{
	    		child_class = $(chkbox_master).attr('child-checkbox-class');
	    	}	
		}	
    }	

    var $chkbox_all        = $('tbody input[type="checkbox"].'+child_class);
    
    var $chkbox_checked    = $('tbody input[type="checkbox"]:checked.'+child_class);
    
    // If none of the checkboxes are checked
    if(typeof chkbox_select_all != 'undefined')
    {	
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
		} 
	    else 
	    {
	        if('indeterminate' in chkbox_select_all)
	        {
	            chkbox_select_all.checked = false;   
	            chkbox_select_all.indeterminate = true;
	        }
	    }
	}    
});

function getListingCheckboxIds(childClass="child-checkbox")
{
    var listing_checked_ids = [];

    if($('.'+childClass+':checked').length > 0)
    {
        $('.'+childClass+':checked').each(function(index) {
             listing_checked_ids.push($(this).val());
        }); 
    }
    
    return listing_checked_ids;    
}

function set_query_para($key,$data)
{
  	var url_string = window.location.protocol+"://"+window.location.hostname+window.location.port+window.location.pathname;
  	var url_string = "";
  	var search = ltrim(window.location.search,"?")
  	var search_join = [];
  	var $target_found = false;
  	search_split = search.split("&");
	if(search!="")
	{
	  	$.each(search_split,function($index,$value)
	  	{
		  	$value_split = $value.split("=");
		  	if($value_split.length=2)
		  	{
				if($value_split[0]==$key)
				{
				  	$value_split[1] = $data
				  	$target_found = true;
				}
		  	}
		  	
		  	$value_join = $value_split.join("=");
		  	
		  	search_join.push($value_join);
	  });
	}
	
	if($target_found==false)
	{
	  search_join.push($key+"="+$data)
	}
	
	url_string  +=("?"+(search_join.join("&")));
	
	history.pushState(null,null,url_string);
}

function ltrim(str, characters) {
	var nativeTrimLeft = String.prototype.trimLeft;
	str = makeString(str);
	if (!characters && nativeTrimLeft) return nativeTrimLeft.call(str);
	characters = defaultToWhiteSpace(characters);
	return str.replace(new RegExp('^' + characters + '+'), '');
}

function makeString(object)
{
	if (object == null) return '';
	return String(object);
}

function defaultToWhiteSpace(characters) {
	if (characters == null)
	return '\\s';
	else if (characters.source)
	return characters.source;
	else
	return '[' + escapeRegExp(characters) + ']';
}  
function escapeRegExp(str) {
	return makeString(str).replace(/([.*+?^=!:${}()|[\]\/\\])/g, '\\$1');
}

function reInitSlick(class_name = "category-list-holder")
{
	$('.'+class_name).slick('reinit');
}
jQuery(document).ready(function($) {
	$('a[data-rel^=lightcase]').lightcase();
});