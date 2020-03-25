   <form method="POST" class="form-horizontal form-flex" role="form" id="form-contact">
        @csrf
        <input type="hidden" name="id" value="{{!empty($result->id) ? $result->id : '' }}">
         <div class="row">
            <div class="col-md-12">
                <div class="table-responsive mt-2">
                    <table id="supplier_contact_person" class="table border-less display table-striped custom-table">
                        <thead>
                            <tr>
                                <th class="checkbox-container">
                                    <div class="d-flex">
                                        <label class="fancy-checkbox">
                                            <input type="checkbox" class="master-checkbox">
                                            <span><i></i></span>
                                        </label>
                                        <div class="dropdown bulk-action-dropdown">
                                            <button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="@lang('messages.modules.bulk_action')">
                                            <span class="icon-moon icon-Drop-Down-1"/>
                                                </button>
                                                
                                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
                                                    @can('product-delete')
                                                    <h4 class="title">@lang('messages.modules.bulk_action')</h4>
                                                    <button type="button" class="btn btn-add delete-many" onclick="deleteContact(this)">
                                                        <span class="icon-moon red icon-Delete"></span>
                                                        @lang('messages.supplier.delete_contact_persons')
                                                    </button>
                                                    @endcan
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                                <th>@lang('messages.supplier.contact_person_name')</th>
                                <th>@lang('messages.supplier.contact_person_email')</th>
                                <th>@lang('messages.supplier.contact_person_phone')</th>
                                <th>@lang('messages.supplier.contact_person_mobile')</th>
                                <th>@lang('messages.supplier.contact_person_designation')</th>
                                <th>@lang('messages.supplier.contact_person_primary')</th>
                                <th data-class-name="action">@lang('messages.table_label.action')</th>
                            </tr>
                        </thead>
                        <tbody id="contact_persons">
                            @if(!empty($result->id))
                                @php
                                    $res_supplier = $result->SupplierContact()->get();
                                @endphp
                                @if(!$res_supplier->isEmpty())
                                    @foreach($res_supplier as $supplierContact)
                                        @php
                                        $supplierContactsInQuery = http_build_query(json_decode(json_encode( $supplierContact), TRUE));
                                        @endphp
                                        <tr tr-temp-id="{{$supplierContact->id}}">
                                            <td>
                                                <div class="d-flex">
                                                    <label class="fancy-checkbox">
                                                        <input type="checkbox" class="child-checkbox" value="{{$supplierContact->id}}">
                                                        <span><i></i></span></label></div>
                                            </td>
                                            <td>{{ucwords($supplierContact->name)}}</td>
                                            <td>{{$supplierContact->email}}</td>
                                            <td>{{$supplierContact->phone}}</td>
                                            <td>{{$supplierContact->mobile}}</td>
                                            <td>{{ucwords($supplierContact->designation)}}</td>
                                            <td>
                                                <label class="fancy-radio">
                                                    <input type="radio" name="primary_contact" value="{{$supplierContact->id}}" {{ ($supplierContact->is_primary == '1') ? 'checked="checked"' : "" }}>
                                                    <span><i></i></span>
                                                </label>
                                            </td>
                                            <td>
                                                <ul class="action-btns">
                                                    <li>
                                                        <a class="btn-edit" href="javascript:;" temp-id="{{$supplierContact->id}}" onclick="editContact(this)"> <span class="icon-moon icon-Edit"></span></a>
                                                    </li>
                                                    <li>
                                                        <a class="btn-delete" href="javascript:;" temp-id="{{$supplierContact->id}}" onclick="deleteContact(this)"><span class="icon-moon icon-Delete"></span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <input type="hidden" name="supplier_contacts[{{$supplierContact->id}}]" value="{{$supplierContactsInQuery}}">
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td  align="center" colspan="100%">@lang('messages.common.no_records_found')</td>
                                    </tr>    
                                @endif
                            @else
                                <tr>
                                    <td  align="center" colspan="100%">@lang('messages.common.no_records_found')</td>
                                </tr>  
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>    

