<?php include 'header.php';?>
<div class="page-loader">
  <img src="img/loader.gif" width="80" alt="loader" />
</div>
<div class="login-container">
    <img class="login-logo img-fluid" src="img/logo.png"  />
    <div class="login-form">
      <img class="img-fluid" src="img/five-color.png" />
      <h3 class="title">Forgot Password?</h3>     
      <p class="subtitle">Enter your Email address below and we will send<br/> a email to reset your password</p> 
      <form class="form">
        <div class="form-group">
            <label class="login-lbl">Email address</label>  
            <input type="text" placeholder="Enter email address" class="form-control" name="" />
        </div>        
        <div class="form-group text-right">
            <button class="btn btn-color-theme btn-rounded">Submit</button>  
        </div>
      </form>    
      <div class="forgot-url">
        <a href="login.php">Back to Login</a>
      </div>       
    </div>
</div>
<?php include 'footer.php';?>