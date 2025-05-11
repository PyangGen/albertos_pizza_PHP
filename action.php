<?php
session_start();
require 'db_connection.php';

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Add product into cart table
    if (isset($_POST['pid'], $_POST['pname'], $_POST['pprice'], $_POST['psize'])) {
        $pid = $_POST['pid'];
        $pname = $_POST['pname'];
        $pprice = $_POST['pprice'];
        $psize = $_POST['psize'];
        $pimage = $_POST['pimage'];
        $pcode = $_POST['pcode'];
        $pqty = 1;

        $total_price = $pprice * $pqty;

        // Check if item with same name, size and email exists
        $stmt = $conn->prepare('SELECT itemName FROM cart WHERE itemName=? AND size=? AND email=?');
        $stmt->bind_param('sss', $pname, $psize, $email);
        $stmt->execute();
        $res = $stmt->get_result();
        $r = $res->fetch_assoc();
        $code = $r['itemName'] ?? '';

        if (!$code) {
            $query = $conn->prepare('INSERT INTO cart (itemName, price, image, quantity, total_price, catName, size, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $query->bind_param('sdsissss', $pname, $pprice, $pimage, $pqty, $total_price, $pcode, $psize, $email);
            $query->execute();

            echo '<div class="alert alert-success alert-dismissible mt-2" style="width: 300px; position: fixed; top: 50%; right: 50%; transform: translate(50%, -50%); z-index: 9999; padding-top: 40px; padding-bottom: 40px; font-size: 17px; text-align: center;">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <strong>Item added to your cart!</strong>
                    </div>';
        } else {
            echo '<div class="alert alert-danger alert-dismissible mt-2" style="width: 300px; position: fixed; top: 50%; right: 50%; transform: translate(50%, -50%); z-index: 9999; padding-top: 40px; padding-bottom: 40px; font-size: 17px; text-align: center;">
                      <button type="button" class="close" data-dismiss="alert">&times;</button>
                      <strong>Item already exists in your cart!</strong>
                    </div>';
        }
    }

    // Cart item counter
    if (isset($_GET['cartItem']) && $_GET['cartItem'] == 'cart_item') {
        $stmt = $conn->prepare('SELECT SUM(quantity) AS qty FROM cart WHERE email=?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $quantity = $row['qty'] !== null ? $row['qty'] : 0;
        echo $quantity;
    }
}
?>
