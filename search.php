<?php
include('server/connection.php');

$query = isset($_GET['q']) ? $_GET['q'] : '';
$results = [];

if ($query) {
    // Search for products matching the query
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ? OR product_category LIKE ?");
    $searchQuery = '%' . $query . '%';
    $stmt->bind_param("ss", $searchQuery, $searchQuery);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($results);
?>
