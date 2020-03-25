<?php include 'header.php';?>
<div class="page-wrapper">
	<?php include 'sidebar.php';?>
	<div class="content-area">
		<div class="content-card custom-scroll">
			<div class="content-card-header">
				<h3 class="page-title">Edit Supplier</h3>
				<span class="pipe"></span>
				
				<ul class="nav nav-tabs supplier-tab" id="myTab" role="tablist">
				  <li class="nav-item">
				    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">
				    	<span class="icon-moon icon-General-Info-1"></span>General Info
				    </a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">
				    	<span class="icon-moon icon-Contact-Info-2"></span>Contact Info
				    </a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" id="payment-tab" data-toggle="tab" href="#payment" role="tab" aria-controls="payment" aria-selected="false">
						<span class="icon-moon icon-Payment-3"></span>Payment Info
				    </a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" id="ratings-tab" data-toggle="tab" href="#ratings" role="tab" aria-controls="ratings" aria-selected="false">
						<span class="icon-moon icon-Reference"></span>Ratings
				    </a>
				  </li>
				  <li class="nav-item">
				    <a class="nav-link" id="terms-tab" data-toggle="tab" href="#terms" role="tab" aria-controls="terms" aria-selected="false">
						<span class="icon-moon icon-Terms--Conditions"></span>Terms and Condition
				    </a>
				  </li>
				</ul>
			</div>
			<div class="card-flex-container">
				<div class="container-fluid">
					<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
							<div class="row">
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Supplier name<span class="asterisk">*</span></label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Account number</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Minimum P.O. amount</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Avg. lead time to ship</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Address line 1</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Address line 2</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">City</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Country</label>
										<div class="col-lg-8">
											<select class="form-control">
												<option>Select Country</option>
												<option>India</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">State</label>
										<div class="col-lg-8">
											<select class="form-control">
												<option>Select State</option>
												<option>Gujarat</option>
											</select>
										</div>
									</div>
								</div>
								<div class="col-lg-6">
									<div class="form-group row">
										<label for="inputPassword" class="col-lg-4 col-form-label">Zipcode</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" id="" placeholder="">
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
							<p>Contact Data</p>
						</div>
						<div class="tab-pane fade" id="payment" role="tabpanel" aria-labelledby="payment-tab">
							<p>Payment Data</p>
						</div>
						<div class="tab-pane fade" id="ratings" role="tabpanel" aria-labelledby="ratings-tab">
							<p>Ratings Data</p>
						</div>
						<div class="tab-pane fade" id="terms" role="tabpanel" aria-labelledby="terms-tab">
							<p>Terms and Condition Data</p>
						</div>
					</div>
				</div>
			</div>
			<div class="content-card-footer">
				<div class="button-container">
					<button class="btn btn-green btn-form">Save</button>
					<button class="btn btn-gray btn-form">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include 'footer.php';?>