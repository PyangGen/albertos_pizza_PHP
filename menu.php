<?php
session_start();
include 'db_connection.php';

// Fetch all unique categories from the database
$categoryQuery = 'SELECT DISTINCT catName FROM menuitem';
$categoryResult = $conn->query($categoryQuery);

$categories = [];
while ($row = $categoryResult->fetch_assoc()) {
    $categories[] = $row['catName'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet'
        href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"
        integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!--poppins-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="menu.css" />
    <title>Menu</title>
    <style>
        .disabled-button {
            background-color: gray;
            color: white;
            cursor: not-allowed;
            pointer-events: none;
        }

        .disabled-button i {
            color: white;
        }

        section:nth-child(odd) {
            background-color: #ffe4c2;

            /* Set background color for odd sections */
        }

        section:nth-child(even) {
            background-color: #feead4;
            /* Set background color for even sections */
        }
        
    </style>
</head>

<body>
    <?php

    if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
        include 'nav-logged.php';
    } else {
        include 'navbar.php';
    }
    ?>
    <div class="heading">
        <div class="row heading-title">Our Menu</div>
        <div class="row heading-description">~Discover a feast of flavors with our exciting menu!</div>
    </div>
    <?php foreach ($categories as $category): ?>
        <section id="<?= strtolower($category) ?>">
            <div id="message"></div>
            <div class="container-fluid">
                <h1 class="mt-1"> <?= strtoupper($category) ?> </h1>
                <?php
// Get the estimated min and max time for this category
$timeStmt = $conn->prepare('SELECT MIN(min_time) AS minTime, MAX(max_time) AS maxTime FROM menucategory WHERE catName = ?');
$timeStmt->bind_param('s', $category);
$timeStmt->execute();
$timeResult = $timeStmt->get_result();
$timeRow = $timeResult->fetch_assoc();

// Check if both values are set and not null
if (!empty($timeRow['minTime']) && !empty($timeRow['maxTime'])):
?>
    <h5 class="text-muted mb-3">
        Estimated delivery time: <?= $timeRow['minTime'] ?> mins - <?= $timeRow['maxTime'] ?> mins
    </h5>
<?php endif; ?>

                <div class="row">
                    <?php
                    $stmt = $conn->prepare('SELECT * FROM menuitem WHERE catName = ?');
                    $stmt->bind_param('s', $category);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    while ($row = $result->fetch_assoc()):
                        $buttonClass = $row['status'] == 'Unavailable' ? 'disabled-button' : '';
                        ?>
                        <div class="col-md-6 col-lg-3 col-sm-12 menu-item col-xs-12">
                            <div class="mt-4" style="background-color: #fdd9c9; border-radius: 5px;">
                                <img src="uploads/<?= $row['image'] ?>" alt="image" class="card-img-top" height="250">
                                <div class="card-body">
                                    <h4 class="card-title text-center mt-3"><?= $row['itemName'] ?></h4>
                                    <p class="card-title text-center description ps-3 pe-3 pt-2 pb-3"><?= $row['description'] ?>
                                    </p>
                                    <?php if ($row['status'] == 'Unavailable'): ?>
                                        <p class="card-status" style="color: red; text-align: center; font-size: 1.3em;">
                                            <?php echo $row['status']; ?></p>
                                    <?php endif; ?>
                                    <div style="text-align: center;">
                                        <?php
                                        $itemId = $row['itemId'];
                                        $sizeQuery = $conn->prepare("SELECT size, price FROM menuitem_sizes WHERE itemId = ?");
                                        $sizeQuery->bind_param('i', $itemId);
                                        $sizeQuery->execute();
                                        $sizeResult = $sizeQuery->get_result();
                                        ?>

                                        <form action="" class="form-submit">
                                            <input type="hidden" class="pid" value="<?= $row['itemId'] ?>">
                                            <input type="hidden" class="pname" value="<?= $row['itemName'] ?>">
                                            <input type="hidden" class="pimage" value="<?= $row['image'] ?>">
                                            <input type="hidden" class="pcode" value="<?= $row['catName'] ?>">
                                            <input type="hidden" class="pprice" value=""> <!-- Will be set via JS -->
                                            <input type="hidden" class="psize" value=""> <!-- Will be set via JS -->

                                            <div class="text-center mb-2">
                                                <?php if ($sizeResult->num_rows > 0): ?>
                                                    <?php while ($sizeRow = $sizeResult->fetch_assoc()): ?>
                                                        <div class="form-check d-inline-block me-3">
                                                            <label class="form-check-label">
                                                                <input class="form-check-input size-checkbox" type="checkbox"
                                                                    name="sizes[]" data-price="<?= $sizeRow['price'] ?>"
                                                                    value="<?= htmlspecialchars($sizeRow['size']) ?>">
                                                                <?= htmlspecialchars($sizeRow['size']) ?> -
                                                                ₱<?= number_format($sizeRow['price']) ?>
                                                            </label>
                                                        </div>
                                                    <?php endwhile; ?>
                                                <?php endif; ?>
                                            </div>


                                            <div class="button-container mt-2 text-center">
                                                <button class="addItemBtn <?= $buttonClass ?>" type="button">
                                                    <i class="fas fa-cart-plus"></i> &nbsp;&nbsp;Add to cart
                                                </button>
                                            </div>
                                        </form>


                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>

                </div>
            </div>
        </section>
        <!-- Error Modal -->
<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                
            </div>
            <div class="modal-body">
                <strong>Please select size and price before adding to cart.</strong>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


    <?php endforeach; ?>
    <!-- Toast Notification Container -->
    <div id="toast" class="toast"
        style="background: rgba(255, 182, 182, 0.9); border: 1px solid rgba(255, 182, 182, 1); font-size: 16px;">
        <button class="toast-btn toast-close">&times;</button>
        <span class="pt-3"><strong>You must log in to add items to the cart.</strong></span><br>
        <button class="toast-btn toast-ok">Okay</button>
    </div>
    <!--Footer-->
    <?php
    include_once('footer.html');
    ?>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.min.js'></script>
    <script type="text/javascript">
        $(document).ready(function () {

            function userIsLoggedIn() {
                return <?php echo isset($_SESSION['userloggedin']) && $_SESSION['userloggedin'] === true ? 'true' : 'false'; ?>;
            }

            function showToast() {
                var toast = $('#toast');
                toast.addClass('show'); // Add the 'show' class to make the toast visible

                // Automatically hide the toast after 3 seconds
                setTimeout(function () {
                    toast.removeClass('show'); // Remove the 'show' class to hide the toast
                }, 5000);
            }

            function getUserEmail() {
                return "<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>";
            }

            $(".addItemBtn").click(function (e) {
    e.preventDefault();

    if (!userIsLoggedIn()) {
        showToast();
        return;
    }

    if ($(this).hasClass('disabled-button')) {
        return;
    }

    var $form = $(this).closest(".form-submit");
    var email = getUserEmail();
    var pid = $form.find(".pid").val();
    var pname = $form.find(".pname").val();
    var pimage = $form.find(".pimage").val();
    var pcode = $form.find(".pcode").val();
    var pqty = 1;

    var isSizeSelected = false;

    $form.find(".size-checkbox:checked").each(function () {
        isSizeSelected = true;
    });

    if (!isSizeSelected) {
        $('#errorModal').modal('show');  // Show the error modal
        return;
    }

    // Proceed with adding items to cart
    $form.find(".size-checkbox:checked").each(function () {
        var psize = $(this).val();
        var pprice = $(this).data("price");

        $.ajax({
            url: 'action.php',
            method: 'post',
            data: {
                pid: pid,
                pname: pname,
                pprice: pprice,
                pqty: pqty,
                pimage: pimage,
                pcode: pcode,
                psize: psize,
                email: email
            },
            success: function (response) {
                $("#message").html(response);
                window.scrollTo(0, 0);
                load_cart_item_number();
            }
        });
    });
});

// Close the modal manually if needed
$(".btn-secondary").click(function () {
    $('#errorModal').modal('hide');  // Close the error modal when the close button is clicked
});




            // Close button functionality
            $('.toast-close').click(function () {
                $('#toast').removeClass('show');
            });
            // Okay button redirection
            $('.toast-ok').click(function () {
                window.location.href = 'login.php'; // Redirect to login.php
            });

            load_cart_item_number();

            function load_cart_item_number() {
                $.ajax({
                    url: 'action.php',
                    method: 'get',
                    data: {
                        cartItem: "cart_item"
                    },
                    success: function (response) {
                        $("#cart-item").html(response);
                    }
                });
            }
        });
    </script>

</body>

</html>