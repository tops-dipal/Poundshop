<ul class="action-btns">
	<li>
		<input type="text" name="price" id="magentoPrice_{{ $id }}" value="{{ $price }}" class="form-control price" attr-val="{{ $price }}" onkeyup ="showSavePriceBtn(this.id,'magentoPricebtn_{{ $id }}')" >
		<div class="btn-container hidden" id="magentoPricebtn_{{ $id }}" >
		<a type="button" class="btn btn-blue btn-header px-4" onclick="storeMagentoPriceLog('{{ $id }}','magentoPricebtn_{{ $id }}')">Save</a>
		</div>
	</li>
</ul>