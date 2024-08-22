<?php 

include('connection.php');

// Modify the query to select products only from the "Science Fiction and Fantasy" category
$stmt = $conn->prepare("SELECT * FROM products WHERE product_category = 'Science Fiction and Fantasy' LIMIT 8");

$stmt->execute();

$featured_products = $stmt->get_result();


?>
