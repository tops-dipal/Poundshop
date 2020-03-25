<div class="sidebar-area">
    <div class="logo-area">
        <button class="btn btn-toggle_sidebar" id="toggle_sidebar">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
        <span id="close_sidebar_mobile" class="icon-moon icon-Close"></span>
        <a href="javascript:void(0)" class="logo-a"><img src="{{asset('img/logo-new.png')}}" class="img-fluid" alt=""></a>
    </div>
    <div class="side-navbar custom-scroll">
        <ul>
            <li class="{{ (in_array(Request::route()->getName(), array('user-dashboard'))) ? 'active' : '' }}"><a title="@lang('messages.modules.dashboard')" href="{{route('user-dashboard')}}"><span class="icon-moon icon-Dashboard f-14"></span><span class="text">@lang('messages.modules.dashboard')</span></a></li>

            @php
            $inventory_drop_open = "";

            $action_name = Request::route()->getActionName();

            $active_inventory = strpos($action_name, 'ProductsController') !== false ? 'active' : '';

            $active_listing = strpos($action_name, 'MagentoListingManager') !== false ? 'active' : '';

            if($active_listing == 'active' || $active_inventory == 'active')
            {
            $inventory_drop_open = 'open';
            }

            @endphp

            <li>
                <a href="javascript:void(0)" class="has-submenu">
                    <span class="icon-moon icon-Inventory"></span>
                    <span class="text">@lang('messages.modules.inventory')</span>
                    <button class="open-submenu {{ $inventory_drop_open }}"></button>
                </a>
                <ul class="sub-menu {{ $inventory_drop_open }}">
                    @can('product-list')
                    <li class="{{ $active_inventory }}">
                        <a href="{{url('product')}}">
                            <span class="icon-moon icon-Inventory"></span>
                            <span class="text">@lang('messages.inventory.inventory_list')
                            </span>
                        </a>
                    </li>
                    @endcan
                   
                    <li class="{{ $active_listing }}">
                        <a href="{{url('listing-manager/magento')}}">
                            <span class="icon-moon icon-Listing-Manager-"></span>
                            <span class="text">@lang('messages.modules.listing_manager')
                            </span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ strpos($action_name, 'BuyByProductController') !== false ? 'active' : '' }}">
                <a href="{{url('buy-by-product')}}">
                    <span class="icon-moon icon-Buyers-Inquiry-2"></span>
                    <span class="text">@lang('messages.common.buy_by_product')
                    </span>
                </a>
            </li>
            @can('supplier-list')
            <li class="{{ strpos($action_name, 'SupplierController') !== false ? 'active' : '' }}"><a href="{{url('supplier')}}"><span class="icon-moon icon-Reference"></span><span class="text">@lang('messages.modules.supplier_list')</span></a></li>
            @endcan

            <li class="{{ (in_array(Request::route()->getName(), array('purchase-orders.index', 'purchase-orders.create', 'purchase-orders.edit')))  ? 'active' : '' }}" ><a href="{{route('purchase-orders.index')}}" title="@lang('messages.modules.purchase_order')"><span class="icon-moon icon-Purchse-Order"></span><span class="text">@lang('messages.modules.purchase_order')</span></a></li>

<!--<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Buyer's Enquiry</span></a></li> -->
<!-- <li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Book-In Request</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Goods In</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Storage - Putaway - Replen</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Despatch</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Spot Check</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Customer Returns Stock Take</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Drop Shipping</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Invoice Matching</span></a></li>
<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Orders</span></a></li> -->
            @php
            $goods_in_drop_open = "";
            
            $goodsin_inventory = (strpos($action_name, 'BookingsController') !== false || strpos($action_name, 'MaterialReceiptController') !== false) ? 'active' : '';

            if($goodsin_inventory == 'active')
            {
            $goods_in_drop_open = 'open';
            }
            @endphp

            <li>
                <a href="javascript:void(0)" class="has-submenu">
                    <span class="icon-moon icon-Goods-In"></span>
                    <span class="text">@lang('messages.goods_in_master.goods_in_title')</span>
                    <button class="open-submenu {{ $goods_in_drop_open }}"></button>
                </a>
                <ul class="sub-menu {{ $goods_in_drop_open }}">
                    <li class="{{ $goodsin_inventory }}">
                        <a href="{{route('booking-in.index')}}">
                            <span class="icon-moon icon-Book-In-Request"></span>
                            <span class="text">@lang('messages.goods_in_master.goods_inwards')
                            </span>
                        </a>
                    </li>
                    <!-- <li class="{{ $active_listing }}">
                            <a href="{{url('listing-manager/magento')}}">
                                    <span class="icon-moon icon-Setting"></span>
                                    <span class="text">@lang('messages.goods_in_master.customer_return')
                                    </span>
                            </a>
                    </li> -->
                </ul>
            </li>
            <li>
                @php
                $open_menu_storage='';
                $put_away_active=(\Request::is('put-away'))  ? 'active' : '';
                $location_assign_active=(\Request::is('location-assign'))  ? 'active' : '';    
                $replen_request_active=  (\Request::is('replen-request'))  ? 'active' : '';
                $replen_active=  (\Request::is('replen'))  ? 'active' : '';
                if($put_away_active=='active' || $location_assign_active=='active' || $replen_request_active=='active' || $replen_active=='active')
                {
                    $open_menu_storage='open';
                }          
                @endphp
                <a href="javascript:void(0)" class="has-submenu">
                    <span class="icon-moon icon-Storage"></span>
                    <span class="text">@lang('messages.storage.storage')</span>
                    <button class="open-submenu {{ $open_menu_storage }}"></button>
                </a>
                <ul class="sub-menu  {{ $open_menu_storage }}">
                    <li class="{{ $put_away_active }}"><a href="{{ route('put-away-dashboard') }}"><span class="icon-moon icon-Put-Away"></span><span class="text">@lang('messages.storage.put_away')</span></a></li>

                    <li class="{{ $location_assign_active }}"><a href="{{route('location-assign.index')}}" title="@lang('messages.storage.location_assign')"><span class="icon-moon icon-Reporting" ></span><span class="text">@lang('messages.storage.location_assign')</span></a></li>

                    <li class="{{ $replen_request_active }}"><a href="{{route('replen-request.index')}}"><span class="icon-moon icon-Replen f-12"></span><span class="text">@lang('messages.storage.replen_request')</span></a></li>

                    <li class="{{ $replen_active }}"><a href="{{route('replen.index')}}"><span class="icon-moon icon-Replen f-12"></span><span class="text">@lang('messages.storage.replen')</span></a></li>
                </ul>
            </li>
            <li>
                @php
                    $active_report = (strpos($action_name, 'ReportController') !== false || strpos($action_name, 'taxPaymentReport') !== false ) ? 'active' : '';
                @endphp
                <a href="javascript:void(0)" class="has-submenu">
                    <span class="icon-moon icon-Reporting"></span>
                    <span class="text">@lang('messages.modules.reports')</span>
                    <button class="open-submenu {{ ($active_report=='active') ? 'open' : '' }}"></button>
                </a>
                <ul class="sub-menu  {{ ($active_report=='active') ? 'open' : '' }}">
                    <li class="{{ strpos($action_name, 'taxPaymentReport') !== false ? 'active' : '' }}"><a href="{{route('tax-paymnet-report-po')}}"><span class="icon-moon icon-Reporting"></span><span class="text">@lang('messages.modules.tax_paymnet_report')</span></a></li>

                    @can('excess-qty-received-report')
                        <li class="{{ strpos($action_name, 'excessQtyReceivedReport') !== false ? 'active' : '' }}"><a href="{{route('excess-qty-received-report')}}"><span class="icon-moon icon-Reporting"></span><span class="text">@lang('messages.modules.excess_qty_report')</span></a></li>
                    @endcan
                </ul>
            </li>

            <li>
                @php
                $open_menu='';
                $user_active=(in_array(Request::route()->getName(), array('users.index', 'users.create', 'users.edit')))  ? 'active' : '';
                $range_active=(in_array(Request::route()->getName(), array('range.index', 'range.create', 'range.edit')))  ? 'active' : '';
                $cat_map_active=(in_array(Request::route()->getName(), array('category-mapping.index','mapping-relation')))  ? 'active' : '';
                $box_active=(in_array(Request::route()->getName(), array('cartons.index', 'cartons.create', 'cartons.edit')))  ? 'active' : '';
                $pallet_active=(in_array(Request::route()->getName(), array('pallets.index', 'pallets.create', 'pallets.edit')))  ? 'active' : '';
                $totes_active=(in_array(Request::route()->getName(), array('totes.index', 'totes.create', 'totes.edit')))  ? 'active' : '';
                $code_active=(in_array(Request::route()->getName(), array('commodity-codes.index', 'commodity-codes.create', 'commodity-codes.edit')))  ? 'active' : '';
                $duty_active=(in_array(Request::route()->getName(), array('import-duty.index', 'import-duty.create', 'import-duty.edit')))  ? 'active' : '';
                $vat_rate_active=(\Request::is('settings/vat_rates'))  ? 'active' : '';
                 $general_active=(\Request::is('settings/general'))  ? 'active' : '';
                $terms_active=(\Request::is('setting/terms'))  ? 'active' : '';
                $location_active=(in_array(Request::route()->getName(), array('locations.index', 'locations.create', 'locations.edit')))  ? 'active' : '';
                $reff_active=(in_array(Request::route()->getName(), array('reference.index')))  ? 'active' : '';
                $site_active=(in_array(Request::route()->getName(), array('warehouse.index', 'warehouse.create', 'warehouse.edit')))  ? 'active' : '';
                $slot_active=(in_array(Request::route()->getName(), array('slot.index')))  ? 'active' : '';
                $qc_active=(in_array(Request::route()->getName(), array('qc-checklist.index', 'qc-checklist.create', 'qc-checklist.edit')))  ? 'active' : '';
                if($user_active=='active' || $range_active=='active' || $cat_map_active=='active' || $box_active=='active' || $pallet_active=='active' || $totes_active=='active' || $code_active=='active' || $duty_active=='active' || $vat_rate_active=='active' || $terms_active=='active' || $location_active=='active' || $reff_active=='active' || $site_active=='active' || $slot_active=='active' || $qc_active=='active' || $general_active=='active')
                {
                $open_menu='open';
                }

                @endphp

                <a href="javascript:void(0)" class="has-submenu">
                    <span class="icon-moon icon-Setting"></span>
                    <span class="text">@lang('messages.modules.setting')</span>
                    <button class="open-submenu {{ $open_menu }}"></button>
                </a>

                <ul class="sub-menu {{ $open_menu }}">


                                        <!-- <li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">@lang('messages.modules.range_management')</span></a></li>
                                        <li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">@lang('messages.modules.category_mapping')</span></a></li> -->
                                        <!-- <li><a href="{{route('roles.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.roles_management')</span></a></li> -->
                   <li class="{{ $box_active }}"><a href="{{route('cartons.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.box_master.box_master')</span></a>

                    </li>

                    <li class="{{ $cat_map_active }}"><a href="{{route('category-mapping.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.category_mapping')</span></a></li>

                    <li class="{{ $code_active }}"><a href="{{route('commodity-codes.index')}}" title="@lang('messages.modules.commodity_code_master')"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.commodity_code_master')</span></a></li>

                    <li class="{{ $general_active }}"><a href="{{route('module-setting',['module_name'=>'general'])}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.common.general_setting')</span></a></li>

                    <li class="{{ $duty_active }}"><a href="{{route('import-duty.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.commodity_code_master.import_duty')</span></a></li>

                    <li class="{{ $location_active }}"><a href="{{route('locations.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.locations_list')</span></a></li>

                    <li class="{{ $pallet_active }}"><a href="{{route('pallets.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.pallets_list')</span></a></li>

                    <li class="{{ $qc_active }}"><a href="{{route('qc-checklist.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.qc_checklist')</span></a></li>

                    <li class="{{ $range_active }}"><a href="{{route('range.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.range_management')</span></a></li>

                    <li class="{{ $reff_active }}"><a href="{{route('reference.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.references')</span></a></li>

                    <li class="{{ $site_active }}"><a href="{{route('warehouse.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.warehouse_master')</span></a></li>

                    <li class="{{ $slot_active }}"><a href="{{route('slot.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.slot_master')</span></a></li>

                    <li class="{{ $terms_active }}"><a href="{{route('setting-terms')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.terms_and_condition')</span></a></li>

                    <li class="{{ $totes_active }}"><a href="{{route('totes.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.totes_master')</span></a></li>

                    <li class="{{ $user_active }}"><a href="{{route('users.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.user_management')</span></a></li>

                    <li class="{{ $vat_rate_active }}"><a href="{{route('module-setting',['module_name'=>'vat_rates'])}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.settings.vat_rates')</span></a></li>
                    <!-- <li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Tax Codes</span></a></li> -->
                </ul>
            </li>


        </ul>
    </div>
    <div class="profile-dropdown">
        <ul>
            <li>
                <div class="item-container">
                    @if(!is_null(Auth::user()->profile_pic))
                    <span class="item-icon user">
                        <img src="{{ Auth::user()->profile_pic }}" height="30" width="20">
                    </span>
                    @else
                    <span class="item-icon user">
                        {{Auth::user()->first_name}}
                    </span>
                    @endif
                    <span class="item-name bold">{{Auth::user()->first_name}}</span>

                    <button class="btn-profile-toggle"><img src="{{asset('img/dropdown.png')}}"></button>

                </div>
            </li>
            <li>
                <div class="item-container">
                    <span class="item-icon profile">
                        <span class="icon-moon icon-Profile"></span>
                    </span>
                    <span class="item-name"><a href="{{ route('users.show',Auth::user()->id) }}">@lang('messages.modules.profile_and_setting')</a></span>
                </div>
            </li>
            <li>
                <div class="item-container">
                    <span class="item-icon logout">
                        <span class="icon-moon icon-Logout"></span>
                    </span>
                    <span class="item-name"><a href="{{ route('logout') }}">@lang('messages.modules.logout')</a></span>
                </div>
            </li>
        </ul>
    </div>
</div>