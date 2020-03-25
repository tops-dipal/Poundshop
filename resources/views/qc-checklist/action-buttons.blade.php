
<ul class="action-btns">
		<li>
            <a class="btn-comman" onClick="printQC('{{ $object->id }}')" title="@lang('messages.common.print_qc')" attr-val="{{$object->id}}" ><span class="icon-moon icon-Print"></span></a>
        </li>
        <li>
            <a class="btn-edit" href="{{route('qc-checklist.edit',$object->id)}}" title="@lang('messages.common.edit')"><span class="icon-moon icon-Edit"></span></a>
        </li>
        <li>
            <a class="btn-delete" href="javascript:;" id="{{$object->id}}" title="@lang('messages.common.delte')"><span class="icon-moon icon-Delete"></span></a>
        </li>
</ul>