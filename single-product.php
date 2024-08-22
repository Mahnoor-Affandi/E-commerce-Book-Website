<?php
session_start();
include('server/connection.php');

// Fetch product details based on product_id
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
}

// Adding product to cart
if (isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) { // Check if user is logged in
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Fetch product details from database
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        $product_name = $product['product_name'];
        $product_price = $product['product_price'];
        $product_image = $product['product_image']; // Ensure product image is fetched

        // Add product to session cart
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = array(
                'name' => $product_name,
                'price' => $product_price,
                'image' => $product_image, // Include product image here
                'quantity' => $quantity
            );
        }

        // Set session variable to trigger popup
        $_SESSION['product_added'] = true;

        header('location: cart.php');
        exit();
    } else {
        // Redirect to login if user is not logged in
        header('location: login.php');
        exit();
    }
}

// Adding product to bookmarks
if (isset($_POST['add_bookmark'])) {
    if (isset($_SESSION['user_id'])) { // Check if user is logged in
        $user_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $product_name = $_POST['product_name'];
        $product_image = $_POST['product_image'];
        $product_price = $_POST['product_price'];

        // Check if the product is already bookmarked
        $stmt = $conn->prepare("SELECT * FROM bookmarks WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $bookmarkExists = $stmt->get_result()->num_rows > 0;

        if (!$bookmarkExists) {
            // Add bookmark to database
            $stmt = $conn->prepare("INSERT INTO bookmarks (user_id, product_id, product_name, product_image, product_price) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iissi", $user_id, $product_id, $product_name, $product_image, $product_price);
            $stmt->execute();
        }

        header('location: single-product.php?product_id=' . $product_id);
        exit();
    }
}
// Fetch reviews based on product_id
if (isset($product_id)) {
    $stmt = $conn->prepare("SELECT r.*, u.user_name FROM reviews r JOIN users u ON r.user_id = u.user_id WHERE r.product_id=? ORDER BY r.created_at DESC");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $reviews = $stmt->get_result();
}

// Fetch recommended products from the same category
if (isset($product_id)) {
    $category = $product['product_category'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_category=? AND product_id != ? ORDER BY RAND() LIMIT 6");
    $stmt->bind_param("si", $category, $product_id);
    $stmt->execute();
    $recommended_products = $stmt->get_result();
}

?>
<?php include 'header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
</head>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

/* Cart Page Specific Styles */
html, body {
    margin: 0;
    padding: 0;
    padding-top: 55px;
    font-family: 'Poppins', sans-serif;

}

body.product-page {
    background-color:#ecf0f1  ; /*ecf0f1 */
}

/* Flexbox container for image and text */
.product-container {
    display: flex;
    flex-wrap: wrap;
    align-items: flex-start;
    gap: 0px; /* Adjust gap as needed */
    padding: 20px;
    width: 115%;
    /*background-color: rgba(0, 0, 0, 0.4);  Dark background with transparency 
    border-radius: 10px;
    backdrop-filter: blur(2.5px); /* Apply blur effect 
    -webkit-backdrop-filter: blur(10px); /* For Safari 
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5); /* Add shadow for a more realistic glass effect 
    color: white; /* Ensure text is readable on the dark background */
}

/* Ensure image takes its natural width without extra margin */
.img-fluid {
    max-width: 100%;
    height: auto;
    max-height: 400px; /* Adjust this value to make the image smaller */
    margin: 0; /* Remove any margin */
    border-radius: 20px;
}

/* Adjust the margin and padding around the text content */
.product-info {
    margin: 0; /* Remove margin */
    padding: 0; /* Remove padding */
    flex: 1;
}

.product-info .product-name{
    font-weight: bold;
}

.product-info .product-price{
    font-size: 1.4rem;
}

/* Limit the description to 4-5 lines */
.product-description {
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 5; /* Adjust the number of lines */
    overflow: hidden;
    text-overflow: ellipsis;
    line-height: 1.6;
    transition: all 0.3s ease;
    font-size: 0.9rem;
}

/* Expanded state */
.product-description.expanded {
    display: block;
    -webkit-line-clamp: unset; /* Show all lines */
    overflow: visible;
}

/* Add "Read More" link */
.read-more {
    display: block;
    margin-top: 5px;
    color: #007bff;
    text-decoration: none;
}

.read-more:hover {
    text-decoration: underline;
}

/* Style for Add to Cart button */
#btn-cart {
    background-color: transparent;    
    border: 1px solid black;
    border-radius: 0px; /* Rounded edges */
    border: 2px solid black; /* Increase the border thickness to 3px */
    color: black;
    padding: 10px 20px;
    margin-top: 10px; /* Space below quantity input */
    transition: background-color 0.3s, border-color 0.3s;
}

#btn-cart:hover {
    background-color: black;
    border-color: #333;
    color: white;
}

/* Style for Bookmark button */
.btn-bookmark i{
    background-color: transparent;
    border: none;
    color: black;
    font-size: 20px; /* Adjust size as needed */
    margin-left: 10px; /* Space next to quantity input */
    transition: color 0.3s;
    color: goldenrod;
}

.btn-bookmark i:hover {
    color: #f7b103;
}

/* Ensure Bookmark button is next to quantity input */
.form-inline {
    display: flex;
    align-items: center;
}

/* Review section styling */
.review {
    margin-bottom: 15px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    background: #fff;
    transition: transform 0.3s, box-shadow 0.3s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.review:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.review .star-rating {
    color: #FFD700;
    display: flex;
    margin-bottom: 10px;
}

.review .star-rating i {
    animation: star-rating 0.5s ease-in-out forwards;
}

@keyframes star-rating {
    from {
        opacity: 0;
        transform: scale(0.5);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.review p {
    margin: 0;
}

.review small {
    color: #777;
}

#review-btn {
    background-color: black;
    border: 1px solid black;
    border-radius: 50px; /* Rounded edges */
    color: white;
    padding: 10px 20px;
    transition: background-color 0.3s, border-color 0.3s;
}

#review-btn:hover {
    background-color: #e8c6c3;
    border-color: #e8c6c3;
    color: black;
}

/* Modern transparent black dropdown styling */
.form-control {
    background-color: rgba(0, 0, 0, 0.5); /* Transparent black background */
    color: #fff; /* White text color */
    border: 1px solid rgba(255, 255, 255, 0.5); /* Transparent white border */
}

.form-control option {
    background-color: rgba(0, 0, 0, 0.8); /* Slightly darker background for options */
    color: #fff; /* White text color for options */
}

/* Animated form fields */
form input, form textarea, form select {
    transition: border-color 0.3s, box-shadow 0.3s;
}

form input:focus, form textarea:focus, form select:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
}

/* Review Reaction Emojis */
.review .review-emoji {
    font-size: 24px;
    margin-right: 10px;
}

/* Like button */
.review .like-button {
    background: none;
    border: none;
    color: #007bff;
    cursor: pointer;
    font-size: 20px;
    margin-top: 10px;
    transition: color 0.3s;
}

.review .like-button:hover {
    color: #0056b3;
}

/* Recommended Products Section */
.recommended-section .card {
    border: none;
    transition: transform 0.3s ease-in-out;
    background: rgba(229, 233, 234, 0.75); /* Transparent glassmorphism effect */
}

.recommended-section .card:hover {
    transform: translateY(-5px);
}

.recommended-section {
  display: flex;
  flex-wrap: wrap; /* Allow cards to wrap to the next row if necessary */
  justify-content: space-between; /* Add spacing between cards */
}

.product-card {
  flex: 0 1 25%; /* Set equal width for each card */
  margin-bottom: 20px; /* Add spacing between cards */
}

.recommended-section .card .card-img-top {
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    max-height: 200px; /* Ensure images have a consistent height */
    object-fit: cover; /* Maintain aspect ratio and cover container */
}

.recommended-section .card .card-body {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    padding: 10px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    font-weight: bold;
}

.recommended-section .card .btn-view {
    background-color: transparent;
            color: #000;
            border: 2px solid #000;
            border-radius: 50px;
            transition: background-color 0.3s, color 0.3s;
            padding: 8px 16px; /* Adjust padding as needed */
            margin-top: auto; /* Push the button to the bottom */
            text-decoration: none;
            width: 100%;
}

.recommended-section .card .btn-view:hover {
    background-color: #000;
    color: #fff;
}


/* Media queries for responsiveness */
@media (max-width: 768px) {
    .product-container {
        flex-direction: column;
    }

    .product-info {
        margin-top: 20px;
    }
}

</style>

<body class="product-page">
<div class="container mt-5 single-product">
    <div class="product-container">
        <div class="col-md-3">
            <img src="assets/imgs/<?php echo $product['product_category']; ?>/<?php echo $product['product_image']; ?>" class="img-fluid" alt="<?php echo $product['product_name']; ?>">
        </div>
        <div class="col-md-6 product-info">
            <h3 class="product-name" ><?php echo $product['product_name']; ?></h3>
            <p class="product-price" >Pkr <?php echo $product['product_price']; ?></p>
            <p id="product-description" class="product-description"><?php echo $product['product_description']; ?></p>
            <a href="#" id="read-more" class="read-more">Read More</a>
            
           <!-- Add to Cart Form -->
<form method="POST" action="">
    <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
    <div class="form-inline">
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" value="1" min="1" max="10" required />
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>" />
        <input type="hidden" name="product_name" value="<?php echo $product['product_name']; ?>" />
        <input type="hidden" name="product_image" value="<?php echo $product['product_image']; ?>" />
        <input type="hidden" name="product_price" value="<?php echo $product['product_price']; ?>" />
        <button type="submit" name="add_bookmark" class="btn btn-bookmark"><i class="fas fa-bookmark"></i></button>
    </div>
    <!-- Move Add to Cart button below the quantity input and Bookmark button -->
    <button type="submit" name="add_to_cart" class="btn btn-cart" id="btn-cart"><i class="fas fa-shopping-cart"></i> Add to Cart</button>
</form>

        </div>
    </div>
</div>

<div class="container mt-5">
    <h4>Leave a Review</h4>
    <form method="POST" action="add_review.php">
        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
        <div class="form-group">
            <label for="rating">Rating:</label>
            <select id="rating" name="rating" class="form-control" required>
                <option value="5">5 - Excellent 😊</option>
                <option value="4">4 - Very Good 🙂</option>
                <option value="3">3 - Good 😐</option>
                <option value="2">2 - Fair 😕</option>
                <option value="1">1 - Poor 😢</option>
            </select>
        </div>
        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" name="submit_review" class="btn review-btn" id="review-btn">Submit Review</button>
    </form>
</div>

<div class="container mt-5">
    <h4>Reviews</h4>
    <?php while ($review = $reviews->fetch_assoc()): ?>
        <div class="review">
            <div class="star-rating">
                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>
                <?php for ($i = $review['rating']; $i < 5; $i++): ?>
                    <i class="far fa-star"></i>
                <?php endfor; ?>
            </div>
            <p><?php echo $review['comment']; ?></p>
            <small>By <i class="fas fa-user"></i> <?php echo $review['user_name']; ?> on <i class="fas fa-calendar-alt"></i> <?php echo date('F j, Y', strtotime($review['created_at'])); ?></small>
            <div class="review-footer">
                <span class="like-count"><?php echo $review['like_count']; ?> Likes</span>
                <button class="like-button" data-review-id="<?php echo $review['review_id']; ?>">
                    <i class="fas fa-thumbs-up"></i>
                </button>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<!------------recommended produts----------->
<div class="container mt-5">
  <h4>You May Also Like</h4>
  <div class="recommended-section">
    <?php while ($rec_product = $recommended_products->fetch_assoc()): ?>
      <div class="col-lg-2 col-md-3 col-sm-4 col-6">
        <div class="card h-100">
          <img src="assets/imgs/<?php echo $rec_product['product_category']; ?>/<?php echo $rec_product['product_image']; ?>" class="card-img-top" alt="<?php echo $rec_product['product_name']; ?>">
          <div class="card-body">
            <h5 class="card-title" style="font-size: 1rem; font-weight: bold; margin-bottom: 5px;"><?php echo $rec_product['product_name']; ?></h5>
            <p class="card-text">Pkr <?php echo $rec_product['product_price']; ?></p>
            <a href="single-product.php?product_id=<?php echo $rec_product['product_id']; ?>" class="btn btn-view">View</a>
            </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</div>


<?php include 'footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    const readMoreLink = $('#read-more');
    const description = $('#product-description');
    
    // Function to toggle the text
    function toggleDescription() {
        if (description.hasClass('expanded')) {
            description.removeClass('expanded');
            readMoreLink.text('Read More');
        } else {
            description.addClass('expanded');
            readMoreLink.text('Read Less');
        }
    }
    
    // Event listener for Read More link
    readMoreLink.on('click', function(event) {
        event.preventDefault();
        toggleDescription();
    });

   // Like button functionality
    $('.like-button').on('click', function() {
        var button = $(this);
        var reviewId = button.data('review-id');
        var likeCountElement = button.siblings('.like-count');
        
        $.ajax({
            url: 'like_review.php',
            type: 'POST',
            data: { review_id: reviewId },
            success: function(response) {
                if(response === 'liked') {
                    button.css('color', '#0056b3'); // Change color on like
                    var currentCount = parseInt(likeCountElement.text());
                    likeCountElement.text((currentCount + 1) + ' Likes');
                } else if(response === 'unliked') {
                    button.css('color', '#007bff'); // Change color on unlike
                    var currentCount = parseInt(likeCountElement.text());
                    likeCountElement.text((currentCount - 1) + ' Likes');
                } else {
                    alert('Failed to like review.');
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
                alert('An error occurred while processing your request.');
            }
        });
    });
});
</script>



</body>
</html>