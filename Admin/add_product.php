<?php
// Include necessary files and establish database connection
include 'header.php'; // Include your header file with necessary HTML structure
include('../server/connection.php'); // Adjust path as per your setup

// Define variables to hold form data
$product_name = $product_category = $product_description = $product_image = $product_image2 = $product_image3 = $product_image4 = $product_price = $product_special_offer = $product_color = '';

// Define error and success messages
$errors = $success = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data (you should implement proper validation)
    $product_name = $_POST['product_name'] ?? '';
    $product_category = $_POST['product_category'] ?? '';
    $product_description = $_POST['product_description'] ?? '';
    $product_price = $_POST['product_price'] ?? '';
    $product_special_offer = $_POST['product_special_offer'] ?? '';
    $product_color = $_POST['product_color'] ?? '';

    // Handle image uploads (assuming single image upload for simplicity)
    if ($_FILES['product_image']['error'] === UPLOAD_ERR_OK) {
        $tmp_name = $_FILES['product_image']['tmp_name'];
        $filename = $_FILES['product_image']['name'];
        $upload_path = '../assets/imgs/' . $filename; // Adjust path as per your setup

        if (move_uploaded_file($tmp_name, $upload_path)) {
            // Image uploaded successfully, proceed with database insertion
            $stmt = $conn->prepare("INSERT INTO products (product_name, product_category, product_description, product_image, product_price, product_special_offer, product_color) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssss", $product_name, $product_category, $product_description, $filename, $product_price, $product_special_offer, $product_color);

            if ($stmt->execute()) {
                $success = "Product added successfully.";
                // Clear form data after successful submission
                $product_name = $product_category = $product_description = $product_price = $product_special_offer = $product_color = '';
            } else {
                $errors = "Failed to add product. Please try again.";
            }
            $stmt->close();
        } else {
            $errors = "Failed to upload image. Please try again.";
        }
    } else {
        $errors = "Image upload error. Please try again.";
    }
}
?>

<!-- HTML structure for adding products -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
</head>
<body>

<?php include 'side_menu.php'; ?>

<div class="container">
    <h2>Add Product</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php echo $errors; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="product_name">Product Name:</label>
            <input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product_name); ?>" required>
        </div>
        <div class="form-group">
            <label for="product_category">Category:</label>
            <input type="text" class="form-control" id="product_category" name="product_category" value="<?php echo htmlspecialchars($product_category); ?>" required>
        </div>
        <div class="form-group">
            <label for="product_description">Description:</label>
            <textarea class="form-control" id="product_description" name="product_description"><?php echo htmlspecialchars($product_description); ?></textarea>
        </div>
        <div class="form-group">
            <label for="product_image">Image:</label>
            <input type="file" class="form-control-file" id="product_image" name="product_image" required>
        </div>
        <div class="form-group">
            <label for="product_price">Price:</label>
            <input type="text" class="form-control" id="product_price" name="product_price" value="<?php echo htmlspecialchars($product_price); ?>" required>
        </div>
        <div class="form-group">
            <label for="product_special_offer">Special Offer:</label>
            <input type="text" class="form-control" id="product_special_offer" name="product_special_offer" value="<?php echo htmlspecialchars($product_special_offer); ?>">
        </div>
        <div class="form-group">
            <label for="product_color">Color:</label>
            <input type="text" class="form-control" id="product_color" name="product_color" value="<?php echo htmlspecialchars($product_color); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<?php include 'footer.php'; // Include your footer file ?>

</body>
</html>
