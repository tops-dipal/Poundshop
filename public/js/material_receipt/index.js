/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

(function ($)
{
    "user strict";

    var dataTableId = 'listing_table';

    var poundShopMaterialReceipt = function ()
    {
        $(document).ready(function ()
        {
        	$.ajaxSetup({
		        headers: {
		            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		        }
		    });

        	$('#txt_search').focus();

        	$("body").on("click",'td.sorting[sort-by],td.sorting_asc[sort-by],td.sorting_desc[sort-by]',function(e)
	        {
	          $('#pagination_sort_by').val($(this).attr("sort-by"))
	          $('#pagination_sort_direction').val($(this).attr("sort-order"))
	          $('#pagination_page').val(1);
	          set_query_para("page",1)
	          set_query_para("sort_direction",$('#pagination_sort_direction').val())
	          set_query_para("sort_by",$('#pagination_sort_by').val())
	          page_update();
	          return false;
	        });

        	$("body").on("click",'ul.pagination li a.page-link',function(e)
		    {
		        e.preventDefault();

		        if($(this).attr('href') && $(this).attr('href')!="#" && !$(this).hasClass("disabled"))
		        {
		            page_value = $(this).attr('page_number');
		            
		            if(page_value!="" && page_value!=undefined)
		            {
		        		$('#pagination_page').val(page_value);
		                set_query_para("page",page_value);
		            }
		            
		            page_update();
		        }
		    	
		    	return false;
		    });    	

        	$('body #txt_search').donetyping(function(e)
		    {
		    	var filter_by_po = $('select[name="filter_by_po"]').val();
		    	
		    	if(filter_by_po != "")
		    	{	
			    	$('select[name="filter_by_po"]').val('');
			    	
			    	var fil_counter = parseInt ($('.filter_count_digit').text());
			    		
			    	if(!isNaN(fil_counter))
			    	{
			    		fil_counter = fil_counter - 1;

			    		if(fil_counter == 0)
			    		{
			    			$('.filter_count').html('');

			    			$('#btnFilter').removeClass('open');
			        		
			        		$('.search-filter-dropdown').removeClass('open'); 
			        		
			        		$('.card-flex-container').removeClass('filter-open'); 
			    		}
			    		else
			    		{	
							$('.filter_count').html(' (<span class="filter_count_digit">'+fil_counter+'</span>)');
			    		}
			    	}	
				}	
				
				set_query_para("search", $('#txt_search').val());
                page_update();
		    });

      //   	$("body").on("keyup",'#txt_search',function(e)
		    // {
		    // 	var keycode = (e.keyCode ? e.keyCode : e.which);
               
      //           if(keycode == '13')
      //           {
      //           	set_query_para("search", $('#txt_search').val());
      //               page_update();
      //           }
		    	
		    // 	$(this).focus();
		    // });

		    $("body").on('change','#show_discrepancies', function(e)
		    {
		    	if($(this).prop('checked'))
		    	{
		    		set_query_para("show_discrepancies", 1);
		    	}
		    	else
		    	{
		    		set_query_para("show_discrepancies", 0);
		    	}	

		    	page_update();
		    });	

        	$("body").on("change",'#per_page_value_dropdown',function(e)
		    {
		        e.preventDefault();

        		let per_page_value = $(this).val();
        		
        		if(per_page_value > 0)
        		{
        			$('#pagination_page').val(1);
        			$('#per_page_value').val(per_page_value);
        			set_query_para("per_page_value",per_page_value);
        			set_query_para("page",1);
        		}	

        		page_update();
        		
        		return false;
        	});

        	$("body").on("keyup",'input[name="qty_received"]',function(e)
		    {
				var row = $(this).parents('tr');
		    	
		    	$(row).find('.difference_label_dev').removeClass('diff-plus').removeClass('diff-minus');
		    	
		    	var ordered_quantity = parseInt($(row).find('input[name="total_quantity"]').val());
		    	
		    	var qty_received = parseInt($(row).find('input[name="qty_received"]').val());
		    	
		    	var is_photobooth = $(row).find('input[name="is_photobooth"]').val();

		    	if(!isNaN(ordered_quantity) && !isNaN(qty_received))
		    	{	
		    		// Add one extra qty if is_photobooth is yes
		    		if(is_photobooth == 1)
		    		{
		    			qty_received = qty_received + 1;
		    		}	

		    		var diff = qty_received - ordered_quantity;
		    		
		    		$(row).find('input[name="difference"]').val(diff);
		    		
		    		$(row).find('.difference_label').text(diff);

		    		if(diff > 0)
		    		{
		    			$(row).find('.difference_label_dev').addClass('diff-plus');
		    		}	
		    		
		    		if(diff < 0)
		    		{
		    			$(row).find('.difference_label_dev').addClass('diff-minus');
		    		}
		    	}
		    	else
		    	{
		    		$(row).find('input[name="difference"]').val(0);
		    		
		    		$(row).find('.difference_label').text(0);	
		    	}	
		    });	

        	// $('body').on('change', '.set_location_details', function(){
        	// 	set_location_details(this);
        	// });

        	c._initialize();
        	
        	page_update();
		});
    };

    var c = poundShopMaterialReceipt.prototype;
    
    c._initialize = function ()
    {
        $('.custom-select-search').selectpicker({
		    liveSearch:true,
		    size:10
		});

		$('.datepicker_disbale_future').datepicker({
	        inline              : true,
	        format              : 'dd-M-yyyy',
	        autoclose           : true,
	        enableOnReadonly    : true,
	        disableTouchKeyboard: true,
	        leftArrow           : '<i class="fa fa-long-arrow-left"></i>',
	        rightArrow          : '<i class="fa fa-long-arrow-right"></i>',
	        todayHighlight      : true,
	        endDate				: SCAN_START_DATE,
	        beforeShowDay       : function (date) {
	        }
		});	
	};
	
	setSearchType = function (me)
	{
		if($(me).val() != "")
		{	
			set_query_para("search_type",$(me).val());
			
		}
		
		page_update();
	}

	page_update = function (){
		$.ajax({
	          url: $('#pagination_url').val() +'?page='+ $('#pagination_page').val(),
	          type: 'POST',
	          data: { 
	            'booking_id':$('#booking_id').val(),
	            'sort_by':$('#pagination_sort_by').val(), 
	            'sort_direction':$('#pagination_sort_direction').val(), 
	            'per_page':$('#per_page_value').val(),
	            'filter_by_po':$('select[name="filter_by_po"]').val(),
	            'search':$.trim($('#txt_search').val()),
	            'search_type':$.trim($('#search_type').val()),
	            'show_discrepancies':$.trim($('#show_discrepancies:checked').val()),
	        },
	        beforeSend: function()
	        {
	         	$("#page-loader").show();   
	        },      
	        complete:function()
	        {
	            $("#page-loader").hide();

	            initialize_datepicker();

	            initialize_best_before_cases();

	        },
	        success: function(data)
	        {
	        	var scroll_postition = $('.custom_fix_header').scrollTop();
				$('#load-ajax-table').html(data);
	            $('.custom_fix_header').scrollTop(scroll_postition);
	            get_booking_details();
			},
	    });

        return false;
	}

	initialize_best_before_cases = function(){
		
		if($('input.open_case').length > 0)
		{
			$('input.open_case').each(function(){
				let form_id = $(this).attr('form');
				$('input[name="is_inner_outer_case"][form="'+form_id+'"]:checked').trigger('change');
			})
		}	
	}


	// $("body .set_location_details").scannerDetection(function(e)
	// {   
 //    	init_set_location_detatils('1'); 
	// });

	// $('body').on('input','.set_location_details', function(e) {
	// 	var el = this;
	// 	setTimeout(function() {
	// 	    init_set_location_detatils(2,el);
	// 	},300);
 //    });

 //    init_set_location_detatils = function(type,el)
	// {
	// 	$(el).prev('span.scan_type').remove();

	// 	if(type == '1')
	// 	{
	// 		html = '<span class="scan_type font-10-dark bold d-block mt-1">Scanner</span>';
	// 	}
	// 	else 
	// 	{
	// 		html = '<span class="scan_type font-10-dark bold d-block mt-1">Manual</span>';
	// 		init_typeahead(el);
	// 	}

	// 	// $(el).before(html);
	// }

	$('body').on('input', '.set_location_details', function(){
		init_typeahead(this);	
	});

	init_typeahead = function(el = $('.set_location_details')){
		
		var warehouse_id = $('#warehouse_id').val();

		var booking_id = $('#booking_id').val();
		
		$(el).next('span.location_type').remove();
		
		$(el).typeahead({
		    ajax: {
				url: BASE_URL+'api-location-auto-suggest-on-input',
				method: 'GET',
				extra_data: {
							'warehouse_id': warehouse_id, 
							'booking_id': booking_id, 
							"keyword" : $(el).val(), 
							'module' : 'material-receipt'
						},
				headers: {
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
			}
		});
	}

	setCasesDetails = function (me){
		
		$(me).parents('tr').find('.cases_blocks').html("");

		var field_value = $(me).val();
		
		var tr_view =  $(me).parents('tr');
		
		var ul_view = $(me).parents('ul');

		var form_id = $(me).attr('form');

		var bestBeforeState = $(ul_view).find('input[name="is_best_before_date"]').prop('checked');

		var is_photobooth = $('input[name="is_photobooth"][form="'+form_id+'"]').val();

		var product_detail_tr = $('input[name="is_photobooth"][form="'+form_id+'"]').parents('tr');

		var inventory_barcode_details  = $('input.inventory_case_details[form="'+form_id+'"]');

		if(field_value == 1)
		{
			var is_variants = $('input[name="is_variant"][form="'+form_id+'"]:checked').val();

			if(is_variants == 1 && field_value == 1)
			{
				el_name = $(me).attr('name');

				$('input[name="'+el_name+'"][form="'+form_id+'"][value=0]').prop('checked',true);

				bootbox.alert({
			        title: "Alert",
			        message: "You cannot set Cases & Barcodes and Variants fields to Yes at a same time.",
			        size: 'small'
			    });

			    return false;
			}	

			enableDisableMasterFields(form_id);

	  		$(ul_view).find('.best-before-date-option').show();
			
			$(tr_view).find('.product-case-detail').show();

			var case_template = $('#template_case_details').clone();
			
			case_template.find('input, select').attr('form', form_id);
			
			setIndexToNameAttr(case_template, '.card-outer-inner', me);

			case_template.find('.case_action').removeAttr('onclick');
			
			case_template.find('.case_action').attr('onclick', 'addCase(this)');
			
			case_template.find('.case_action').attr('form', form_id);

			case_template.find('.move_location_action').attr('form', form_id);
			
			case_template.find('.case_action').html('<span class="icon-moon icon-Add font-10"></span>');

			if(bestBeforeState == false)
			{	
				case_template.find('.best-date').hide();
			}
			else if($('input.po_best_before_date[form="'+form_id+'"]').length > 0)
			{

				let best_before_date = $('input.po_best_before_date[form="'+form_id+'"]').val();
				
				case_template.find('.best-date').find('input.datepicker').attr('value',best_before_date);
			}

			var outer_case_inc_count = case_template.find(".radio_for_outer:checked").val()

			if(outer_case_inc_count == '0')
			{
				case_template.find('.for_outer').hide();
			}

			var inner_case_inc_count = case_template.find(".radio_for_inner:checked").val()

			if(inner_case_inc_count == '0')
			{
				case_template.find('.for_inner').hide();
			}	

			if(inventory_barcode_details.length > 0)
			{
				let outer_barcode_input = 'inner_outer_case_detail[1][outer][barcode]';
				
				var outer_qty_per_box_input = 'inner_outer_case_detail[1][outer][qty_per_box]';
				
				let outer_no_of_box = 'inner_outer_case_detail[1][outer][no_of_box]';
				
				let inner_barcode_input = 'inner_outer_case_detail[1][inner][barcode]';
				
				let inner_qty_per_box_input = 'inner_outer_case_detail[1][inner][qty_per_box]';
				
				case_template.find('input[name="'+outer_barcode_input+'"]').attr('value',inventory_barcode_details.attr('attr-outer-barcode'));

				case_template.find('input[name="'+outer_qty_per_box_input+'"]').attr('value',inventory_barcode_details.attr('attr-outer-qty'));

				case_template.find('input[name="'+outer_no_of_box+'"]').attr('value',1);

				case_template.find('input[name="'+inner_barcode_input+'"]').attr('value',inventory_barcode_details.attr('attr-inner-barcode'));
				
				case_template.find('input[name="'+inner_qty_per_box_input+'"]').attr('value',inventory_barcode_details.attr('attr-inner-qty'));
			}
			else
			{
				var outer_qty_per_box_input = "";
			}	

			var loose_case_template = $('#template_loose_location').clone();

			loose_case_template.find('input, select').attr('form', form_id);

			loose_case_template.find('.loose_location_action').removeAttr('onclick');

			loose_case_template.find('.loose_location_action').attr('onclick', 	'addLoseLocation(this)');
			
			loose_case_template.find('.loose_location_action').attr('form', form_id);

			loose_case_template.find('.loose_location_action').html('<span class="icon-moon icon-Add font-10"></span>');

			loose_case_template.find('.move_location_action').attr('form', form_id);

			setIndexToNameAttr(loose_case_template, '.card-loose', me);

			if(bestBeforeState == false)
			{	
				loose_case_template.find('.best-date').hide();
			}
			else if($('input.po_best_before_date[form="'+form_id+'"]').length > 0)
			{

				let best_before_date = $('input.po_best_before_date[form="'+form_id+'"]').val();
				
				loose_case_template.find('.best-date').find('input.datepicker').attr('value',best_before_date);
			}

			html = $(case_template).html() + $(loose_case_template).html(); 
			
			if(is_photobooth == '1')
			{
				var photobooth_template = $('#template_case_photobooth').clone();
				
				html = html + $(photobooth_template).html();

				// $(product_detail_tr).find('.photobooth_js_labels').hide();
			}	

			$(tr_view).find('.cases_blocks').html(html);
			
			initialize_datepicker();
			
			if(outer_qty_per_box_input.length > 0)
			{	
				setTotalCaseQty($('input[name="'+outer_qty_per_box_input+'"][form="'+form_id+'"]'));
			}
		}
		else
		{
			if(is_photobooth == '1')
			{
				// $(product_detail_tr).find('.photobooth_js_labels').show();
			}
				
			enableDisableMasterFields(form_id);

			$(tr_view).find('.product-case-detail').hide();
			// $(tr_view).find('.cases_blocks').html("");
			$(ul_view).find('.best-before-date-option').hide();
			
		}

		return false;
	};

	enableDisableMasterFields = function(form_id){
		
		// var is_variants = $('input[name="is_variant"][form="'+form_id+'"]:checked').val();
		
		// var is_inner_outer_case = $('input[name="is_inner_outer_case"][form="'+form_id+'"]:checked').val();

		// var product_type = $('input[name="product_type"][form="'+form_id+'"]').val();
			
		// if(is_inner_outer_case == 1 || is_variants == 1)
		// {
		// 	readonly = true;
		// }
		// else
		// {
		// 	readonly = false;
		// }	

		// if(product_type == 'parent' || product_type == '')
		// {
		// 	readonly = true;
		// }	
		
		// if(readonly == true)
		// {
		// 	$('input[name="qty_received"][form="'+form_id+'"]').val("");
		// 	$('input[name="location"][form="'+form_id+'"]').val("");
		// 	$('input[name="location"][form="'+form_id+'"]').parent('td').find('span.location_type').remove();
		// }

		// $('input[name="qty_received"][form="'+form_id+'"]').prop('readonly', readonly);
		
		// $('input[name="location"][form="'+form_id+'"]').prop('readonly', readonly);
		
		// $('input[name="qty_received"][form="'+form_id+'"]').trigger('keyup');

		// return true;
	}

	setBestBeforeDate = function(me)
	{
		var state = $(me).prop('checked');
    	
    	var tr_view =  $(me).parents('tr');

    	if(state == true)
    	{
    		$(tr_view).find('.cases_blocks').find('.best-date').show();
    	}	
    	else
    	{
    		$(tr_view).find('.cases_blocks').find('.best-date').hide();
    	}
		
		initialize_datepicker();
	}

	addCase = function (me)
	{
		var tr_view =  $(me).parents('tr');
		
		var form_id =  $(me).attr('form');

		var bestBeforeState = $(tr_view).find('input[name="is_best_before_date"][form="'+form_id+'"]').prop('checked');
		
		if($(tr_view).find('.card-outer-inner').length > 0)
		{
			var case_template = $('#template_case_details').clone();
			
			case_template.find('input, select').attr('form', form_id);

			case_template.find('.case_action').attr('form', form_id);

			case_template.find('.move_location_action').attr('form', form_id);

			setIndexToNameAttr(case_template, '.card-outer-inner', me);

			var outer_case_inc_count = case_template.find(".radio_for_outer:checked").val()

			if(outer_case_inc_count == '0')
			{
				case_template.find('.for_outer').hide();
			}

			var inner_case_inc_count = case_template.find(".radio_for_inner:checked").val()

			if(inner_case_inc_count == '0')
			{
				case_template.find('.for_inner').hide();
			}	

			if(bestBeforeState == false)
			{	
				case_template.find('.best-date').hide();
			}	
			else if($('input.po_best_before_date[form="'+form_id+'"]').length > 0)
			{

				let best_before_date = $('input.po_best_before_date[form="'+form_id+'"]').val();
				
				case_template.find('.best-date').find('input.datepicker').attr('value',best_before_date);
			}

			html = $(case_template).html();

			$(tr_view).find('.card-outer-inner:last').after(html);
			
			initialize_datepicker();
		}
		
		return false;
	};

	removeCase = function(me)
	{
		var inner_id = $(me).parents('.card-outer-inner').find('input.outer_id').val();
		var outer_id = $(me).parents('.card-outer-inner').find('input.inner_id').val();
		var form_id = $(me).parents('.card-outer-inner').find('input.inner_id').attr('form');
			
		if(typeof outer_id != 'undefined' && typeof inner_id != 'undefined')	
		{
			var html = '<input type="hidden" name="remove_case_details[]" from="'+form_id+'" value="'+outer_id+'">';
			html += '<input type="hidden" name="remove_case_details[]" from="'+form_id+'" value="'+inner_id+'">';
			$('#'+form_id).append(html);
		}	

		var el = $(me).parents('.cases_blocks');

		$(me).parents('.card-outer-inner').remove();

		setTotalCaseQty(me, el);
	}

	addLoseLocation = function(me)
	{
		var tr_view =  $(me).parents('tr');
		
		var form_id =  $(me).attr('form');

		var bestBeforeState = $(tr_view).find('input[name="is_best_before_date"][form="'+form_id+'"]').prop('checked');

		if($(tr_view).find('.card-outer-inner').length > 0)
		{
			var loose_case_template = $('#template_loose_location').clone();
				
			loose_case_template.find('input, select').attr('form', form_id);

			loose_case_template.find('.loose_location_action').attr('form', form_id);

			loose_case_template.find('.move_location_action').attr('form', form_id);

			setIndexToNameAttr(loose_case_template, '.card-loose', me);

			if(bestBeforeState == false)
			{	
				loose_case_template.find('.best-date').hide();
			}
			else if($('input.po_best_before_date[form="'+form_id+'"]').length > 0)
			{

				let best_before_date = $('input.po_best_before_date[form="'+form_id+'"]').val();
				
				loose_case_template.find('.best-date').find('input.datepicker').attr('value',best_before_date);
			}

			html = $(loose_case_template).html();

			$(tr_view).find('.card-loose:last').after(html);

			initialize_datepicker();
		}	
	}

	removeLoseLocation = function(me)
	{
		var loose_id = $(me).parents('.card-loose').find('input.loose_id').val();
		
		var form_id = $(me).parents('.card-loose').find('input.loose_id').attr('form');
		
		if(typeof loose_id != 'undefined')
		{
			var html = '<input type="hidden" name="remove_case_details[]" from="'+form_id+'" value="'+loose_id+'">';	
			$('#'+form_id).append(html);
		}	

		var el = $(me).parents('.cases_blocks');
		
		$(me).parents('.card-loose').remove();

		setTotalCaseQty(me, el);
	}

	addMoveLocationForOuter = function(me)
	{
		var tr_view =  $(me).parents('tr');

		var current_view =  $(me).parents('.product-location-row');
		
		var form_id =  $(me).attr('form');	

		var set_index =  $(me).parents('.product-location-row').find('input[nameAttrIndex]').attr('nameAttrIndex');	

		var bestBeforeState = $(tr_view).find('input[name="is_best_before_date"][form="'+form_id+'"]').prop('checked');
		
		if($(current_view).length > 0)
		{
			var template_move_prduct_to_location = $('#template_move_prduct_to_location').clone();
			
			template_move_prduct_to_location.find('input, select').attr('form', form_id);

			template_move_prduct_to_location.find('.move_location_action').attr('form', form_id);

			if(bestBeforeState == false)
			{	
				template_move_prduct_to_location.find('.best-date').hide();
			}
			else if($('input.po_best_before_date[form="'+form_id+'"]').length > 0)
			{

				let best_before_date = $('input.po_best_before_date[form="'+form_id+'"]').val();
				
				template_move_prduct_to_location.find('.best-date').find('input.datepicker').attr('value',best_before_date);
			}

			template_move_prduct_to_location.find('input, select, textarea').each(function(){
				let current_name = $(this).attr('name');
				
				let new_name = replaceIndexWithString(current_name, set_index);
				
				$(this).attr('name', new_name);

				$(this).attr('nameattrindex', set_index);
			});
			
			html = $(template_move_prduct_to_location).html();

			$(me).parents('.outer').find('.product-location-row:last').after(html);
			
			initialize_datepicker();
		}	
	}

	removeMoveLocationForOuter = function(me)
	{
		var location_id = $(me).parents('.product-location-row').find('input.case_location_id').val();
		
		var form_id = $(me).parents('.product-location-row').find('input.case_location_id').attr('form');
		
		if(typeof location_id != 'undefined')
		{
			html = '<input type="hidden" name="remove_case_locations[]" from="'+form_id+'" value="'+location_id+'">';
			$('#'+form_id).append(html);
		}	

		$(me).parents('.product-location-row').remove();
	}

	addMoveLocationForInner = function(me)
	{
		var tr_view =  $(me).parents('tr');

		var current_view =  $(me).parents('.product-location-row');
		
		var form_id =  $(me).attr('form');	

		var set_index =  $(me).parents('.product-location-row').find('input[nameAttrIndex]').attr('nameAttrIndex');	

		var bestBeforeState = $(tr_view).find('input[name="is_best_before_date"][form="'+form_id+'"]').prop('checked');
		
		if($(current_view).length > 0)
		{
			var template_move_prduct_to_location = $('#template_move_prduct_to_location').clone();
			
			template_move_prduct_to_location.find('input, select').attr('form', form_id);

			template_move_prduct_to_location.find('.move_location_action').attr('form', form_id);

			if(bestBeforeState == false)
			{	
				template_move_prduct_to_location.find('.best-date').hide();
			}
			else if($('input.po_best_before_date[form="'+form_id+'"]').length > 0)
			{

				let best_before_date = $('input.po_best_before_date[form="'+form_id+'"]').val();
				
				template_move_prduct_to_location.find('.best-date').find('input.datepicker').attr('value',best_before_date);
			}

			template_move_prduct_to_location.find('input, select, textarea').each(function(){
				let current_name = $(this).attr('name');
				
				let new_name = replaceIndexWithString(current_name, set_index);
				
				new_name = new_name.replace('[outer]', '[inner]');

				$(this).removeClass('required').removeClass('required_digit');

				$(this).attr('name', new_name);

				$(this).attr('nameattrindex', set_index);
			});

			html = $(template_move_prduct_to_location).html();

			$(me).parents('.inner').find('.product-location-row:last').after(html);

			initialize_datepicker();
		}	
	}


	addMoveLocationForLoose = function(me)
	{
		var tr_view =  $(me).parents('tr');

		var current_view =  $(me).parents('.product-location-row');
		
		var form_id =  $(me).attr('form');	

		var set_index =  $(me).parents('.product-location-row').find('input[nameAttrIndex]').attr('nameAttrIndex');	

		var bestBeforeState = $(tr_view).find('input[name="is_best_before_date"][form="'+form_id+'"]').prop('checked');
		
		if($(current_view).length > 0)
		{
			var template_move_prduct_to_location = $('#template_move_prduct_to_location').clone();
			
			template_move_prduct_to_location.find('input, select').attr('form', form_id);

			template_move_prduct_to_location.find('.move_location_action').attr('form', form_id);

			template_move_prduct_to_location.find('.move_location_action').parents('div').find('span:contains("Move Boxes")').text('Move');

			if(bestBeforeState == false)
			{	
				template_move_prduct_to_location.find('.best-date').hide();
			}
			else if($('input.po_best_before_date[form="'+form_id+'"]').length > 0)
			{

				let best_before_date = $('input.po_best_before_date[form="'+form_id+'"]').val();
				
				template_move_prduct_to_location.find('.best-date').find('input.datepicker').attr('value',best_before_date);
			}

			template_move_prduct_to_location.find('input, select, textarea').each(function(){
				let current_name = $(this).attr('name');
				
				current_name = current_name.replace('[index][outer]', '[loose][index]');
				
				let new_name = replaceIndexWithString(current_name, set_index);
				

				$(this).attr('name', new_name);

				$(this).attr('nameattrindex', set_index);
			});

			html = $(template_move_prduct_to_location).html();

			$(me).parents('.card-loose').find('.product-location-row:last').after(html);

			initialize_datepicker();
		}	
	}	

	setIndexToNameAttr = function (el, el_for_length, current_el)
	{
		var tr_view =  $(current_el).parents('tr');
		// var index =  $(tr_view).find(el_for_length).length;
		if(el_for_length == '.card-outer-inner')
		{	
			var index =  $(tr_view).find('.radio_for_outer:last').attr('nameattrindex');
		}

		if(el_for_length == '.card-loose')
		{
			var index =  $(tr_view).find('.card-loose:last').find('input:first').attr('nameattrindex');
		}	

		if(typeof index == 'undefined')
		{
			index = 0;
		}	
		
		if(el.length > 0)
		{
			var set_index = parseInt(index) + 1;

			el.find('input, select, textarea').each(function(){
				let current_name = $(this).attr('name');
				
				let new_name = replaceIndexWithString(current_name, set_index);
				
				$(this).attr('name', new_name);

				$(this).attr('nameAttrIndex', set_index);
			});
			
			return el;
		}	
	}

	replaceIndexWithString = function(str, index)
	{
		if(str.indexOf('index') != -1){
			str = str.replace('index', index);
		}	

		return str;
	}

	setVariants = function(me)
	{
		var field_value = $(me).val();
		
		var form_id = $(me).attr('form');

		var tr_view = $(me).parents('tr');
		
		var is_inner_outer_case = $('input[name="is_inner_outer_case"][form="'+form_id+'"]:checked').val();

		if(is_inner_outer_case == 1 && field_value == 1)
		{
			el_name = $(me).attr('name');

			$('input[name="'+el_name+'"][form="'+form_id+'"][value=0]').prop('checked',true);

			bootbox.alert({
	            title: "Alert",
	            message: "You cannot set Cases & Barcodes and Variants fields to Yes at a same time.",
	            size: 'small'
	        });

	        return false;
		}	

		if(field_value == 1)
		{
			enableDisableMasterFields(form_id);
			$(tr_view).find('.product-varient').show();
		}
		else
		{
			enableDisableMasterFields(form_id)
			$(tr_view).find('.product-varient').hide();
		}
	}

	showHideMovetoLocation = function(me)
	{
		var form_id = $(me).attr('form');
		
		var el_index = $(me).attr('nameattrindex');
		
		if($(me).hasClass('radio_for_outer'))
		{
			var div = $(me).parents('.outer').find('.product-location-row');

			var el_name = 'inner_outer_case_detail['+el_index+'][outer][no_of_box]';
		}
		else
		{
			var div =  $(me).parents('.inner').find('.product-location-row');

			var el_name = 'inner_outer_case_detail['+el_index+'][inner][no_of_box]';
		}	


		if($(me).val() == 1)
		{
			$(div).show();
			$('input[name="'+el_name+'"][form="'+form_id+'"]').show();
		}	
		else
		{
			$(div).hide();
			$('input[name="'+el_name+'"][form="'+form_id+'"]').hide();
		}
		
		setTotalCaseQty(me);
	}

	setTotalCaseQty = function(me, el = "")
	{

		var total_qty_received = 0;

		var form_id = $(me).attr('form');
		
		if(el == "")
		{
			var el = $(me).parents('.cases_blocks');
		}	

		if($(el).find('.card-outer-inner').length > 0)
		{	
			$(el).find('.card-outer-inner').each(function(){
				
				var outer_total = 0;

				var case_index = $(this).find('input[name^="inner_outer_case_detail"]').attr('nameattrindex')
				
				var outer_inc_count = $('input[name="inner_outer_case_detail['+case_index+'][outer][is_include_count]"][form="'+form_id+'"]:checked').val();
						
				var outer_qty_per_box = parseInt($('input[name="inner_outer_case_detail['+case_index+'][outer][qty_per_box]"][form="'+form_id+'"]').val());

				var outer_no_of_box = parseInt($('input[name="inner_outer_case_detail['+case_index+'][outer][no_of_box]"][form="'+form_id+'"]').val());

				var inner_inc_count = $('input[name="inner_outer_case_detail['+case_index+'][inner][is_include_count]"][form="'+form_id+'"]:checked').val();

				var inner_qty_per_box = parseInt($('input[name="inner_outer_case_detail['+case_index+'][inner][qty_per_box]"][form="'+form_id+'"]').val());

				var inner_no_of_box = parseInt($('input[name="inner_outer_case_detail['+case_index+'][inner][no_of_box]"][form="'+form_id+'"]').val());

				if(!isNaN(outer_qty_per_box) && !isNaN(outer_no_of_box))
		    	{
		    		if(outer_inc_count == 1)
		    		{
		    			outer_total = outer_qty_per_box * outer_no_of_box;
		    		}	

		    		if(inner_inc_count == 1 && !isNaN(inner_qty_per_box) && !isNaN(inner_no_of_box))
		    		{
		    			let inner_qty = inner_qty_per_box * inner_no_of_box;

		    			outer_total = outer_total + inner_qty;
		    		}	
		    		
		    		total_qty_received = total_qty_received + outer_total;
		    	}	
				
				$(this).find('.outer_total').text(outer_total);
			});

			$(el).find('.card-loose').each(function(){
				
				var loose_qty_per_box = 0;

				var case_index = $(this).find('input[name^="inner_outer_case_detail[loose]"]').attr('nameattrindex')

				loose_qty_per_box = parseInt($('input[name="inner_outer_case_detail[loose]['+case_index+'][qty_per_box]"][form="'+form_id+'"]').val());
				
				if(!isNaN(loose_qty_per_box))
		    	{
		    		$(this).find('.loose_total').text(loose_qty_per_box);

		    		total_qty_received = total_qty_received + loose_qty_per_box;
		    	}
		    	else
		    	{
		    		$(this).find('.loose_total').text(0);
		    	}	
			});
		}	
		
		$('input[name="qty_received"][form="'+form_id+'"]').val(total_qty_received);
		
		$('input[name="qty_received"][form="'+form_id+'"]').trigger('keyup');
	}

	setInnerOuterBarcodeDetails = function(me)
	{
		var outer_barcode = $(me).val();

		var form_id = $(me).attr('form');

		var product_id = $('input[name="product_id"][form="'+form_id+'"]').val();

		var index = $(me).attr('nameattrindex');
		
		if(outer_barcode != "" 
			&& typeof index != 'undefined' && index != ""
			&& typeof product_id != 'undefined' && product_id != ""
		 )
		{	
			$.ajax({
	                url: BASE_URL+'api-inner-outer-barcode-details',
	                type: "GET",
	                datatype:'JSON',
	                data:{
	                		'product_id':product_id,
	                		'outer_barcode':outer_barcode
	                	},
	                headers: 
	                {
	                    'Authorization': 'Bearer ' + API_TOKEN,
	                },
	                beforeSend: function () 
	                {
	                  
	                },
	                success: function (response) 
	                {
	                    if (response.status == 1) 
	                    {
	                    	if(typeof response.data.outer_details != 'undefined')
	                    	{	
		                    	if($('input[name="inner_outer_case_detail['+index+'][outer][qty_per_box]"]').val() == "")
		                    	{
		                    		$('input[name="inner_outer_case_detail['+index+'][outer][qty_per_box]"]').val(response.data.outer_details.case_quantity);
		                    	}

		                    	if($('input[name="inner_outer_case_detail['+index+'][outer][no_of_box]"]').val() == "")
		                    	{
		                    		$('input[name="inner_outer_case_detail['+index+'][outer][no_of_box]"]').val(1);
		                    	}
							}	

		                    if(typeof response.data.inner_details != 'undefined')
		                    {	
		                    	if($('input[name="inner_outer_case_detail['+index+'][inner][barcode]"]').val() == "")
		                    	{
		                    		$('input[name="inner_outer_case_detail['+index+'][inner][barcode]"]').val(response.data.inner_details.barcode);
		                    	}

		                    	if($('input[name="inner_outer_case_detail['+index+'][inner][qty_per_box]"]').val() == "")
		                    	{
		                    		$('input[name="inner_outer_case_detail['+index+'][inner][qty_per_box]"]').val(response.data.inner_details.case_quantity);
		                    	}	
		                    }	
	                		
	                		if(typeof response.data.outer_details != 'undefined')
	                    	{
		                		if(typeof response.data.outer_details.case_quantity != 'undefined')
		                		{	
		                			$('input[name="inner_outer_case_detail['+index+'][outer][qty_per_box]"]').trigger('keyup');
		                		}
		                	}	
	                	}
	                },
	                error: function (xhr, err) 
	                {
	                   
	                }
	            });
		}	
	}

	$.validator.addMethod("location_required", function(value, element, params ) {
	  	
	  	let form_id = $(element).attr('form');
	  		
	  	let is_inner_outer_case = $('input[name="is_inner_outer_case"][form="'+form_id+'"]:checked').val();

	  	let is_variants = $('input[name="is_variant"][form="'+form_id+'"]:checked').val();

	  	let response = false;

	  	if(is_inner_outer_case == 1 || is_variants == 1)
	  	{
	  		response = true;
	  	}
	  	else
	  	{
	  		if(value.length > 0)
	  		{
	  			response = true;
	  		}	
	  	}	

		return response;

	}, $.validator.format( "Required" ) );


	$.validator.addMethod("qty_required", function(value, element, params ) {
	  	
	  	let form_id = $(element).attr('form');
	  		
	  	let is_variants = $('input[name="is_variant"][form="'+form_id+'"]:checked').val();

	  	let response = false;

	  	if(is_variants == 1)
	  	{
	  		response = true;
	  	}
	  	else
	  	{
	  		if(value.length > 0)
	  		{
	  			response = true;
	  		}	
	  	}	

		return response;
	}, $.validator.format( "Required" ) );

	$.validator.addMethod("delivery_note_qty_required", function(value, element, params ) {
	  	
	  	let form_id = $(element).attr('form');
	  		
	  	let is_variants = $('input[name="is_variant"][form="'+form_id+'"]:checked').val();
	  	
	  	let consider_DNQ_parent_el = $('input[name="consider_parent_delivery_note_qty"][form="'+form_id+'"]').length;
	  	
	  	if(consider_DNQ_parent_el > 0)
	  	{	
			var consider_DNQ_parent_status = $('input[name="consider_parent_delivery_note_qty"][form="'+form_id+'"]').is(':checked');
			
			if(consider_DNQ_parent_status == true)
			{	
				if(value.length > 0)
		  		{
		  			return true;
		  		}
		  		else
		  		{
		  			return false;
		  		}
		  	}
		  	else
		  	{
		  		return true;	
		  	}
		}
		else if(form_id.indexOf("var_") >= 0)
		{
			let booking_po_product_id = $('input[name="booking_po_product_id"][form="'+form_id+'"]').val();

			var parent_form_id = form_id.replace("var_"+booking_po_product_id+'_', "");

			if(typeof parent_form_id != 'undefined')
		  	{	
		  		let consider_DNQ_parent_el = $('input[name="consider_parent_delivery_note_qty"][form="'+parent_form_id+'"]').length;
  	
			  	if(consider_DNQ_parent_el > 0)
			  	{	
			  		var consider_DNQ_parent_status	 = $('input[name="consider_parent_delivery_note_qty"][form="'+parent_form_id+'"]').is(':checked');
					
					if(consider_DNQ_parent_status == false)
					{
						if(value.length > 0)
				  		{
				  			return true;
				  		}
				  		else
				  		{
				  			return false;
				  		}	
				  	}
				  	else
				  	{
				  		return true;
				  	}	
				}
				else{
					if(value.length > 0)
			  		{
			  			return true;
			  		}
			  		else
			  		{
			  			return false;
			  		}
				}
			}
		}	
		else if(is_variants == 1)
		{
			return true;
		}	
		else
		{
			if(value.length > 0)
	  		{
	  			return true;
	  		}
	  		else
	  		{
	  			return false;
	  		}
		}	
			
	}, $.validator.format( "Required" ) );

	$.validator.addMethod("check_atleast_one", function(value, element, params ) {
	  	return $(element+':checked').length > 0;
	}, $.validator.format( "Required" ) );

	setValidation = function(el, form_id)
	{
		$(el).validate({
			focusInvalid: true,
	        invalidHandler: function(form, validator) {
	            if (!validator.numberOfInvalids())
	                return;
	            $('html, body').animate({
	                scrollTop: $(validator.errorList[0].element).offset().top-30
	            }, 1000);
	        },
	        errorElement: 'p',
	        errorClass: 'invalid-feedback', // default input error message class
	        rules: {
	        	"barcode":{
	                required: true,
	            },
	        	"delivery_note_qty":{
	                delivery_note_qty_required: true,
	            	digits: true,
	            },
	            "qty_received":{
	                qty_required : true,
	                digits: true,
	            },
	            // "location":{
	            //     location_required: true,
	            // },
	            "variation_theme":{
	                required: true,
	            },
	            "var_sku_id[]" :{
	            	check_atleast_one : true,
	            },
	        },
	        messages:{
	        	delivery_note_qty: {
	                required: "Required",
	                digits : "Only digits allowed",
	            },
	            qty_received: {
	                required: "Required",
	                digits : "Only digits allowed",
	            },
	            // location_required: {
	            //     required: "Required",
	            // },
	        },
	        errorPlacement: function (error, element) {
	            
	            if(element.hasClass('required') || element.hasClass('required_digit'))
	        	{
	        		if(element.parents('.cases_blocks').find('.invalid-feedback').length > 0)
	                {
	                	element.parents('.cases_blocks').find('.invalid-feedback').text('All fields are required with valid input type.');
	                }   
	                else
	                {

	                    error.insertBefore(element.parents('.card-outer-inner'));
	                } 
	        	}
	        	else
	        	{
	        		error.insertAfter(element);
	        	}	
	        },
	        highlight: function (element) { 
	            $(element).addClass('validation-error');
	        },
	        success: function (label, input) {
	            label.remove();
	            $(input).removeClass('validation-error');
	        },
		});

		// $('.required_digit[form="'+form_id+'"]').each(function(){
		// 	$(this).rules("add", { 
		// 	  	required: true,
  //               digits: true,
  //               min: 1,
		// 	});
		// });

		// $('.required_mr[form="'+form_id+'"]').each(function(){
		// 	$(this).rules("add", { 
		// 	  	required: true,
  //           });
		// });
	}

	$('body').on('submit', 'form', function(e){
			
		e.preventDefault();

		if($(this).hasClass('material-receipt-save-product'))
		{	
			var form_id = $(this).attr('id');
			
			setValidation(this, form_id);
		}	

		if($(this).valid())
        {
        	var set_varient_alert = false;

        	if($('input[name="is_variant"][form="'+form_id+'"]:checked').val() == 1)
        	{
	        	if($('#manageVariation').hasClass('show'))
	        	{
	        		if($('input[name^="var_sku_id"][form="'+form_id+'"]:checked').length <= 0 
	        		)
	        		{
	        			set_varient_alert = true;	
	        		}	
	        	}
	        	else if($('input[name="variation_selected"][form="'+form_id+'"]').val() != 1)
	        	{
	        		set_varient_alert = true;	
	        	}	
	        }	

        	if(set_varient_alert == true)
        	{
        		bootbox.alert({
		            title: "Alert",
		            message: "Please select atleast one variation to proceed with this product.",
		            size: 'small'
		        });

		        return false;
        	}	
        	else
        	{
	        	var dataString = new FormData($(this)[0]);

	        	$('button[type="submit"][form="'+form_id+'"]').attr('disabled', true);
	        	
	        	$.ajax({
	                type: "POST",
	                url: $(this).attr("action"),
	                data: dataString,
	                datatype: 'JSON',
	                processData: false,
	                contentType: false,
	                cache: false,
	                headers: {
	                    'Authorization': 'Bearer ' + API_TOKEN,
	                },
	                beforeSend: function () {
	                    $('button[type="submit"][form="'+form_id+'"]').attr('disabled', true);
	                    $("#page-loader").show();
	                },
	                complete: function (data) {
				      $('button[type="submit"][form="'+form_id+'"]').attr('disabled', false);
	                  $("#page-loader").hide();
				    },
	                success: function (response) {
	                	if (response.status_code == 200) 
	                    {
	                    	PoundShopApp.commonClass._displaySuccessMessage(response.message);
	                    	$('#manageVariation').html('');
	                    	$('#manageVariation').modal('hide');
	                    	page_update();
	                    	//getQuarntinLocationProducts();
	                    }	
	                },
	                error: function (xhr, err) {
	                  	
	                    if($('input[name="booking_po_product_id"][form='+form_id+']').val() == "")
	                    {	
		                  
		                  	var errorResp = JSON.parse(xhr.responseText);
						  	
					  		if(typeof errorResp.data.booking_po_product_id !== 'undefined')
		                	{
			                  	$('input[name="booking_po_product_id"][form='+form_id+']').val(errorResp.data.booking_po_product_id);
		                	}	
			              
			            }   
	                  
	                  $('button[type="submit"][form="'+form_id+'"]').attr('disabled', false);
	                  $("#page-loader").hide();
	                  PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
	                }
	            });	
	        }    
        } 
	});
	
	// start- get quarantin location product after save//
	bindProductQCDropDownData=function(data,selectedProductArr){
		var dropDownHtml='';
		for(var i=0;i<data.length;i++)
		{
			var selectedProduct="";
			if(jQuery.inArray(data[i].id, selectedProductArr) !== -1)
			{
				selectedProduct="selected=selected";
			}
			dropDownHtml+="<option value='"+data[i].id+"' "+selectedProduct+">"+data[i].title+"</option>";
		}
		$('body #product_qc').html(dropDownHtml);
		$(document).find("#product_qc").selectpicker("refresh");
		$('body #product_qc').trigger('change');
	}

	getQuarntinLocationProducts= function(){
		var booking_id = $('#booking_id').val();
		$.ajax({
	            type: "POST",
	            url: BASE_URL+'api-quarantin-location-products',
	            datatype: 'JSON',
	            data:{
	            	booking_id : booking_id
	            },
	            headers: {
	                'Authorization': 'Bearer ' + API_TOKEN,
	            },
	            success: function (response) {
	            	if(response.status_code==200)
	            	{
	            		bindProductQCDropDownData(response.data.productList,response.data.selectedProductQc);
	            	}
	            }
	        });
	}

	// end- get quarantin location product after save//

	get_booking_details = function()
	{
		var pendingCountLabel = $('#pendingCountLabel');
		var booking_id = $('#booking_id').val();
		
		if(booking_id != "")
		{	
			$.ajax({
	            type: "POST",
	            url: BASE_URL+'api-booking-details',
	            datatype: 'JSON',
	            data:{
	            	id : booking_id
	            },
	            headers: {
	                'Authorization': 'Bearer ' + API_TOKEN,
	            },
	            beforeSend: function () {
	            	$(pendingCountLabel).text('Loading...');  
	            },
	            complete: function (data) {
			      
			    },
	            success: function (response) {
	            	if (response.status_code == 200) 
	                {
	                	if(typeof response.data.total_completed_products != 'undefined' && typeof response.data.total_products)
	                	{
	                		var pending_products = 0;
	                		var total_products = parseInt(response.data.total_products);
	                		
	                		var total_completed_products = parseInt(response.data.total_completed_products);
	                		
	                		if(!isNaN(total_products) && !isNaN(total_completed_products))
	                		{
	                			pending_products = total_products - total_completed_products;
	                			
	                			pending_products = pending_products < 0 ? 0 : pending_products;
	                		}	
	                	}	
	                	
	                	$(pendingCountLabel).text(pending_products);
	                }	
	                else{
	                	$(pendingCountLabel).text('-');
	                }
	            },
	            error: function (xhr, err) {
	              $(pendingCountLabel).text('-');
	            }
	        });	
	    }
	    else
	    {
	    	$(pendingCountLabel).text('-');
	    }    
	}	

	setBookingCompleted = function(me)
	{
		$.ajax({
                url: BASE_URL+'api-set-booking-completed',
                type: "POST",
                data: {
                	"booking_id" : $('input[name="booking_id"]').val(),	
                },
                datatype: 'JSON',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                complete: function (data) {
			      $("#page-loader").hide();
			    },
                success: function (response) {
                	if (response.status_code == 200) 
                    {
                    	PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    	location.reload(true);
                    }	
				},
                error: function (xhr, err) {
                  $("#page-loader").hide();
                  PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });	
	}

	setArrivedDate = function(me)
	{
		$.ajax({
                url: BASE_URL+'api-set-booking-arrived-date',
                type: "POST",
                data: {
                	"booking_id" : $('input[name="booking_id"]').val(),	
                	"arrived_date" : $(me).val(),
                },
                datatype: 'JSON',
                headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
                beforeSend: function () {
                    $("#page-loader").show();
                },
                complete: function (data) {
			      $("#page-loader").hide();
			    },
                success: function (response) {
                	if (response.status_code == 200) 
                    {
                    	PoundShopApp.commonClass._displaySuccessMessage(response.message);
                    }	
                },
                error: function (xhr, err) {
                  $("#page-loader").hide();
                  PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
                }
            });	
	}

	initialize_datepicker = function()
	{
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
	}

	manageVariants = function(me, product_id)
	{
		var form_id = $(me).attr('form');
		
		if(typeof form_id != 'undefined')
		{	
			setValidation('form#'+form_id, form_id);

			if($('form#'+form_id).valid())
			{
				var booking_id = $('#booking_id').val();
				
				var booking_po_product_id = $('input[name="booking_po_product_id"][form="'+form_id+'"]').val();

				if(product_id != "" && booking_id != "" && typeof booking_po_product_id != "undefined")
				{
					$.ajax({
		                url: WEB_BASE_URL+'/material-receipt/ajax-manage-variations',
		                type: "POST",
		                data: {
		                	"product_id" : product_id,
		                	"booking_id" : booking_id,
		                	"form_id" : form_id,
		                	"booking_po_product_id" : booking_po_product_id,
		                },
		                datatype: 'HTML',
		                headers: {
		                    'Authorization': 'Bearer ' + API_TOKEN,
		                },
		                beforeSend: function () {
		                    $("#page-loader").show();
		                },
		                complete: function (data) {
					      $("#page-loader").hide();
					    },
		                success: function (response) {
		                	$('#manageVariation').html(response);
		                	$('#manageVariation').modal('show');
		                	variationDocLoadUpdate();
		                },
		                error: function (xhr, err) {
		                  $("#page-loader").hide();
		                  PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
		                }
		            });		
				}
			}
		}			
	}

	// set_location_details = function (me)
	// {
	// 	var warehouse_id = $('#warehouse_id').val();

	// 	$(me).next('span.location_type').remove();

	// 	if($(me).val() != "" && warehouse_id.length > 0)
	// 	{
	// 		$.ajax({
 //                url: BASE_URL+'api-location-by-keyword',
 //                type: "GET",
 //                data: {
 //                	"keyword" : $(me).val(),
 //                	"warehouse_id" : warehouse_id,
 //                },
 //                datatype: 'JSON',
 //                headers: {
 //                    'Authorization': 'Bearer ' + API_TOKEN,
 //                },
 //                beforeSend: function () {
                    
 //                },
 //                success: function (response) {
 //                	if(response.status == 1)
 //                	{
 //                		if(typeof response.data.type_of_location != 'undefined')
 //                		{	
 //                			html = '<span class="location_type font-10-dark bold d-block mt-1">'+response.data.type_of_location+'</span>';

 //                			$(me).after(html);
 //                		}
 //                	}
 //                	else
 //                	{
 //                		$(me).val('');
 //                	}
 //                },
 //                error: function (xhr, err) {
 //                  $(me).val('');
 //                  $("#page-loader").hide();
 //                  PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
 //                }
 //            });	
	// 	}	
	// }

	newProductReturnToSupplier = function (me)
	{
		if($.trim($('#txt_search').val()) == "")
		{
			bootbox.alert({
	            title: "Alert",
	            message: "Please scan or search barcode first.",
	            size: 'small'
	        });
	        return false;
		}	
		else
		{	
			bootbox.confirm({ 
	            title: "Confirm",
	            message: "Are you sure you want to add new product to this booking? This process cannot be undone.",
	            buttons: {
	                cancel: {
	                    label: 'Cancel',
	                    className: 'btn-gray'
	                },
	                confirm: {
	                    label: 'Add Product',
	                    className: 'btn-blue'
	                }
	            },
	            callback: function (result) 
	            {
	                if(result==true)
	                {
						$.ajax({
				                url: BASE_URL+'api-material-receipt-save-product-for-return-to-supplier',
				                type: "POST",
				                data: {
				                	'booking_id':$('#booking_id').val(),
				                	'barcode':$.trim($('#txt_search').val()),
				                },
				                datatype: 'JSON',
				                headers: {
				                    'Authorization': 'Bearer ' + API_TOKEN,
				                },
				                beforeSend: function () {
				                  $("#page-loader").show();  
				                },
				                success: function (response) {
				                	$("#page-loader").hide();
				                	if(response.status == 1)
				                	{	
					                	PoundShopApp.commonClass._displaySuccessMessage(response.message);
					                	page_update();	
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
	}

	$.fn.extend({
	    donetyping: function(callback,timeout){
	        timeout = timeout || 200; // 1 second default timeout
	        var timeoutReference,
	            doneTyping = function(el){
	                if (!timeoutReference) return;
	                timeoutReference = null;
	                callback.call(el);
	            };
	        return this.each(function(i,el){
	            var $el = $(el);
	            // Chrome Fix (Use keyup over keypress to detect backspace)
	            // thank you @palerdot
	            $el.is(':input') && $el.on('keyup keypress paste',function(e){
	                // This catches the backspace button in chrome, but also prevents
	                // the event from triggering too preemptively. Without this line,
	                // using tab/shift+tab will make the focused element fire the callback.
	                if (e.type=='keyup' && e.keyCode!=8) return;

	                // Check if timeout has been set. If it has, "reset" the clock and
	                // start over again.
	                if (timeoutReference) clearTimeout(timeoutReference);
	                timeoutReference = setTimeout(function(){
	                    // if we made it here, our timeout has elapsed. Fire the
	                    // callback
	                    doneTyping(el);
	                }, timeout);
	            }).on('blur',function(){
	                // If we can, fire the event since we're leaving the field
	                doneTyping(el);
	            });
	        });
	    }
	}); 

	printBarcode = function(me)
	{
		var barcode = $(me).parent('div').find('.input_barcode').val();
		
		if(barcode != "")
		{	
			var count = 1;
			var left  = ($(window).width()/2)-(900/2);
	    	var top   = ($(window).height()/2)-(600/2);
			var popup = window.open(WEB_BASE_URL+'/print-product-barcodes?barcode='+barcode+'&count='+count,"popupWindow", "width=900, height=600, scrollbars=yes,top="+top+", left="+left);
		}
		else
		{
			bootbox.alert({
	            title: "Alert",
	            message: "Please enter barcode.",
	            size: 'small'
	        });

	        return false;
		}
	}	

	setDNQtyParent = function (me)
	{
		var alert_msg = "Make sure you have saved you all your changes.";

		if($(me).prop('checked'))
		{
			var previous_state = false;

			var consider_parent_delivery_note_qty = 1;
		}	
		else
		{
			var previous_state = true;
			
			var consider_parent_delivery_note_qty = 0;
		}

		bootbox.confirm({ 
	            title: "Confirm",
	            message: alert_msg,
	            buttons: {
	                cancel: {
	                    label: 'Cancel',
	                    className: 'btn-gray'
	                },
	                confirm: {
	                    label: 'Continue',
	                    className: 'btn-blue'
	                }
	            },
	            callback: function (result) 
	            {
	                if(result==true)
	                {
	                	var form_id = $(me).attr('form');
	                	var booking_po_product_id = $('input[name="booking_po_product_id"][form="'+form_id+'"]').val();
	                	$.ajax({
			                url: BASE_URL+'api-material-receipt-set-parent-product-delivery-note-qty',
			                type: "POST",
			                data: {
			                	'booking_po_product_id': booking_po_product_id,
			                	'consider_parent_delivery_note_qty': consider_parent_delivery_note_qty,
			                },
			                datatype: 'JSON',
			                headers: {
			                    'Authorization': 'Bearer ' + API_TOKEN,
			                },
			                beforeSend: function () {
			                  $("#page-loader").show();  
			                },
			                success: function (response) {
			                	$("#page-loader").hide();
			                	if(response.status == 1)
			                	{	
				                	PoundShopApp.commonClass._displaySuccessMessage(response.message);
				                	page_update();	
			                	}
			                },
			                error: function (xhr, err) {
			                  $("#page-loader").hide();
			                  PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
			                }
			            });	
	                }
	                else
	                {
	                	$(me).prop('checked', previous_state);
	                }
	            }
	    });            	
	}

	removeReturnToSupplierProduct = function (me)
	{
		var form_id = $(me).attr('form');
		
		var booking_po_product_id = $('input[name="booking_po_product_id"][form="'+form_id+'"]').val();
		
		if(typeof booking_po_product_id != 'undefined' && booking_po_product_id != "")
		{	
			bootbox.confirm({ 
	            title: "Confirm",
	            message: "Are you sure you want to remove this product from booking? This process cannot be undone.",
	            buttons: {
	                cancel: {
	                    label: 'Cancel',
	                    className: 'btn-gray'
	                },
	                confirm: {
	                    label: 'Remove Product',
	                    className: 'btn-red'
	                }
	            },
	            callback: function (result) 
	            {
	                if(result==true)
	                {
						$.ajax({
				                url: BASE_URL+'api-material-receipt-remove-product-from-booking',
				                type: "POST",
				                data: {
				                	'booking_po_product_id':booking_po_product_id,
				                },
				                datatype: 'JSON',
				                headers: {
				                    'Authorization': 'Bearer ' + API_TOKEN,
				                },
				                beforeSend: function () {
				                  $("#page-loader").show();  
				                },
				                success: function (response) {
				                	$("#page-loader").hide();
				                	if(response.status == 1)
				                	{	
					                	PoundShopApp.commonClass._displaySuccessMessage(response.message);
					                	page_update();	
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
	            message: "Invalid product for delete.",
	            size: 'small'
	        });

	        return false;
		}	
	}

	//Search outside the datatables
    $('.cancle_fil').click(function()
    {
        $('.filter_count').html('');
        
        $('#custom_advance_search_fields').find('input,select,textarea').each(function()
        {
        	if(typeof $(this).val() != undefined && !$(this).hasClass('clear_except'))
            {
                if($(this).attr('type') == 'checkbox')
                {
                    $(this).prop('checked', false); 
                }
                else if($(this).attr('type') == 'radio')
                {
                    $(this).prop('checked', false); 
                }
                else if($(this).attr('type') == 'button')
                {

                }    
                else
                {
                    $(this).val('');    
                }    
            }    
        });
        $('#btnFilter').removeClass('open');
        $('.search-filter-dropdown').removeClass('open'); 
        $('.card-flex-container').removeClass('filter-open'); 
        $('#txt_search').val('');
        page_update();
    });

    checkPutWayStart = function(form_id)
    {
    	putaway_start = false;

    	var booking_po_product_id = $('input[name="booking_po_product_id"][form="'+form_id+'"]').val();

    	if(booking_po_product_id.length > 0)
    	{
    		$.ajax({
                    url: BASE_URL+'api-material-receipt-check-putaway-start',
                    type: "POST",
                    datatype:'JSON',
                    data:{'booking_po_product_id':booking_po_product_id},
                    async:false,
                    headers: 
                    {
                        'Authorization': 'Bearer ' + API_TOKEN,
                    },
                    success: function (response) 
                    {
                        if (response.status == 1) 
	                    {
	                    	if(typeof response.data.putaway_start !== 'undefined')
	                    	{	
	                    		putaway_start = response.data.putaway_start;
	                    	}
	                    	else
	                    	{
	                    		putaway_start = true;
	                    	}
	                    }
	                    else
	                    {
	                    	putaway_start = true;

	                    	PoundShopApp.commonClass._displayErrorMessage('Something went wrong.');
	                    }
                    },
                    error: function (xhr, err) 
                    {
                       
                    }
                })
    		.always(function(){
    			$("#page-loader").hide();
    		});
    	}
    	else
    	{
    		$("#page-loader").hide();
    	}	

    	return putaway_start;
    }

    sendValidationText = function(type)
    {
    	if(type == 'best_before_date')
    	{
    		PoundShopApp.commonClass._displayErrorMessage("You cannot set Best Before Date, once putaway is started.");
    	}

    	if(type == 'cases_and_barcodes')
    	{
    		PoundShopApp.commonClass._displayErrorMessage("You cannot change state of cases and barcode, onces it is added.");
    	}	

    	if(type == 'variants')
    	{
    		PoundShopApp.commonClass._displayErrorMessage("You cannot change state of variant, onces it is added.");
    	}	
    }

	window.PoundShopApp = window.PoundShopApp || {}
    window.PoundShopApp.poundShopMaterialReceipt = new poundShopMaterialReceipt();

})(jQuery);    

 

$(".po_item_master").click(function () {
    $("input[name='descri[]']").prop('checked', $(this).prop('checked'));
});

function send_email(me)
{
	var booking_id=$('#booking_id').val();
    bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure, you want to send Material Receipt to supplier ? This process cannot be undone.",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Send',
                className: 'btn-blue'
            }
        },
        callback: function (result) 
        {
            if(result==true)
            {
                $.ajax({
                    url: BASE_URL+'api-material-receipt-supplier-email',
                    type: "POST",
                    datatype:'JSON',
                    data:{'id':booking_id},
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
                        if (response.status == 1) {
                            PoundShopApp.commonClass._displaySuccessMessage(response.message);
                            PoundShopApp.commonClass.table.draw();
                        }
                    },
                    error: function (xhr, err) 
                    {
                       $("#page-loader").hide();
                    }
                });
            }
        }
    });     
}

// function change_status_disc(statustype)
// {
//     var descval = [];  
//     $("input[name='descri[]']:checked").each(function() {  
//         descval.push($(this).attr('value'));
//     });

//     var button_title='Debit Note';
//     if(statustype=='2')
//     {
//         button_title='Keep it';
//     }
//     else if(statustype=='3')
//     {
//         button_title='Dispose of';
//     }
//     else if(statustype=='4  ')
//     {
//         button_title='Return to Supplier';
//     }

    
//     if (typeof descval !== 'undefined' && descval.length > 0) 
//     {
//         bootbox.confirm({ 
//             title: "Confirm",
//             message: "Are you sure you want to do action on selected records? This process cannot be undone.",
//             buttons: {
//                 cancel: {
//                     label: 'Cancel',
//                     className: 'btn-gray'
//                 },
//                 confirm: {
//                     label: button_title,
//                     className: 'btn-blue'
//                 }
//             },
//             callback: function (result) 
//             {
//                 if(result==true)
//                 {
//                     var join_selected_values = descval.join(","); 
//                     $.ajax({
//                         url: BASE_URL + 'api-material-action-multiple',
//                         type: "post",
//                         //processData: false,
//                         data: {'ids':join_selected_values,'status':statustype},
//                         headers: {
//                             'Authorization': 'Bearer ' + API_TOKEN,
//                         },
//                         beforeSend: function () {
//                             $("#page-loader").show();
//                         },
//                         success: function (response) 
//                         {
//                             $("#page-loader").hide();
//                             if (response.status == 1) 
//                             {
//                                 PoundShopApp.commonClass._displaySuccessMessage(response.message);
//                                 $(".po_item_master").prop( "checked", false );
//                                 $(".po_item_master").prop("indeterminate", false);                                
//                                 $('.desc_checkbox').prop( "checked", false );
//                                 var product_id=$('div.descripencies-list').closest('tr').find('.product_id_class').val();
//                                	$('.form_detail_'+product_id).click(); 	
//                             }
//                         },
//                         error: function (xhr, err) {
//                             $("#page-loader").hide();
//                             PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
//                         }

//                     });
//                 }
//             }
//         });
//      }
//     else
//     {
//         bootbox.alert({
//             title: "Alert",
//             message: "Please select atleast one descripencies.",
//             size: 'small'
//         });
//         return false;
//     }
// }

$('#item-modal').on('hidden.bs.modal', function () {
  	if($('#descripency_reload_table').val() == 1)
  	{	
  		page_update();
  		$('#descripency_reload_table').val(0);
  	}
});

function show_discrepancy(booking_po_product_id,product_id)
{
    $('#add_desc_counter').val("0");
    $('#item-modal').modal('show');
    if(booking_po_product_id != '')
    {
        $('.btn-blue').attr('disabled', true);
        $.ajax({
            type: "POST",
            url: BASE_URL + 'api-view-descrepancy',
            data: {'booking_po_product_id':booking_po_product_id,'product_id':product_id},
            //processData: false,
            headers: {
                'Authorization': 'Bearer ' + API_TOKEN,
            },
            beforeSend: function () {
              $("#page-loader").show();
            },
            success: function (data) 
            {
                $('.btn-blue').attr('disabled', false);
                $('#desc_booking_po_product_id').val(booking_po_product_id);
                $('#desc_product_id').val(product_id);
                $("#discrepancy_table tbody").html('');
                $("#discrepancy_table tbody").append(data);
                $('#item-modal').modal('show');
                $("#page-loader").hide();                
                var ret = BASE_URL.replace('api/','js/custom-file-input.js');
                $.getScript(ret, function() {			        
			    });

            },
            error: function (xhr, err) 
            {
                $('.btn-blue').attr('disabled', false);
                PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
            }
        });
    }
    else
    {
        $('.btn-blue').attr('disabled', false);
        $('#item-modal').modal('show');
    }
}

function storedisc()
{
    //var dataString = $("#add_descrepancy").serialize();    
    var form = $("form#add_descrepancy")[0];
    var dataString = new FormData(form);
    var overall_qty=0;

	$('.desc_itm_qty').each(function()
    {
        overall_qty += +$(this).val();    
	});	
	var product_id=$('#desc_product_id').val();
	var desc_max_qty=$('.qty_diff_class_'+product_id).val();
	
	desc_max_qty=Math.abs(desc_max_qty)

	var exist_text=$('.numeric_only').length;
	
	$('.btn-blue').attr('disabled', true);
    
    $.ajax({
        type: "POST",
        url: BASE_URL + 'api-store-descrepancy',
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
        success: function (response) 
        {
            $('.btn-blue').attr('disabled', false);
            $("#page-loader").hide();
            if (response.status == 1) 
            {    
                $('#item-modal').modal('hide');  
                page_update();
            }
        },
        error: function (xhr, err) 
        {
            $('.btn-blue').attr('disabled', false);
            PoundShopApp.commonClass._commonFormErrorShow(xhr, err);
        }
    });
	   
}

function add_new_desc()
{
    var newcounter=parseInt($('#add_desc_counter').val());
    newcounter++;
    $.ajax({
        url: BASE_URL + 'api-add-descrepancy',
        type: 'POST',
        data: { 
            'ids':newcounter        
        },
        headers: 
        {
            'Authorization': 'Bearer ' + API_TOKEN,
        },
        beforeSend: function()
        {
            $("#page-loader").show();   
        },      
        complete:function()
        {
            $("#page-loader").hide();
        },
        success: function(data)
        {
            $("#discrepancy_table tbody").append(data);
            $('#add_desc_counter').val(newcounter);
            var ret = BASE_URL.replace('api/','js/custom-file-input.js');
            	$.getScript(ret, function() {			        
		    });
        },
    });

    return false;
}

function delete_image_desc(parent_id,desc_image_id)
{
	bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to delete this image ? This process cannot be undone.",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Ok',
                className: 'btn-blue'
            }
        },
        callback: function (result) 
        {
            if(result==true)
            {
				$.ajax({
                    url: BASE_URL+'api-desc-image-delete',
                    type: "POST",
                    datatype:'JSON',
                    data:{'id':desc_image_id},
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
                            $('.desc_img_cl_'+desc_image_id).remove();                            
                            if ($(".desc_parent_class_"+parent_id).length > 0) 
                            {
                            }
                            else
                            {
                            	$('.desc_imag_main_'+parent_id).remove();
                            }

                        }
                    },
                    error: function (xhr, err) 
                    {
                       $("#page-loader").hide();
                    }
                });
			}
        }
    }); 	
}	


$("body").on("keydown", ".numeric_only", function (e) 
{
    var key = e.charCode || e.keyCode || 0;    
    return (
        key == 8 || 
        key == 9 ||
        key == 13 ||
        key == 46 ||
        (key >= 35 && key <= 40) ||
        (key >= 48 && key <= 57) ||
        (key >= 96 && key <= 105));
});  

function added_desc_tr_delete(delete_tr)
{
	bootbox.confirm({ 
	    title: "Confirm",
	    message: "Are you sure you want to delete descripency? This process cannot be undone.",
	    buttons: {
	        cancel: {
	            label: 'Cancel',
	            className: 'btn-gray'
	        },
	        confirm: {
	            label: 'Delete',
	            className: 'btn-blue'
	        }
	    },
	    callback: function (result) 
	    {
	        if(result==true)
	        {
	    		$('.add_desc_tr_'+delete_tr).remove();
	    	}
	    }
	});    		
}

function updated_desc_tr_delete(delete_tr)
{
	var booking_po_product_id = $('#desc_booking_po_product_id').val();

    bootbox.confirm({ 
        title: "Confirm",
        message: "Are you sure you want to delete descripency? This process cannot be undone.",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Delete',
                className: 'btn-blue'
            }
        },
        callback: function (result) 
        {
            if(result==true)
            {
            	$.ajax({
	                url: BASE_URL+'api-descrepancy-delete',
	                type: "POST",
	                data: {
	                	'booking_po_product_id': booking_po_product_id,
	                	'delete_id': delete_tr,
	                },
	                datatype: 'JSON',
	                headers: {
	                    'Authorization': 'Bearer ' + API_TOKEN,
	                },
	                beforeSend: function () {
	                  $("#page-loader").show();  
	                },
	                success: function (response) {
	                	$("#page-loader").hide();
	                	
	                	if(response.status == true)
	                	{
	                		$('#descripency_reload_table').val(1);
	                	}	
	                },
	                error: function (xhr, err) {
	                  $("#page-loader").hide();
	                  PoundShopApp.commonClass._displayErrorMessage('Something went wrong.');
	                }
	            });	

                $('.update_desc_tr_'+delete_tr).remove();
            }
        }
    });    
}  

$("body").on("click",'.po_item_master',function(e)
{ 
	//$(".master").click(function () {
    $("input[name='descri[]']").prop('checked', $(this).prop('checked'));
});


//for checkbox all and none case
// function updateDataTableSelectAllCtrl(table)
// {
//     var $table             = table[0];    
//     var $chkbox_all        = $('tbody input[type="checkbox"]', $table);
//     var $chkbox_checked    = $('tbody input[type="checkbox"]:checked', $table);
//     //var chkbox_select_all  = $('thead input[name="ids[]"]', $table).get(0);
//     var chkbox_select_all= $('.po_item_master').get(0);

//     // If none of the checkboxes are checked
//     if($chkbox_checked.length === 0)
//     {
//         chkbox_select_all.checked = false;
//         if('indeterminate' in chkbox_select_all){
//             chkbox_select_all.indeterminate = false;
//         }

//         // If all of the checkboxes are checked
//     } 
//     else if ($chkbox_checked.length === $chkbox_all.length)
//     {
//         chkbox_select_all.checked = true;
//         if('indeterminate' in chkbox_select_all)
//         {
//             chkbox_select_all.indeterminate = false;
//         }

//         // If some of the checkboxes are checked
//     } 
//     else 
//     {
//          if('indeterminate' in chkbox_select_all)
//         {
//             chkbox_select_all.checked = false;   
//             chkbox_select_all.indeterminate = true;
//         }
//     }
// }

// $(document).ready(function ()
// {   
//     var rows_selected = [];   
//     var table = $('.product_list_table'); 
// 	$("body").on("click",'.desc_checkbox',function(e)
// 	{
// 		updateDataTableSelectAllCtrl(table);        
//         e.stopPropagation();
// 	});
// });

$.fn.filter_input_not_blank = function()
{   
      return $(this).map(function()
      {
          if($.trim($(this).val())!="")
          {
             return this; 
          }
          
      });
};

$.fn.same_level_children = function()
{   
    return jQuery.merge($(this),$(this).siblings());
};  


var variation_theme_change = function (current_object) 
{   
    hide_variations();
    
    if ($(current_object).val() != "") 
    {
        if ($("table tbody tr.variation-row-template").nextAll("tr").length > 0) 
        {
            bootbox.confirm({
                size: "small",
                title: '<i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Warning',
                message: "Changes in variation will delete all existing variation product.Continue anyway?",
                callback: function (result) {
                    if (result) {
                        $("table tbody tr.variation-row-template").nextAll("tr").each(function () {
                            $("table tbody tr.variation-row-template").nextAll("tr").find("input.child-checkbox").prop("checked", true);
                            
                            remove_variation();
                            
                            show_variations(current_object);

                            variation_column_hide_show(1, "hide");
                            
                            variation_column_hide_show(2, "hide");
                        });

                    }
                    else 
                    {
                        if ($("select[name=variation_theme]").data("last_value") != 'undefined') 
                        {
                            $("select[name=variation_theme]").val($("select[name=variation_theme]").data("last_value"));
                        }

                        if($("select[name=variation_theme]").val().length > 0)
                        {    
                            show_variations(current_object);

                            variation_add_edit();
                        }
                    }
                }
            });
        }
        else 
        {
            show_variations(current_object);
        }       
    }
}

function hide_variations()
{
    $('#make_variations').hide();

    $("#theme_2_div").hide();

    $("#theme_1_div").hide();
}

function show_variations(current_object)
{
    $("select[name^=variation_theme]").data("last_value", $("select[name^=variation_theme]").val())
    
    variation_add_edit('show');

    option = $(current_object).find("option:selected");

    if (option.length > 0) 
    {
        var theme_1 = $(option).attr("theme_1");
        
        var theme_2 = $(option).attr("theme_2");

        if (theme_1.length > 0) 
        {
            $(current_object).attr("value", "size");

            $(".theme_1_label").html(theme_1);

            $("table th.variation-size-header span").html(theme_1);

            $("#theme_1_div").show();
        }

        if (theme_2.length > 0) {
            $(current_object).attr("value", "size-color");

            $(".theme_2_label").html(theme_2);
            
            $("table th.variation-color-header span").html(theme_2);
            
            $("#theme_2_div").show();
        }

        $('#make_variations').show();
    }
    else
    {
        hide_variations();
    }   
}

$.fn.variation_add = function () {
    strlength = $.trim($(this).val()).length;
    if ($(this).parent().nextAll("div").length == 0 && $.trim($(this).val()) != "") 
    {
        clone_input = $(this).parent().clone();
        $(clone_input).find("input").val("");
        $(this).parent().after(clone_input);
    }
};

$('body').on('click', '.btn-add-variation', function (e) {
    variation_type = $("select[name=variation_theme]").attr("value");

    option = $("select[name=variation_theme]").find("option:selected");
    
    var theme_1 = $(option).attr("theme_1");
    
    var theme_2 = $(option).attr("theme_2");

    variation_box_count = 0;

    if (variation_type == "size") {
        variation_box_count = $("input.size-init-input-box").filter_input_not_blank().length
    }
    else if (variation_type == "color") {
        variation_box_count = $("input.color-init-input-box").filter_input_not_blank().length
    }
    else if (variation_type == "size-color") {

        variation_box_count = $("input.size-init-input-box").filter_input_not_blank().length
        
        if ($("input.color-init-input-box").filter_input_not_blank().length < variation_box_count) 
        {
            variation_box_count = $("input.color-init-input-box").filter_input_not_blank().length;
        }
    }

    var theme_array = [];

    size = $("input.size-init-input-box:visible").filter_input_not_blank();

    color = $("input.color-init-input-box:visible").filter_input_not_blank();

    color_index = [];
    
    if (variation_box_count > 0) {
        if (variation_type == "size") {
            if (size.length > 0) {
                size.each(function () {
                    size_value = $(this).val();
                    theme_array.push({size: size_value});
                });
            }
        }
        else if (variation_type == "color") {
            if (color.length > 0) {
                color.each(function () {
                    color_value = $(this).val();
                    theme_array.push({color: color_value});
                });
            }
        }
        else if (variation_type == "size-color") {
            if (size.length > 0 && color.length > 0) {
                color.each(function () {
                    color_value = $(this).val();
                    size.each(function (index) {
                        size_value = $(this).val();
                        theme_array.push({size: size_value, color: color_value});
                    });
                });
            }

        }
    }

    if (variation_box_count != null && variation_box_count > 0 && theme_array.length > 0) {

        for (var i = 0; i < theme_array.length; i++) 
        {
            var var_row_clone = $("tr.variation-row-template:first").clone().removeClass("display-none").removeClass("variation-row-template");

            $(var_row_clone).find("input,select").prop("disabled", false);

            var variation_title = $('#manageVariationTitle').val().trim();

            if (variation_type == "size") {
                $(var_row_clone).find("input[name^=var_size]").val(theme_array[i].size)
                variation_title += ' - '+theme_array[i].size;
            }
            else if (variation_type == "color") {
                variation_column_hide_show(2)
                $(var_row_clone).find("input[name^=var_color]").val(theme_array[i].color)
                variation_title += ' - '+theme_array[i].color;
            }
            else if (variation_type == "size-color") {
                variation_column_hide_show(1)
                variation_column_hide_show(2)
                $(var_row_clone).find("input[name^=var_size]").val(theme_array[i].size)
                $(var_row_clone).find("input[name^=var_color]").val(theme_array[i].color)
                variation_title += ' - '+theme_array[i].size;
                variation_title += ', '+theme_array[i].color;
            }

            $(var_row_clone).find("input[name^=var_title]").val(variation_title)
            
            get_variation_sku(var_row_clone)
            
            $("table.variation-table tr:last").after(var_row_clone);
        }

        variation_column_hide_show(1, "hide");

        variation_column_hide_show(2, "hide");

        if (variation_type == "size") {
            variation_column_hide_show(1)
        }
        else if (variation_type == "color") {
            variation_column_hide_show(2)
        }
        else if (variation_type == "size-color") {
            variation_column_hide_show(1)

            variation_column_hide_show(2)
        }

        $("input.size-init-input-box:not(:first)").parent().remove();

        $("input.size-init-input-box:first").val("");

        $("input.color-init-input-box:not(:first)").parent().remove();

        $("input.color-init-input-box:first").val("")

        variation_add_edit();
    }
    else {
        PoundShopApp.commonClass._displayErrorMessage('Please enter variation theme combination.');
    }
});

function variation_column_hide_show (index, type) {
    if (type == "hide") {
        $("table.variation-table tbody tr:not(.variation-row-template):first").same_level_children().find("td:nth-child(" + (index + 2) + ")").hide();

        $("table.variation-table thead tr:not(.variation-row-template):first").same_level_children().find("th:nth-child(" + (index + 2) + ")").hide();
    }
    else {
        $("table.variation-table tbody tr:not(.variation-row-template):first").same_level_children().find("td:nth-child(" + (index + 2) + ")").show();
        $("table.variation-table thead tr:not(.variation-row-template):first").same_level_children().find("th:nth-child(" + (index + 2) + ")").show();
    }

}

variation_add_edit = function ($hide_show) {
    if ($hide_show == 'show') {
        $('#theme_values').show();
        $("#add_variation_title, #add_variation_button").show();

        $("#edit_variation_button, #edit_variation_title").hide();
    }
    else {
        $('#theme_values').hide();
        $("#add_variation_title, #add_variation_button").hide();

        $("#edit_variation_button, #edit_variation_title").show();
    }

}

$('body').on('click', '#edit_variation_button', function (e) {
    variation_add_edit("show");
});

$('body').on('keypress change blur keyup', 'input.size-init-input-box, input.color-init-input-box', function (e) {
    $(this).variation_add();
});

function get_variation_sku(current_obj)
{
    return $.ajax({
            url: BASE_URL+'api-product-get-sku',
            type: "GET",
            datatype:'JSON',
            data:{},
            headers: {
                    'Authorization': 'Bearer ' + API_TOKEN,
                },
            beforeSend: function () {
                $("#page-loader").show();
            },
            success: function (response) {
                $("#page-loader").hide();
                if(response.status == true && typeof response.data.sku != 'undefined')
                {
                    $(current_obj).find("input[name^=var_sku]").val(response.data.sku);
                    $(current_obj).find("input[name^=mp_sku]").val(response.data.sku);
                }   
            },
            error: function (xhr, err) {
               $("#page-loader").hide();
            }
        });
}

remove_variation = function () {
    $("table tbody input.child-checkbox:checked:not([disabled])").closest("tr").each(function () 
    {
        $(this).remove();
    });
}

function variationDocLoadUpdate()
{ 
    $('select[name=variation_theme]').on('change', function(){
        variation_theme_change(this);
    });

    $('body').on('keypress change blur keyup', 'input.size-init-input-box, input.color-init-input-box', function (e) {
        $(this).variation_add();
    });

    if($("select[name=variation_theme]").val() != "" && typeof $("select[name=variation_theme]").val() != 'undefined')
    { 
        show_variations($("select[name=variation_theme]"));

        variation_add_edit();

        option = $("select[name=variation_theme]").find("option:selected");

        var theme_1 = $(option).attr("theme_1");

        var theme_2 = $(option).attr("theme_2");

        if (theme_1.length > 0) 
        {
            variation_column_hide_show(1);
        }

        if (theme_2.length > 0) 
        {    
            variation_column_hide_show(2);
        }
    }
    else
    {
        hide_variations();
    }    
}

var previewVariationImage = function(input, block,nextbtn="",videoShower=""){
    var fileTypes = ['jpg', 'jpeg', 'png','mp4'];
    var extension = input.files[0].name.split('.').pop().toLowerCase();  
    var isSuccess = fileTypes.indexOf(extension) > -1; 
    block.hide();
    block.parents('td').find('.btn-remove-img').remove(); 
    if(isSuccess){
        var size=(input.files[0].size);
        
        var reader = new FileReader();
        if(fileTypes.indexOf(extension)==3)
        {
            if(size>10000000)
            { 
                bootbox.alert({
                    title: "Alert",
                    message: "Video size should be less than  or equal 10 MB.",
                    size: 'small'
                });
                $('.btn-blue').attr('disabled',true);
                return false;
            }
            else
            {
                $('.btn-blue').attr('disabled',false);
                
                if(videoShower!="")
                {
                    videoShower.show();
                
                    reader.onload = function (e) {
                        videoShower.attr('src', e.target.result);
                        videoShower.after('<button type="button" class="btn-remove-img" onclick="delete_variation_img(this)" attr-original-url="">&times</button>');
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            }    
        }
        else
        {
            if(size>10000000)
            {
                bootbox.alert({
                    title: "Alert",
                    message: "Image size should be less than  or equal 10 MB.",
                    size: 'small'
                });
                $('.btn-blue').attr('disabled',true);
               return false;
                
            }else
            {
                $('.btn-blue').attr('disabled',false);
                block.show();
                videoShower.hide();
                reader.onload = function (e) {
                    block.attr('src', e.target.result);
                    block.after('<button type="button" class="btn-remove-img" onclick="delete_variation_img(this)" attr-original-url="">&times</button>');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
      
        if(nextbtn!='')
        {
            nextbtn.show();
        }
    }else{
        $(input).val('');
        alert('Please select video or image file.');
    }

};

function delete_variation_img(me)
{
    if(typeof $(me).attr('attr-original-url') != 'undefined')
    {
    	if($(me).attr('attr-original-url').length > 0)
        {
        	let form_id = $(me).attr('form');
        	$("form#"+form_id).append("<input type='hidden' name='var_remove_product_image[]' value='" + $(me).attr('attr-original-url') + "' />");
        } 

        img = $(me).parents('td').find('img');
        
        video = $(me).parents('td').find('video');
        
        $(img).show();
        
        $(video).hide();
        
        $(img).attr('src', NO_PRODUCT_IMG_URL);   
        
        $(me).parents('td').find('input[name^="var_img"]').val('');
        
        $(me).remove();
    }    
}

function advanceSearch(e)
{   
    e.preventDefault();

    var counter=0;

    if($('#custom_advance_search_fields').length > 0)
    {    
        $('#custom_advance_search_fields').find('input,select,textarea').each(function()
        {
            if(typeof $(this).val() != 'undefined' && $(this).val() != null)
            {
                if($(this).val().length > 0)
                {    
                    if(this.nodeName.toLowerCase() === 'select') {
                        counter++;    
                    }    

                    if($(this).attr('type') == 'text' || $(this).attr('type') == 'textarea')
                    {
                        counter++;    
                    }   
                    
                    if($(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio')
                    {
                        if($(this).prop('checked') == true)
                        {
                            counter++;    
                        }    
                    }
                }    
            }    
        });

        if(counter == 0)
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
        	if($('select[name="filter_by_po"]').val() != "")
        	{
        		$('#txt_search').val('');
        	}	

            $('.filter_count').html(' (<span class="filter_count_digit">'+counter+'</span>)');
            $('#btnFilter').removeClass('open');
            $('.search-filter-dropdown').removeClass('open'); 
            $('.card-flex-container').removeClass('filter-open'); 
            page_update();     
        }    
    }    
}