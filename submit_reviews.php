<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = $_POST['orderId'];
    $reviewText = $_POST['reviewText'];
    $rating = $_POST['rating'];
    $email = $_SESSION['email'];
    $videoPath = null;

    // Validate email
    $emailQuery = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $emailQuery->bind_param('s', $email);
    $emailQuery->execute();
    $emailResult = $emailQuery->get_result();
    if ($emailResult->num_rows === 0) {
        die('Error: The email does not exist in the users table.');
    }
    $emailQuery->close();

    // Handle video upload
    if (isset($_FILES['reviewVideo']) && $_FILES['reviewVideo']['error'] === UPLOAD_ERR_OK) {
        $videoTmp = $_FILES['reviewVideo']['tmp_name'];
        $videoName = basename($_FILES['reviewVideo']['name']);
        $uploadDir = 'uploads/reviews/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
        $videoPath = $uploadDir . uniqid() . '_' . $videoName;

        if (!move_uploaded_file($videoTmp, $videoPath)) {
            die('Error uploading video file.');
        }
    }

    // Insert or update review
    $stmt = $conn->prepare("INSERT INTO reviews (order_id, email, rating, review_text, video_path) VALUES (?, ?, ?, ?, ?) 
        ON DUPLICATE KEY UPDATE review_text = VALUES(review_text), video_path = VALUES(video_path)");
    $stmt->bind_param('isiss', $orderId, $email, $rating, $reviewText, $videoPath);

    if ($stmt->execute()) {
        echo '<script>alert("Review submitted successfully!");</script>';
        echo '<script>window.location.href = "orders.php";</script>';
    } else {
        echo 'Error: ' . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
