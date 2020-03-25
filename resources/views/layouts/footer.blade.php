@include('report-stock-control.index')
<script src="{{ asset('js/jquery-2.2.4.min.js')}}" ></script>

<script src="{{URL::asset('js/jQuery-validation-plugin/jquery.validate.js')}}"></script>
@if(\Request::is('product/form/*') || \Request::is('listing-manager/magento/*'))
<script src="{{URL::asset('js/jQuery-validation-plugin/custom-additional-methods.js')}}"></script>
@else
<script src="{{URL::asset('js/jQuery-validation-plugin/additional-methods.js')}}"></script>
@endif

<script src="{{ asset('js/jquery.animsition.js')}}"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<script>
//Animation Pre loader jQuery Code
jQuery(".animsition").animsition({
    inClass: 'fade-in',
    outClass: 'fade-out',
    inDuration: 1500,
    outDuration: 800,
    linkElement: '.animsition-link',
    // e.g. linkElement   :   'a:not([target="_blank"]):not([href^=#])'
    loading: true,
    loadingParentElement: 'body', //animation wrapper element
    loadingClass: 'animsition-loading',
    unSupportCss: ['animation-duration',
        '-webkit-animation-duration',
        '-o-animation-duration'
    ],
    //"unSupportCss" option allows you to disable the "animation" in case the css property in the array is not supported by your browser.
    //The default setting is to disable the "animation" in a browser that does not support "animation-duration".

    overlay: false,
    overlayClass: 'animsition-overlay-slide',
    overlayParentElement: 'body'
});
//Animation Pre loader jQuery Code
</script>
<script src="{{ asset('js/jquery.dataTables.min.js')}}"></script>
<!-- <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script> -->

<script src="{{ asset('js/dataTables.rowReorder.min.js')}}"></script>
<script src="{{ asset('js/dataTables.responsive.min.js')}}"></script>
<script src="{{ asset('js/dataTables.fixedColumns.min.js')}}"></script>

<script src="{{ asset('js/popper.js')}}"></script>
<!--<script src="{{ asset('js/poundshop.js')}}"></script>-->


<script src="{{ asset('js/bootstrap.min.js')}}"></script><!--
--><script src="https://cdnjs.cloudflare.com/ajax/libs/typeahead.js/0.11.1/typeahead.bundle.min.js"></script><!--
--><script src="{{asset('js/bootstrap-datepicker.js')}}"></script><!--
--><script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script><!--
--><script src="{{asset('js/bootstrap-tagsinput.min.js')}}"></script>

<script src="{{ asset('js/jquery.slimscroll.min.js')}}"></script>
<script src="{{ asset('js/perfect-scrollbar.min.js')}}"></script>
<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('js/bootbox/bootbox.min.js') }}"></script>
<script src="{{ asset('js/jquery.responsivetabs.js') }}"></script>
<script src="{{ asset('js/slick.js') }}"></script>
<script src="{{ asset('js/lightcase.js') }}"></script>
<script src="{{ asset('js/custom-file-input.js') }}"></script>
<script src="{{ asset('js/main.js')}}"></script>
<script src="{{ asset('js/lazyload.js')}}"></script>
<script src="{{ asset('js/image-uploader.min.js') }}"></script>
<script src="https://cdn.ckeditor.com/4.10.0/standard/ckeditor.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.12/dist/js/select2.min.js"></script>
<script src="{{ asset('js/components/common.js?v='.CSS_JS_VERSION) }}"></script>
<script src="{{ asset('js/report-stock-control/index.js')}}"></script>

<!-- <script src="{{ asset('js/Moment.js?v='.CSS_JS_VERSION)}}"></script>
<script src="{{ asset('js/timepicker.min.js?v='.CSS_JS_VERSION)}}"></script> -->
@yield('script')
