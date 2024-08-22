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
        }
    }
    return $total;
}

// Calculate total price and add shipping charges
if (isset($_SESSION['cart'])) {
    $totalPrice = calculateTotalPrice($_SESSION['cart'], $conn);
    $shippingCharges = 250;
    $finalTotal = $totalPrice + $shippingCharges;

    // Store total amount in session for use on thank you page
    $_SESSION['order_total'] = $finalTotal;
} else {
    die("Cart is empty. Please add items to your cart.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_order'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $address = $_POST['address'];

    // Store order details in session
    $_SESSION['order'] = [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'city' => $city,
        'address' => $address,
        'payment_method' => 'Cash on Delivery',
        'total_price' => $finalTotal
    ];

    // Order processing logic (previously in processorder.php)
    if (!isset($_SESSION['user_id'])) {
        die("User ID not found in session.");
    }
    $user_id = $_SESSION['user_id'];
    $order_date = date('Y-m-d H:i:s');
    $order_status = "Pending";

    // Insert order into orders table
    $stmt = $conn->prepare("INSERT INTO orders (order_cost, order_status, user_id, user_phone, user_city, user_address, order_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("disssss", $finalTotal, $order_status, $user_id, $phone, $city, $address, $order_date);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert each item in cart into order_items table
    foreach ($_SESSION['cart'] as $product_id => $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, product_price, product_quantity, user_id, order_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iissdiss", $order_id, $product_id, $item['name'], $item['image'], $item['price'], $item['quantity'], $user_id, $order_date);
        $stmt->execute();
        $stmt->close();
    }

    // Clear cart session after order is placed
    unset($_SESSION['cart']);

    // Redirect to thank you page for order confirmation
    header('Location: thank_you.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

<style>
    /* Checkout Page Specific Styles */
html, body{
  margin: 0;
  padding: 0;
  height: 100%;
}


body #checkout-page{
    background-color: #ecf0f1;
}

.container {
    padding: 20px;
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group .form-control {
    border-radius: 50px;
    background: rgba(229, 233, 234, 0.75); /* Transparent glassmorphism effect */
    border: 1px solid rgba(255, 255, 255, 0.18);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    color: #000; /* Adjust text color to match your design */
    padding: 12px; /* Adjust padding as needed */
}

.form-group .form-control:focus {
    outline: none;
    box-shadow: none;
}

#confirm-order-btn {
    background-color: transparent;
    color: black;
    border: 2px solid black;
    border-radius: 50px;
    transition: background-color 0.3s, color 0.3s;
    padding: 8px 16px;
    width: 100%;
}


#confirm-order-btn:hover {
    background-color: #000;
    color: #fff;
}

#card {
            background: none; /* Transparent background */
            border-radius: 16px;
            border: none; /* Remove border */
            margin-top: 20px; /* Adjust margin as needed */
        }

.card-body {
            padding: 20px;
        }

        .table {
            background: rgba(229, 233, 234, 0.75); /* Transparent glassmorphism effect */
            color: #000; /* Adjust text color to match your design */
        }

        .table th,
        .table td {
            border: none;
        }

        .table th {
            color: #000;
        }

        .table img {
            max-width: 60px; /* Increase image size */
            height: auto;
            border-radius: 8px;
            margin-right: 10px; /* Add margin for spacing */
            float: left; /* Float image left */
        }

        .order-item {
            background-color: rgba(229, 233, 234, 0.75); /* Transparent glassmorphism effect */
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 10px;
            overflow: hidden; /* Clear float */
        }

        .order-item-details {
            margin-left: 70px; /* Adjust margin to create space for image */
        }

        .order-item-details h6 {
            margin: 0;
            font-size: 16px; /* Increase text size */
            margin-bottom: 5px; /* Add margin for spacing */
        }

        .order-item-details p {
            margin: 0;
            font-size: 14px; /* Increase text size */
        }

        @media (max-width: 768px) {
            .form-group {
                margin-bottom: 10px;
            }

            #confirm-order-btn {
                font-size: 0.9rem;
                padding: 8px 16px;
            }
        }

        @media (max-width: 576px) {
            .form-control {
                padding: 10px; /* Adjust padding for smaller screens */
            }

            #confirm-order-btn {
                font-size: 0.8rem;
                padding: 6px 12px;
            }

            .table img {
                max-width: 50px; /* Adjust image size for smaller screens */
            }

            .order-item-details {
                margin-left: 60px; /* Adjust margin for smaller screens */
            }

            .order-item-details h6 {
                font-size: 14px; /* Adjust text size for smaller screens */
            }

            .order-item-details p {
                font-size: 12px; /* Adjust text size for smaller screens */
            }
        }

</style>
    <?php include 'header.php'; ?>
</head>
<body class="checkout-page" id="checkout-page">
<div class="container mt-5">
    <div class="row">
        <!-- Checkout Form -->
        <div class="col-md-6">
            <h2>Checkout</h2>
            <form method="post" action="checkout.php">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea id="address" name="address" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="payment_method" class="form-label">Payment Method</label>
                    <input type="text" class="form-control" id="payment_method" name="payment_method" value="Cash on Delivery" readonly>
                </div>
                <button type="submit" name="confirm_order" id="confirm-order-btn" class="btn confirm-order-btn">Confirm Order</button>
            </form>
        </div>
       <!-- Order Summary -->
       <div class="col-md-6">
            <h2>Order Summary</h2>
            <div class="card" id="card" style="height: 500px; overflow-y: auto;">
                <div class="card-body">
                    <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                        <?php foreach ($_SESSION['cart'] as $product_id => $item): ?>
                            <div class="order-item">
                                <div class="table">
                                    <img src="assets/imgs/<?php echo $item['image']; ?>" class="img-fluid" alt="Product Image">
                                    <div class="order-item-details">
                                        <h6><?php echo htmlspecialchars($item['name']); ?></h6>
                                        <p>Price: Rs <?php echo htmlspecialchars($item['price']); ?></p>
                                        <p>Quantity: <?php echo $item['quantity']; ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <hr>
                        <p><strong>Subtotal:</strong> Rs <?php echo isset($totalPrice) ? $totalPrice : 0; ?></p>
                        <p><strong>Shipping Charges:</strong> Rs <?php echo isset($shippingCharges) ? $shippingCharges : 0; ?></p>
                        <p><strong>Total Price:</strong> Rs <?php echo isset($finalTotal) ? $finalTotal : 0; ?></p>
                    <?php else: ?>
                        <p>Your cart is empty. Please add items to your cart.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
