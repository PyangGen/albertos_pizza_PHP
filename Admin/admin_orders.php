<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
    header("Location: ../login.php");
    exit();
}

include 'db_connection.php'; // Make sure to include your database connection

$statusFilter = isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '';
$searchOrderId = isset($_GET['searchOrderId']) ? $_GET['searchOrderId'] : '';

$query = "SELECT order_id, order_date, firstName, lastName, phone, grand_total, order_status, pmode, cancel_reason, image FROM orders";
$conditions = [];

if (!empty($statusFilter)) {
    $conditions[] = "order_status = '" . $conn->real_escape_string($statusFilter) . "'";
}

if (!empty($searchOrderId)) {
    $conditions[] = "order_id LIKE '%" . $conn->real_escape_string($searchOrderId) . "%'";
}

if (!empty($conditions)) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY order_id DESC";

$result = $conn->query($query);

?>
<?php
include 'sidebar.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <!--poppins-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="sidebar.css">
    <link rel="stylesheet" href="admin_orders.css">
    <style>
  .content{
    margin-bottom: 40px;
  }
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
  background-color: #fb4a36;
  color: white;
  font-weight: bold;
  border-color: #fb4a36;
}

  
</style>
</head>

<body>
    <div class="sidebar">
        <button class="close-sidebar" id="closeSidebar">&times;</button>
       
        <!-- Profile Section -->
    <div class="profile-section">
      <img src="../uploads/<?php echo htmlspecialchars($admin_info['profile_image']); ?>" alt="Profile Picture">
      <div class="info">
        <h3>Welcome Back!</h3>
        <p><?php echo htmlspecialchars($admin_info['firstName']) . ' ' . htmlspecialchars($admin_info['lastName']); ?></p>
      </div>
    </div>

    <!-- Navigation Items -->

    <ul>
            <li><a href="index.php" ><i class="fas fa-chart-line"></i> Overview</a></li>
            <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
            <li><a href="admin_orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
            <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="reviews.php"><i class="fas fa-star"></i> Reviews</a></li>
            <li><a href="staffs.php" ><i class="fas fa-users"></i> Staffs</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> Profile Setting</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </div>
    <div class="content">
        <div class="header">
            <button id="toggleSidebar" class="toggle-button">
                <i class="fas fa-bars"></i>
            </button>
            <h2><i class="fas fa-shopping-cart"></i> Orders</h2>
        </div>

        <div class="actions">
            <div>
            <button id="refreshButton" onclick="refreshPage()" title="Refresh">
                <i class="fas fa-sync-alt"></i>
            </button>
           
            </div>
            
            <div class="filter-orders">
                <select id="statusFilter" name="statusFilter" onchange="filterByStatus()">
                    <option value="">All Orders</option>
                    <option value="Pending">Pending</option>
                    <option value="On Process">Process</option>
                    <option value="On Process">On the way </option>
                    <option value="Completed">Completed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>
                <input type="text" id="searchOrderId" placeholder="Search by Order ID" oninput="searchByOrderId()">
            </div>
        </div>
        <?php
// Connect to database
$conn = new mysqli("localhost", "root", "", "restaurant"); // Update this

// Set how many results per page
$resultsPerPage = 6;

// Get current page number from URL, default to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;

// Calculate offset
$offset = ($page - 1) * $resultsPerPage;

// Count total orders for pagination
$totalResult = $conn->query("SELECT COUNT(*) AS total FROM orders");
$totalRows = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $resultsPerPage);

// Fetch limited records
$sql = "SELECT * FROM orders ORDER BY order_id DESC LIMIT $resultsPerPage OFFSET $offset";
$result = $conn->query($sql);

// Display orders
echo "<table>
    <tr>
        <th>Order ID</th>
        <th>Customer Name</th>
        <th>Contact</th>
        <th>Total</th>
        <th>Order Status</th>
        <th>Payment Mode</th>
        <th>Cancel Reason</th>
        <th>Action</th>
    </tr>";

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusClass = '';
        switch ($row['order_status']) {
            case 'Pending': $statusClass = 'status-pending'; break;
            case 'Processing': $statusClass = 'status-processing'; break;
            case 'Completed': $statusClass = 'status-completed'; break;
            case 'Cancelled': $statusClass = 'status-cancelled'; break;
            case 'On the way': $statusClass = 'status-ontheway'; break;
        }

        echo "<tr>
            <td>" . $row['order_id'] . "</td>
            <td>" . $row['firstName'] . " " . $row['lastName'] . "</td>
            <td>" . $row['phone'] . "</td>
            <td>₱ " . $row['grand_total'] . "</td>
            <td><span class='status $statusClass'>" . $row['order_status'] . "</span></td>
            <td>" . htmlspecialchars($row['pmode']) . 
    ($row['pmode'] === 'Gcash' && !empty($row['image']) ? 
    "<br><img src='../uploads/" . htmlspecialchars($row['image']) . "' 
        alt='Payment Image' 
        style='width:40px; height:auto; border-radius:3px; cursor:pointer;' 
        onclick=\"openModal('../uploads/" . htmlspecialchars($row['image']) . "')\">" 
    : "") . 
"</td>

            <td>" . ($row['order_status'] == 'Cancelled' ? $row['cancel_reason'] : '-') . "</td>
            <td><button id='viewbtn' onclick=\"viewDetails(" . $row['order_id'] . ")\">View Details</button></td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='8' style='text-align: center;'>No Orders Found</td></tr>";
}

echo "</table>";

// Display pagination with Previous and Next
echo "<div class='pagination-container'>";

// Previous button
if ($page > 1) {
    $prevPage = $page - 1;
    echo "<a href='?page=$prevPage'>&laquo; Prev</a>";
}

// Numbered pages
for ($i = 1; $i <= $totalPages; $i++) {
    $activeClass = ($i == $page) ? "active" : "";
    echo "<a class='$activeClass' href='?page=$i'>$i</a>";
}

// Next button
if ($page < $totalPages) {
    $nextPage = $page + 1;
    echo "<a href='?page=$nextPage'>Next &raquo;</a>";
}

echo "</div>";



$conn->close();
?>

    </div>
    <!-- Image Modal -->
<div id="imageModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.8); justify-content:center; align-items:center; z-index:9999;">
    <span onclick="closeModal()" style="position:absolute; top:20px; right:30px; color:white; font-size:30px; cursor:pointer;">&times;</span>
    <img id="modalImage" src="" style="max-width:90%; max-height:90%;">
</div>


    <?php
    include_once ('footer.html');
    ?>
    <script src="sidebar.js"></script>
    <script>
        function openModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}
                function viewDetails(orderId) {
            window.location.href = 'view_order.php?orderId=' + orderId;
        }
    const modal = document.querySelector('.modal');
    const buttons = document.querySelectorAll('.toggle-button');

    buttons.forEach(button => {
        button.addEventListener('click', () => {
            modal.classList.toggle('open');
        });
    });

    function filterByStatus() {
        var statusFilter = document.getElementById('statusFilter').value;
        var dateFilter = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : ''; // Optional date filter
        var searchOrderId = document.getElementById('searchOrderId').value.trim();
        window.location.href = 'admin_orders.php?statusFilter=' + encodeURIComponent(statusFilter) + '&dateFilter=' + encodeURIComponent(dateFilter) + '&searchOrderId=' + encodeURIComponent(searchOrderId);
    }

    function searchByOrderId() {
        filterByStatus(); // Call filterByStatus to update results based on search input
    }

    function refreshPage() {
        window.location.href = 'admin_orders.php'; // Reload the page
    }

    // Set the status filter select value based on the query parameter
    document.getElementById('statusFilter').value = "<?= isset($_GET['statusFilter']) ? $_GET['statusFilter'] : '' ?>";

    // Optional: Set the date filter value if you have a date filter
    if (document.getElementById('dateFilter')) {
        document.getElementById('dateFilter').value = "<?= isset($_GET['dateFilter']) ? $_GET['dateFilter'] : '' ?>";
    }

    // Set the search input value based on the query parameter
    document.getElementById('searchOrderId').value = "<?= isset($_GET['searchOrderId']) ? $_GET['searchOrderId'] : '' ?>";

    // Attach event listeners to filters
    document.getElementById('statusFilter').addEventListener('change', filterByStatus);
    if (document.getElementById('dateFilter')) {
        document.getElementById('dateFilter').addEventListener('change', filterByStatus);
    }
    document.getElementById('searchOrderId').addEventListener('input', searchByOrderId);
    document.getElementById('refreshButton').addEventListener('click', refreshPage);
</script>



</body>

</html>