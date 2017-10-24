<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>MyCillin - Registration</title>

    <link rel="icon" type="image/jpeg" href="<?php echo base_url(); ?>assets/img/logo-icon.png" />

    <!-- Bootstrap -->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/bootstrap.min.css" >

    <!-- Optional theme -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/bootstrap-theme.min.css" >

    <link href="<?php echo base_url(); ?>assets/bootpop/jquery.bootpop.css" rel="stylesheet">

    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/regis.css" >


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/bootstrap/jquery-321.min.js"></script>
    <!-- Latest compiled and minified JavaScript -->
    <script type="text/javascript" src="<?php echo base_url(); ?>assets/bootstrap/bootstrap.min.js" ></script>

    <script src="<?php echo base_url(); ?>assets/bootpop/jquery.bootpop.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>assets/own/regis.js" ></script>

  </head>
  <body>
    <div class="container">
      <h1 class="well text-center">Registration Form</h1>
      <div class="col-lg-12 well">
        <div class="row">
          <div class="col-sm-12">
            <div class="row">
              <div class="col-sm-6 form-group">
                <label>First Name</label>
                <input type="text" placeholder="Enter First Name Here.." class="form-control" id="inp-fname">
              </div>
              <div class="col-sm-6 form-group">
                <label>Last Name</label>
                <input type="text" placeholder="Enter Last Name Here.." class="form-control" id="inp-lname">
              </div>
            </div> 
            <div class="row">
              <div class="col-sm-6 form-group">
                <label>Email</label>
                <input type="text" placeholder="Email" class="form-control" id="inp-email">
              </div>
              <div class="col-sm-6 form-group">
                <label>Password</label>
                <input type="password" placeholder="Password" class="form-control" id="inp-password">
                <input type="hidden" value="<?php echo $rfid; ?>" id="inp-rfid" disabled>
              </div>
            </div>
            <button class="btn btn-lg btn-info" id="btn-submit-regis">Submit</button>         
          </div>
        </div>
      </div>
    </div>
  </body>
</html>