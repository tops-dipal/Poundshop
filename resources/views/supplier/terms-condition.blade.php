  <form action="{{ url('api/api-supplier-save-terms-condition') }}" method="POST" class="form-horizontal form-flex" role="form" tab_switch_save id="form-terms">
    @csrf
    <input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
    <div class="row">
        <div class="col-lg-12">
            <div class="form-group row">
                <label class="col-lg-12 col-form-label mb-3">
                    @lang('messages.supplier.term_condition')
                </label>
                <div class="col-lg-12">
                    <textarea name="term_condition" class="form-control ckeditor">{{ !empty(old('term_condition')) ? old('term_condition') : @$result->term_condition }}</textarea>
                </div>
            </div>
        </div>
    </div>
</form>    