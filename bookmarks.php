<?php
session_start();
include('server/connection.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // If user is not logged in, redirect to login page
    header("Location: login.php");
    exit; // Always call exit after header redirection to stop further script execution
}

$user_id = $_SESSION['user_id'];

// Fetch bookmarks for the current user
$sql = "SELECT b.*, p.product_id, p.product_name, p.product_image, p.product_price FROM bookmarks b
        INNER JOIN products p ON b.product_id = p.product_id
        WHERE b.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmarks</title>
    <link rel="stylesheet" href="assets/css/bookmarks.css">
    <?php include 'header.php'; // Ensure header.php doesn't have conflicting redirection logic ?>
</head>

<style>
body.bookmarks-page {
    background-color: #ecf0f1;
}

.bookmarks-page h1 {
    text-align: center;
    margin: 20px 0;
}

.bookmarks-page .container {
    padding: 20px;
}

.bookmarks-page .row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -10px; /* Adjust spacing */
}

.bookmarks-page .col-lg-2 {
    flex: 0 0 16.66%; /* 6 products per row */
    max-width: 16.66%;
    padding: 10px; /* Adjust spacing between products */
    box-sizing: border-box; /* Include padding in width calculation */
}

.bookmarks-page .card {
    background-color: #ecf0f1;
    border: none;
    transition: transform 0.3s ease-in-out;
    display: flex;
    flex-direction: column;
    height: 100%; /* Ensure the card takes full height */
}

.bookmarks-page .card:hover {
    transform: translateY(-5px);
}

.bookmarks-page .card .card-img-top {
    object-fit: contain; /* Ensure the image is not cropped */
    border-top-left-radius: 10px;
    border-top-right-radius: 10px;
    width: 100%;
    height: 250px; /* Adjust as needed */
}

.bookmarks-page .card .card-body {
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    flex-grow: 1; /* Allow the body to take remaining space */
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-title {
    margin-top: 0; /* Remove top margin of the card title */
}

.bookmarks-page .card .btn-group {
    display: flex;
    justify-content: space-between;
}

.bookmarks-page .card .btn {
    background-color: transparent;
    color: #000;
    border: 2px solid #000;
    border-radius: 20px;
    transition: background-color 0.3s, color 0.3s;
    padding: 5px 10px; /* Smaller padding */
    font-size: 0.8rem; /* Smaller font size */
}

.bookmarks-page .card .btn:hover {
    background-color: #000;
    color: #fff;
}

.bookmarks-page .card .btn-danger {
    background-color: #dc3545;
    color: #fff;
    border: none; /* Remove the black outline */
    border-radius: 20px;
    transition: background-color 0.3s, color 0.3s;
}

.bookmarks-page .card .btn-danger:hover {
    background-color: #c82333;
    color: #fff;
}

/* Responsive Styles */
@media (max-width: 1200px) {
    .bookmarks-page .col-lg-2 {
        flex: 0 0 16.66%; /* 6 products per row */
        max-width: 16.66%;
    }
}

@media (max-width: 992px) {
    .bookmarks-page .col-lg-2 {
        flex: 0 0 20%; /* 5 products per row */
        max-width: 20%;
    }
}

@media (max-width: 768px) {
    .bookmarks-page .col-lg-2 {
        flex: 0 0 25%; /* 4 products per row */
        max-width: 25%;
    }
}

@media (max-width: 576px) {
    .bookmarks-page .col-lg-2 {
        flex: 0 0 33.33%; /* 3 products per row */
        max-width: 33.33%;
    }
}

@media (max-width: 480px) {
    .bookmarks-page .col-lg-2 {
        flex: 0 0 50%; /* 2 products per row */
        max-width: 50%;
    }
}
</style>
<body class="bookmarks-page">
    <h1>Bookmarks</h1>
    <div class="container">
        <div class="row">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="col-lg-2 col-md-4 col-sm-6">
                    <div class="card mb-4 bookmark-card">
                        <img src="assets/imgs/<?php echo $row['product_image']; ?>" class="card-img-top" alt="<?php echo $row['product_name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row['product_name']; ?></h5>
                            <p class="card-text">Price: Rs <?php echo $row['product_price']; ?></p>
                            <div class="btn-group">
                                <!-- Add to Cart Form -->
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>" />
                                    <input type="hidden" name="product_image" value="<?php echo $row['product_image']; ?>" />
                                    <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>" />
                                    <input type="hidden" name="product_price" value="<?php echo $row['product_price']; ?>" />
                                    <input type="hidden" name="product_quantity" value="1" />
                                    <button type="submit" name="add_to_cart" class="btn btn-primary">
                                        <i class="fa-light fa-cart-shopping-fast"></i>
                                    </button>
                                </form>
                                <!-- Remove from Bookmark Form -->
                                <form method="POST" action="remove_bookmark.php">
                                    <input type="hidden" name="bookmark_id" value="<?php echo $row['bookmark_id']; ?>" />
                                    <button type="submit" name="remove_bookmark" class="btn btn-danger">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
    <?php include 'footer.php'; // Ensure footer.php doesn't have conflicting redirection logic ?>
</body>
</html>
