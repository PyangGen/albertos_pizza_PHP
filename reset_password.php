<?php
session_start();
include_once('connection1.php');

if (!isset($_SESSION['otp_verified'])) {
    header("Location: forgot.php");
    exit();
}

$msg = "";

if (isset($_POST['reset'])) {
    $email = $_SESSION['reset_email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $msg = "Passwords do not match!";
    } else {
        $new_password = password_hash($password, PASSWORD_DEFAULT);

        // Update password in the database
        $query = "UPDATE users SET password='$new_password', otp=NULL, otp_expiry=NULL WHERE email='$email'";
        mysqli_query($connection, $query);

        // Clear session data
        unset($_SESSION['otp_verified']);
        unset($_SESSION['reset_email']);

        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <style>
        .box {
            max-width: 400px;
            background-color: white;
            color: black;
            border: 1px solid #1860c3;
            border-radius: 5px;
            padding: 20px;
            margin: 50px auto;
            text-align: center;
        }
        .btn-orange {
            background-color:#1860c3;
            color: white;
            font-weight: bold;
        }
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">  
        <h3 align="center">Reset Password</h3>
        <div class="box">
            <form method="post">  
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="password" class="form-control" required />
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="submit" name="reset" value="Reset Password" class="btn btn-orange btn-block" />
                </div>
                <?php if (!empty($msg)): ?>
                    <p class="error"><?php echo $msg; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</body>
</html>
