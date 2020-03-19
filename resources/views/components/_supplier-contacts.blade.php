@if(!empty($suppliers->SupplierContact))
    <option value=""> Select Supplier Contact</option>
    @foreach($suppliers->SupplierContact as $supplier)
    <option @if($supplier->is_primary) selected="selected" @endif value="{{$supplier->id}}">{{$supplier->name}}</option>
    @endforeach
@else
    <option value=""> Select Supplier Contact</option>
@endif