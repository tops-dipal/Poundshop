<div class="sidebar-area">
	<div class="logo-area">				
		<button class="btn btn-toggle_sidebar" id="toggle_sidebar">
			<span class="bar"></span>
			<span class="bar"></span>
			<span class="bar"></span>
		</button>
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
				<a href="javascript:void(0)">
					<span class="icon-moon icon-Setting"></span>
					<span class="text">@lang('messages.modules.inventory')</span>
					<button class="open-submenu {{ $inventory_drop_open }}"></button>
				</a>
				<ul class="sub-menu {{ $inventory_drop_open }}">
					
					@can('product-list')
					<li class="{{ $active_inventory }}">
						<a href="{{url('product')}}">
							<span class="icon-moon icon-Setting"></span>
							<span class="text">@lang('messages.inventory.inventory_list')
							</span>
						</a>
					</li>
					@endcan

					<li class="{{ $active_listing }}">
						<a href="{{url('listing-manager/magento')}}">
							<span class="icon-moon icon-Setting"></span>
							<span class="text">@lang('messages.modules.listing_manager')
							</span>
						</a>
					</li>
			</ul>		
			

            @can('supplier-list')
            <li class="{{ strpos($action_name, 'SupplierController') !== false ? 'active' : '' }}"><a href="{{url('supplier')}}"><span class="icon-moon icon-Reference"></span><span class="text">@lang('messages.modules.supplier_list')</span></a></li>
			@endcan

                        <li class="{{ (in_array(Request::route()->getName(), array('purchase-orders.index', 'purchase-orders.create', 'purchase-orders.edit')))  ? 'active' : '' }}" ><a href="{{route('purchase-orders.index')}}" title="@lang('messages.modules.purchase_order')"><span class="icon-moon icon-Purchse-Order"></span><span class="text">@lang('messages.modules.purchase_order')</span></a></li>
			
<!--			 <li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Buyer's Enquiry</span></a></li> -->
			



			<!-- <li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Book-In Request</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Goods In</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Storage - Putaway - Replen</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Despatch</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Spot Check</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Customer Returns Stock Take</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Drop Shipping</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Invoice Matching</span></a></li>
			<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">Orders</span></a></li> -->
			<li >
				@php
				$tax_report_active=(\Request::is('tax-payment-report-po'))  ? 'active' : '';
				@endphp
				<a href="javascript:void(0)">
					<span class="icon-moon icon-Setting"></span>
					<span class="text">@lang('messages.modules.reports')</span>
					<button class="open-submenu {{ ($tax_report_active=='active') ? 'open' : '' }}"></button>
				</a>
				<ul class="sub-menu  {{ ($tax_report_active=='active') ? 'open' : '' }}">
					<li class="{{ $tax_report_active }}"><a href="{{route('tax-paymnet-report-po')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.tax_paymnet_report')</span></a></li>
				</ul>
			</li>

			<li>
				@php
					$open_menu='';
					$user_active=(in_array(Request::route()->getName(), array('users.index', 'users.create', 'users.edit')))  ? 'active' : '';
					$range_active=(in_array(Request::route()->getName(), array('range.index', 'range.create', 'range.edit')))  ? 'active' : '';
					$cat_map_active=(in_array(Request::route()->getName(), array('category-mapping.index')))  ? 'active' : '';
					$box_active=(in_array(Request::route()->getName(), array('cartons.index', 'cartons.create', 'cartons.edit')))  ? 'active' : '';
					$pallet_active=(in_array(Request::route()->getName(), array('pallets.index', 'pallets.create', 'pallets.edit')))  ? 'active' : '';
					$totes_active=(in_array(Request::route()->getName(), array('totes.index', 'totes.create', 'totes.edit')))  ? 'active' : '';
					$code_active=(in_array(Request::route()->getName(), array('commodity-codes.index', 'commodity-codes.create', 'commodity-codes.edit')))  ? 'active' : '';
					$duty_active=(in_array(Request::route()->getName(), array('import-duty.index', 'import-duty.create', 'import-duty.edit')))  ? 'active' : '';
					$vat_rate_active=(\Request::is('settings/vat_rates'))  ? 'active' : '';
					$terms_active=(\Request::is('setting/terms'))  ? 'active' : '';
					$location_active=(in_array(Request::route()->getName(), array('locations.index', 'locations.create', 'locations.edit')))  ? 'active' : '';
					$reff_active=(in_array(Request::route()->getName(), array('reference.index')))  ? 'active' : '';
					$site_active=(in_array(Request::route()->getName(), array('warehouse.index', 'warehouse.create', 'warehouse.edit')))  ? 'active' : '';

					if($user_active=='active' || $range_active=='active' || $cat_map_active=='active' || $box_active=='active' || $pallet_active=='active' || $totes_active=='active' || $code_active=='active' || $duty_active=='active' || $vat_rate_active=='active' || $terms_active=='active' || $location_active=='active' || $reff_active=='active' || $site_active=='active')
					{
						$open_menu='open';
					}

				@endphp

				<a href="javascript:void(0)">
					<span class="icon-moon icon-Setting"></span>
					<span class="text">@lang('messages.modules.setting')</span>
					<button class="open-submenu {{ $open_menu }}"></button>
				</a>

				<ul class="sub-menu {{ $open_menu }}">


					<!-- <li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">@lang('messages.modules.range_management')</span></a></li>
					<li><a href="javascript:void(0)"><span class="icon-moon icon-Reference"></span><span class="text">@lang('messages.modules.category_mapping')</span></a></li> -->
					<!-- <li><a href="{{route('roles.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.roles_management')</span></a></li> -->
				 	<li class="{{ $user_active }}"><a href="{{route('users.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.user_management')</span></a></li> 
					<li class="{{ $range_active }}"><a href="{{route('range.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.range_management')</span></a></li>
					<li class="{{ $cat_map_active }}"><a href="{{route('category-mapping.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.category_mapping')</span></a></li>
					<li class="{{ $site_active }}"><a href="{{route('warehouse.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.warehouse_master')</span></a></li>
					<!-- <li><a href="javascript:void(0)"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.terms_and_condition')</span></a></li>
					<li><a href="javascript:void(0)"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.refrences')</span></a></li> -->
					<li class="{{ $box_active }}"><a href="{{route('cartons.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.box_master.box_master')</span></a>

					</li>
					<li class="{{ $pallet_active }}"><a href="{{route('pallets.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.pallets_list')</span></a></li>
					<li class="{{ $totes_active }}"><a href="{{route('totes.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.totes_master')</span></a></li>
					<li class="{{ $code_active }}"><a href="{{route('commodity-codes.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.commodity_code_master')</span></a></li>
					<li class="{{ $duty_active }}"><a href="{{route('import-duty.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.commodity_code_master.import_duty')</span></a></li>
					<li class="{{ $vat_rate_active }}"><a href="{{route('module-setting',['module_name'=>'vat_rates'])}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.settings.vat_rates')</span></a></li>
					<li class="{{ $location_active }}"><a href="{{route('locations.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.locations_list')</span></a></li>
					<li class="{{ $reff_active }}"><a href="{{route('reference.index')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.references')</span></a></li>
					<li class="{{ $terms_active }}"><a href="{{route('setting-terms')}}"><span class="icon-moon icon-Setting"></span><span class="text">@lang('messages.modules.terms_and_condition')</span></a></li>
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