<?php
include("php/dbconnect.php");
include("php/checklogin.php");
$error = '';
if(isset($_POST['save']))
{

$oldpassword = mysqli_real_escape_string($conn,$_POST['oldpassword']);
$newpassword = mysqli_real_escape_string($conn,$_POST['newpassword']);
$sql = "select * from user where id= '".$_SESSION['rainbow_uid']."' and password='".md5($oldpassword )."'";
$q = $conn->query($sql);
if($q->num_rows>0)
{

$sql = "update user set  password = '".md5($newpassword)."' WHERE id = '".$_SESSION['rainbow_uid']."'";
$r = $conn->query($sql);
echo '<script type="text/javascript">window.location="setting.php?act=1"; </script>';
}else
{
$error = '<div class="alert alert-danger">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Error!</strong> Wrong old password
</div>';
}

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Settings — School Fees Payment System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- Legacy CSS -->
    <link href="css/basic.css" rel="stylesheet" />
    <link href="css/custom.css" rel="stylesheet" />
    <!-- Modern CSS -->
    <link href="css/modern.css" rel="stylesheet" />

    <script src="js/jquery-1.10.2.js"></script>
    <script type="text/javascript" src="js/validation/jquery.validate.min.js"></script>
</head>
<?php
include("php/header.php");
?>
        <div id="page-wrapper">
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
                        <h1 class="page-head-line">Setting</h1>
                     
<?php
if(isset($_REQUEST['act']) &&  @$_REQUEST['act']=='1')
{
echo '<div class="alert alert-success">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>Success!</strong> Password Change Successfully.
</div>';

}
echo $error;
?>
                    </div>
                </div>
                <!-- /. ROW  -->
                <div class="row">
				
                    <div class="col-sm-8 col-sm-offset-2">
               <div class="panel panel-primary">
                        <div class="panel-heading">
                          Change Password
                        </div>
						<form action="setting.php" method="post" id="signupForm1" class="form-horizontal">
                        <div class="panel-body">
						
						
						
						
						<div class="form-group">
								<label class="col-sm-4 control-label" for="Old">Old Password</label>
								<div class="col-sm-5">
									<input type="password" class="form-control" id="oldpassword" name="oldpassword"  />
								</div>
							</div>
							
							
							<div class="form-group">
								<label class="col-sm-4 control-label" for="Password"> New Password</label>
								<div class="col-sm-5">
									 <input class="form-control" name="newpassword" id="newpassword" type="password">
								</div>
							</div>
							
							
							<div class="form-group">
								<label class="col-sm-4 control-label" for="Confirm">Confirm Password</label>
								<div class="col-sm-5">
									   <input class="form-control" name="confirmpassword" type="password">
								</div>
							</div>
						
						<div class="form-group">
								<div class="col-sm-9 col-sm-offset-4">
									<button type="submit" name="save" class="btn btn-primary">Save </button>
								</div>
							</div>
                         
                           
                           
                         
                           
                         </div>
							</form>
							
                        </div>
                            </div>
            
			
                </div>
                <!-- /. ROW  -->

            
            </div>
            <!-- /. PAGE INNER  -->
        </div>
        <!-- /. PAGE WRAPPER  -->
    </div>
    <!-- /. WRAPPER  -->

    <div id="footer-sec">
        School Fees Payment System &nbsp;|&nbsp; Powered by <a href="http://code-projects.org/" target="_blank">Code-Projects</a>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- MetisMenu -->
    <script src="js/jquery.metisMenu.js"></script>
    <script src="js/custom1.js"></script>

</body>
</html>
