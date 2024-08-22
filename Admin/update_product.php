<?php
// Include necessary files and establish database connection
include('../server/connection.php'); // Adjust path as per your setup

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_category = $_POST['product_category'];
    $product_price = $_POST['product_price'];
    $product_image = $_FILES['product_image']['name'];

    // File upload handling
    if ($product_image) {
        $target_dir = "../assets/imgs/";
        $target_file = $target_dir . basename($product_image);
        move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file);
    } else {
        // If no new image is uploaded, retain the old image
        $stmt = $conn->prepare("SELECT product_image FROM products WHERE product_id = ?");
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $stmt->bind_result($product_image);
        $stmt->fetch();
        $stmt->close();
    }
 
    // Update the product details in the database
    $stmt = $conn->prepare("UPDATE products SET product_name = ?, product_category = ?, product_price = ?, product_image = ? WHERE product_id = ?");
    $stmt->bind_param('ssisi', $product_name, $product_category, $product_price, $product_image, $product_id);

    if ($stmt->execute()) {
        echo "Product updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    // Redirect back to view products page after update
    header("Location: view_products.php");
    exit();
} else {
    echo "Invalid request method!";
}
?>
