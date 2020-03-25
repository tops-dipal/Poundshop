<!-- Contact Modal -->
<div class="modal fade" id="reportstockModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" style="display: none;">
    <div class="custom-modal modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header align-items-center">
                <h5 class="modal-title" id="exampleModalLabel">@lang('messages.modules.locations_edit')</h5>
                <div>
                    <button type="button" class="btn btn-gray font-12 px-3" data-dismiss="modal">@lang('messages.common.cancel')</button>
                    <button type="button" class="btn btn-blue font-12 px-3 ml-2" onclick="saveLocation(this)">@lang('messages.common.save')</button>
                </div>
            </div>
            <div class="modal-body p-4">
                <form id="locationsEditForm" method="post">
                    <input type="hidden" name="edit_record_id" value="" id="edit_record_id">
                    <div class="row">                        
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.case_pack')</label>
                                <div class="col-lg-8">
                                    <select class="form-control" id="edi_case_pack" name="edi_case_pack">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>                                                     
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.length')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_length" name="edi_length" maxlength="6" value="" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.width')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_width" name="edi_width" maxlength="6" value="" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.height')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_height" name="edi_height" maxlength="6" value="" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.cbm')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_cbm" name="edi_cbm" value="" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row align-items-center">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.table_label.stor_weight_short_kg')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="edi_stor_weight" name="edi_stor_weight" value="" onkeypress="return fun_AllowOnlyAmountAndDot(this.id);" disabled="">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>            
        </div>
    </div>
</div>