<?php
session_start();
include_once('db_connection.php');

$msg = "";

if(isset($_POST['verify'])) {
    $email = $_SESSION['reset_email'];
    $otp = mysqli_real_escape_string($conn, $_POST['otp']);

    // Check OTP validity
    $check_otp = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND otp='$otp' AND otp_expiry > NOW()");
    
    if(mysqli_num_rows($check_otp) > 0) {
        // OTP is valid, allow password reset
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit();
    } else {
        $msg = "Invalid or expired OTP!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Verify OTP</title>
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
            background-color: #1860c3;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">  
        <h3 align="center">Verify OTP</h3>
        <div class="box">
            <form method="post">  
                <div class="form-group">
                    <label>Enter OTP</label>
                    <input type="text" name="otp" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="submit" name="verify" value="Verify OTP" class="btn btn-orange btn-block" />
                </div>
                <p><?php echo $msg; ?></p>
            </form>
        </div>
    </div>
</body>
</html>
