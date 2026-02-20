<?php
include("php/dbconnect.php");

$error = '';
if(isset($_POST['login']))
{
$username = mysqli_real_escape_string($conn, trim($_POST['username']));
$password = mysqli_real_escape_string($conn, $_POST['password']);

if($username=='' || $password=='') {
    $error='All fields are required';
}

$sql = "select * from user where username='".$username."' and password = '".md5($password)."'";
$q = $conn->query($sql);
if($q->num_rows==1) {
    $res = $q->fetch_assoc();
    $_SESSION['rainbow_username'] = $res['username'];
    $_SESSION['rainbow_uid']      = $res['id'];
    $_SESSION['rainbow_name']     = $res['name'];
    echo '<script>window.location="index.php";</script>';
} else {
    $error = 'Invalid Username or Password';
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login — School Fees Payment System</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- Font Awesome 6 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" />
    <!-- Inter Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <!-- Modern CSS -->
    <link href="css/modern.css" rel="stylesheet" />
</head>
<body class="login-page">

    <div class="login-card">
        <!-- Logo -->
        <div class="login-logo">
            <div class="logo-icon">
                <i class="fa fa-eye"></i>
            </div>
            <div>
                <h3>School Fees System</h3>
                <p>Sign in to continue to your dashboard</p>
            </div>
        </div>

        <!-- Error -->
        <?php if($error!=''): ?>
        <div style="background:rgba(239,68,68,.08); border:1px solid rgba(239,68,68,.2); border-radius:8px; padding:10px 14px; margin-bottom:16px; font-size:13px; color:#c0392b; display:flex; align-items:center; gap:8px;">
            <i class="fa fa-circle-exclamation"></i><?php echo $error; ?>
        </div>
        <?php endif; ?>

        <!-- Form -->
        <form role="form" action="login.php" method="post">
            <div class="form-group input-group mb-3">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control" placeholder="Username" name="username" required />
            </div>

            <div class="form-group input-group mb-3">
                <span class="input-group-addon"><i class="fa fa-lock"></i></span>
                <input type="password" class="form-control" placeholder="Password" name="password" required />
            </div>

            <div style="text-align:right; margin-bottom:18px;">
                <a href="#" class="forgot-link">Forgot password?</a>
            </div>

            <button class="btn btn-primary" type="submit" name="login">
                <i class="fa fa-right-to-bracket" style="margin-right:6px;"></i>Sign In
            </button>
        </form>

        <!-- Henu branding -->
        <div style="text-align:center; margin-top:24px; padding-top:18px; border-top:1px solid #e8eaf0;">
            <p style="color:#b0b8c9; font-size:11px; margin:0;">
                <i class="fa fa-eye" style="color:#4361ee; margin-right:4px;"></i>
                Powered by <strong style="color:#8a94a6;">Henu OS Pvt. Ltd.</strong>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
