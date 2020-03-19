<ul class="action-btns">
	<li>
		<div class="input-btn">
            <input type="text" name="qty" id="magentoQty_{{ $id }}" value="{{ $qty }}" class="form-control qty" attr-val="{{ $qty }}" onkeyup ="showSaveQtyBtn(this.id,'magentoQtybtn_{{ $id }}')" >
            <span id="error_magentoQty_{{ $id }}" class="invalid-feedback">Please enter quantity</span>
            <div class="btn-container hidden" id="magentoQtybtn_{{ $id }}">
                <a type="button" class="btn btn-blue btn-header px-4 " onclick="storeMagentoQtyLog('{{ $id }}','magentoQtybtn_{{ $id }}')">save</a>
            </div>
        </div>
		
	</li>
</ul>