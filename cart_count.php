<?php
session_start();
require 'db_connection.php';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM cart WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    echo $count;
    $stmt->close();
} else {
    echo 0;
}
?>
