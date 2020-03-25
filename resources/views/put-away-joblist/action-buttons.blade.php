<ul class="action-btns">
        
        @if($listing=='to-be-listed')
        <li>
           <a class='btn-edit' href="{{route('magento.add',['id'=>$object->id,'store_id'=>$store_id])}}"><span class="icon-moon icon-Edit"></span></a>
        </li>
        @elseif($listing=='inprogress')
            <li>
            @if($object->posting_result_status==0 || $object->posting_result_status==3)
                @if($object->is_revised==1)
                   <a class='btn-edit' href="{{route('magento.edit',$object->magento_id)}}"><span class="icon-moon icon-Edit"></span></a>
                @else
                  <a class='btn-edit' href="{{route('magento.add',['id'=>$object->product_master_id,'store_id'=>$store_id])}}"><span class="icon-moon icon-Edit"></span></a>
                @endif

            @endif           
            </li>
            <li>
                 @if($object->posting_result_status==2 || $object->posting_result_status==3 && !empty($object->posting_result))
                <a class="btn-delete" href="#" id="postingResultBtn_{{ $object->magento_id }}" title="Expand" attr-data="{{ json_encode(unserialize($object->posting_result)) }}" onclick="expandPostingResult('postingResultBtn_{{ $object->magento_id }}')">
                    <span class="icon-moon icon-Spot-Check"></span>
                </a>
                
                
            @endif
            </li>

        @elseif($listing=='already-listed')
        <li>
           <a class='btn-edit' href="{{route('magento.edit',$object->id)}}"><span class="icon-moon icon-Edit"></span></a>
        </li>
        <li>
            @if($object->is_enabled==1)
                <a class="btn-add btn-light-green enable-magento-product"  href="javascript:;" attr-id="{{ $object->id }}" id="{{ $object->id }}" title="enabled"><span class="icon-moon size-sm icon-Active"></span></a>
            @else
                <a class="btn-edit enable-magento-product"  href="javascript:;" attr-id="{{ $object->id }}" id="{{ $object->id }}" title="disabled"><span class="icon-moon size-sm icon-Active"></span></a>
            @endif
        </li>
        <li>
            <a class="btn-delete delist-btn" href="javascript:;" attr-id="{{ $object->id }}" id="{{ $object->id }}"><span class="icon-moon size-sm icon-Delete"></span></a>
        </li>
        @endif

</ul>