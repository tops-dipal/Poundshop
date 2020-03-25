<?php include 'header.php';?>
	<div class="page-wrapper">
		<?php include 'sidebar.php';?>
		<div class="content-area">			
			<div class="content-card">
				<div class="content-card-header">
					<h3 class="page-title">Buyer's Inquiry</h3>
					<span class="pipe"></span>
					<div class="btn-group stock-tab">
						<button type="button" class="btn dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="icon-moon icon-Stock"></span>Stock</button>
					  <div class="dropdown-menu dropdown-menu-right">
					    <button class="dropdown-item active" type="button"><span class="icon-moon icon-Stock"></span>Stock</button>
					    <button class="dropdown-item" type="button"><span class="icon-moon icon-Sale"></span>Sales</button>
					    <button class="dropdown-item" type="button"><span class="icon-moon icon-Supplier"></span>Suppliers</button>
					    <button class="dropdown-item" type="button"><span class="icon-moon icon-Purchse-Order"></span>P/Orders</button>
					  </div>
					</div>
					<div class="right-items">
						<div class="barcode-scanner">
							<img src="img/barcode-scanner.png" alt="">
							5264870031548
						</div>
						<ul class="product-category">
							<li>Food</li>
							<li>Drink</li>
							<li>Coffee</li>							
							<li><a href="#" class="category-filter"><span class="icon-moon icon-Filter"></span></a></li>
						</ul>
						<button class="btn btn-add btn-green"><span class="icon-moon icon-Add-PO"></span>Add to P.O</button>
					</div>
				</div>
				<div class="card-flex-container">
					<div class="custom-row-gutter-ten buyer-inquiry-status custom-scroll">
						<div class="custom-col-5">
							<div class="dashboard-card red">
								<div class="dash-card-header">
									<div class="header-icon">
										<span class="icon-moon icon-Budget"></span>
									</div>
									<h4 class="header-title">Budget</h4>
								</div>
								<ul class="dash-card-count">
									<li><span class="title">Budget Range</span><span class="count">8500</span></li>
									<li><span class="title">Actual Range</span><span class="count">9000</span></li>
									<li><span class="title">Missing Range</span><span class="count">500</span></li>								
								</ul>
							</div>
						</div>
						<div class="custom-col-5">
							<div class="dashboard-card blue">
								<div class="dash-card-header">
									<div class="header-icon">
										<span class="icon-moon icon-Stock1"></span>
									</div>
									<h4 class="header-title">Stock</h4>
								</div>
								<ul class="dash-card-count">
									<li><span class="title">In Stock</span><span class="count">8500</span></li>
									<li><span class="title">Repeat</span><span class="count">9000</span></li>
									<li><span class="title">Free Stock</span><span class="count">500</span></li>								
								</ul>
							</div>
						</div>
						<div class="custom-col-5">
							<div class="dashboard-card pink">
								<div class="dash-card-header">
									<div class="header-icon">
										<span class="icon-moon icon-Order"></span>
									</div>
									<h4 class="header-title">Order</h4>
								</div>
								<ul class="dash-card-count">
									<li><span class="title">On Order</span><span class="count">8500</span></li>
									<li><span class="title">On Hold</span><span class="count">9000</span></li>
									<li><span class="title">Allocated</span><span class="count">500</span></li>								
								</ul>
							</div>
						</div>
						<div class="custom-col-5">
							<div class="dashboard-card orange">
								<div class="dash-card-header">
									<div class="header-icon">
										<span class="icon-moon icon-Product"></span>
									</div>
									<h4 class="header-title">Product</h4>
								</div>
								<ul class="dash-card-count">
									<li><span class="title">Booked In</span><span class="count">8500</span></li>
									<li><span class="title">Disabled</span><span class="count">9000</span></li>
									<li><span class="title">Promotion</span><span class="count">500</span></li>								
									<li><span class="title">Essentials</span><span class="count">500</span></li>								
									<li><span class="title">Missing Information</span><span class="count">500</span></li>								
								</ul>
							</div>
						</div>
						<div class="custom-col-5">
							<div class="dashboard-card green">
								<div class="dash-card-header">
									<div class="header-icon">
										<span class="icon-moon icon-Brand"></span>
									</div>
									<h4 class="header-title">Brand</h4>
								</div>
								<ul class="dash-card-count">
									<li><span class="title">New</span><span class="count">8500</span></li>
									<li><span class="title">Total Brand</span><span class="count">9000</span></li>
								</ul>
							</div>
						</div>
						<div class="custom-col-half">
							<div class="dashboard-card card-bordered mini-height color-1">
								<div class="dash-card-header">
									<div class="header-icon">
										<span class="icon-moon icon-Product-Status"></span>
									</div>
									<h4 class="header-title">Product Status</h4>
								</div>
								<ul class="dash-card-count">
									<li><span class="title">Slow Moving</span><span class="count">8500</span></li>
									<li><span class="title">Seasonal</span><span class="count">9000</span></li>
									<li><span class="title">Out of Date</span><span class="count">9000</span></li>
									<li><span class="title">Number of days stock-holding</span><span class="count">9000</span></li>
								</ul>
							</div>
						</div>
						<div class="custom-col-half">
							<div class="dashboard-card card-bordered mini-height color-2">
								<div class="dash-card-header">
									<div class="header-icon">
										<span class="icon-moon icon-Margin-Rate-of-Sale"></span>
									</div>
									<h4 class="header-title">Marginal Rate Of Sales</h4>
								</div>
								<ul class="dash-card-count">
									<li><span class="title">Margin</span><span class="count">42%</span></li>
									<li><span class="title">ROS</span><span class="count">9000</span></li>
									<li><span class="title">MROS</span><span class="count">500</span></li>								
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php include 'footer.php';?>