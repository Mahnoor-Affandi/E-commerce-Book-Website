<?php 
include 'header.php';
include('../server/connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit;
}

// Fetch orders from the database
$stmt = $conn->prepare("SELECT orders.order_id, orders.order_cost, orders.order_status, users.user_name, users.user_email, orders.order_date 
                        FROM orders 
                        JOIN users ON orders.user_id = users.user_id");
$stmt->execute();
$stmt->bind_result($order_id, $order_cost, $order_status, $user_name, $user_email, $order_date);
$orders = [];
while ($stmt->fetch()) {
    $orders[] = [
        'order_id' => $order_id,
        'order_cost' => $order_cost,
        'order_status' => $order_status,
        'user_name' => $user_name,
        'user_email' => $user_email,
        'order_date' => $order_date,
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Orders</title>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>Orders</title>    
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'side_menu.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="main-content">
                <h1 class="mt-4">All Orders</h1>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_email']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_cost']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                                <td>
                                    <a href="order_details.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-info btn-sm">View</a>
                                    <a href="update_order.php?order_id=<?php echo $order['order_id']; ?>" class="btn btn-warning btn-sm">Update</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
