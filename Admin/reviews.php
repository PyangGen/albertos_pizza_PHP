<?php
session_start();
if (!isset($_SESSION['adminloggedin'])) {
  header("Location: ../login.php");
  exit();
}

include 'db_connection.php';
?>
<?php
include 'sidebar.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Review Management</title>

  <!--poppins-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="admin_reservation.css">
  <link rel="stylesheet" href="sidebar.css">
  <link rel="stylesheet" href="admin_review.css">
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
    background-color: #fb4a36;
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
      <li><a href="admin_menu.php"><i class="fas fa-utensils"></i> Menu Management</a></li>
      <li><a href="admin_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
      <li><a href="reservations.php"><i class="fas fa-calendar-alt"></i> Reservations</a></li>
      <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
      <li><a href="reviews.php" class="active"><i class="fas fa-star"></i> Reviews</a></li>
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
      <h2><i class="fas fa-star"></i> Reviews</h2>
    </div>

    <div class="actions">
      <select id="statusFilter" name="statusFilter" onchange="filterByStatus()">
        <option value="">All</option>
        <option value="pending">Pending</option>
        <option value="approved">Approved</option>
        <option value="rejected">Rejected</option>
      </select>
    </div>

    <?php
    // Include the database connection
    include 'db_connection.php';

    // Define items per page
    $itemsPerPage = 5;

    // Get the current page from the URL, default to 1 if not set
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;

    // Calculate the offset for the query
    $offset = ($page - 1) * $itemsPerPage;

    // Modify the SQL query to include pagination (no search)
    $sql = "SELECT * FROM reviews LIMIT $itemsPerPage OFFSET $offset"; // Add LIMIT and OFFSET for pagination
    
    // Get total reviews count for pagination
    $totalResult = mysqli_query($conn, "SELECT COUNT(*) AS count FROM reviews");
    $totalRows = mysqli_fetch_assoc($totalResult)['count'];
    $totalPages = ceil($totalRows / $itemsPerPage);

    // Execute the query
    $result = mysqli_query($conn, $sql);
    ?>
   



    <div class="table">
      <table id="reviewTable">
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Email</th>
            <th>Review Text</th>
            <th>Rating</th>
            <th>Video</th>
            <th>Status</th>
            <th>Response</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    $ratingStars = str_repeat('&#9733;', $row['rating']) . str_repeat('&#9734;', 5 - $row['rating']);
    $videoPath = !empty($row['video_path']) ? $row['video_path'] : null;


    echo "<tr>
      <td>{$row['order_id']}</td>
      <td>{$row['email']}</td>
      <td>{$row['review_text']}</td>
      <td class='rating-stars'>{$ratingStars}</td>
      <td>";

      if ($videoPath) {
        echo "<video width='300' height='200' controls style='border-radius: 8px;'>
                <source src='{$videoPath}' type='video/mp4'>
                Your browser does not support the video tag.
              </video>";
      } else {
        echo "No Video";
      }
      echo "</td>
      
      <td>
        <select id='status-{$row['order_id']}' onchange='updateStatus({$row['order_id']}, this.value)' class='status-select'>
          <option value='pending' " . ($row['status'] == 'pending' ? 'selected' : '') . ">Pending</option>
          <option value='approved' " . ($row['status'] == 'approved' ? 'selected' : '') . ">Approved</option>
          <option value='rejected' " . ($row['status'] == 'rejected' ? 'selected' : '') . ">Rejected</option>
        </select>
      </td>
      <td>{$row['response']}</td>
      <td>
        <button onclick='openEditReviewModal(this)' 
          data-id='{$row['order_id']}'
          data-email='{$row['email']}'
          data-review_text='{$row['review_text']}'
          data-rating='{$row['rating']}'
          data-response='{$row['response']}'>
          <i class='fas fa-edit'></i>
        </button>
        <button onclick=\"deleteReview('{$row['order_id']}', '{$row['email']}')\">
          <i class='fas fa-trash'></i>
        </button>
      </td>
    </tr>";
  }
} else {
  echo "<tr><td colspan='8' style='text-align: center;'>No Reviews</td></tr>";
}
mysqli_close($conn);
?>


        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination-container">
      <?php
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
      ?>
    </div>

  </div>





  <!-- Modal for editing review -->
  <div id="editReviewModal" class="modal">
    <div class="modal-overlay"></div>
    <div class="modal-container">
      <form id="editReviewForm" method="POST" action="edit_review.php">
        <div class="modal-header">
          <h2>Edit Review</h2>
          <span class="close-icon" onclick="closeEditReviewModal()">&times;</span>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="number" name="order_id" id="editOrder_id" class="input" readonly>
            <label for="editOrder_id" class="label">Order ID</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="email" name="email" id="editEmail" class="input" readonly>
            <label for="editEmail" class="label">Email</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="review_text" id="editReview_text" class="input" readonly>
            <label for="editReview_text" class="label">Review Text</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="rating" id="editRating" class="input" readonly>
            <label for="editRating" class="label">Rating</label>
          </div>
        </div>
        <div class="modal-content">
          <div class="input-group">
            <input type="text" name="response" id="editResponse" class="input" required>
            <label for="editResponse" class="label">Response</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="button" onclick="closeEditReviewModal()">Cancel</button>
          <button type="submit" class="button">Save</button>
        </div>
      </form>
    </div>
  </div>

  <?php
  include_once('footer.html');
  ?>
  <script>
    function updateStatus(order_id, status) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "update_review_status.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
          // Check if the response indicates success
          if (xhr.responseText.trim() === "Status updated successfully") {
            // Optionally, display a success message
            alert("Status updated successfully");
          } else {
            // Display an error message
            alert("Error updating status: " + xhr.responseText);
          }
        }
      };
      xhr.send("order_id=" + encodeURIComponent(order_id) + "&status=" + encodeURIComponent(status));
    }



    function deleteReview(orderId, email) {
      // Confirm and handle review deletion
      if (confirm('Are you sure you want to delete this review?')) {
        // Send delete request to server
        fetch('delete_review.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            orderId: orderId,
            email: email
          })
        })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Review deleted successfully');
              location.reload(); // Reload the page to see the updated list
            } else {
              alert('Error deleting review');
            }
          });
      }
    }

    function openEditReviewModal(button) {
      // Get user data from data attributes
      const order_id = button.getAttribute('data-id');
      const email = button.getAttribute('data-email');
      const review_text = button.getAttribute('data-review_text');
      const rating = button.getAttribute('data-rating');
      const response = button.getAttribute('data-response');

      // Set the values in the editReviewForm
      document.getElementById('editOrder_id').value = order_id;
      document.getElementById('editEmail').value = email;
      document.getElementById('editReview_text').value = review_text;
      document.getElementById('editRating').value = rating;
      document.getElementById('editResponse').value = response;

      // Open the edit review modal
      document.getElementById('editReviewModal').classList.add('open');
    }

    function closeEditReviewModal() {
      document.getElementById('editReviewModal').classList.remove('open');
    }

    function filterByStatus() {
      // Get the selected status from the dropdown
      const status = document.getElementById('statusFilter').value;

      // Create an XMLHttpRequest object
      var xhr = new XMLHttpRequest();

      // Set up the request
      xhr.open('POST', 'fetch_review_status.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      // Define what happens on successful data submission
      xhr.onload = function () {
        if (xhr.status === 200) {
          // Update the table with the filtered results
          document.querySelector('#reviewTable tbody').innerHTML = xhr.responseText;
        }
      };

      // Send the request with the selected status
      xhr.send('status=' + encodeURIComponent(status));
    }
   
  </script>
</body>

</html>