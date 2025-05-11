<?php
session_start();
require 'db_connection.php';

// Check if user is logged in
if (!isset($_SESSION['userloggedin']) || $_SESSION['userloggedin'] !== true) {
  header('location:login.php');
  exit;
}

// Get the email from the session
$email = $_SESSION['email'];

// Fetch user data
$stmt = $conn->prepare('SELECT firstName, lastName, contact, email FROM users WHERE email=?');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Retrieve selected items from POST request
$selectedItems = json_decode($_POST['selected_items'], true);

// Fetch cart items from the database
$itemDetails = [];
foreach ($selectedItems as $item) {
  $stmt = $conn->prepare('SELECT * FROM cart WHERE id=? AND email=?');
  $stmt->bind_param('is', $item['id'], $email);
  $stmt->execute();
  $result = $stmt->get_result();
  $itemDetails[] = $result->fetch_assoc();
}

// Calculate subtotal and total
$subtotal = 0;
$deliveryFee = 0;
foreach ($itemDetails as $item) {
  $itemPrice = $item['price'];
  $itemQuantity = $item['quantity'];
  $subtotal += $itemPrice * $itemQuantity;
}
$deliveryFee = ($_POST['payment_mode'] === 'Pick_up') ? 0 : 50;
$total = $subtotal + $deliveryFee;

$gcashImagePath = '';
$gcashImageId = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['payment_mode'] === 'Gcash') {
 

  // Fetch the latest image by ID
  $stmt = $conn->prepare("SELECT id, image_path FROM gcash_images ORDER BY id DESC LIMIT 1");
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    $gcashImageId = $row['id'];
    $gcashImagePath = $row['image_path'];
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
  <!-- Bootstrap CSS -->
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="order_review.css">
  <title>Complete Order</title>
</head>

<body>
  <?php include('nav-logged.php'); ?>
  <div class="title mt-2">
    <h3>Hi <?php echo $user['firstName'] . " " . $user['lastName']; ?>, Complete your order!</h3>
  </div>
  <div class=" main mt-4">
    <div class="order-fee">

      <h4>Order Details</h4>
      <hr>
      <form action="process_order.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="total" value="<?= $total ?>">
        <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
        <input type="hidden" name="order_id" value="<?= $orderId ?>">
        <input type="hidden" name="selected_items" value='<?= json_encode($selectedItems) ?>'>
        <input type="hidden" name="payment_mode" value="<?= htmlspecialchars($_POST['payment_mode']) ?>">
        <div class="form-group row">
        <div class="col">
    <label for="firstName">First Name:</label>
    <input type="text" class="form-control" id="firstName" name="firstName" value="<?= htmlspecialchars($user['firstName'] ?? '') ?>" required
           oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
</div>
<div class="col">
    <label for="lastName">Last Name:</label>
    <input type="text" class="form-control" id="lastName" name="lastName" value="<?= htmlspecialchars($user['lastName'] ?? '') ?>" required
           oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')">
</div>
</div>
  
        <div class="form-group row">
        <div class="col">
  <label for="contact">Contact:</label>
  <div class="input-group">
    <span class="input-group-text">+63</span>
    <input type="text" class="form-control" id="contact" name="contact" maxlength="9" pattern="\d{9}" required  value="<?= htmlspecialchars($user['contact'] ?? '') ?>"
           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,9);">
  </div>
</div>

          <div class="col">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($email) ?>" readonly>
          </div>
        </div>
        <div class="form-group">
          <label for="order_note">Order Note:</label>
          <textarea class="form-control" id="order_note" name="order_note" rows="3"></textarea>
        </div>
        <div class="form-group">
                        <b><label for="city">City</label></b>
                        <input type="text" class="form-control" id="city" name="city" value="Mandaue City, 6014" readonly>
                    </div>

                    <div class="form-group">
                        <b><label for="barangay">Barangay</label></b>
                        <select class="form-control" id="barangay" name="barangay" required>
                            <option value="" disabled selected>Select Barangay</option>
                            <option value="Alang-Alang">Barangay Alang-Alang</option>
                            <option value="Bakilid">Barangay Bakilid</option>
                            <option value="Banilad">Barangay Banilad</option>
                            <option value="Cambaro"> Barangay Cambaro</option>
                            <option value="Basak">Barangay Basak</option>
                            <option value="Cabancalan">Barangay Cabancalan</option>
                            <option value="Canduman">Barangay Canduman</option>
                            <option value="Casuntingan">Barangay Casuntingan</option>
                            <option value="Cubacub">Barangay Cubacub</option>
                            <option value="Casili">Barangay Casili</option>
                            <option value="Maguikay">Barangay Maguikay</option>
                            <option value="Centro">Barangay Centro</option>
                            <option value="Guizo">Barangay Guizo</option>
                            <option value="Ibabao-Estancia">Barangay Ibabao-Estancia</option>
                            <option value="Labogon">Barangay Labogon</option>
                            <option value="Looc">Barangay Looc</option>
                            <option value="Mantuyong">Barangay Mantuyong</option>
                            <option value="Jagobiao">Barangay Jagobiao</option>
                            <option value="Opao">Barangay Opao</option>
                            <option value="Paknaan">Barangay Paknaan</option>
                            <option value="Tabok">Barangay Tabok</option>
                            <option value="Pagsabungan">Barangay Pagsabungan</option>
                            <option value="Subangdaku">Barangay Subangdaku</option>
                            <option value="Tipolo">Barangay Tipolo</option>
                            <option value="Umapad">Barangay Umapad</option>
                            <option value="Tingub">Barangay Tingub</option>
                            <option value="Tawason">Barangay Tawason</option>
                            
                        </select>
                    </div>

                    <div class="form-group">
                        <b><label for="street">House No./Building/Street Name</label></b>
                        <input class="form-control" id="street" name="street" placeholder="Enter street name" type="text">
                    </div>

<!-- Insert QR code display here -->
<?php if (!empty($gcashImagePath)) : ?>
  <div class="form-group mt-3">
    <label><h6>GCash QR Code</h6></label><br>
    <a href="Admin/<?= htmlspecialchars($gcashImagePath) ?>" download>
      <img src="Admin/<?= htmlspecialchars($gcashImagePath) ?>" alt="GCash QR Code" style="max-width: 100px;">
    </a>
    <br>
    <small ><a class="text-primary" href="Admin/<?= htmlspecialchars($gcashImagePath) ?>" download>Click the image or here to download</a></small>
    <p>Customer upload gcash screenshot</p>
    <input type="file" name="gcash_screenshot" accept="image/*" required> 
  </div>
<?php endif; ?>



    </div>


    <div class="order-summary">
      <h4>Order Summary</h4>
      <hr>
      <div class="order-items mb-2">
        <?php foreach ($itemDetails as $item) : ?>
          <div class="order-item d-flex align-items-center">
            <?php if (!empty($item['image'])) : ?>
              <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="Item Image" class="ms-1">
            <?php else : ?>
              <span>No image available</span>
            <?php endif; ?>
            <div class="ms-1 row d-flex justify-content-between w-100">
              <div class="col d-flex flex-column justify-content-center ">
                <div class="d-flex flex-row mb-1"><strong><?= htmlspecialchars($item['itemName']) ?></strong></div>
                <div class="d-flex flex-row ">Quantity: <?= htmlspecialchars($item['quantity']) ?></div>
              </div>
              <div class="col d-flex flex-column justify-content-center">
                <div class="d-flex flex-row justify-content-end align-items-center mt-2"><?= htmlspecialchars($item['size']) ?> ₱ <?= htmlspecialchars($item['price'], 0) ?> x <?= htmlspecialchars($item['quantity']) ?></div>
                <div class="d-flex flex-row justify-content-end align-items-start mb-2">
                  <span class="badge rounded-pill text-light p-2 mt-2 item-total-price" style="background-color: #fb4a36;">₱ <?= $item['total_price'] ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <h4 class="mt-1 ">Order Fee</h4>
      <hr>
      <div class="summary-details">
        <div class="fee-details">
          <div><strong>Subtotal:</strong></div>
          <div>₱ <?= number_format($subtotal) ?></div>
        </div>
        <div class="fee-details">
          <div><strong>Payment Method:</strong></div>
          <div><?= htmlspecialchars($_POST['payment_mode']) ?></div>
        </div>
        <div class="fee-details">
          <div><strong>Delivery Fee:</strong></div>
          <div>₱ <?= number_format($deliveryFee) ?></div>
        </div>
        <div class="fee-details">
          <div><strong>Total:</strong></div>
          <div>₱ <?= number_format($total) ?></div>
        </div>
      </div>
      <button type="submit" class="order-btn ">Place Order</button>

      </form>
    </div>


  </div>

  <?php
include_once ('footer.html');
?>

  <!-- Bootstrap JS -->
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>
  

  <script>
    $(document).ready(function() {
      console.log('Page is ready. Calling load_cart_item_number.');
      load_cart_item_number();

      function load_cart_item_number() {
        $.ajax({
          url: 'action.php',
          method: 'get',
          data: {
            cartItem: "cart_item"
          },
          success: function(response) {
            $("#cart-item").html(response);
          }
        });
      }
    });
  </script>
</body>

</html>