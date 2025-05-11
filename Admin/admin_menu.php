<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php';

$uploadSuccess = false;
$uploadError = '';
$imagePath = '';

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['gcash_image'])) {
    $imageName = uniqid('') . '.' . pathinfo($_FILES["gcash_image"]["name"], PATHINFO_EXTENSION);
    $targetFile = $imageName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES["gcash_image"]["tmp_name"], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO gcash_images (image_path) VALUES (?)");
            $stmt->bind_param("s", $targetFile);
            $stmt->execute();
            $stmt->close();

            // Save success and image path in session, then redirect
            $_SESSION['upload_success'] = true;
            $_SESSION['uploaded_image'] = $targetFile;

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $uploadError = "Failed to upload image.";
        }
    } else {
        $uploadError = "Invalid file type. Allowed: jpg, jpeg, png, gif.";
    }
}

// After redirect: Check if an upload was just completed
if (isset($_SESSION['upload_success'])) {
    $uploadSuccess = true;
    $imagePath = $_SESSION['uploaded_image'];

    // Clear session variables so refresh doesn't re-display message
    unset($_SESSION['upload_success']);
    unset($_SESSION['uploaded_image']);
}

// If no upload success, fetch the latest image from database
if (!$uploadSuccess && empty($imagePath)) {
    $result = $conn->query("SELECT image_path FROM gcash_images ORDER BY uploaded_at DESC LIMIT 1");
    if ($row = $result->fetch_assoc()) {
        $imagePath = $row['image_path'];
    }
}

$conn->close();
?>


<?php
include 'sidebar.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menu Management</title>

    <!--poppins-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="admin_menu.css">
</head>
<style>
    .pagination-container {
  text-align: center;
  margin: 20px 0;
}

.pagination-container a {
  display: inline-block;
  margin: 0 5px;
  padding: 8px 14px;
  background-color: #f4f4f4;
  color: #333;
  border: 1px solid #ccc;
  border-radius: 6px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.pagination-container a:hover {
  background-color: #ffc9b3;
  color: white;
  border-color: #ffc9b3;
}

.pagination-container a.active {
  background-color:#fb4a36;
  color: white;
  font-weight: bold;
  border-color: #fb4a36;
}
</style>

<body>
    <div class="sidebar">
        <button class="close-sidebar" id="closeSidebar">&times;</button>

        <!-- Profile Section -->
        <div class="profile-section">
            <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
            <div class="info">
                <h3>Welcome Back!</h3>
                <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?>
                </p>
            </div>
        </div>

        <!-- Navigation Items -->

        <ul>
            <li><a href="index.php"><i class="fas fa-chart-line"></i> Overview</a></li>
            <li><a href="admin_menu.php" class="active"><i class="fas fa-utensils"></i> Menu Management</a></li>
            <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="staffs.php"><i class="fas fa-users"></i> Staffs</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h2><i class="fas fa-utensils"></i> Menu Management</h2>
        </div>
        <div class="modal-row">
            <div>
                <button onclick="openModal()"><i class="fas fa-plus"></i> &nbsp;Add New Category</button>
                <button onclick="openItemModal()"> <i class="fas fa-plus"></i> &nbsp;Add New Item</button>
                <button onclick="openPayGcash()"> <i class="fas fa-plus"></i> &nbsp;Add Gcash</button>
                <button onclick="openViewCategoryModal()"> <i class="fas fa-eye"></i> &nbsp;View Categories</button>
            </div>
            <div class="search-bar ">
                <select id="categoryFilter" onchange="filterCategories()">
                    <option value="">All Categories</option>
                    <?php
                    $sql = "SELECT catName FROM menucategory";
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<option value='{$row['catName']}'>{$row['catName']}</option>";
                    }
                    ?>
                </select>

            </div>

        </div>

        <?php
include 'db_connection.php';

// Get current page from query string, default is 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 3; // Items per page
$offset = ($page - 1) * $limit;

// Get total number of menu items
$totalQuery = "SELECT COUNT(*) AS total FROM menuitem";
$totalResult = mysqli_query($conn, $totalQuery);
$totalRow = mysqli_fetch_assoc($totalResult);
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit);

// Retrieve paginated menu items
$sql = "SELECT * FROM menuitem LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $sql);
?>

<table id="menuTable">
    <thead>
        <tr>
            <th>Item Name</th>
            <th>Image</th>
            <th>Description</th>
            <th>Sizes/Price</th>
            <th>Category</th>
            <th>Status</th>
            <th>Popular</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $isPopularChecked = $row['is_popular'] ? 'checked' : '';
                $itemId = $row['itemId'];

                // Get sizes for this item
                $sizeQuery = "SELECT size, price FROM menuitem_sizes WHERE itemId = '$itemId'";
                $sizeResult = mysqli_query($conn, $sizeQuery);
                $sizesPrices = [];
                while ($sizeRow = mysqli_fetch_assoc($sizeResult)) {
                    $sizesPrices[] = $sizeRow;
                }

                echo "<tr data-category='{$row['catName']}'>
                    <td>{$row['itemName']}</td>
                    <td><img src='../uploads/{$row['image']}' alt='{$row['itemName']}' width='50'></td>
                    <td>{$row['description']}</td>
                    <td><ul>";
                foreach ($sizesPrices as $size) {
                    echo "<li>{$size['size']} - Rs {$size['price']}</li>";
                }
                echo "</ul></td>
                    <td>{$row['catName']}</td>
                    <td>{$row['status']}</td>
                    <td>
                        <div class='toggler'>
                            <input id='toggler-{$row['itemId']}' name='toggler-{$row['itemId']}' type='checkbox' value='1' $isPopularChecked onchange='togglePopular({$row['itemId']}, this)'>
                            <label for='toggler-{$row['itemId']}'>
                                <!-- SVG icons here -->
                            </label>
                        </div>
                    </td>
                    <td>
                        <button id='editbtn' onclick='openEditItemModal(this)'
                            data-itemid='{$row['itemId']}'
                            data-itemname='{$row['itemName']}'
                            data-description='{$row['description']}'
                            data-image='{$row['image']}'
                            data-category='{$row['catName']}'
                            data-status='{$row['status']}'
                            data-sizes='" . htmlspecialchars(json_encode($sizesPrices), ENT_QUOTES, 'UTF-8') . "'>
                            <i class='fas fa-edit'></i>
                        </button>
                        <button id='deletebtn' onclick=\"deleteItem('{$row["itemId"]}')\"><i class='fas fa-trash'></i></button>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='8' style='text-align: center;'>No menu items found</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Pagination Links -->
<div class="pagination-container">
    <?php if ($page > 1): ?>
        <a href="?page=<?php echo $page - 1; ?>" class="pagination-link">&laquo; Prev</a>
    <?php endif; ?>

    <?php
    for ($i = 1; $i <= $totalPages; $i++) {
        $activeClass = ($i == $page) ? 'active' : '';
        echo "<a href='?page=$i' class='pagination-link $activeClass'>$i</a>";
    }
    ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?php echo $page + 1; ?>" class="pagination-link">Next &raquo;</a>
    <?php endif; ?>
</div>



    </div>
    <div class="modal" id="categoryModal">
    <div class="modal-overlay"></div>
    <div class="modal-container">
        <form class="form" method="POST" action="add_category.php" onsubmit="return validateTimeRange()">
            <div class="modal-header">
                <h2>Add New Category</h2>
                <span class="close-icon" onclick="closeModal()">&times;</span>
            </div>
            <div class="modal-content">
                <div class="input-group">
                    <input type="text" name="catName" id="catName" class="input" required>
                    <label for="catName" class="label">Category Name</label>
                </div>
                <div class="input-group">
                    <input type="number" name="min_time" id="min_time" class="input" required min="1">
                    <label for="min_time" class="label">Minimum Time (minutes)</label>
                </div>
                <div class="input-group">
                    <input type="number" name="max_time" id="max_time" class="input" required min="1">
                    <label for="max_time" class="label">Maximum Time (minutes)</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="button" onclick="closeModal()">Cancel</button>
                <button type="submit" class="button">Save</button>
            </div>
        </form>
    </div>
</div>
    <!-- Add Item Modal -->
    <div class="modal" id="itemModal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <form class="form" method="POST" action="add_item.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h2>Add New Item</h2>
                    <span class="close-icon" onclick="closeItemModal()">&times;</span>
                </div>
                <div class="modal-content">
                    <div class="input-group">
                        <input type="text" name="itemName" id="itemName" class="input" required>
                        <label for="itemName" class="label">Item Name</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="description" id="description" class="input" required>
                        <label for="description" class="label">Description</label>
                    </div>
                    <div class="input-group">
                        <select name="status" id="status" class="input" required>
                            <option value="">Status</option>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                        <label for="status" class="label">Status</label>
                    </div>
                    <!-- Multiple Sizes with Price and Checkbox -->
                    <div class="input-group" id="sizesContainer">
                        <label class="label">Sizes & Prices</label>
                        <div class="size-entry ">
                            <input type="checkbox" name="sizes[0][enabled]" value="1">
                            <input type="text" name="sizes[0][size]" placeholder="Size (e.g. Small)" class="input"
                                required>
                            <input type="number" name="sizes[0][price]" placeholder="Price" class="input" step="0.01"
                                required>
                        </div>
                    </div>
                    <button type="button" class="button" onclick="addSizeField()">+ Add Another Size</button>
                    <div class="input-group">
                        <select name="catName" id="catName" class="input" required>
                            <option value="">Select Category</option>
                            <?php
                            $sql = "SELECT catName FROM menucategory";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['catName']}'>{$row['catName']}</option>";
                            }
                            ?>
                        </select>
                        <label for="catName" class="label">Category</label>
                    </div>
                    <div class="input-group">
                        <input type="file" name="image" id="image" class="input" accept="image/*" required>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button" onclick="closeItemModal()">Cancel</button>
                    <button type="submit" class="button">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div class="modal" id="editItemModal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <form class="form" method="POST" action="edit_item.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h2>Edit Item</h2>
                    <span class="close-icon" onclick="closeEditItemModal()">&times;</span>
                </div>
                <div class="modal-content">
                    <input type="hidden" name="itemId" id="editItemId">
                    <input type="hidden" name="existingImage" id="editExistingImage">
                    <div class="input-group">
                        <input type="text" name="itemName" id="editItemName" class="input" required>
                        <label for="editItemName" class="label">Item Name</label>
                    </div>
                    <div class="input-group">
                        <input type="text" name="description" id="editDescription" class="input" required>
                        <label for="editDescription" class="label">Description</label>
                    </div>
                    <div class="input-group">
                        <select name="status" id="editStatus" class="input" required>
                            <option value="Available">Available</option>
                            <option value="Unavailable">Unavailable</option>
                        </select>
                        <label for="editStatus" class="label">Status</label>
                    </div>
                    <!-- Multiple Sizes with Price and Checkbox -->
                    <div class="input-group" id="editsizesContainer">
                        <label class="label">Sizes & Prices</label>
                        <div class="size-entry">
                            <input type="checkbox" name="sizes[0][enabled]" value="1">
                            <input type="text" name="sizes[0][size]" placeholder="Size (e.g. Small)" class="input"
                                required>
                            <input type="number" name="sizes[0][price]" placeholder="Price" class="input" step="0.01"
                                required>
                        </div>
                    </div>
                    <button type="button" class="button" onclick="editaddSizeField()">+ Add Another Size</button>
                    <div class="input-group">
                        <select name="catName" id="editCatName" class="input" required>
                            <?php
                            $sql = "SELECT catName FROM menucategory";
                            $result = mysqli_query($conn, $sql);
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<option value='{$row['catName']}'>{$row['catName']}</option>";
                            }
                            ?>
                        </select>
                        <label for="editCatName" class="label">Category</label>
                    </div>

                    <div class="input-group">
                        <input type="file" name="image" id="editImage" class="input" accept="image/*">
                        <small>Leave empty if not changing</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="button" onclick="closeEditItemModal()">Cancel</button>
                    <button type="submit" class="button">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Categories Modal -->
    <div class="modal" id="viewCategoryModal">
        <div class="modal-overlay"></div>
        <div class="modal-container" style="background: #fef0e8;">
            <div class="modal-header" style=" border-bottom: 1px solid #ffc9b3">
                <h2>Categories</h2>
                <span class="close-icon" onclick="closeViewCategoryModal()">&times;</span>
            </div>
            <div class="modal-content">
                <div class="input-group">
                    <table id="categoryTable" style="width:100%; border-collapse: collapse;">
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Min Time (minutes)</th>
                                <th>Max Time (minutes)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT catName, min_time, max_time FROM menucategory";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>{$row['catName']}</td>";
                                    echo "<td>{$row['min_time']}</td>";
                                    echo "<td>{$row['max_time']}</td>";
                                    echo "<td><button class='delete-btn' onclick=\"deleteCategory('{$row['catName']}')\"><i class='fas fa-trash'></i></button></td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='2'>No categories found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #ffc9b3">
                <button type="button" class="button" onclick="closeViewCategoryModal()">Close</button>
            </div>
        </div>
    </div>

   <!-- Add Gcash Modal -->
<div class="modal" id="addGcashModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2>Add Gcash</h2>
            <span class="close-icon" onclick="closePayGcash()">&times;</span>
        </div>
        <form action="admin_menu.php" method="POST" enctype="multipart/form-data">
            <div class="modal-content">
                <?php if (!empty($uploadError)): ?>
                    <p style="color: red;"><?= $uploadError ?></p>
                <?php endif; ?>

                <?php if (!empty($imagePath)): ?>
                    <div class="input-group">
                        <label>Current Gcash QR:</label><br>
                        <img src="<?= htmlspecialchars($imagePath) ?>" alt="GCash QR" style="width: 150px; border: 1px solid #ccc; padding: 4px; border-radius: 8px; margin-bottom: 10px;">

                    </div>
                <?php endif; ?>

                <div class="input-group">
                    <label for="gcash_image">Upload Gcash QR Image:</label><br>
                    <input type="file" name="gcash_image" accept="image/*" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="button">Upload</button>
                <button type="button" class="button" onclick="closePayGcash()">Cancel</button>
            </div>
        </form>
    </div>
</div>


    <script>
        function openViewCategoryModal() {
            document.getElementById('viewCategoryModal').classList.add('open');
        }

        function closeViewCategoryModal() {
            document.getElementById('viewCategoryModal').classList.remove('open');
        }

        function deleteCategory(catName) {
            if (confirm(`Are you sure you want to delete the category: ${catName}?`)) {
                // Perform AJAX request to delete the category from the database
                var xhr = new XMLHttpRequest();
                xhr.open("POST", "delete_category.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            alert("Category deleted successfully.");
                            // Reload the modal content or remove the row from the table
                            location.reload(); // Reload the page to reflect changes
                        } else {
                            alert("Failed to delete the category. Please try again.");
                        }
                    }
                };
                xhr.send("catName=" + encodeURIComponent(catName));
            }
        }
    </script>


    <?php
    include_once('footer.html');
    ?>
    <script>
        function togglePopular(itemId, checkbox) {
            var isPopular = checkbox.checked ? 1 : 0;

            // Create an AJAX request
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "update_popular_status.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        console.log("Status updated successfully.");
                    } else {
                        console.error("Error updating status.");
                    }
                }
            };

            xhr.send("itemId=" + itemId + "&is_popular=" + isPopular);
        }
    </script>

    <script src="sidebar.js"></script>
    <script>
        const modal = document.querySelector('.modal');
        const buttons = document.querySelectorAll('.toggleButton');

        buttons.forEach(button => {
            button.addEventListener('click', () => {
                modal.classList.toggle('open');
            });
        });
    </script>
    <script>
        let sizeIndex = 1;

        function openModal() {
            document.getElementById('categoryModal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('categoryModal').classList.remove('open');
        }

        function openItemModal() {
            document.getElementById('itemModal').classList.add('open');
        }

        function closeItemModal() {
            document.getElementById('itemModal').classList.remove('open');
        }
        function openPayGcash() {
        document.getElementById('addGcashModal').classList.add('open');
    }

    function closePayGcash() {
        document.getElementById('addGcashModal').classList.remove('open');
    }

    <?php if ($uploadSuccess || isset($_POST['gcash_image'])): ?>
        // Auto-open modal after upload
        window.addEventListener('DOMContentLoaded', () => {
            openPayGcash();
        });
    <?php endif; ?>

        function openEditItemModal(button) {
            // Get item details
            const itemId = button.getAttribute('data-itemid');
            const itemName = button.getAttribute('data-itemname');
            const description = button.getAttribute('data-description');
            const image = button.getAttribute('data-image');
            const category = button.getAttribute('data-category');
            const status = button.getAttribute('data-status');

            // Parse sizes JSON safely
            let sizes = [];
            try {
                sizes = JSON.parse(button.getAttribute('data-sizes')) || [];
            } catch (e) {
                console.error("Invalid sizes JSON:", e);
            }

            // Set static modal fields
            document.getElementById('editItemId').value = itemId;
            document.getElementById('editItemName').value = itemName;
            document.getElementById('editDescription').value = description;
            document.getElementById('editStatus').value = status;
            document.getElementById('editCatName').value = category;
            document.getElementById('editExistingImage').value = image;

            // Populate sizes dynamically
            const container = document.getElementById('editsizesContainer');
            container.innerHTML = ''; // Clear existing entries
            sizeIndex = 1;

            sizes.forEach((sizeObj, idx) => {
                const entry = document.createElement("div");
                entry.className = "size-entry";
                entry.innerHTML = `
                <input type="checkbox" name="sizes[${idx}][enabled]" value="1" checked>
                <input type="text" name="sizes[${idx}][size]" value="${sizeObj.size}" placeholder="Size (e.g. Medium)" class="input" required>
                <input type="number" name="sizes[${idx}][price]" value="${sizeObj.price}" placeholder="Price" class="input" step="0.01" required>
            `;
                container.appendChild(entry);
                sizeIndex = idx + 1;
            });

            document.getElementById('editItemModal').classList.add('open');
        }

        function closeEditItemModal() {
            document.getElementById('editItemModal').classList.remove('open');
        }

        function filterCategories() {
            const category = document.getElementById('categoryFilter').value;
            const rows = document.querySelectorAll('#menuTable tbody tr');
            rows.forEach(row => {
                row.style.display = (category === "" || row.dataset.category === category) ? '' : 'none';
            });
        }

        function editItem(itemId) {
            window.location.href = `edit_item.php?id=${itemId}`;
        }

        function deleteItem(itemId) {
            if (confirm("Are you sure you want to delete this item?")) {
                window.location.href = `delete_item.php?id=${itemId}`;
            }
        }

        function addSizeField() {
            const container = document.getElementById("sizesContainer");
            const entry = document.createElement("div");
            entry.className = "size-entry";
            entry.innerHTML = `
            <input type="checkbox" name="sizes[${sizeIndex}][enabled]" value="1">
            <input type="text" name="sizes[${sizeIndex}][size]" placeholder="Size (e.g. Medium)" class="input" required>
            <input type="number" name="sizes[${sizeIndex}][price]" placeholder="Price" class="input" step="0.01" required>
        `;
            container.appendChild(entry);
            sizeIndex++;
        }
        function editaddSizeField() {
            const container = document.getElementById("editsizesContainer");
            const entry = document.createElement("div");
            entry.className = "size-entry";
            entry.innerHTML = `
        <input type="checkbox" name="sizes[${sizeIndex}][enabled]" value="1">
        <input type="text" name="sizes[${sizeIndex}][size]" placeholder="Size (e.g. Medium)" class="input" required>
        <input type="number" name="sizes[${sizeIndex}][price]" placeholder="Price" class="input" step="0.01" required>
    `;
            container.appendChild(entry);
            sizeIndex++;
        }
        function validateTimeRange() {
    const minTime = parseInt(document.getElementById('min_time').value);
    const maxTime = parseInt(document.getElementById('max_time').value);

    if (minTime > maxTime) {
        alert('Minimum time cannot be greater than maximum time.');
        return false;
    }
    return true;
}
    </script>



</body>

</html>