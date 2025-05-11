<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['itemId'];
    $itemName = $_POST['itemName'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $catName = $_POST['catName'];
    $image = $_FILES['image']['name'] ? $_FILES['image']['name'] : $_POST['existingImage'];

    // Handle image upload
    if ($_FILES['image']['name']) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($image);
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                die("Error uploading file.");
            }
        } else {
            die("File is not an image.");
        }
    }

    // Update main item
    $sql = "UPDATE menuitem SET itemName=?, description=?, status=?, catName=?, image=? WHERE itemId=?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssi", $itemName, $description, $status, $catName, $image, $itemId);
        if (!$stmt->execute()) {
            die("Error updating item: " . $stmt->error);
        }
        $stmt->close();
    }

    // Delete old sizes for this item
    $conn->query("DELETE FROM menuitem_sizes WHERE itemId = $itemId");

    // Insert only selected sizes (those with 'enabled' checkbox)
    if (isset($_POST['sizes']) && is_array($_POST['sizes'])) {
        foreach ($_POST['sizes'] as $sizeData) {
            if (isset($sizeData['enabled']) && $sizeData['enabled'] == 1) {
                $size = $conn->real_escape_string($sizeData['size']);
                $price = floatval($sizeData['price']);
                $conn->query("INSERT INTO menuitem_sizes (itemId, size, price) VALUES ($itemId, '$size', $price)");
            }
        }
    }

    $conn->close();
    header("Location: admin_menu.php");
    exit();
}
?>
