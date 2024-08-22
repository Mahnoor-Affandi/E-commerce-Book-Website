<?php
session_start();
include('server/connection.php');

// Redirect to login if not logged in
if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit;
}

// Check if order_id is passed
if (!isset($_POST['order_id']) && !isset($_GET['order_id'])) {
    header('location: orders.php'); // Redirect if order_id is not provided
    exit;
}

// Fetch order details based on order_id
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : $_GET['order_id'];

$stmt_order = $conn->prepare("SELECT * FROM orders WHERE order_id = ?");
$stmt_order->bind_param('i', $order_id);
$stmt_order->execute();
$result_order = $stmt_order->get_result();

if ($result_order->num_rows > 0) {
    $order = $result_order->fetch_assoc();
} else {
    header('location: orders.php'); // Redirect if order not found
    exit;
}
?>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');

html, body {
    background-color: #f6f0e8;

}

/* Back Link Styling */
.order-details .back-link {
    display: flex;
    align-items: center;
    padding: 10px;
    max-width: 200px;
    background-color: rgba(255, 255, 255, 0.9); /* Slightly transparent background */
    border: none;
    text-decoration: none;
    color: #333;
    font-size: 1rem;
    font-weight: bold;
    top: 20px; /* Adjust top position */
    left: 20px; /* Align to the left */
    z-index: 1000; /* Ensure it's above other content */
}

.back-link i {
    margin-right: 8px;
    font-size: 1.2rem;
}


.order-details h2 {
    font-size: 2.5rem;
    color: #333;
    text-align: center;
    margin-bottom: 20px;
}

.order-details .order-info {
    margin-bottom: 30px;
}

.order-details .order-info p {
    font-size: 1.2rem;
    color: #333;
    margin: 5px 0;
}

.order-details .order-items {
    margin-top: 30px;
}

.order-details .order-items h3 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 10px;
    text-align: center;
}

.order-details .order-items .item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 15px;
    margin-bottom: 15px;
    background: #f8f4ee ; /* Glassmorphism effect for each item */
    backdrop-filter: blur(10px);
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.order-details .order-items .item img {
    max-width: 100px;
    height: auto;
    border-radius: 5px;
    margin-right: 15px;
}

.order-details .order-items .item .details {
    flex: 1;
}

.order-details .order-items .item .details p {
    margin: 5px 0;
}

.order-details .order-items .item .details .price {
    font-weight: bold;
}

.order-details .order-items .item .details .quantity {
    color: #555;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .order-details .order-info p {
        font-size: 1rem;
    }

    .order-details .order-items h3 {
        font-size: 1.5rem;
    }

    .order-details .order-items .item {
        flex-direction: column;
        align-items: flex-start;
    }

    .order-details .order-items .item img {
        margin-bottom: 10px;
    }
}


</style>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="order-details-page" >

    <section class="order-details">
    <a href="account.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Account
        </a>

        <div class="container">
            <h2>Order Details</h2>
            <hr>
            <div class="order-info">
                <p><strong>Order ID:</strong> <?php echo $order['order_id']; ?></p>
                <p><strong>Total Cost:</strong> PKR <?php echo $order['order_cost']; ?></p>
                <p><strong>Status:</strong> <?php echo $order['order_status']; ?></p>
                <p><strong>Date:</strong> <?php echo $order['order_date']; ?></p>
            </div>

            <div class="order-items">
                    <h3>Order Items</h3>
                    <div class="item-container">
                        <?php
                        $stmt_items = $conn->prepare("SELECT oi.product_name, oi.product_price, oi.product_quantity, p.product_image 
                                                        FROM order_items oi
                                                        JOIN products p ON oi.product_id = p.product_id
                                                        WHERE oi.order_id = ?");
                        $stmt_items->bind_param('i', $order_id);
                        $stmt_items->execute();
                        $result_items = $stmt_items->get_result();

                        while ($item = $result_items->fetch_assoc()) {
                            echo "<div class='item'>";
                            echo "<img src='assets/imgs/" . $item['product_image'] . "' alt='Product Image'>";
                            echo "<div class='details'>";
                            echo "<p><strong>Product Name:</strong> " . $item['product_name'] . "</p>";
                            echo "<p class='price'><strong>Price:</strong> PKR" . $item['product_price'] . "</p>";
                            echo "<p class='quantity'><strong>Quantity:</strong> " . $item['product_quantity'] . "</p>";
                            echo "</div>";
                            echo "</div>";
                        }
                        ?>
                    </div>
                </div>

        </div>
    </section>

    <?php include 'footer.php'; ?>
</body>
</html>
