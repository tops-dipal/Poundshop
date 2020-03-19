<form method="post" id="po-terms-form" action="{{route('api-purchase-orders.update-terms')}}" enctype="multipart/form-data" >
@csrf
<input type="hidden" id="id" name="id" value="{{$purchaseOrder->id}}" />
<div class="row">
    <div class="col-lg-12">
        <div class="form-group row">
            <label class="col-lg-2 col-form-label">
               @lang('messages.purchase_order.terms') 
            </label>
            <div class="col-lg-10">
                <textarea name="term_condition" id="term_condition" class="form-control ckeditor">{{ !empty(old('term_condition')) ? old('term_condition') : $purchaseOrder->terms_poundshop }}</textarea>
            </div>
        </div>
    </div>
    <div class="col-lg-12">
        <div class="form-group row">
            <label class="col-lg-2 col-form-label">
                
                @lang('messages.supplier.term_condition')
            </label>
            <div class="col-lg-10">
                <textarea name="term_supplier_condition" id="term_supplier_condition" class="form-control ckeditor">{{ !empty(old('term_supplier_condition')) ? old('term_supplier_condition') : $purchaseOrder->terms_supplier }}</textarea>
            </div>
        </div>
    </div>
</div>   
    
</form>