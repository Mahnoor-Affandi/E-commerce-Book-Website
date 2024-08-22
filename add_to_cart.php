<?php
session_start();
include('server/connection.php');

if(isset($_POST['product_id']) && isset($_POST['quantity'])){
    if(isset($_SESSION['user_id'])){ // Check if user is logged in
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        // Fetch product details from database
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();

        $product_name = $product['product_name'];
        $product_price = $product['product_price'];

        // Add product to session cart
        if(isset($_SESSION['cart'][$product_id])){
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = array(
                'name' => $product_name,
                'price' => $product_price,
                'quantity' => $quantity
            );
        }

        header('location: cart.php');
        exit();
    } else {
        // Redirect to login if user is not logged in
        header('location: login.php');
        exit();
    }
} else {
    // Handle invalid request
    header('location: index.php'); // Redirect to home or appropriate page
    exit();
}
?>
