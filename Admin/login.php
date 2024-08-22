<?php
include('../server/connection.php');
include('session.php');

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('location: index.php');
    exit;
}

$error_message = '';
$email = '';

if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Ensure the same hashing method is used during registration

    $stmt = $conn->prepare("SELECT admin_id, admin_name, admin_email, admin_password FROM admins WHERE admin_email = ? AND admin_password = ? LIMIT 1");

    // Bind parameters
    $stmt->bind_param('ss', $email, $password);

    if ($stmt->execute()) {
        $stmt->bind_result($admin_id, $admin_name, $admin_email, $admin_password);
        $stmt->store_result();

        // Check if a row is returned
        if ($stmt->num_rows() == 1) {
            $stmt->fetch();

            // Store data in session variables
            $_SESSION['admin_id'] = $admin_id;
            $_SESSION['admin_name'] = $admin_name;
            $_SESSION['admin_email'] = $admin_email;
            $_SESSION['admin_logged_in'] = true;

            // Logged in successfully
            header('location: index.php?login_success=Logged in Successfully');
            exit;
        } else {
            $error_message = 'Could not verify your account!';
            // Clear inputs on error
            $email = '';
        }
    } else {
        $error_message = 'Something went wrong!';
        // Clear inputs on error
        $email = '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_styles.css">
</head>
<body>
     <!-- Navigation Bar -->
     <nav class="navbar navbar-expand-lg navbar-light bg-black">
        <a class="navbar-brand">BOOK SHOP</a>      
        <div class="collapse navbar-collapse" id="navbarNav"></div>
    </nav>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="text-center mt-5 mb-4">Admin Login</h3>
                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" id="login-btn" name="login_btn" value="Login">Login</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
