<?php
include('server/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            // Insert the email into the database (you can modify the table name and column names as needed)
            $stmt = $conn->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            
            // Redirect with success message
            header("Location: index.php?success=1#subscribe");
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() == 1062) {
                // Duplicate entry error (email already exists)
                header("Location: index.php?error=2#subscribe");
            } else {
                // Redirect with general error message
                header("Location: index.php?error=1#subscribe");
            }
        }
    } else {
        // Redirect with error message for invalid email
        header("Location: index.php?error=1#subscribe");
    }
}
?>
