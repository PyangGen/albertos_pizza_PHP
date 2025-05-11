<?php
$host = "localhost";  // or your server name
$user = "root";       // your database username
$pass = "";           // your database password (empty for default XAMPP)
$dbname = "restaurant"; // your database name

$connection = mysqli_connect($host, $user, $pass, $dbname);

// Check connection
if (!$connection) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
