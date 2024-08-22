<?php
// Include necessary files and establish database connection
include 'header.php'; // Include your header file with necessary HTML structure
include('../server/connection.php'); // Adjust path as per your setup

// Fetch all products from the database
$stmt = $conn->prepare("SELECT product_id, product_name, product_category, product_price, product_image FROM products");
$stmt->execute();
$stmt->bind_result($product_id, $product_name, $product_category, $product_price, $product_image);
$products = [];
while ($stmt->fetch()) {
    $products[] = [
        'product_id' => $product_id,
        'product_name' => $product_name,
        'product_category' => $product_category,
        'product_price' => $product_price,
        'product_image' => $product_image, // Assuming product_image stores the filename
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> 
    <title>View Products</title>
</head>
<body style="background-color: black; color: white;">

<?php include 'side_menu.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>View Products</h2>
        <li class="list-group-item">
            <a href="add_product.php">
                <i class="bi bi-plus-circle"></i> Add Product
            </a>
        </li>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['product_id']; ?></td>
                        <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($product['product_category']); ?></td>
                        <td><?php echo htmlspecialchars($product['product_price']); ?></td>
                        <td>
                            <a href="product_details.php?id=<?php echo $product['product_id']; ?>" class="btn btn-info btn-sm">View</a>
                            <a href="edit_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="delete_product.php?id=<?php echo $product['product_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
