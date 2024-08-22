<?php
session_start();
include('server/connection.php');

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $reset_token = bin2hex(random_bytes(32)); // Generate a unique token

    // Update the user record with the reset token
    $stmt = $conn->prepare("UPDATE users SET reset_token = ? WHERE user_email = ?");
    $stmt->bind_param('ss', $reset_token, $email);
    
    if ($stmt->execute()) {
        // Send the reset email
        $reset_link = "http://localhost/Finel-Project/reset-password.php?token=" . $reset_token;
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: " . $reset_link;
        $headers = "From: no-reply@yourwebsite.com\r\n";
        $headers .= "Reply-To: no-reply@yourwebsite.com\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        
        mail($email, $subject, $message, $headers);
        
        echo "A password reset link has been sent to your email.";
    } else {
        echo "Failed to send reset link.";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form method="POST" action="forgot-password.php">
            <?php if (isset($success_message)) { echo "<p style='color:green;'>$success_message</p>"; } ?>
            <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>
            <div class="form-group">
                <label for="email">Enter your email address</label>
                <input type="email" name="email" id="email" required>
            </div>
            <button type="submit" name="reset_request">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
