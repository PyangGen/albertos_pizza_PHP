<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
    header('Location: login.php');
    exit;
}

// Only proceed if form was submitted
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    die('Invalid request. Please go back and submit the form again.');
}

// Retrieve and sanitize form data
$firstName = $_POST['firstName'] ?? '';
$lastName = $_POST['lastName'] ?? '';
$email = $_POST['email'] ?? '';
$city = $_POST['city'] ?? '';
$barangay = $_POST['barangay'] ?? '';
$street = $_POST['street'] ?? '';

$contact = $_POST['contact'] ?? '';
$orderNote = $_POST['order_note'] ?? '';
$paymentMode = $_POST['payment_mode'] ?? '';
$total = isset($_POST['total']) ? floatval($_POST['total']) : 0;
$subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;
$selectedItems = isset($_POST['selected_items']) ? json_decode($_POST['selected_items'], true) : [];

if (!is_array($selectedItems) || count($selectedItems) === 0) {
    die('No items selected. Please add items to your cart and try again.');
}

$imagePath = '';

// If payment method is GCash, validate file upload
if ($paymentMode === 'Gcash') {
    if (isset($_FILES['gcash_screenshot']) && $_FILES['gcash_screenshot']['error'] === UPLOAD_ERR_OK) {
       

        $fileTmp = $_FILES['gcash_screenshot']['tmp_name'];
        $fileName = basename($_FILES['gcash_screenshot']['name']);
        $targetPath =  $fileName;

        if (move_uploaded_file($fileTmp, $targetPath)) {
            $imagePath = $targetPath;
        } else {
            die("Failed to upload GCASH screenshot.");
        }
    } else {
        die("GCASH screenshot is required for Gcash payment.");
    }
}

$conn->begin_transaction();

try {
    // Insert order record
    $stmt = $conn->prepare('INSERT INTO orders (firstName, lastName, email, phone, city, barangay, street, sub_total, grand_total, pmode, note, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param('sssssssddsss', $firstName, $lastName, $email, $contact, $city, $barangay, $street, $subtotal, $total, $paymentMode, $orderNote, $imagePath);
    $stmt->execute();
    $orderId = $stmt->insert_id;

    // Prepare insert for order items
    $stmt = $conn->prepare('INSERT INTO order_items (order_id, itemName, quantity, size, price, total_price, image) VALUES (?, ?, ?, ?, ?, ?, ?)');
    if (!$stmt) {
        throw new Exception('Failed to prepare order items statement: ' . $conn->error);
    }

    foreach ($selectedItems as $item) {
        $itemId = $item['id'] ?? 0;
        $itemQuantity = $item['quantity'] ?? 0;

        $itemStmt = $conn->prepare('SELECT * FROM cart WHERE id=? AND email=?');
        $itemStmt->bind_param('is', $itemId, $email);
        $itemStmt->execute();
        $itemResult = $itemStmt->get_result();
        $itemDetails = $itemResult->fetch_assoc();

        if (!$itemDetails) {
            throw new Exception('Item not found in cart.');
        }

        $itemName = $itemDetails['itemName'];
        $itemPrice = $itemDetails['price'];
        $itemSize = $itemDetails['size'];
        $totalPrice = $itemPrice * $itemQuantity;
        $itemImage = $itemDetails['image'];

        $stmt->bind_param('isssdds', $orderId, $itemName, $itemQuantity, $itemSize, $itemPrice, $totalPrice, $itemImage);
        $stmt->execute();

        // Delete ordered item from cart
        $deleteStmt = $conn->prepare('DELETE FROM cart WHERE id=? AND email=?');
        $deleteStmt->bind_param('is', $itemId, $email);
        $deleteStmt->execute();
    }

    $conn->commit();

    // Redirect to confirmation
    header('Location: order_confirm.php?order_id=' . $orderId);
    exit;

} catch (Exception $e) {
    $conn->rollback();
    echo 'Error: ' . htmlspecialchars($e->getMessage());
}
?>
