<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $itemName = $_POST['itemName'];
    $description = $_POST['description'];
    $status = $_POST['status'];
    $catName = $_POST['catName'];
    $dateCreated = date("Y-m-d H:i:s");
    $updatedDate = date("Y-m-d H:i:s");

    // File upload handling
    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if the file is a valid image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    }

    // Check file size (limit = 50MB)
    if ($_FILES["image"]["size"] > 50000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image = $_FILES["image"]["name"];

            // Validate: at least one size must be submitted
            if (!isset($_POST['sizes']) || !is_array($_POST['sizes']) || count($_POST['sizes']) === 0) {
                die("Error: At least one size with price must be provided.");
            }

            // Insert menu item (without price)
            $stmt = $conn->prepare("INSERT INTO menuitem (itemName, description, image, status, catName, dateCreated, updatedDate) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $itemName, $description, $image, $status, $catName, $dateCreated, $updatedDate);

            if ($stmt->execute()) {
                $itemId = $stmt->insert_id;

                // Insert sizes
                $sizeStmt = $conn->prepare("INSERT INTO menuitem_sizes (itemId, size, price) VALUES (?, ?, ?)");

                foreach ($_POST['sizes'] as $sizeEntry) {
                    if (isset($sizeEntry['enabled']) && $sizeEntry['enabled'] == "1") {
                        $size = $sizeEntry['size'];
                        $sprice = $sizeEntry['price'];
                        $sizeStmt->bind_param("isd", $itemId, $size, $sprice);
                        $sizeStmt->execute();
                    }
                }

                $sizeStmt->close();

                echo '<script>alert("New item added successfully."); window.location.href="admin_menu.php";</script>';
                exit();
            } else {
                echo "Database error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    $conn->close();
    exit();
}
?>
