<?php
session_start();
include('server/connection.php');

// Function to calculate total price
function calculateTotalPrice($cart, $conn) {
    $total = 0;
    foreach ($cart as $product_id => $item) {
        // Fetch product details from database
        $stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            // Calculate total price
            $total += $product['product_price'] * $item['quantity'];
        } else {
            // Handle case where product is not found
            // You can choose to skip, remove from cart, or log an error here
            // For now, let's skip displaying it
            continue;
        }
        // Store product details in session for display purposes
        $_SESSION['cart'][$product_id]['name'] = $product['product_name'];
        $_SESSION['cart'][$product_id]['price'] = $product['product_price'];
        $_SESSION['cart'][$product_id]['image'] = $product['product_image'];
    }
    return $total;
}

// Check if a product is being added from bookmarks page
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    // Add product to cart session
    $_SESSION['cart'][$product_id] = array(
        'name' => $product_name,
        'price' => $product_price,
        'image' => $product_image,
        'quantity' => $product_quantity
    );

    // Redirect back to bookmarks page or any other desired page
    header('Location: bookmarks.php');
    exit();
}
?>

<?php include 'header.php'; ?>

<style>
/* Cart Page Specific Styles */
html, body {
    height: 100%;
    margin: 0;
    padding: 0;
}

body.cart-page {
    background-color: #ecf0f1;
}

.cart-container {
    padding: 70px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.item-container {
    background: rgba(229, 233, 234, 0.75); /* Transparent glassmorphism effect */
    border-radius: 16px;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    padding: 20px;
    margin-bottom: 15px;
}


.item-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.item-row img {
    max-width: 80px; /* Smaller image size */
    height: auto;
    border-radius: 8px;
}

.item-details {
    flex-grow: 1;
    margin-left: 15px;
}

.item-details h5 {
    font-size: 1.1rem; /* Larger text size */
    margin: 0;
}

.item-details p {
    font-size: 1rem; /* Larger text size */
    margin: 0;
}

.item-actions {
    display: flex;
    align-items: center;
}

.item-actions form {
    display: flex;
    align-items: center;
}

.item-actions input[type="number"] {
    width: 60px;
    margin-right: 10px;
    border-radius: 20px;
    border: 1px solid #ced4da;
    padding: 5px;
}

.item-actions .btn {
    background-color: transparent;
    color: #000;
    border: 2px solid #000;
    border-radius: 20px;
    transition: background-color 0.3s, color 0.3s;
    padding: 5px 10px; /* Smaller padding */
    font-size: 0.8rem; /* Smaller font size */
}

.item-actions .btn-outline-secondary {
    background-color: #6c757d;
    color: #fff;
    border: none; /* Remove the black outline */
}

.item-actions .btn-outline-secondary:hover {
    background-color: #5a6268;
    color: #fff;
}

.item-actions .btn-outline-danger {
    background-color: transparent;
    color: red;
    border: none; /* Remove the black outline */
}

.item-actions .btn-outline-danger:hover {
    background-color: transparent; /* Keep background transparent on hover */
    color: red; /* Keep text color red on hover */
}

/* FontAwesome Icons */
.fa-trash {
    margin-left: 5px;
    color: red;
    font-size: 1.5rem; /* Make the icon larger */
}

.text-right {
    text-align: right;
}

/* Checkout Button Styles */
#checkout-button {
  background-color: black;
  color: white ;
  border: 2px solid #000;
  border-radius: 20px; /* Rounded corners */
  padding: 8px 16px;
  transition: background-color 0.3s, color 0.3s;
  margin-top: 20px;
}

#checkout-button:hover {
  background-color: #000;
  color: #fff;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .item-row {
        flex-direction: column;
        align-items: flex-start;
    }

    .item-details {
        margin-left: 0;
        margin-top: 10px;
    }

    .btn-primary {
        font-size: 0.9rem;
        padding: 8px 16px;
    }
}

@media (max-width: 576px) {
    .item-details h5, .item-details p {
        font-size: 0.8rem;
    }

    .btn-checkout {
        font-size: 0.8rem;
        padding: 6px 12px;
    }
}
</style>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
</head>
<body class="cart-page">
<div class="cart-container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h2>Your Cart</h2>
            <?php
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $product_id => $item) {
                    echo '<div class="item-container">';
                    echo '<div class="item-row">';
                    echo '<img src="assets/imgs/' . $item['image'] . '" class="img-fluid" />';
                    echo '<div class="item-details">';
                    echo '<h5>' . htmlspecialchars($item['name']) . '</h5>';
                    echo '<p>Rs ' . htmlspecialchars($item['price']) . '</p>';
                    echo '</div>';
                    echo '<div class="item-actions">';
                    echo '<form method="post" action="update_cart.php">';
                    echo '<input type="hidden" name="product_id" value="' . $product_id . '" />';
                    echo '<input type="number" name="quantity" value="' . $item['quantity'] . '" min="1" max="10" />';
                    echo '<button type="submit" name="update_quantity" class="btn btn-sm btn-outline-secondary">Update</button>';
                    echo '</form>';
                    echo '<form method="post" action="remove_from_cart.php" style="margin-left: 10px;">';
                    echo '<input type="hidden" name="product_id" value="' . $product_id . '" />';
                    echo '<button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash fa-lg"></i></button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                // Display total price
                echo '<div class="item-container">';
                echo '<div class="item-row">';
                echo '<div class="item-details">';
                echo '<h5>Total Price:</h5>';
                echo '</div>';
                echo '<div class="item-details">';
                echo '<h5>Rs ' . calculateTotalPrice($_SESSION['cart'], $conn) . '</h5>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            } else {
                // Cart is empty
                echo '<div class="item-container">';
                echo '<div class="item-row">';
                echo '<p>Your cart is empty.</p>';
                echo '</div>';
                echo '</div>';
            }
            ?>

            <?php if (!empty($_SESSION['cart'])): ?>
                <div class="text-right">
                    <a href="checkout.php" class="btn btn-checkout" id="checkout-button">Proceed to Checkout</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
