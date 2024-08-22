<?php 
include 'header.php';
include('../server/connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit;
}

$order_id = $_GET['order_id'];

// Fetch order details from the database
$stmt = $conn->prepare("SELECT orders.order_id, orders.order_cost, orders.order_status, orders.user_id, orders.user_phone, orders.user_city, orders.user_address, orders.order_date, users.user_name, users.user_email 
                        FROM orders 
                        JOIN users ON orders.user_id = users.user_id 
                        WHERE orders.order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$stmt->bind_result($order_id, $order_cost, $order_status, $user_id, $user_phone, $user_city, $user_address, $order_date, $user_name, $user_email);
$order = [];
if ($stmt->fetch()) {
    $order = [
        'order_id' => $order_id,
        'order_cost' => $order_cost,
        'order_status' => $order_status,
        'user_id' => $user_id,
        'user_phone' => $user_phone,
        'user_city' => $user_city,
        'user_address' => $user_address,
        'order_date' => $order_date,
        'user_name' => $user_name,
        'user_email' => $user_email,
    ];
}
$stmt->close();

// Fetch ordered items
$stmt = $conn->prepare("SELECT product_name, product_image, product_quantity 
                        FROM order_items 
                        WHERE order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$stmt->bind_result($product_name, $product_image, $product_quantity);
$items = [];
while ($stmt->fetch()) {
    $items[] = [
        'product_name' => $product_name,
        'product_image' => $product_image,
        'product_quantity' => $product_quantity,
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>Orders Details</title>


<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'side_menu.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="main-content">
                <h1 class="mt-4">Order Details</h1>
                <?php if ($order): ?>
                    <div class="order-details">
                        <h3>Order ID: <?php echo htmlspecialchars($order['order_id']); ?></h3>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['user_name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($order['user_email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($order['user_phone']); ?></p>
                        <p><strong>City:</strong> <?php echo htmlspecialchars($order['user_city']); ?></p>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($order['user_address']); ?></p>
                        <p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                        <p><strong>Total Cost:</strong> <?php echo htmlspecialchars($order['order_cost']); ?></p>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
                        
                        <h3>Ordered Items</h3>
                        <?php if ($items): ?>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Product Name</th>
                                        <th>Image</th>
                                        <th>Quantity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                            <td><img src="../assets/imgs/<?php echo htmlspecialchars($item['product_image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px;"></td>
                                            <td><?php echo htmlspecialchars($item['product_quantity']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>

                            </table>
                        <?php else: ?>
                            <p>No items found for this order.</p>
                        <?php endif; ?>
                        
                        <a href="update_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-warning">Update Order</a>
                    </div>
                <?php else: ?>
                    <p>No order details found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
