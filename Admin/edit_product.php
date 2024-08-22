
<?php 

include 'header.php'; // Include your header file with necessary HTML structure
include('../server/connection.php'); // Adjust path as per your setup

// Fetch the product details to edit
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("SELECT product_id, product_name, product_category, product_price, product_image FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $_GET['id']);
    $stmt->execute();
    $stmt->bind_result($product_id, $product_name, $product_category, $product_price, $product_image);
    $stmt->fetch();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>Edit Product</title>
</head>
<body>

<?php include 'side_menu.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>Edit Product</h2>
        <form action="update_product.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="form-group">
                <label for="product_name">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_category">Product Category</label>
                <input type="text" class="form-control" id="product_category" name="product_category" value="<?php echo htmlspecialchars($product_category); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_price">Product Price</label>
                <input type="number" class="form-control" id="product_price" name="product_price" value="<?php echo htmlspecialchars($product_price); ?>" required>
            </div>
            <div class="form-group">
                <label for="product_image">Product Image</label>
                <input type="file" class="form-control" id="product_image" name="product_image">
                <img src="../assets/imgs/<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>" width="100">
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
