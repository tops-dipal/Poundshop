<?php include 'header.php';?>
	<div class="page-wrapper">
		<?php include 'sidebar.php';?>
		<div class="content-area">			
			<div class="content-card custom-scroll">
				<div class="content-card-header">
					<h3 class="page-title">Supplier List</h3>		
					<div class="right-items">
						<button class="btn btn-add btn-blue"><span class="icon-moon icon-Supplier"></span>Add Supplier</button>
						<button class="btn btn-add btn-red"><span class="icon-moon icon-Delete"></span>Delete Suppliers</button>
					</div>					
				</div>	
				<div class="card-flex-container d-flex">					    
    				<div class="d-flex-xs-block">
						<table id="table_id" class="display">
						    <thead>
						        <tr>
						            <th>
						            	<div class="d-flex">
							            	<label class="fancy-checkbox">
							                    <input name="agree" type="checkbox">
							                    <span><i></i></span>
							                </label>
							            	Supplier
							            </div>
						            </th>
						            <th>Account</th>
						            <th>Contact</th>
						            <th data-class-name="email">Email</th>
						            <th>Phone</th>
						            <th>City</th>
						            <th data-class-name="action">Actions</th>				
						        </tr>
						    </thead>
						    <tbody>
						        <?php include 'tr.php';?>
						        <?php include 'tr.php';?>
						        <?php include 'tr.php';?>
						        <?php include 'tr.php';?>
						        <?php include 'tr.php';?>
						        <?php include 'tr.php';?>
						    </tbody>
						</table>				
					</div>
				</div>
			</div>
		</div>
	</div>
<?php include 'footer.php';?>