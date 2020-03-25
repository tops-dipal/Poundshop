 <div class="row" id="refreshDiv">
    
    @php
      $countAisleAssign=1;
      $lastElement=count($defaultSiteAisleUserData)-1;
     $nullAisleCount=0;

      @endphp
      <input type="hidden" name="total_existing_record" value="{{ (count($defaultSiteAisleUserData)==0) ? 0: count($defaultSiteAisleUserData)  }}" id="total_existing_record">
      @forelse($defaultSiteAisleUserData as $key=>$value)
      
      <div class="col-lg-12 assign_aisle_div_{{ $countAisleAssign }} assign_aisle_div" id="assign_aisle_div_{{ $countAisleAssign }}">
          <input type="hidden" name="update_id[]" id="update_id_{{ $countAisleAssign }}"  value="{{ $value['id'] }}">
              <div class="form-group row">
                  <div class="col-lg-2">
                   <select class="form-control" id="aisle_{{ $countAisleAssign }}" name="aisle[]">
                   
                         @forelse($defaultSiteAisleData as $ak=>$av)
                         
                         <option value="{{ $av }}" {{ ($av==$value['aisle']) ? "selected='selected'" : '' }}>{{ $av }}</option>
                        
                         @empty
                         @endforelse
                      </select>
                  </div>
                  <div class="col-lg-4">
                   <select class="form-control" id="user_id_{{ $countAisleAssign }}" name="user_id[]">
                          @forelse($defaultSiteUsersData as $uk=>$uv)
                         <option value="{{ $uv['id'] }}" {{ ($uv['id']==$value['user_id']) ? "selected='selected'" : '' }}>{{ $uv['first_name'] }} {{ $uv['last_name'] }}</option>
                         @empty
                         @endforelse
                      </select>
                  </div>
                  <div class="col-lg-4 btnDiv{{ $countAisleAssign }}">
                      <a class="btn-delete bg-light-red deleteBtn deleteBtn_{{ $countAisleAssign }}" attr-div="assign_aisle_div_{{ $countAisleAssign }}" href="javascript:void(0);" id="" attr-val="{{ $value['id'] }}"><span class="icon-moon icon-Delete"></span></a>
                    
                  </div>
              </div>
          </div>
          @php
          $countAisleAssign++;
          @endphp
          @empty
            @if(count($defaultSiteAisleData)==0)
            <span>No any bulk locations</span>
            @endif
   
      @endforelse
     
  
   </div>
  
  

</div>
