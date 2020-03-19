@if(!empty($result))
    @php
        $range_details = [];
        
        // Default edit for product
        if(!empty($result->buying_range))
        {
            $range_details = $result->buying_range;
        }

        // this view is getting called from ajax also
        if(!empty($result->category_name))
        {
            $range_details = $result;       
        }

    @endphp    

    @if(!empty($range_details))
        <div class="row">
            <div class="col-lg-6">
                <div class="card mt-3">
                    <div class="card-header">
                        <h4 class="bold">Assigned Buying Range</h4>
                    </div>
                    <div class="card-body">
                        <p class="set_buying_range_bath">{{ !empty($range_details) ? $range_details->path : '' }}</p>
                    </div>
                </div>
            </div>
            
            @php
            	$assign_magento_categories = $range_details->magentoCategories;
            @endphp

            @if(!$assign_magento_categories->isEmpty())
        	    <div class="col-lg-6 set_selling_range_bath">
                    <div class="card mt-3">
                        <div class="card-header">
                            <h4 class="bold">Assigned Selling Categories</h4>
                        </div>
                        <div class="card-body">
                		    @foreach($assign_magento_categories as $magento_category)
                		    	<p>{{$magento_category->name}}</p>
                		    @endforeach
                        </div>
                    </div>
        		</div>
            @endif
        </div>
    @endif     
@endif