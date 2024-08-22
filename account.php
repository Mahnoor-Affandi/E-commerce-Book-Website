<?php 
session_start();
include('server/connection.php');

// Check if logout request is received
if(isset($_GET['logout'])){
    if(isset($_SESSION['logged_in'])){
        unset($_SESSION['logged_in']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        header('location: login.php');
        exit;
    }
} else if (!isset($_SESSION['logged_in'])) {
    header('location: login.php');
    exit;
}

if(isset($_POST['change_password'])){
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmpassword'];
    $user_email = $_SESSION['user_email'];

    if($password !== $confirm_password){
        header('location:account.php?error=Passwords do not match');
        exit;
    } else if(strlen($password) < 6){
        header('location: account.php?error=Password must be at least 6 characters');
        exit;
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE users SET user_password=? WHERE user_email=?");
        $stmt->bind_param('ss', $hashed_password, $user_email);

        if($stmt->execute()){
            header('location:account.php?message=Password has been updated successfully');
            exit;
        } else {
            header('location:account.php?error=Could not update the password');
            exit;
        }
    }    
}

// Debugging: Check if the session variables are set correctly
error_log("Session Email: " . (isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Not Set'));
error_log("Session Name: " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Not Set'));

if(isset($_SESSION['logged_in'])){
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT user_name, user_email, user_profile_picture FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_email'] = $user['user_email'];  // Set the session variable
        $_SESSION['user_name'] = $user['user_name'];    // Set the session variable
        $user_profile_picture = $user['user_profile_picture'];
    }
}

if(isset($_SESSION['logged_in'])){
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $orders = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #f6f0e8;
            color: black;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            padding-left: 0%;
            padding-right: 0%;
        }
        .account-container {
            display: flex;
            flex-wrap: wrap;
            background-color: #f8f4ee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 60px; /* Add margin to avoid overlapping the navbar */
        }
        .sidebar {
            width: 250px;
            background-color: #cdaa7c;
            color: #fff;
            padding: 20px;
        }
        .sidebar img{
            width: 130px;
            height: 130px;
            border-radius: 50%;
            display: block;
            margin: 0 auto 50px; /* Adjusted bottom margin to push text down */
        }
        .sidebar .bi-person-circle {
        width: 150px; /* Increased size */
        height: 150px; /* Increased size */
        border-radius: 50%;
        display: block;
        margin: 0 auto 50px; /* Adjusted bottom margin to push text down */
        font-size: 150px; /* For icon size */
        color: #e8d8c3;
        }
        .sidebar p .email{
            margin: 10px 0;
            text-align: start;
            font-size: 0.9rem;
            margin-top: 78px;
        }
        .sidebar p .name{
            margin: 10px 0;
            text-align: start;
            font-size: 0.9rem;
            margin-top: 70px;
        }
        .sidebar a {
            display: block;
            padding: 10px;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            margin: 5px 0;
            text-align: start;
            font-size: 1rem;
        }
        .sidebar a:hover {
            background-color: #c29760;
            text-decoration: none;
            color: white;
            border-radius: 50px;
        }
        .content {
            flex: 1;
            padding: 20px;
        }
        .content h2 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
        }
        .form-group input {
            width: calc(100% - 22px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 50px;
        }
        .change-pass-btn{
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #cdaa7c;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            justify-content: center;
            width: 100%;
        }
        .change-pass-btn:hover {
            background-color: #bd8e52;
            text-decoration: none;
            color: white;
        }

        .detail-btn {
            display: inline-block;
            padding: 10px 20px;
            color: #fff;
            background-color: #cdaa7c;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }
        .detail-btn:hover {
            background-color: #bd8e52;
            text-decoration: none;
            color: white;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th, table td {
            padding: 15px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #cdaa7c;
            color: white;
        }
        /* General Responsive Styling */
@media (max-width: 1200px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
}

@media (max-width: 992px) {
    .account-container {
        flex-direction: column;
    }

    .sidebar {
        width: 100%;
        text-align: center;
    }

    .sidebar img {
        width: 100px;
        height: 100px;
        margin-bottom: 30px;
    }

    .sidebar p {
        font-size: 1rem;
        margin-top: 30px;
    }
}

@media (max-width: 768px) {
    .sidebar {
        padding: 15px;
    }

    .sidebar a {
        font-size: 0.9rem;
    }

    .content {
        padding: 15px;
    }

    .form-group {
        margin-bottom: 10px;
    }

    .form-group input {
        padding: 8px;
    }

    .change-pass-btn, .detail-btn {
        font-size: 0.9rem;
        padding: 8px 15px;
    }

    table th, table td {
        padding: 10px;
    }
}

@media (max-width: 576px) {
    .container {
        padding-left: 5px;
        padding-right: 5px;
    }

    .sidebar {
        width: 100%;
        padding: 10px;
        text-align: center;
    }

    .sidebar img {
        width: 80px;
        height: 80px;
    }

    .sidebar p {
        font-size: 0.9rem;
        margin-top: 20px;
    }

    .form-group input {
        font-size: 0.85rem;
        padding: 6px;
    }

    .change-pass-btn, .detail-btn {
        font-size: 0.85rem;
        padding: 6px 12px;
    }

    table {
        font-size: 0.85rem;
    }
}

    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"> <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="account-container">
             <div class="sidebar">
                <h2>Account Info</h2>
                <?php if (!empty($user_profile_picture)) { ?>
                    <img src="<?php echo htmlspecialchars($user_profile_picture); ?>" alt="Profile Picture">
                <?php } else { ?>
                    <i class="bi bi-person-circle"></i>
                <?php } ?>
                <p class="name" ><?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Name not set'; ?></p>
                <p class="email" ><?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'Email not set'; ?></p>
                <a href="#orders">View Orders</a>
                <a href="account.php?logout=1">Log Out</a>
            </div>
            <div class="content">
                <h3>Change Password</h3>
                <form method="POST" action="account.php">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword">Confirm Password</label>
                        <input type="password" id="confirmpassword" name="confirmpassword" required>
                    </div>
                    <button type="submit" name="change_password" class="change-pass-btn">Change Password</button>
                    <p><?php if(isset($_GET['error'])){ echo htmlspecialchars($_GET['error']);}?></p>
                    <p><?php if(isset($_GET['message'])){ echo htmlspecialchars($_GET['message']);}?></p>
                </form>
                <h2 id="orders">Your Orders</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Order Cost</th>
                            <th>Order Status</th>
                            <th>Order Date</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $orders->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_cost']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_status']); ?></td>
                            <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                            <td><a class="detail-btn" href="order_details.php?order_id=<?php echo htmlspecialchars($row['order_id']); ?>">Details</a></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>    
    <?php include 'footer.php'; ?>
</body>
</html>
