<?php
session_start();
include('server/connection.php');

if(isset($_POST['review_id']) && isset($_SESSION['user_id'])) {
    $review_id = $_POST['review_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the review has already been liked by this user
    $stmt = $conn->prepare("SELECT * FROM review_likes WHERE review_id=? AND user_id=?");
    $stmt->bind_param("ii", $review_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If already liked, remove the like and decrement the like count
        $stmt = $conn->prepare("DELETE FROM review_likes WHERE review_id=? AND user_id=?");
        $stmt->bind_param("ii", $review_id, $user_id);
        $stmt->execute();
        
        $stmt = $conn->prepare("UPDATE reviews SET like_count = like_count - 1 WHERE review_id=?");
        $stmt->bind_param("i", $review_id);
        $stmt->execute();
        
        echo 'unliked';
    } else {
        // If not liked, add a new like and increment the like count
        $stmt = $conn->prepare("INSERT INTO review_likes (review_id, user_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $review_id, $user_id);
        if($stmt->execute()) {
            $stmt = $conn->prepare("UPDATE reviews SET like_count = like_count + 1 WHERE review_id=?");
            $stmt->bind_param("i", $review_id);
            $stmt->execute();
            
            echo 'liked';
        } else {
            echo 'error';
        }
    }
} else {
    echo 'error';
}
?>
