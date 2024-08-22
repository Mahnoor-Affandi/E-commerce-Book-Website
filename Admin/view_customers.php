<?php
// Include necessary files and establish database connection
include 'header.php'; // Include your header file with necessary HTML structure
include('../server/connection.php'); // Adjust path as per your setup

// Fetch all users from the database
$stmt = $conn->prepare("SELECT user_id, user_name, user_email, user_profile_picture FROM users");
$stmt->execute();
$stmt->bind_result($user_id, $user_name, $user_email, $user_profile_picture);
$users = [];
while ($stmt->fetch()) {
    $users[] = [
        'user_id' => $user_id,
        'user_name' => $user_name,
        'user_email' => $user_email,
        'user_profile_picture' => $user_profile_picture, // Assuming profile picture filename stored
    ];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>View Customers</title>
</head>
<body>


<?php include 'side_menu.php'; ?>

<div class="main-content">
    <div class="container">
        <h2>View Customers</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['user_id']; ?></td>
                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['user_email']); ?></td>
                        <td>
                            <a href="edit_customer.php?id=<?php echo $user['user_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


</body>
</html>
