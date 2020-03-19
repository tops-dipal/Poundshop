@component('mail::message')

Hello {{$purchaseOrder->supplier->name}},

Please see the attached PDF purchase order.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
