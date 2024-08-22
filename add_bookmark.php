<?php
session_start();
include('server/connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$product_image = $_POST['product_image'];
$product_price = $_POST['product_price'];

// Check if the product is already bookmarked by the user
$check_sql = "SELECT * FROM bookmarks WHERE user_id = ? AND product_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $user_id, $product_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    // Insert the bookmark
    $insert_sql = "INSERT INTO bookmarks (user_id, product_id, product_name, product_image, product_price) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iisss", $user_id, $product_id, $product_name, $product_image, $product_price);
    $insert_stmt->execute();
}

header("Location: shop.php");
exit;
?>
