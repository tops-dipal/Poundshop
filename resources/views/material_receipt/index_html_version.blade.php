@extends('layouts.app')
@section('title', !empty($prefix_title) ? $prefix_title." - ".env('APP_NAME') : env('APP_NAME'))

@section('content')
<div class="content-card custom-scroll">
    <div class="content-card-header">
        <h3 class="page-title">Material Receipt</h3> 
        <div class="center-items">
            <div class="d-flex flex-one align-items-center mr-4">
            	<span class="font-12-dark text-nowrap mr-3">Arrived Date:</span>	
            	<input type="text" class="form-control header-form-control w-180" name="" />
            </div>
            <div class="d-flex flex-one ml-4">
            	<p class="mr-4">
            		<span class="font-12-dark d-block">Start Date:</span>
            		<span class="font-12-dark bold">18 Dec 2019, 11:30am</span>
            	</p>
            	<p>
            		<span class="font-12-dark d-block">Complete Date:</span>
            		<span class="font-12-dark bold">20 Dec 2019, 11:30am</span>
            	</p>             	
            </div>
        </div>    
        <div class="right-items">
			<button class="btn btn-light-blue btn-header">
				<span class="icon-moon icon-Mail font-10 mr-2 ml-0"></span>Send to supplier
			</button>           
        	<button class="btn btn-green btn-header">
        		<span class="icon-moon icon-Select font-10 mr-2"></span>
        		Completed
        	</button>           
            <button id="btnFilter" class="btn btn-filter btn-header">
            	<span class="icon-moon icon-Filter"></span>
            	@lang('messages.modules.filter') <span class="filter_count"> </span><span class="icon-moon icon-Drop-Down-1"/>
            </button>
            <div class="search-filter-dropdown">
                <form class="form-horizontal form-flex" id="po-search-form">
                    <div class="form-fields">                        
                        <div class="filter-container">
                            <h2 class="title">@lang('messages.modules.filter_by')</h2>                            
                        </div>
                    </div>
                    <div class="form-buttons">
                        <input type="button" class="btn btn-gray cancle_fil" title="@lang('messages.modules.button_cancel')" value="@lang('messages.modules.button_cancel')">
                        <input type="button" class="btn btn-blue apply_fil" title="@lang('messages.modules.button_apply')" value="@lang('messages.modules.button_apply')" onclick="advanceSearch();">
                    </div>
                </form>
            </div>
        </div>                  
    </div>  
    <div class="card-flex-container d-flex pt-0">                        
        <div class="d-flex-xs-block flex-column">
            <div class="material-receipt-header pb-2">
            	<div class="bg-gray d-flex align-items-center">
	            	<span class="font-18-dark mr-5 color-red">Pending Product: <span class="font-18-dark bold color-red">254</span></span>
	            	<span class="font-12-dark mr-5 color-blue">Booking In Ref. No. <a href="#" class="bookin-ref font-12-dark bold color-blue">BR449377933</a></span>
	            	<span class="font-12-dark mr-5">Supplier: <span class="font-12-dark bold">John Richards LLC.</span></span>
	            	<span class="font-14-dark">Receiving Warehouse: <span class="font-12-dark bold">Warehouse Name Here</span></span>
	            </div>
	            <div class="d-flex align-items-center">
	            	<div class="search-product-container mr-3">
	            		<select class="form-control select-product-filter">
	            			<option>All Products</option>
	            			<option>SKU</option>
	            		</select>
	            		<input type="text" class="form-control" placeholder="search or scan with barcode, SKU, supplier SKU,product ID and product title" name="" />
	            	</div>
	            	<div>
	            		<label class="fancy-checkbox sm">
	            			<input type="checkbox" name="">
	            			<span class="font-14-dark bold"><i></i>Show Descripencies</span>
	            		</label>
	            	</div>
	            </div>
            </div>
			<div class="material-receipt-body p-2">
				<table class="table custom-table cell-align-top">
					<thead>
						<tr>							
							<td class="w-25 sorting">Product Info.</td>
							<td class="w-13 sorting_asc">Qty. Ordered</td>
							<td class="w-12 sorting_desc">Delivery Note Qty.</td>
							<td class="w-10 sorting">Qty. Rec.</td>
							<td class="w-10 sorting">Location <small class="font-10-dark d-block">Scan Barcode</small></td>
							<td class="w-10 sorting">Qty. Diff.</td>
							<td class="w-20 sorting">
								<div class="d-inline-flex position-relative">
									<label class="fancy-checkbox sm">
										<input type="checkbox" name="ids[]" class="po_item_master">
										<span><i></i></span>
									</label>
									<div class="dropdown bulk-action-dropdown">
										<button class="btn dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" title="Bulk Action">
										<span class="icon-moon icon-Drop-Down-1">
										</span></button>
										<div class="dropdown-menu dropdown-menu-right" aria-labelledby="">
											<h4 class="title">Bulk Action</h4>
											<a class="btn btn-add delete-many" title="Delete Items">
												<span class="icon-moon red icon-Delete"></span>
												Delete Items                                                        
											</a>
										</div>
									</div>
								</div>
								Action
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="border-none">
								<p class="product-title font-14-dark bold mb-3">Mens Novelty Christmas Socks All I Want For Christmas</p>
								
								<button class="btn btn-more-detail mr-3 d-inline-block" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
								    <span class="icon-moon icon-Add"></span>
								</button>		
								<button class="btn btn-blue font-12 bold px-4 py-2">Save</button>	
							</td>
							<td class="border-none">
								<p class="font-14-dark bold">50</p>								
							</td>
							<td class="border-none">								
								<input  type="text" name="" value="50" class="font-14-dark bold w-60">								
							</td>
							<td class="border-none">															
								<input  type="text" name="" value="40" class="font-14-dark bold w-60">												
							</td>
							<td class="border-none">								
								<input  type="text" name="" value="Z02X03" class="font-14-dark bold" />
								<span class="font-10-dark bold">Pallet Pick</span>
							</td>
							<td class="border-none">								
								<div class="d-flex align-items-center group-item diff-minus">									
									<span class="desc">
										<span class="font-14-dark bold">-10</span>
									</span>									
								</div>
							</td>
							<td class="border-none">
								<div class="descripencies-list">
									<label class="fancy-checkbox sm d-block">
										<input type="checkbox" name="ids[]" class="po_item_master">
										<span class="font-14-dark d-flex align-items-center"><i></i>32 Damaged<span class="icon-moon icon-Right-Arrow font-8 ml-2"></span></span>
									</label>
									<label class="fancy-checkbox sm d-block">
										<input type="checkbox" name="ids[]" class="po_item_master">
										<span class="font-14-dark d-flex align-items-center"><i></i>23 Shortage<span class="icon-moon icon-Right-Arrow font-8 ml-2"></span></span>
									</label>
								</div>
							</td>
						</tr>
						
						<tr>
							<td colspan="7" class="p-0">
								<div class="collapse" id="collapseExample">
									<table style="width: 100%;">
										<tr>
											<td class="w-25">
												<div class="d-flex mr-product-detail">
													<div class="product-tags">
														<p class="tag new">New Product</p>
														<p class="tag master">Master Product</p>
													</div>
													<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="80" height="80" alt="">
													<div class="ml-2">										
														<div class="group-item mt-4">
													        <p class="title font-14-dark mb-2">Barcode</p>
													        <div class="d-flex align-items-center">
														        <span class="desc mr-3">
														            <input  type="text" name="" class="font-14-dark bold">
														        </span>
														        <span class="icon-moon icon-Spot-Check font-18"></span>
														    </div>
													    </div>
													</div>
												</div>
												<div class="mt-2">
													<p class="stk-req-lbl">Stock Required in Pick Face <strong>100 Qty</strong></p>
												</div>
											</td>
											<td class="w-13">
												<p class="font-14-dark color-purple mt-5 mr-5">Photo Required: <span class="font-14-dark color-purple bold">Yes</span></p>			
											</td>											
											<td class="w-12">&nbsp;</td>
											<td class="w-10">
												<p class="font-14-dark bold mt-4">694</p>							
												<p class="font-14-dark bold mt-4">1</p>				
											</td>
											<td class="w-10">
												<p class="font-14-dark bold mt-4">T0015</p>							
												<p class="font-14-dark bold mt-4">Photobooth</p>			
											</td>
											<td class="w-10">&nbsp;</td>
											<td class="w-20">&nbsp;</td>
										</tr>
									</table>
									<table style="width: 100%;">
										<tr>
											<td class="border-none v-align-middle">
												<div class="d-flex align-items-center my-3">
													<span class="font-12-dark mr-5">
														SKU<span class="font-12-dark bold color-blue ml-2">IBM001506</span>
													</span>
													<span class="font-14-dark">
														Supplier SKU<span class="font-16-dark bold color-blue ml-2">SUPP001506</span>
													</span>
												</div>
											</td>
											<td class="border-none v-align-middle">
												<div class="d-flex align-items-center mr-5">																	
													<span class="font-1-dark color-purple ml-5">Checked-in By: <span class="font-12-dark color-purple bold">Nicky Taylor 24 Dec 2019, 8:15pm</span></span>
													
												</div>
											</td>
											<td class="border-none">
												<button class="btn btn-blue font-10 bold text-uppercase">Add/View Discrepancy<br> Details</button>
											</td>
										</tr>
									</table>
								    <div class="case-location-box">
								    	<div class="left">
								    		<ul>
								    			<li>
								    				<p class="font-14-dark color-purple mb-4">Bast Before Date</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="bbd">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="bbd">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    			<li>
								    				<p class="font-14-dark color-purple mb-4">Are there Inner Outer Cases?</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="outer_case">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="outer_case">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    			<li>
								    				<p class="font-14-dark color-purple bold mb-4">Variants</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="variants">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="variants">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    		</ul>
								    	</div>
										<div class="right">
											<div class="product-case-detail">
												<!-- Title Header -->
												<div class="card-titles">
													<div class="content">
														<div class="card-cols">
															<div class="barcode">
																<span class="font-14-dark bold">Barcode</span>
															</div>
															<div class="case">
																<span class="font-14-dark bold">Case</span>
															</div>
															<div class="inclide-in-cnt">
																<span class="font-14-dark bold">Include in Count</span>
															</div>
															<div class="qty-box">
																<span class="font-14-dark bold">Qty./Box</span>
															</div>
															<div class="box">
																<span class="font-14-dark bold">No of Boxes</span>
															</div>
															<div class="total">
																<span class="font-14-dark bold">Total</span>
															</div>
															<div class="blank">&nbsp;</div>
														</div>
													</div>
													<div class="add-more">&nbsp;</div>
												</div>
												<!-- Title Header END -->

												<!-- Inner/Outer Combine -->
												<div class="card-outer-inner">
													<div class="content">
														<div class="outer">
															<div class="card-cols">
																<div class="barcode">
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																		<span class="icon-moon icon-Spot-Check font-18"></span>
																	</div>
																</div>
																<div class="case">
																	<span class="font-14-dark">Outer Case</span>
																</div>
																<div class="inclide-in-cnt">
																	<label class="fancy-radio sm mr-3">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>Yes</span>
																	</label>
																	<label class="fancy-radio sm">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>No</span>
																	</label>
																</div>
																<div class="qty-box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="total">
																	<span class="font-14-dark">360</span>
																</div>
															</div>
															<!-- Multiple Location Row  -->
															<div class="product-location-row">
																<div class="d-flex mt-2 align-items-center">
																	<span class="font-14-dark">Move</span>
																	<input type="text" class="form-control w-60 mx-3" name="">
																	<span class="font-14-dark">to Location</span>
																	<input type="text" class="form-control w-90 mx-3" name="">
																	<div class="best-date ml-5">
																		<span class="font-14-dark">Best Before Date</span>
																		<input type="text" class="form-control w-120 mx-3" name="">
																	</div>
																	<a href="javascript:void(0)">
																		<span class="icon-moon icon-Add font-10"></span>
																	</a>
																</div>
															</div>
															<!-- Multiple Location Row END  -->
															<!-- Multiple Location Row  -->
															<div class="product-location-row">
																<div class="d-flex mt-2 align-items-center">
																	<span class="font-14-dark">Move</span>
																	<input type="text" class="form-control w-60 mx-3" name="">
																	<span class="font-14-dark">to Location</span>
																	<input type="text" class="form-control w-90 mx-3" name="">
																	<div class="best-date ml-5">
																		<span class="font-14-dark">Best Before Date</span>
																		<input type="text" class="form-control w-120 mx-3" name="">
																	</div>
																	<a href="javascript:void(0)">
																		<span class="icon-moon icon-Add font-10"></span>
																	</a>
																</div>
															</div>
															<!-- Multiple Location Row END  -->
														</div>														
														<div class="inner">
															<div class="card-cols">
																<div class="barcode">
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																		<span class="icon-moon icon-Spot-Check font-18"></span>
																	</div>
																</div>
																<div class="case">
																	<span class="font-14-dark">Inner Case</span>
																</div>
																<div class="inclide-in-cnt">
																	<label class="fancy-radio sm mr-3">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>Yes</span>
																	</label>
																	<label class="fancy-radio sm">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>No</span>
																	</label>
																</div>
																<div class="qty-box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="box">&nbsp;</div>
																<div class="total">&nbsp;</div>
															</div>
														</div>
													</div>
													<div class="add-more">
														<a href="javascript:void(0)">
															<span class="icon-moon icon-Add font-10"></span>
														</a>
													</div>
												</div>
												<!-- Inner/Outer Combine END -->

												<!-- Loose product -->
												<div class="card-loose mt-2">
													<div class="content">
														<div class="card-cols">
															<div class="barcode">
																<div class="d-flex align-items-center">
																	<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																	<span class="icon-moon icon-Spot-Check font-18"></span>
																</div>
															</div>
															<div class="case">
																<span class="font-14-dark">Loose</span>
															</div>
															<div class="inclide-in-cnt">&nbsp;</div>
															<div class="qty-box">
																<input type="text" class="form-control w-60" name="">
															</div>
															<div class="box">&nbsp;</div>
															<div class="total">
																<span class="font-14-dark">35</span>
															</div>
														</div>
													</div>
													<div class="add-more">
														<a href="javascript:void(0)">
															<span class="icon-moon icon-Add font-10"></span>
														</a>
													</div>
												</div>
												<!-- Loose product END -->
											</div>
											<div class="product-varient mt-2">
												<span class="font-14-dark bold">Variants</span>
												<button type="button" class="btn btn-green font-12 ml-4" data-toggle="modal" data-target="#myModal">
												  Manage Varients
												</button>

												<!-- The Modal -->
												<div class="modal fade" id="myModal">
												  <div class="custom-modal modal-dialog modal-lg">
												    <div class="modal-content">

												      <!-- Modal Header -->
												       <div class="modal-header align-items-center">
										                <h5 class="modal-title" id="exampleModalLabel">Add Variations</h5>
										                <div>
										                    <button type="button" class="btn btn-blue font-12 px-3" data-dismiss="modal">
										                    	Add Products
										                    </button>
										                    <button type="button" class="btn btn-gray font-12 px-3 ml-2" data-dismiss="modal">@lang('messages.common.cancel')</button>
										                    <button type="button" class="btn btn-green font-12 px-3 ml-2">@lang('messages.common.submit')</button>
										                </div>
										            </div>

												      <!-- Modal body -->
												      <div class="modal-body">
												        <p class="font-16-dark mt-2 mb-4 bold">Mens Novelty Christmas Socks All I Want For Christmas</span></p>
												        <div class="row align-items-center my-3">
												        	<div class="col-lg-4">
												        		<select class="form-control">
													        		<option>Variation Type</option>
													        		<option>Color</option>
													        		<option>Size</option>
													        		<option>Color - Size</option>
													        	</select>	
												        	</div>
												        	<div class="col-lg-8">													        	
													        	<label class="fancy-checkbox">
													        		<input type="checkbox" name="">
													        		<span><i></i>All Variants can be put in same location.</span>
													        	</label>
													        </div>
												        </div>
												        <table class="custom-table">
												        	<thead>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>Image</td>
												        			<td>Size</td>
												        			<td>Color</td>
												        			<td class="w-30">SKU</td>
												        			<td>Action</td>
												        		</tr>
												        	</thead>
												        	<tbody>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        	</tbody>
												        </table>

												      </div>

												    </div>
												  </div>
												</div>
											</div>
											<div class="form-group mt-4">
												<label class="font-14-dark bold mb-2">Comments</label>
												<textarea class="form-control" placeholder="Comment here" rows="4"></textarea>
											</div>
										</div>
								    </div>
								</div>
							</td>
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td class="border-none">
								<p class="product-title font-14-dark bold mb-3">Yazoo Strawberry Milk Drink 4 Pack 200ml OUT OF DATE 04/01/2020</p>
								
								<button class="btn btn-more-detail mr-3 d-inline-block" type="button" data-toggle="collapse" data-target="#collapseExample1" aria-expanded="false" aria-controls="collapseExample">
								    <span class="icon-moon icon-Add"></span>
								</button>		
								<button class="btn btn-blue font-12 bold px-4 py-2">Save</button>	
							</td>
							<td class="border-none">
								<p class="font-14-dark bold">80</p>								
							</td>
							<td class="border-none">								
								<input  type="text" name="" value="80" class="font-14-dark bold w-60">								
							</td>
							<td class="border-none">															
								<input  type="text" name="" value="80" class="font-14-dark bold w-60">												
							</td>
							<td class="border-none">								
								<input  type="text" name="" value="C02V06" class="font-14-dark bold" />
								<span class="font-10-dark bold">Pallet Pick</span>
							</td>
							<td class="border-none">								
								<div class="d-flex align-items-center group-item">									
									<span class="desc">
										<span class="font-14-dark bold">0</span>
									</span>									
								</div>
							</td>
							<td class="border-none">
								<div class="descripencies-list">
									<label class="fancy-checkbox sm d-block">
										<input type="checkbox" name="ids[]" class="po_item_master">
										<span class="font-14-dark d-flex align-items-center"><i></i>32 Damaged<span class="icon-moon icon-Right-Arrow font-8 ml-2"></span></span>
									</label>
									<label class="fancy-checkbox sm d-block">
										<input type="checkbox" name="ids[]" class="po_item_master">
										<span class="font-14-dark d-flex align-items-center"><i></i>23 Shortage<span class="icon-moon icon-Right-Arrow font-8 ml-2"></span></span>
									</label>
								</div>
							</td>
						</tr>
						
						<tr>
							<td colspan="7" class="p-0">
								<div class="collapse" id="collapseExample1">
									<table style="width: 100%;">
										<tr>
											<td class="w-25">
												<div class="d-flex mr-product-detail">
													<div class="product-tags">
														<p class="tag new">New Product</p>
														<p class="tag master">Master Product</p>
													</div>
													<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="80" height="80" alt="">
													<div class="ml-2">										
														<div class="group-item mt-4">
													        <p class="title font-14-dark mb-2">Barcode</p>
													        <div class="d-flex align-items-center">
														        <span class="desc mr-3">
														            <input  type="text" name="" class="font-14-dark bold">
														        </span>
														        <span class="icon-moon icon-Spot-Check font-18"></span>
														    </div>
													    </div>
													</div>
												</div>
												<div class="mt-2">
													<p class="stk-req-lbl">Stock Required in Pick Face <strong>100 Qty</strong></p>
												</div>
											</td>
											<td class="w-13">
												<p class="font-14-dark color-purple mt-5 mr-5">Photo Required: <span class="font-14-dark color-purple bold">Yes</span></p>			
											</td>											
											<td class="w-12">&nbsp;</td>
											<td class="w-10">
												<p class="font-14-dark bold mt-4">694</p>							
												<p class="font-14-dark bold mt-4">1</p>				
											</td>
											<td class="w-10">
												<p class="font-14-dark bold mt-4">T0015</p>							
												<p class="font-14-dark bold mt-4">Photobooth</p>			
											</td>
											<td class="w-10">&nbsp;</td>
											<td class="w-20">&nbsp;</td>
										</tr>
									</table>
									<table style="width: 100%;">
										<tr>
											<td class="border-none v-align-middle">
												<div class="d-flex align-items-center my-3">
													<span class="font-12-dark mr-5">
														SKU<span class="font-12-dark bold color-blue ml-2">IBM001506</span>
													</span>
													<span class="font-14-dark">
														Supplier SKU<span class="font-16-dark bold color-blue ml-2">SUPP001506</span>
													</span>
												</div>
											</td>
											<td class="border-none v-align-middle">
												<div class="d-flex align-items-center mr-5">																	
													<span class="font-1-dark color-purple ml-5">Checked-in By: <span class="font-12-dark color-purple bold">Nicky Taylor 24 Dec 2019, 8:15pm</span></span>
													
												</div>
											</td>
											<td class="border-none">
												<button class="btn btn-blue font-10 bold text-uppercase">Add/View Discrepancy<br> Details</button>
											</td>
										</tr>
									</table>
								    <div class="case-location-box">
								    	<div class="left">
								    		<ul>
								    			<li>
								    				<p class="font-14-dark color-purple mb-4">Bast Before Date</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="bbd">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="bbd">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    			<li>
								    				<p class="font-14-dark color-purple mb-4">Are there Inner Outer Cases?</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="outer_case">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="outer_case">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    			<li>
								    				<p class="font-14-dark color-purple bold mb-4">Variants</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="variants">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="variants">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    		</ul>
								    	</div>
										<div class="right">
											<div class="product-case-detail">
												<!-- Title Header -->
												<div class="card-titles">
													<div class="content">
														<div class="card-cols">
															<div class="barcode">
																<span class="font-14-dark bold">Barcode</span>
															</div>
															<div class="case">
																<span class="font-14-dark bold">Case</span>
															</div>
															<div class="inclide-in-cnt">
																<span class="font-14-dark bold">Include in Count</span>
															</div>
															<div class="qty-box">
																<span class="font-14-dark bold">Qty./Box</span>
															</div>
															<div class="box">
																<span class="font-14-dark bold">No of Boxes</span>
															</div>
															<div class="total">
																<span class="font-14-dark bold">Total</span>
															</div>
															<div class="blank">&nbsp;</div>
														</div>
													</div>
													<div class="add-more">&nbsp;</div>
												</div>
												<!-- Title Header END -->

												<!-- Inner/Outer Combine -->
												<div class="card-outer-inner">
													<div class="content">
														<div class="outer">
															<div class="card-cols">
																<div class="barcode">
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																		<span class="icon-moon icon-Spot-Check font-18"></span>
																	</div>
																</div>
																<div class="case">
																	<span class="font-14-dark">Outer Case</span>
																</div>
																<div class="inclide-in-cnt">
																	<label class="fancy-radio sm mr-3">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>Yes</span>
																	</label>
																	<label class="fancy-radio sm">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>No</span>
																	</label>
																</div>
																<div class="qty-box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="total">
																	<span class="font-14-dark">360</span>
																</div>
															</div>
															<!-- Multiple Location Row  -->
															<div class="product-location-row">
																<div class="d-flex mt-2 align-items-center">
																	<span class="font-14-dark">Move</span>
																	<input type="text" class="form-control w-60 mx-3" name="">
																	<span class="font-14-dark">to Location</span>
																	<input type="text" class="form-control w-90 mx-3" name="">
																	<div class="best-date ml-5">
																		<span class="font-14-dark">Best Before Date</span>
																		<input type="text" class="form-control w-120 mx-3" name="">
																	</div>
																	<a href="javascript:void(0)">
																		<span class="icon-moon icon-Add font-10"></span>
																	</a>
																</div>
															</div>
															<!-- Multiple Location Row END  -->
															<!-- Multiple Location Row  -->
															<div class="product-location-row">
																<div class="d-flex mt-2 align-items-center">
																	<span class="font-14-dark">Move</span>
																	<input type="text" class="form-control w-60 mx-3" name="">
																	<span class="font-14-dark">to Location</span>
																	<input type="text" class="form-control w-90 mx-3" name="">
																	<div class="best-date ml-5">
																		<span class="font-14-dark">Best Before Date</span>
																		<input type="text" class="form-control w-120 mx-3" name="">
																	</div>
																	<a href="javascript:void(0)">
																		<span class="icon-moon icon-Add font-10"></span>
																	</a>
																</div>
															</div>
															<!-- Multiple Location Row END  -->
														</div>														
														<div class="inner">
															<div class="card-cols">
																<div class="barcode">
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																		<span class="icon-moon icon-Spot-Check font-18"></span>
																	</div>
																</div>
																<div class="case">
																	<span class="font-14-dark">Inner Case</span>
																</div>
																<div class="inclide-in-cnt">
																	<label class="fancy-radio sm mr-3">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>Yes</span>
																	</label>
																	<label class="fancy-radio sm">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>No</span>
																	</label>
																</div>
																<div class="qty-box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="box">&nbsp;</div>
																<div class="total">&nbsp;</div>
															</div>
														</div>
													</div>
													<div class="add-more">
														<a href="javascript:void(0)">
															<span class="icon-moon icon-Add font-10"></span>
														</a>
													</div>
												</div>
												<!-- Inner/Outer Combine END -->

												<!-- Loose product -->
												<div class="card-loose mt-2">
													<div class="content">
														<div class="card-cols">
															<div class="barcode">
																<div class="d-flex align-items-center">
																	<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																	<span class="icon-moon icon-Spot-Check font-18"></span>
																</div>
															</div>
															<div class="case">
																<span class="font-14-dark">Loose</span>
															</div>
															<div class="inclide-in-cnt">&nbsp;</div>
															<div class="qty-box">
																<input type="text" class="form-control w-60" name="">
															</div>
															<div class="box">&nbsp;</div>
															<div class="total">
																<span class="font-14-dark">35</span>
															</div>
														</div>
													</div>
													<div class="add-more">
														<a href="javascript:void(0)">
															<span class="icon-moon icon-Add font-10"></span>
														</a>
													</div>
												</div>
												<!-- Loose product END -->
											</div>
											<div class="product-varient mt-2">
												<span class="font-14-dark bold">Variants</span>
												<button type="button" class="btn btn-green font-12 ml-4" data-toggle="modal" data-target="#myModal">
												  Manage Varients
												</button>

												<!-- The Modal -->
												<div class="modal fade" id="myModal">
												  <div class="custom-modal modal-dialog modal-lg">
												    <div class="modal-content">

												      <!-- Modal Header -->
												       <div class="modal-header align-items-center">
										                <h5 class="modal-title" id="exampleModalLabel">Add Variations</h5>
										                <div>
										                    <button type="button" class="btn btn-blue font-12 px-3" data-dismiss="modal">
										                    	Add Products
										                    </button>
										                    <button type="button" class="btn btn-gray font-12 px-3 ml-2" data-dismiss="modal">@lang('messages.common.cancel')</button>
										                    <button type="button" class="btn btn-green font-12 px-3 ml-2">@lang('messages.common.submit')</button>
										                </div>
										            </div>

												      <!-- Modal body -->
												      <div class="modal-body">
												        <p class="font-16-dark mt-2 mb-4 bold">Mens Novelty Christmas Socks All I Want For Christmas</span></p>
												        <div class="row align-items-center my-3">
												        	<div class="col-lg-4">
												        		<select class="form-control">
													        		<option>Variation Type</option>
													        		<option>Color</option>
													        		<option>Size</option>
													        		<option>Color - Size</option>
													        	</select>	
												        	</div>
												        	<div class="col-lg-8">													        	
													        	<label class="fancy-checkbox">
													        		<input type="checkbox" name="">
													        		<span><i></i>All Variants can be put in same location.</span>
													        	</label>
													        </div>
												        </div>
												        <table class="custom-table">
												        	<thead>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>Image</td>
												        			<td>Size</td>
												        			<td>Color</td>
												        			<td class="w-30">SKU</td>
												        			<td>Action</td>
												        		</tr>
												        	</thead>
												        	<tbody>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        	</tbody>
												        </table>

												      </div>

												    </div>
												  </div>
												</div>
											</div>
											<div class="form-group mt-4">
												<label class="font-14-dark bold mb-2">Comments</label>
												<textarea class="form-control" placeholder="Comment here" rows="4"></textarea>
											</div>
										</div>
								    </div>
								</div>
							</td>
						</tr>
					</tbody>
					<tbody>
						<tr>
							<td class="border-none">
								<p class="product-title font-14-dark bold mb-3">Original Source Hand Wash Coconut And Shea Butter 250ml</p>
								
								<button class="btn btn-more-detail mr-3 d-inline-block" type="button" data-toggle="collapse" data-target="#collapseExample2" aria-expanded="false" aria-controls="collapseExample">
								    <span class="icon-moon icon-Add"></span>
								</button>		
								<button class="btn btn-blue font-12 bold px-4 py-2">Save</button>	
							</td>
							<td class="border-none">
								<p class="font-14-dark bold">100</p>								
							</td>
							<td class="border-none">								
								<input  type="text" name="" value="100" class="font-14-dark bold w-60">								
							</td>
							<td class="border-none">															
								<input  type="text" name="" value="105" class="font-14-dark bold w-60">												
							</td>
							<td class="border-none">								
								<input  type="text" name="" value="A01B03" class="font-14-dark bold" />
								<span class="font-10-dark bold">Pallet Pick</span>
							</td>
							<td class="border-none">								
								<div class="d-flex align-items-center group-item diff-plus">									
									<span class="desc">
										<span class="font-14-dark bold">5</span>
									</span>									
								</div>
							</td>
							<td class="border-none">
								<div class="descripencies-list">
									<label class="fancy-checkbox sm d-block">
										<input type="checkbox" name="ids[]" class="po_item_master">
										<span class="font-14-dark d-flex align-items-center"><i></i>32 Damaged<span class="icon-moon icon-Right-Arrow font-8 ml-2"></span></span>
									</label>
									<label class="fancy-checkbox sm d-block">
										<input type="checkbox" name="ids[]" class="po_item_master">
										<span class="font-14-dark d-flex align-items-center"><i></i>23 Shortage<span class="icon-moon icon-Right-Arrow font-8 ml-2"></span></span>
									</label>
								</div>
							</td>
						</tr>
						
						<tr>
							<td colspan="7" class="p-0">
								<div class="collapse" id="collapseExample2">
									<table style="width: 100%;">
										<tr>
											<td class="w-25">
												<div class="d-flex mr-product-detail">
													<div class="product-tags">
														<p class="tag new">New Product</p>
														<p class="tag master">Master Product</p>
													</div>
													<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" width="80" height="80" alt="">
													<div class="ml-2">										
														<div class="group-item mt-4">
													        <p class="title font-14-dark mb-2">Barcode</p>
													        <div class="d-flex align-items-center">
														        <span class="desc mr-3">
														            <input  type="text" name="" class="font-14-dark bold">
														        </span>
														        <span class="icon-moon icon-Spot-Check font-18"></span>
														    </div>
													    </div>
													</div>
												</div>
												<div class="mt-2">
													<p class="stk-req-lbl">Stock Required in Pick Face <strong>100 Qty</strong></p>
												</div>
											</td>
											<td class="w-13">
												<p class="font-14-dark color-purple mt-5 mr-5">Photo Required: <span class="font-14-dark color-purple bold">Yes</span></p>			
											</td>											
											<td class="w-12">&nbsp;</td>
											<td class="w-10">
												<p class="font-14-dark bold mt-4">694</p>							
												<p class="font-14-dark bold mt-4">1</p>				
											</td>
											<td class="w-10">
												<p class="font-14-dark bold mt-4">T0015</p>							
												<p class="font-14-dark bold mt-4">Photobooth</p>			
											</td>
											<td class="w-10">&nbsp;</td>
											<td class="w-20">&nbsp;</td>
										</tr>
									</table>
									<table style="width: 100%;">
										<tr>
											<td class="border-none v-align-middle">
												<div class="d-flex align-items-center my-3">
													<span class="font-12-dark mr-5">
														SKU<span class="font-12-dark bold color-blue ml-2">IBM001506</span>
													</span>
													<span class="font-14-dark">
														Supplier SKU<span class="font-16-dark bold color-blue ml-2">SUPP001506</span>
													</span>
												</div>
											</td>
											<td class="border-none v-align-middle">
												<div class="d-flex align-items-center mr-5">																	
													<span class="font-1-dark color-purple ml-5">Checked-in By: <span class="font-12-dark color-purple bold">Nicky Taylor 24 Dec 2019, 8:15pm</span></span>
													
												</div>
											</td>
											<td class="border-none">
												<button class="btn btn-blue font-10 bold text-uppercase">Add/View Discrepancy<br> Details</button>
											</td>
										</tr>
									</table>
								    <div class="case-location-box">
								    	<div class="left">
								    		<ul>
								    			<li>
								    				<p class="font-14-dark color-purple mb-4">Bast Before Date</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="bbd">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="bbd">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    			<li>
								    				<p class="font-14-dark color-purple mb-4">Are there Inner Outer Cases?</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="outer_case">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="outer_case">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    			<li>
								    				<p class="font-14-dark color-purple bold mb-4">Variants</p>
								    				<label class="fancy-radio sm mr-5">
								    					<input type="radio" name="variants">
								    					<span><i></i> Yes</span>
								    				</label>
								    				<label class="fancy-radio sm">
								    					<input type="radio" name="variants">
								    					<span><i></i> No</span>
								    				</label>
								    			</li>
								    		</ul>
								    	</div>
										<div class="right">
											<div class="product-case-detail">
												<!-- Title Header -->
												<div class="card-titles">
													<div class="content">
														<div class="card-cols">
															<div class="barcode">
																<span class="font-14-dark bold">Barcode</span>
															</div>
															<div class="case">
																<span class="font-14-dark bold">Case</span>
															</div>
															<div class="inclide-in-cnt">
																<span class="font-14-dark bold">Include in Count</span>
															</div>
															<div class="qty-box">
																<span class="font-14-dark bold">Qty./Box</span>
															</div>
															<div class="box">
																<span class="font-14-dark bold">No of Boxes</span>
															</div>
															<div class="total">
																<span class="font-14-dark bold">Total</span>
															</div>
															<div class="blank">&nbsp;</div>
														</div>
													</div>
													<div class="add-more">&nbsp;</div>
												</div>
												<!-- Title Header END -->

												<!-- Inner/Outer Combine -->
												<div class="card-outer-inner">
													<div class="content">
														<div class="outer">
															<div class="card-cols">
																<div class="barcode">
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																		<span class="icon-moon icon-Spot-Check font-18"></span>
																	</div>
																</div>
																<div class="case">
																	<span class="font-14-dark">Outer Case</span>
																</div>
																<div class="inclide-in-cnt">
																	<label class="fancy-radio sm mr-3">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>Yes</span>
																	</label>
																	<label class="fancy-radio sm">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>No</span>
																	</label>
																</div>
																<div class="qty-box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="total">
																	<span class="font-14-dark">360</span>
																</div>
															</div>
															<!-- Multiple Location Row  -->
															<div class="product-location-row">
																<div class="d-flex mt-2 align-items-center">
																	<span class="font-14-dark">Move</span>
																	<input type="text" class="form-control w-60 mx-3" name="">
																	<span class="font-14-dark">to Location</span>
																	<input type="text" class="form-control w-90 mx-3" name="">
																	<div class="best-date ml-5">
																		<span class="font-14-dark">Best Before Date</span>
																		<input type="text" class="form-control w-120 mx-3" name="">
																	</div>
																	<a href="javascript:void(0)">
																		<span class="icon-moon icon-Add font-10"></span>
																	</a>
																</div>
															</div>
															<!-- Multiple Location Row END  -->
															<!-- Multiple Location Row  -->
															<div class="product-location-row">
																<div class="d-flex mt-2 align-items-center">
																	<span class="font-14-dark">Move</span>
																	<input type="text" class="form-control w-60 mx-3" name="">
																	<span class="font-14-dark">to Location</span>
																	<input type="text" class="form-control w-90 mx-3" name="">
																	<div class="best-date ml-5">
																		<span class="font-14-dark">Best Before Date</span>
																		<input type="text" class="form-control w-120 mx-3" name="">
																	</div>
																	<a href="javascript:void(0)">
																		<span class="icon-moon icon-Add font-10"></span>
																	</a>
																</div>
															</div>
															<!-- Multiple Location Row END  -->
														</div>														
														<div class="inner">
															<div class="card-cols">
																<div class="barcode">
																	<div class="d-flex align-items-center">
																		<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																		<span class="icon-moon icon-Spot-Check font-18"></span>
																	</div>
																</div>
																<div class="case">
																	<span class="font-14-dark">Inner Case</span>
																</div>
																<div class="inclide-in-cnt">
																	<label class="fancy-radio sm mr-3">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>Yes</span>
																	</label>
																	<label class="fancy-radio sm">
																		<input type="radio" name="inc_in" />
																		<span class="font-12-dark"><i></i>No</span>
																	</label>
																</div>
																<div class="qty-box">
																	<input type="text" class="form-control w-60" name="">
																</div>
																<div class="box">&nbsp;</div>
																<div class="total">&nbsp;</div>
															</div>
														</div>
													</div>
													<div class="add-more">
														<a href="javascript:void(0)">
															<span class="icon-moon icon-Add font-10"></span>
														</a>
													</div>
												</div>
												<!-- Inner/Outer Combine END -->

												<!-- Loose product -->
												<div class="card-loose mt-2">
													<div class="content">
														<div class="card-cols">
															<div class="barcode">
																<div class="d-flex align-items-center">
																	<input type="text" class="form-control font-14-dark bold mr-3"  name="">
																	<span class="icon-moon icon-Spot-Check font-18"></span>
																</div>
															</div>
															<div class="case">
																<span class="font-14-dark">Loose</span>
															</div>
															<div class="inclide-in-cnt">&nbsp;</div>
															<div class="qty-box">
																<input type="text" class="form-control w-60" name="">
															</div>
															<div class="box">&nbsp;</div>
															<div class="total">
																<span class="font-14-dark">35</span>
															</div>
														</div>
													</div>
													<div class="add-more">
														<a href="javascript:void(0)">
															<span class="icon-moon icon-Add font-10"></span>
														</a>
													</div>
												</div>
												<!-- Loose product END -->
											</div>
											<div class="product-varient mt-2">
												<span class="font-14-dark bold">Variants</span>
												<button type="button" class="btn btn-green font-12 ml-4" data-toggle="modal" data-target="#myModal">
												  Manage Varients
												</button>

												<!-- The Modal -->
												<div class="modal fade" id="myModal">
												  <div class="custom-modal modal-dialog modal-lg">
												    <div class="modal-content">

												      <!-- Modal Header -->
												       <div class="modal-header align-items-center">
										                <h5 class="modal-title" id="exampleModalLabel">Add Variations</h5>
										                <div>
										                    <button type="button" class="btn btn-blue font-12 px-3" data-dismiss="modal">
										                    	Add Products
										                    </button>
										                    <button type="button" class="btn btn-gray font-12 px-3 ml-2" data-dismiss="modal">@lang('messages.common.cancel')</button>
										                    <button type="button" class="btn btn-green font-12 px-3 ml-2">@lang('messages.common.submit')</button>
										                </div>
										            </div>

												      <!-- Modal body -->
												      <div class="modal-body">
												        <p class="font-16-dark mt-2 mb-4 bold">Mens Novelty Christmas Socks All I Want For Christmas</span></p>
												        <div class="row align-items-center my-3">
												        	<div class="col-lg-4">
												        		<select class="form-control">
													        		<option>Variation Type</option>
													        		<option>Color</option>
													        		<option>Size</option>
													        		<option>Color - Size</option>
													        	</select>	
												        	</div>
												        	<div class="col-lg-8">													        	
													        	<label class="fancy-checkbox">
													        		<input type="checkbox" name="">
													        		<span><i></i>All Variants can be put in same location.</span>
													        	</label>
													        </div>
												        </div>
												        <table class="custom-table">
												        	<thead>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>Image</td>
												        			<td>Size</td>
												        			<td>Color</td>
												        			<td class="w-30">SKU</td>
												        			<td>Action</td>
												        		</tr>
												        	</thead>
												        	<tbody>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        		<tr>
												        			<td>
												        				<label class="fancy-checkbox">
															        		<input type="checkbox" name="">
															        		<span><i></i></span>
															        	</label>
												        			</td>
												        			<td>
												        				<img src="http://3.10.236.168/magento/pub/media/catalog/product/w/b/wb03-purple-0.jpg" height="80" width="80" alt="" />
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" name="">
												        			</td>
												        			<td>
												        				<input type="text" class="form-control" disabled name="">
												        			</td>
												        			<td>
												        				<a class="btn-delete" href="javascript:;">
												        					<span class="icon-moon icon-Delete"></span>
												        				</a>
												        			</td>
												        		</tr>
												        	</tbody>
												        </table>

												      </div>

												    </div>
												  </div>
												</div>
											</div>
											<div class="form-group mt-4">
												<label class="font-14-dark bold mb-2">Comments</label>
												<textarea class="form-control" placeholder="Comment here" rows="4"></textarea>
											</div>
										</div>
								    </div>
								</div>
							</td>
						</tr>
					</tbody>
				</table>				
			</div>
            <div class="material-receipt-footer">
            	<div>
            		<span class="font-12-dark">Showing 1 to 25 of 298</span>
            	</div>
            </div>
        </div>
    </div>
    <div class="checklist-container">
    	<a href="javascript:void(0)" class="btn-checklist-toggle">
    		<span class="icon-moon icon-Spot-Check font-18"></span>
    	</a>    	
    	<div class="checklist-detail">
    			<h3 class="title">Supplier Delivery Note</h3>
    			<div class="d-flex align-items-center mb-4">
	    			<span class="font-14-dark mr-2">Delivery Note No.</span>
	    			<span class="font-14-dark bold">BRC1321564893612</span>
	    		</div>

    			<div class="upload-img-container mb-5">
    				<figure>
    					<img src="../public/img/no-img-black.png">	
	    				<div class="dn-file">
	    					<input type="file" id="dn_file" name="">
	    					<label for="dn_file">Choose File</label>
	    				</div> 
    				</figure>    				   				
    			</div>
    			<h3 class="title">QC Checklist</h3>
    			<form>
    				<div class="form-group">
    					<label class="font-12-dark mb-2">Select Checklist</label>
    					<div>
	    					<select class="form-control custom-select-search" multiple="">
	    						<option>Select Option</option>
	    						<option>Standard Checklist</option>
	    						<option>Standard CL 2</option>
	    						<option>Standard CL 3</option>
	    					</select>
	    				</div>
    				</div>
    				<div class="form-group">
    					<label class="fancy-checkbox mt-3">
    						<input type="checkbox" name="">
    						<span class="font-14-dark"><i></i>Verify each part is visually acceptable.</span>
    					</label>
    					<label class="fancy-checkbox mt-3">
    						<input type="checkbox" name="">
    						<span class="font-14-dark"><i></i>Verify receipt of a Material Test Report</span>
    					</label>
    					<label class="fancy-checkbox mt-3">
    						<input type="checkbox" name="">
    						<span class="font-14-dark"><i></i>This Checklist item is not Followed</span>
    					</label>
    				</div>
    				<div class="form-group">
    					<label class="font-14-dark bold mb-2">Comments</label>
    					<textarea rows="4" class="form-control" placeholder="comment here"></textarea>
    				</div>
    				<div class="upload-img-container mb-2">
	    				<figure>
	    					<img src="../public/img/no-img-black.png">	
		    				<div class="dn-file">
		    					<input type="file" id="dn_file" name="">
		    					<label for="dn_file">Choose File</label>
		    				</div> 
	    				</figure>    				   				
	    			</div>
    			</form>
    	</div>
    </div>
</div>
@endsection

@section('script')
	<script type="text/javascript" src="{{asset('js/material_receipt/index.js')}}"></script>
@endsection
