<?php
// Start or resume the session
session_start();

// Include necessary files and establish database connection
include 'header.php'; // Include your header file
include('../server/connection.php'); // Adjust path as per your setup

// Initialize variables to hold form data
$user_name = $user_email = '';
$errors = $success = '';

// Retrieve customer ID from URL parameter
if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch customer details from the database
    $stmt = $conn->prepare("SELECT user_name, user_email FROM users WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();

    // Assign fetched values to variables
    if ($customer) {
        $user_name = $customer['user_name'];
        $user_email = $customer['user_email'];
    } else {
        // Handle case where customer is not found
        $errors = "Customer not found.";
    }

    $stmt->close();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input data (you should implement proper validation)
    $user_name = $_POST['user_name'] ?? '';
    $user_email = $_POST['user_email'] ?? '';

    // Update customer details in the database
    $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_email = ? WHERE user_id = ?");
    $stmt->bind_param('ssi', $user_name, $user_email, $user_id);

    if ($stmt->execute()) {
        $success = "Customer details updated successfully.";
        
        // Clear form data after successful submission using session
        $_SESSION['user_name'] = '';
        $_SESSION['user_email'] = '';

        // Redirect to view customers page or any other appropriate page
        header("Location: view_customers.php");
        exit();
    } else {
        $errors = "Failed to update customer details. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>Edit Customer</title>
</head>
<body>

<?php include 'side_menu.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>Edit Customer</h2>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php echo $errors; ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label for="user_name">Name:</label>
                <input type="text" class="form-control" id="user_name" name="user_name" value="<?php echo isset($user_name) ? htmlspecialchars($user_name) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="user_email">Email:</label>
                <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo isset($user_email) ? htmlspecialchars($user_email) : ''; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Customer</button>
        </form>
    </div>
</div>

</body>
</html>
