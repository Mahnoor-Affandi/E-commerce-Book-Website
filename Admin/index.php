<?php 
include 'header.php';
include('../server/connection.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('location: login.php');
    exit;
}

// Fetch total number of orders
$order_count_query = "SELECT COUNT(*) as total_orders FROM orders";
$order_count_result = mysqli_query($conn, $order_count_query);
$order_count_row = mysqli_fetch_assoc($order_count_result);
$total_orders = $order_count_row['total_orders'];

// Fetch total sales
$sales_query = "SELECT SUM(order_cost) as total_sales FROM orders";
$sales_result = mysqli_query($conn, $sales_query);
$sales_row = mysqli_fetch_assoc($sales_result);
$total_sales = $sales_row['total_sales'];

// Fetch total number of users
$user_count_query = "SELECT COUNT(*) as total_users FROM users";
$user_count_result = mysqli_query($conn, $user_count_query);
$user_count_row = mysqli_fetch_assoc($user_count_result);
$total_users = $user_count_row['total_users'];

// Fetch recent orders
$recent_orders_query = "SELECT order_id, order_cost, order_status, user_id, user_phone, user_city, user_address, order_date FROM orders ORDER BY order_date DESC LIMIT 5";
$recent_orders_result = mysqli_query($conn, $recent_orders_query);
$recent_orders = [];
while ($row = mysqli_fetch_assoc($recent_orders_result)) {
    $recent_orders[] = $row;
}
?>


<style>
        /* Add the custom CSS here */
        .main-content {
            margin-left: 20px;
            margin-right: 20px;
            padding: 20px;
            width: auto;
            box-sizing: border-box;
        }
        .container-fluid {
            display: flex;
        }
        .row {
            flex-grow: 1;
            display: flex;
        }
        .col-md-2 {
            flex: 0 0 240px;
        }
        .col-md-10 {
            flex: 1;
            padding-left: 0;
            padding-right: 0;
        }

        /* Overall styles */
body {
  background-color: #000;
  color: #fff;
  font-family: sans-serif;
}

.main-content {
  padding: 20px;
}

    </style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2">
            <?php include 'side_menu.php'; ?>
        </div>
        <div class="col-md-10">
            <div class="main-content">
                <h1 class="mt-4">Admin Dashboard</h1>
                <div class="row">
                    <div class="col-md-4">
                    <div class="card mb-3" style="background-color:  rgba(233, 136, 186, 0.8); border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                            <div class="card-header">Total Orders</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $total_orders; ?></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-3" style="background-color: rgba(82, 127, 172, 0.8); border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                            <div class="card-header">Total Sales</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo number_format($total_sales, 2); ?> PKR</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                    <div class="card mb-3" style="background-color: rgba(109, 71, 109, 0.8); border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
                    <div class="card-header">Total Users</div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $total_users; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                
                <h2>Recent Orders</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Cost</th>
                            <th>Status</th>
                            <th>User ID</th>
                            <th>Phone</th>
                            <th>City</th>
                            <th>Address</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['order_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_cost']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_status']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_id']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_phone']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_city']); ?></td>
                                <td><?php echo htmlspecialchars($order['user_address']); ?></td>
                                <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


