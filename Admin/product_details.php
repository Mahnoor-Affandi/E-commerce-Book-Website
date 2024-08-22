
<?php
// Include necessary files and establish database connection
include 'header.php'; // Include your header file
include('../server/connection.php'); // Adjust path as per your setup

// Retrieve product ID from URL parameter
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    // Fetch product details from the database
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
}
?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>Product Details</title>
</head>
<body>

<?php include 'side_menu.php'; ?>

<div class="main-content">
    <div class="container">
        <?php if (!empty($product)): ?>
            <h2>Product Details</h2>
            <div class="card">
                <img src="../assets/imgs/<?php echo htmlspecialchars($product['product_image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($product['product_name']); ?></h5>
                    <p class="card-text">Category: <?php echo htmlspecialchars($product['product_category']); ?></p>
                    <p class="card-text">Price: <?php echo htmlspecialchars($product['product_price']); ?></p>
                    <p class="card-text">Description: <?php echo htmlspecialchars($product['product_description']); ?></p>
                </div>
            </div>
        <?php else: ?>
            <p>Product not found.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
