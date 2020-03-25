<style>
.error{color: red;}
</style>
<div class="modal fade" id="item-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="custom-modal modal-dialog modal-lg" role="document">
    <form class="form-horizontal form-flex" method="post" id="add_descrepancy">
      <div class="modal-content">
        <div class="modal-header align-items-center">
          <h5 class="modal-title" id="exampleModalLabel">@lang('messages.discrepancy.discrepancy')</h5>
          <div>
            <button type="button" class="btn btn-gray font-12 px-4" data-dismiss="modal" aria-label="Close">@lang('messages.common.cancel')</button>
            <button type="button" class="btn btn-green font-12 px-4 ml-3" onclick="storedisc();" title="@lang('messages.common.save')">@lang('messages.common.save')</button>
            <input type="hidden" value="" name="booking_po_product_id" id="desc_booking_po_product_id" />
            <input type="hidden" value="" name="product_id" id="desc_product_id" />
            <input type="hidden" value="0" name="add_desc_counter" id="add_desc_counter" />
            <input type="hidden" value="" name="deleted_id" id="desc_deleted_id" />
            <input type="hidden" value="" id="descripency_reload_table" value="0" />
            
          </div>
        </div>
        <div class="modal-body">        
          <table id="discrepancy_table" class="table border-less" style="width:100%">
            <thead>
              <tr>
                <th>@lang('messages.discrepancy.qty')</th>
                <th>@lang('messages.discrepancy.type')</th>
                <th>@lang('messages.discrepancy.image')</th>
                <th>@lang('messages.discrepancy.Action')</th>            
              </tr>
            </thead>
            <tbody>
            
            </tbody>        
          </table>
          <div class="text-right">
            <button type="button" onclick="add_new_desc();" class="btn btn-blue font-12 px-4">@lang('messages.discrepancy.add_diff')</button>
          </div>  
        </div>        
      </div>
    </form>
  </div>
</div>