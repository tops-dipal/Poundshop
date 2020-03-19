@forelse ($result as $row)
	@php
		
		$tags = $row->tags->pluck('name')->toArray();
                        
        foreach(product_logic_base_tags() as $db_tag_field => $tag_caption)
        {
            $db_tag_field = 'is_'.$db_tag_field;
                
            if($row->$db_tag_field == 1)
            {
                $tags[] = $tag_caption;
            }    
        }
	@endphp
    <tr role="row" attr-par-id = "{{$id}}">
    	<td>{{ View::make('product.list-checkbox',['object'=>$row]) }}</td>
    	<td class="pl-4">{{ View::make('product.listing-image',['object'=>$row]) }}</td>
    	@can('product-edit')
            <td> <a href="{{url('product/form/'.$row->id.'?active_tab=stock-file')}}">{{ !empty($row->title) ? $row->title : '-' }} </a></td>    
        @else
            <td>{{ !empty($row->title) ? $row->title : '-' }}</td>
        @endcan
        <td>{{$row->sku}}</td>
    	<td> - </td>
    	<td> - </td>
    	<td> - </td>
    	<td>{{!empty($row->product_identifier) ? $row->product_identifier : '-' }}</td>
    	<td>{{!empty($row->last_cost_price) ? $row->last_cost_price : '-' }}</td>
    	<td>{{!empty($row->single_selling_price) ? $row->single_selling_price : '-'}}</td>
    	<td>{{!empty($tags) ? implode(', ', $tags) : '-'}}</td>
    	<td> - </td>
        <td> {{ ($row->is_listed_on_magento == 1) ?Lang::get('messages.inventory.magento_enabled') : Lang::get('messages.inventory.magento_disabled') }} </td>
    	<td> {{ View::make('product.action-buttons',['object'=>$row]) }} </td>
    </tr>	
@empty
    <tr role="row" attr-par-id = "{{$id}}">
		<td colspan="100%">@lang('messages.inventory.no_variations_found')</td>
	</tr>	

@endforelse
