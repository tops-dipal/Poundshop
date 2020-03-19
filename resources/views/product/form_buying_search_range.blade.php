<div class="row">
	<div class="col-lg-8">
        <div class="form-group row mb-5">
            <label class="col-lg-2 col-form-label"> @lang('messages.inventory.range') </label>
            <div class="col-lg-4">
                <input type="text" class="form-control" placeholder="Enter keyword" id="category-search-box" value="">
            </div>
            <div class="col-lg-4">
            	<button type="button" class="btn btn-blue btn-header px-4" onclick="get_categories_by_keyword(this)" id="category_search">@lang('messages.common.search')</button>
            </div>	
        </div>
    	<div id="categories_by_keyword">
    		<!-- selected category -->
    		@if(!empty($result->buying_range))
	    	<div class="mb-5">
	    		<div class="form-group" >
				    <label class="fancy-radio">
				        <input type="radio" name="sel_category" value="" onchange="set_category_id(this)" checked="checked">
				        <span class="bold category_radio_label"><i></i>
				        	{{ !empty($result) ? $result->buying_range->category_name : '' }}
				        </span>
				    </label>
				</div>
				<span class="category_path">
					{{ !empty($result) ? $result->buying_range->path : '' }}
				</span>
			</div>
			@endif
    	</div>	
    </div>
</div>

<div id="magento_range_content">
    @include('product.magento_range_content')
</div>

<div id="searched_cat_template" class="display-none">
	<div class="mb-5">
		<div class="form-group">
		    <label class="fancy-radio">
		        <input type="radio" name="sel_category" value="" onchange="set_category_id(this)">
		        <span class="bold category_radio_label"><i></i>category_label_delimeter</span>
		    </label>
		</div>
		<span class="category_path"></span>
	</div>
</div>	


