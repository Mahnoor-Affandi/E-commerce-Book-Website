<?php 

include('connection.php');

// Query for the "Graphic Novels and Comics" category
$stmt = $conn->prepare("SELECT * FROM products WHERE product_category = 'Graphic Novels and Comics' LIMIT 8");
$stmt->execute();
$featured_products = $stmt->get_result();

?>
