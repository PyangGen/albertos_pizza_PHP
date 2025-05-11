<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $catName = $conn->real_escape_string($_POST['catName']);
    $minTime = (int)$_POST['min_time'];
    $maxTime = (int)$_POST['max_time'];
    $dateCreated = date("Y-m-d H:i:s");

    // Server-side validations
    if ($minTime > $maxTime) {
        echo '<script>alert("Minimum time cannot be greater than maximum time."); window.history.back();</script>';
        exit();
    }

    // Check for duplicate category name
    $checkQuery = "SELECT catId FROM menucategory WHERE catName = '$catName'";
    $result = $conn->query($checkQuery);

    if ($result->num_rows > 0) {
        echo '<script>alert("Category name already exists."); window.history.back();</script>';
        exit();
    }

    // Insert if not duplicate
    $sql = "INSERT INTO menucategory (catName, min_time, max_time, dateCreated)
            VALUES ('$catName', '$minTime', '$maxTime', '$dateCreated')";

    if ($conn->query($sql) === TRUE) {
        echo '<script>alert("Category added successfully."); window.location.href="admin_menu.php";</script>';
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
