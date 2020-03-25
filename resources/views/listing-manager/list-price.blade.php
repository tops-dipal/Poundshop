<ul class="action-btns">
	<li><div class="position-relative">
                    <span class="pound-sign">@lang('messages.common.pound_sign')</span>
                    <input type="text" only_numeric class="form-control price" placeholder="" name="price" id="magentoPrice_{{ $id }}" value="{{ $price }}" onkeyup ="showSavePriceBtn(this.id,'magentoPricebtn_{{ $id }}')"  attr-val="{{ $price }}" >
                </div>
		
		<div class="btn-container hidden" id="magentoPricebtn_{{ $id }}" >
		<a type="button" class="btn btn-blue btn-header px-4" onclick="storeMagentoPriceLog('{{ $id }}','magentoPricebtn_{{ $id }}')">Save</a>
		</div>
	</li>
</ul>