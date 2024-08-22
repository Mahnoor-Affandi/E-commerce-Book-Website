<?php
include('../server/connection.php'); // Adjust the path to your connection file as needed

// Check if the request method is POST and the ID is provided
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']); // Ensure the ID is an integer

    // Prepare and execute the SQL statement to delete the subscriber
    $stmt = $conn->prepare("DELETE FROM newsletter_subscribers WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // Redirect back to the admin dashboard with a success message
    header("Location: newsletter_sub.php?success=unsubscribed");
    exit();
} else {
    // Redirect to the dashboard with an error message if the ID is not provided
    header("Location: newsletter_sub.php?error=no_id");
    exit();
}
?>
