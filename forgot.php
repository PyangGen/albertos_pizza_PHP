<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once('db_connection.php'); // Ensure the connection file is included
date_default_timezone_set("Asia/Manila");
$msg = "";

if (!$conn) {
    die("Database connection failed."); // Debugging step
}

if (isset($_POST['pwdrst'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $check_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
    $res = mysqli_num_rows($check_email);

    if ($res > 0) {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+5 minutes"));

        $query = "UPDATE users SET otp='$otp', otp_expiry='$expiry' WHERE email='$email'";
        mysqli_query($conn, $query);

        $message = "
        <div>
            <p><b>Hello!</b></p>
            <p>Your OTP for password reset is: <b>$otp</b></p>
            <p>This OTP will expire in 5 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        </div>";

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'almafepepania155@gmail.com';
            $mail->Password = 'tmwrasqybbbdllpw';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('almafepepania155@gmail.com', 'Basta System');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = $message;

            $mail->send();
            $_SESSION['reset_email'] = $email;
            header("Location: verify_otp.php");
            exit();
        } catch (Exception $e) {
            $msg = "Email sending failed: " . $mail->ErrorInfo;
        }
    } else {
        $msg = "No user found with that email address.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <style>
        .box {
            max-width: 400px;
            background-color: white;
            color: black;
            border: 1px solid #1860c3;
            border-radius: 5px;
            padding: 20px;
            border-color: #1860c3;
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
        <h3 align="center">Forgot Password</h3>
        <div class="box">
            <form method="post">  
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" required />
                </div>
                <div class="form-group">
                    <input type="submit" name="pwdrst" value="Send OTP" class="btn btn-orange btn-block" />
                </div>
                <p><?php echo $msg; ?></p>
            </form>
        </div>
    </div>
</body>
</html>
