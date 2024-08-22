<?php
session_start();
include('server/connection.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_POST['remove_bookmark'])) {
    $bookmark_id = $_POST['bookmark_id'];
    $user_id = $_SESSION['user_id'];

    // Prepare SQL to delete the bookmark
    $sql = "DELETE FROM bookmarks WHERE bookmark_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $bookmark_id, $user_id);

    // Execute the statement
    if ($stmt->execute()) {
        header("Location: bookmarks.php");
        exit;
    } else {
        echo "Error deleting bookmark: " . $conn->error;
    }
} else {
    header("Location: bookmarks.php");
    exit;
}
?>
