

<?php
// Include necessary files and establish database connection
include('../server/connection.php'); // Adjust path as per your setup

// Process deletion if product ID is provided via GET parameter
if (isset($_GET['id'])) {
    $product_id = $_GET['id']; 

    // Delete product from the database
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param('i', $product_id);
    if ($stmt->execute()) {
        // Redirect to view_products.php after successful deletion
        header('Location: view_products.php');
        exit;
    } else {
        echo "Error deleting product: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Product ID not specified.";
}
?>
