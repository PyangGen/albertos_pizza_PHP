<?php
session_start();
include 'db_connection.php';

if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// JOIN menuitem and menuitem_sizes on itemId, only for popular items
$sql = "SELECT 
          mi.itemId, mi.itemName, mi.image, mi.description, 
          ms.size, ms.price 
        FROM 
          menuitem mi
        LEFT JOIN 
          menuitem_sizes ms 
        ON 
          mi.itemId = ms.itemId
        WHERE 
          mi.is_popular = 1";

$result = $conn->query($sql);

$popularItems = [];

if ($result) {
  while ($row = $result->fetch_assoc()) {
    $itemId = $row['itemId'];

    // Initialize item entry if not already set
    if (!isset($popularItems[$itemId])) {
      $popularItems[$itemId] = [
        'itemId' => $itemId,
        'itemName' => $row['itemName'],
        'image' => $row['image'],
        'description' => $row['description'],
        'sizes' => []
      ];
    }

    // Add size and price if available
    if (!empty($row['size']) && !empty($row['price'])) {
      $popularItems[$itemId]['sizes'][] = [
        'size' => $row['size'],
        'price' => $row['price']
      ];
    }
  }

  $result->close();
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!--Bootstrap CSS-->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous" />
  <!--poppins-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <!--Icon-->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.min.css' />
  <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css' />
  <link href="https://fonts.googleapis.com/css2?family=Allura&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/assets/owl.carousel.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <!-- Chewy Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chewy&display=swap" rel="stylesheet">
  <!-- AOS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="index.css">
  <title>Home</title>
</head>

<body>
  <?php
  if (isset($_SESSION['userloggedin']) && $_SESSION['userloggedin']) {
    include 'nav-logged.php';
  } else {
    include 'navbar.php';
  }
  ?>

  <div class="main">
    <section>
      <div class="container mt-3">
        <div class="row d-flex justify-content-start align-items-start main-container">
          <div class="col-md-5 col-sm-12 col-lg-5 reveal main-text mb-4 text-align-justify mt-5" data-aos="fade-up">
            <h2>Welcome to <span style="color: #fb4a36;"> Alberto's Pizza,</span></h2>
            <h4 style="color: gray; font-weight: 450;">"a taste you'll surelly miss..."</h4>
            <p style="font-size: 18px; text-align: justify;">
            Alberto’s Pizza proudly started as a small-time pizza store in Cebu City near the Vicente Sotto Memorial Medical Center. To stand above the rest of the competition, the founders of Alberto’s Pizza wanted their products to be as affordable as they can be without hurting the quality and freshness of their pizzas. Alberto’s only focused on deliveries and take-outs due to the limited space available but after several months, word spread out very quickly and they were getting more and more orders from doctors, nurses, interns, and even patients! Different people from all walks of life began to discover this hidden gem of a pizza parlor and they all can’t get enough of Alberto’s Pizza’s delicious menu.
            </p>
            <div class="buttondiv">
              <div>
                <a href="menu.php">
                  <button class="button">
                    Start Order
                    <svg class="cartIcon" viewBox="0 0 576 512">
                      <path d="M0 24C0 10.7 10.7 0 24 0H69.5c22 0 41.5 12.8 50.6 32h411c26.3 0 45.5 25 38.6 50.4l-41 152.3c-8.5 31.4-37 53.3-69.5 53.3H170.7l5.4 28.5c2.2 11.3 12.1 19.5 23.6 19.5H488c13.3 0 24 10.7 24 24s-10.7 24-24 24H199.7c-34.6 0-64.3-24.6-70.7-58.5L77.4 54.5c-.7-3.8-4-6.5-7.9-6.5H24C10.7 48 0 37.3 0 24zM128 464a48 48 0 1 1 96 0 48 48 0 1 1 -96 0zm336-48a48 48 0 1 1 0 96 48 48 0 1 1 0-96z"></path>
                    </svg>
                  </button>
                </a>
              </div>
              <!-- <div>
                <a class="button1" href="menu.php">
                  <span class="button__icon-wrapper">
                    <svg width="10" class="button__icon-svg" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 15">
                      <path fill="currentColor" d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"></path>
                    </svg>
                    <svg class="button__icon-svg button__icon-svg--copy" xmlns="http://www.w3.org/2000/svg" width="10" fill="none" viewBox="0 0 14 15">
                      <path fill="currentColor" d="M13.376 11.552l-.264-10.44-10.44-.24.024 2.28 6.96-.048L.2 12.56l1.488 1.488 9.432-9.432-.048 6.912 2.304.024z"></path>
                    </svg>
                  </span>
                  Explore Menu
                </a>
              </div> -->
            </div>
          </div>
          <div class="col-md-7 col-sm-12 col-lg-7 d-flex justify-content-center align-items-start slide-in-right main-image">
            <img src="images/Pizza.png" class="img" style=" width: 85%; height: 80%;">
          </div>
        </div>
        <div class="row">
          <!-- Menu Section -->
          <section>
            <div class="menu-section">
              <div class="container-fluid">
                <div class="row">
                  <div class="row d-flex justify-content-center align-items-center mb-4 font-weight-bold" id="text">
                    <h1>OUR <span>MENU</span></h1>
                  </div>
                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/appe-index.avif');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                          <h3>Appetizer</h3>
                          <p>Start your meal with our delicious appetizers that set the tone for a delightful dining experience.</p>
                          <a href="menu.php#appetizer">
                            <button class="explore-btn">Explore Variety</button></a>
                        </div>
                      </div>
                      <div class="card-bottom">
                        <h3>Appetizer</h3>
                        <a href="menu.php#appetizer">
                          <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/index-pizza.jpg');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                          <h3>Pizza</h3>
                          <p>Indulge in our wide variety of pizzas, each crafted with the finest ingredients and baked to perfection.</p>
                          <a href="menu.php#pizza">
                            <button class="explore-btn">Explore Variety</button></a>
                        </div>
                      </div>
                      <div class="card-bottom">
                        <h3>Pizza</h3>
                        <a href="menu.php#pizza">
                          <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/index-burger.avif');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                          <h3>Burger</h3>
                          <p>Savor our juicy burgers, loaded with fresh toppings and bursting with flavor in every bite.</p>
                          <a href="menu.php#burger">
                            <button class="explore-btn">Explore Variety</button></a>
                        </div>
                      </div>
                      <div class="card-bottom">
                        <h3>Burger</h3>
                        <a href="menu.php#burger">
                          <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-3 col-md-6 mb-4">
                    <div class="category-card" style="background-image: url('images/bev-index.jpeg');" data-aos="fade-up">
                      <div class="card-overlay">
                        <div class="overlay-content">
                          <h3>Beverage</h3>
                          <p>Quench your thirst with our selection of refreshing beverages, perfect for any meal.</p>
                          <a href="menu.php#beverage">
                            <button class="explore-btn">Explore Variety</button></a>
                        </div>
                      </div>
                      <div class="card-bottom">
                        <h3>Beverage</h3>
                        <a href="menu.php#beverage">
                          <button class="explore-btn">Explore Variety</button></a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>
    </section>
  </div>

  <!-- Why Choose Us Section  -->
  <section class="why-choose-us" id="why-choose-us">
    <div class="container">
      <div class="row why-us-content">
        <div class="col-md-12 col-lg-6 col-sm-12 col-xs-12 mt-5 reveal d-flex justify-content-start align-items-start" data-aos="fade-up">
          <img src="images/Why-Us.png" width="100%" height="auto" loading="lazy" alt="delivery boy" class="w-100 delivery-img" data-delivery-boy>
        </div>
        <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 d-flex flex-column justify-content-center reveal" data-aos="fade-up">
          <h1>WHY <span>CHOOSE US?</span></h1>
          <p class="content">Our restaurant offers the best food delivery service with fresh and high-quality ingredients.</p>
          <ul class="why-choose-us-list">
            <li data-aos="fade-up">
              <div class="image-wrapper mt-1">
                <img src="icons/delivery-man.png" alt="Fast Delivery">
              </div>
              <div class="feature-content">
                <h4>Fast Delivery</h4>
                <p>Enjoy prompt and reliable delivery to your doorstep.</p>
              </div>
            </li>
            <li data-aos="fade-up">
              <div class="image-wrapper">
                <img src="icons/vegetables.png" alt="Fresh Ingredients">
              </div>
              <div class="feature-content">
                <h4>Fresh Ingredients</h4>
                <p>We use only the freshest and highest quality ingredients.</p>
              </div>
            </li>
            <li data-aos="fade-up">
              <div class="image-wrapper">
                <img src="icons/waiter (1).png" alt="Friendly Service" class="why-us-image">
              </div>
              <div class="feature-content">
                <h4>Friendly Service</h4>
                <p>Experience warm and welcoming customer service.</p>
              </div>
            </li>
            <li data-aos="fade-up">
              <div class="image-wrapper">
                <img src="icons/tasty.png" alt="Exceptional Taste">
              </div>
              <div class="feature-content">
                <h4>Exceptional Taste</h4>
                <p>Indulge in flavors that are truly exceptional.</p>
              </div>
            </li>
          </ul>
        </div>
      </div>

      <!-- Top picks section -->
      <div class="popular reveal" data-aos="fade-up">
        <h1 class="text-center mt-3">OUR <span>TOP PICKS</span></h1>
        <P class="text-center" style="font-size: 1.3rem;">~Handpicked meals that are a hit with everyone.</P>

        <div id="cardCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="8000" data-aos="fade-up">
          <div class="carousel-inner">

            <div id="toast" class="toast">
              <button class="toast-btn toast-close">&times;</button>
              <span class="pt-3"><strong>You must log in to add items to the cart.</strong></span>
              <button class="toast-btn toast-ok">Okay</button>
            </div>
            <?php
$chunkedItems = array_chunk($popularItems, 3); // Group items into chunks of 3
$isActive = true; // First carousel item is active

foreach ($chunkedItems as $items) {
    echo '<div class="carousel-item' . ($isActive ? ' active' : '') . '">';
    echo '<div class="d-flex justify-content-center">';

    foreach ($items as $item) {
        // Start creating the card for each item
        echo '<div class="card m-2" style="width: 18rem;">';
        
        // Image with associated data (size/price) passed in data attributes
        echo '<img src="uploads/' . htmlspecialchars($item['image']) . '" 
      class="card-img-top item-image" 
      alt="' . htmlspecialchars($item['itemName']) . '"
      data-name="' . htmlspecialchars($item['itemName']) . '" 
      data-description="' . htmlspecialchars($item['description']) . '" 
      data-image="uploads/' . htmlspecialchars($item['image']) . '" 
      data-sizes="' . htmlspecialchars(json_encode($item['sizes']), ENT_QUOTES, 'UTF-8') . '"
>';


        // Card body with title and "Add to Cart" button
        echo '<div class="card-body text-center">';
        echo '<h5 class="card-title">' . htmlspecialchars($item['itemName']) . '</h5>';
        echo '<button class="button-cart" 
            onclick="addToCart(this)" 
            data-id="' . $item['itemId'] . '" 
            data-name="' . htmlspecialchars($item['itemName'], ENT_QUOTES) . '" 
            data-image="' . htmlspecialchars($item['image'], ENT_QUOTES) . '" 
            data-code="' . htmlspecialchars($item['itemName'], ENT_QUOTES) . '" 
            data-price="' . (isset($item['sizes'][0]['price']) ? $item['sizes'][0]['price'] : 0) . '">
        Add to Cart
      </button>';

        echo '</div>'; // Close card-body
        echo '</div>'; // Close card
    }

    echo '</div>'; // Close d-flex justify-content-center
    echo '</div>'; // Close carousel-item
    $isActive = false; // Ensure only the first item is active in the carousel
}
?>

          </div>


          <button class="carousel-control-prev" type="button" data-bs-target="#cardCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#cardCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
    </div>
  </section>

  <!-- About Us section -->
  <div class="aboutus" id="About-Us" style="background-image: url(images/about-bg.png); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <section class="our-story-section p-5">
      <div class="container ">
        <div class="row" data-aos="fade-up">
          <h1 style="text-align: center;"><span style="color: #fb4a36;">ABOUT </span>US</h1>
          <h4 style="text-align: center;" class="mb-5">Delighting the Philippines, One Slice at a Time!</h4>
        </div>
        <div class="story-content row mb-2">
          <div class="story-text col-lg-6 col-md-6 col-sm-12 reveal mt-2" data-aos="fade-up" data-os-interval="300">
            <p>At <strong>Alberto’s Pizza</strong>, we take pride in crafting delicious, affordable pizzas that bring people together. Born in the heart of the Philippines, we’ve become a beloved name in local dining—thanks to our generous toppings, flavorful crusts, and budget-friendly prices.</p>
            <p>Founded in 2002 in Cebu City, Alberto’s Pizza has grown from a humble local shop to a popular pizza destination across the country. Our commitment to quality ingredients, homegrown recipes, and friendly service has earned the trust and love of Filipino families and students alike.</p>
            <p>Whether you’re celebrating with friends, grabbing a quick bite, or simply craving comfort food, Alberto’s Pizza is your go-to place for great taste at a great value. Come and enjoy the flavor that truly says "Lami gyud!"</p>
            <a href="menu.php" class="about_btn">
              <i class="fa-solid fa-burger"></i>Order Now
            </a>
          </div>
          <div class="story-image col-lg-6 col-md-6 col-sm-12 d-flex justify-content-end align-items-start slide-in-right" data-aos="fade-up">
            <img src="images/Burger.png" alt="Crafting Memorable Meals" style="width: 100%; height: auto;">
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- Table Reservation -->
  <section class="table-reservation" id="Reservation">
    <div class="row text-center ms-4" data-aos="fade-up">
      <h1 class="mb-2">TABLE <span style="color: #fb4a36;">RESERVATION</span></h1>
      <h5 class="mb-5">Book your dining experience with us and enjoy a delightful meal.</h5>
    </div>
    <div class="table ms-4 me-5" data-aos="fade-up">
      <div class="reservation row reveal">
        <div class="reservation-image col-lg-7 col-md-6 col-sm-12" style="background: none !important; padding: 0 !important;">
          <img src="images/table.jpg" alt="Reservation" style="background: none ; width: 100%; height: 100%; padding: 0 !important;" class=" w-100 h-100">
        </div>
        <div class="reservation-section col-lg-5 col-md-6 col-sm-12">
          <h2 style="background-color: #feead4;">Reserve Now!</h2>
          <form id="reservation-form" action="reservations.php" method="POST">
            <div class="form-row">
              <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
              </div>
              <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="phone">Contact:</label>
                <input type="tel" id="phone" name="contact" required>
              </div>
              <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" id="date" name="reservedDate" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label for="reservedTime">Time:</label>
                <input type="time" id="time" name="reservedTime" required>
              </div>
              <div class="form-group">
                <label for="guests">Number of Guests:</label>
                <input type="number" id="guests" name="noOfGuests" required min="1">
              </div>
            </div>
            <button type="submit" value="submit">Reserve Now</button>
          </form>
        </div>
      </div>
    </div>
  </section>

  <!-- Review  -->
  <section class="testimonial" id="review">
  <div class="container">
    <div class="row">
      <div class="col-lg-8 offset-lg-2 col-md-10 offset-md-1">
        <div class="text-center mb-5" data-aos="fade-up">
          <h1>Hear From Our <span>Happy Customers!</span></h1>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="clients-carousel owl-carousel" data-aos="fade-up">
      <?php 
require 'db_connection.php';
$query = "SELECT * FROM reviews WHERE status = 'approved'";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
  while ($review = $result->fetch_assoc()) {
    $image = !empty($review['profile_image']) ? "uploads/" . $review['profile_image'] : "uploads/default.jpg";
    $videoPath = !empty($review['video_path']) ? "" . $review['video_path'] : null;

    echo '<div class="single-box" style="display: flex; gap: 20px; align-items: center; margin-bottom: 30px;">';

    // Left side: image + review text
    echo '  <div class="review-content" style="flex: 1;">';
    echo '    <div class="img-area"><img alt="Reviewer" class="img-fluid" src="' . $image . '" style="width: 100px; height: 100px; object-fit: cover; border-radius: 50%;"></div>';
    echo '      <h4>' . htmlspecialchars($review['email']) . '</h4>';
    echo '    <div class="content">';
    echo '      <p style="margin-top: 10px;">"' . htmlspecialchars($review['review_text']) . '"</p>';
    
    echo '    </div>';
    echo '  </div>';

    // Right side: video
    echo '  <div class="review-video" style="flex-shrink: 0;">';
    if ($videoPath) {
      echo '    <video width="300" height="200" controls style="border-radius: 8px;">';
      echo '      <source src="' . $videoPath . '" type="video/mp4">';
      echo '      Your browser does not support the video tag.';
      echo '    </video>';
    }
    echo '  </div>';

    echo '</div>';
  }
} else {
  echo '<div class="single-box"><div class="content"><p>No reviews available yet.</p></div></div>';
}
?>



      </div>
    </div>
  </div>
</section>


  <!-- footer -->
  <footer>
    <div class="footer-container">
      <div class="footer-row">
        <div class="footer-col" id="contact">
          <h4>Contact Us</h4>
          <p>Fb: albertospizza</p>
          <p>Email: albertospizzamain</p>
          <p>Phone: (032) 254 0042</p>
        </div>
        <div class="footer-col">
          <h4>Follow Us</h4>
          <div class="social-icons">
            <a href="#"><i class="fab fa-facebook-f"></i></a>
            <a href="#"><i class="fab fa-instagram"></i></a>
            <a href="#"><i class="fab fa-twitter"></i></a>
            <a href="#"><i class="fab fa-youtube"></i></a>
          </div>
        </div>
        <div class="footer-col">
          <h4>Subscribe</h4>
          <form action="#">
            <input type="email" placeholder="Your email address" required style="background-color: #f9f9f9; color: #333; margin-top: 12px;">
            <button type="submit">Subscribe</button>
          </form>
        </div>
      </div>
      <div class="footer-bottom">
        <h4> Fast Food WordPress Theme Copyright &copy; 2025 All Rights Reserved.</h4>
      </div>
    </div>
  </footer>
<!-- Modal for showing price, description, and image -->
<div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="itemModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="itemModalLabel">Item Details</h5>
      </div>
      <div class="modal-body d-flex flex-column align-items-center">
        <img id="modalItemImage" src="" alt="Item Image" class="img-fluid mb-3" style="max-width: 300px; max-height: 300px; object-fit: contain;">
        <h5 id="modalItemName"></h5>
        <p id="modalItemDescription"></p>
        <div id="modalItemSize" class="text-center"></div>
        <div id="modalItemPrice" class="text-center"></div>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

      </div>
    </div>
  </div>
</div>
<!-- Bootstrap 5 Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.2.1/owl.carousel.min.js">
  </script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js">
  </script>
  <!-- AOS -->
  <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>
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
  <script>
    $('.clients-carousel').owlCarousel({
      loop: true,
      nav: false,
      autoplay: true,
      autoplayTimeout: 5000,
      animateOut: 'fadeOut',
      animateIn: 'fadeIn',
      smartSpeed: 450,
      margin: 30,
      responsive: {
        0: {
          items: 1
        },
        768: {
          items: 2
        },
        991: {
          items: 2
        },
        1200: {
          items: 2
        },
        1920: {
          items: 2
        }
      }
    });
  </script>
  <script>
function addToCart(button) {
  const userLoggedIn = <?php echo isset($_SESSION['userloggedin']) ? 'true' : 'false'; ?>;

  if (!userLoggedIn) {
    showToast();
    return;
  }

  const pid = button.getAttribute('data-id');
  const pname = button.getAttribute('data-name');
  const pprice = button.getAttribute('data-price');
  const pimage = button.getAttribute('data-image');
  const pcode = button.getAttribute('data-code');

  fetch('add_to_cart.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams({
      pid: pid,
      pname: pname,
      pprice: pprice,
      pimage: pimage,
      pcode: pcode
    })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      alert(data.message);
      loadCartItemCount(); // 🔁 Update cart badge count
    } else {
      alert(data.message);
    }
  })
  .catch(err => {
    console.error('Error:', err);
  });
}
function loadCartItemCount() {
  fetch('cart_count.php')
    .then(res => res.text())
    .then(count => {
      document.getElementById('cart-item').textContent = count;
    })
    .catch(err => console.error('Cart count error:', err));
}

document.addEventListener('DOMContentLoaded', loadCartItemCount);

    function showToast() {
      var toast = document.getElementById("toast");
      toast.className = "toast show";

      // Handle "Okay" button click
      document.querySelector('.toast-ok').onclick = function() {
        window.location.href = 'login.php'; // Redirect to login page
      };

      // Handle "Close (X)" button click
      document.querySelector('.toast-close').onclick = function() {
        toast.className = toast.className.replace("show", "hide");
      };
    }
  </script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <!-- Bootstrap JS and dependencies -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const elements = document.querySelectorAll('.animate-on-scroll');
      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('reveal');
          }
        });
      }, {
        threshold: 0.1
      });

      elements.forEach(element => {
        observer.observe(element);
      });
    });
    document.querySelectorAll('.item-image').forEach(image => {
    image.addEventListener('click', function () {
        const name = this.getAttribute('data-name');
        const description = this.getAttribute('data-description');
        const imgSrc = this.getAttribute('data-image');
        const sizesJson = this.getAttribute('data-sizes');

        // Update modal fields
        document.getElementById('modalItemName').innerText = name;
        document.getElementById('modalItemDescription').innerText = description;
        document.getElementById('modalItemImage').src = imgSrc;

        const sizeContainer = document.getElementById('modalItemSize');
        const priceContainer = document.getElementById('modalItemPrice');

        sizeContainer.innerHTML = ''; // Clear previous content
        priceContainer.innerHTML = ''; // Clear previous content

        if (sizesJson) {
            try {
                const sizes = JSON.parse(sizesJson); // Parse the sizes JSON
                if (sizes.length > 0) {
                    sizes.forEach(sizeObj => {
                        // Create new paragraph for size
                        const sizeText = document.createElement('p');
                        sizeText.textContent = 'Size: ' + sizeObj.size;
                        sizeContainer.appendChild(sizeText);

                        // Create new paragraph for price
                        const priceText = document.createElement('p');
                        priceText.textContent = 'Price: ₱' + sizeObj.price;
                        priceContainer.appendChild(priceText);
                    });
                } else {
                    sizeContainer.innerHTML = "<p>No sizes available</p>";
                    priceContainer.innerHTML = "<p>No prices available</p>";
                }
            } catch (err) {
                console.error('Error parsing JSON for sizes:', err);
            }
        } else {
            sizeContainer.innerHTML = "<p>No size data available</p>";
            priceContainer.innerHTML = "<p>No price data available</p>";
        }

        $('#itemModal').modal('show'); // Show modal
    });
});

    
  </script>


</body>
</html>