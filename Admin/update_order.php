<?php
include 'header.php';
include('../server/connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit;
}

// Check if order_id is provided in the URL
if (!isset($_GET['order_id'])) {
    // Redirect to view_orders.php if order_id is not provided
    header('location: view_orders.php');
    exit;
}

$order_id = $_GET['order_id'];

// Fetch order details from the database
$stmt = $conn->prepare("SELECT order_id, order_cost, order_status FROM orders WHERE order_id = ?");
$stmt->bind_param('i', $order_id);
$stmt->execute();
$stmt->bind_result($order_id, $order_cost, $order_status);
$stmt->fetch();
$stmt->close();

// Handle form submission for updating order status
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs (not shown here for brevity)

    // Update order status in the database
    $new_status = $_POST['order_status'];

    $update_stmt = $conn->prepare("UPDATE orders SET order_status = ? WHERE order_id = ?");
    $update_stmt->bind_param('si', $new_status, $order_id);

    if ($update_stmt->execute()) {
        // Redirect to order details page with success message
        header("location: order_details.php?order_id=$order_id&update_success=true");
        exit;
    } else {
        // Redirect to order details page with error message
        header("location: order_details.php?order_id=$order_id&update_success=false");
        exit;
    }

    $update_stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>Update Orders</title>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'side_menu.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="main-content">
                <h1 class="mt-4">Update Order</h1>
                <?php if (isset($_GET['update_success'])): ?>
                    <?php if ($_GET['update_success'] == 'true'): ?>
                        <div class="alert alert-success" role="alert">
                            Order status updated successfully!
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger" role="alert">
                            Failed to update order status. Please try again.
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="order_status">Order Status</label>
                        <input type="text" class="form-control" id="order_status" name="order_status" value="<?php echo htmlspecialchars($order_status); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                    <a href="view_orders.php" class="btn btn-secondary">Back to Orders</a>
                </form>
            </div>
        </div>
    </div>
</div>
