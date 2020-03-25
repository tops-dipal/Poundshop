<?php include 'header.php';?>
<div class="login-container">
    <img class="login-logo img-fluid" src="img/logo.png"  />
    <div class="login-form">
      <img class="img-fluid" src="img/five-color.png" />
      <h3 class="title">Login</h3>
      <p class="subtitle">Welcome, please login to your account.</p>
      <form class="form">
        <div class="form-group">
            <label class="login-lbl">Email address</label>  
            <input type="text" placeholder="Enter email address" class="form-control" name="" />
        </div>
        <div class="form-group">
            <label class="login-lbl">Password</label>  
            <input type="password" placeholder="Enter password" class="form-control" name="" />
        </div>
        <div class="form-group text-right">
            <button class="btn btn-color-theme btn-rounded">Login</button>  
        </div>
      </form>
      <div class="forgot-url">
        <a href="forgot.php">Reset/Forgot your password</a>
      </div>

      <p class="privacy-term">Protected by Poundshop and subject to the Google <a>Privacy Policy</a> and <a>Terms of Service</a>.</p>
    </div>
</div>
<?php include 'footer.php';?>