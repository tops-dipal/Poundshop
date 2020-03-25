<form action="{{route('api-supplier.store')}}" method="POST" class="form-horizontal form-flex" role="form" tab_switch_save id="form-references">
    @csrf
    <input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
    <div class="row">
        <div class="col-lg-8">                         
            @for ($i = 1; $i < 4; $i++)
                <div class="references-card repeat_{{ $i }}">
                    <h4 class="title">@lang('messages.references.references') {{ $i }}</h4>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.references.supp_name')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control supp_name" name="supp_name[]" id="supp_name_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->supplier_name:'' }}" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.references.cont_per')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control cont_per" name="cont_per[]" id="cont_per_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->contact_person:'' }}" disabled="">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-4 col-form-label number">@lang('messages.references.cont_nu')</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control number cont_numb" name="cont_numb[]" id="cont_numb_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->contact_no:'' }}" maxlength="12" disabled="">
                                </div>
                            </div>
                        </div>                                
                        <div class="col-lg-12">
                            <div class="form-group row">
                                <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.references.cont_email')</label>
                                <div class="col-lg-8">
                                    <input type="email" class="form-control cont_email" name="cont_email[]" id="cont_email_{{ $i }}" value="{{ !empty($reference_data[$i-1])?$reference_data[$i-1]->contact_email:'' }}" disabled="">
                                </div>
                            </div>
                        </div>                                                              
                    </div>
                </div>
            @endfor
        </div>
        <?php 
        if(isset($reference_data[0]) && !empty($reference_data[0]))
        {?>

            <div class="col-lg-4">
                <h3 class="page-title-inner mb-3">@lang('messages.supplier.last_email_detail')</h3>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.last_email_sent'):</label>
                            <div class="col-lg-8">
                                {{ isset($reference_data[0]->created_at)?system_date_time($reference_data[0]->created_at):'' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group row">
                            <label for="inputPassword" class="col-lg-4 col-form-label">@lang('messages.supplier.last_email_sent_by'):</label>
                            <div class="col-lg-8">
                                {{ isset($reference_data[0]->email_send_user)?$reference_data[0]->email_send_user:'' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }?>

    </div>
</form>    