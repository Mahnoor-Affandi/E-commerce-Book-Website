<?php
session_start();

include 'header.php'; // Include your header file with necessary HTML structure
include('../server/connection.php');

// Ensure the user is logged in as an admin (optional)
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

// Handle search query
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}

// Handle filtering
$filter_column = "";
$filter_value = "";
if (isset($_GET['filter_column']) && isset($_GET['filter_value'])) {
    $filter_column = $_GET['filter_column'];
    $filter_value = $_GET['filter_value'];
}

// Fetch subscribers from the database with optional search and filter
$sql = "SELECT * FROM newsletter_subscribers WHERE 1";

if ($search_query != "") {
    $sql .= " AND email LIKE ?";
    $search_query = "%" . $search_query . "%";
}

if ($filter_column != "" && $filter_value != "") {
    $sql .= " AND " . $filter_column . " = ?";
}

$stmt = $conn->prepare($sql);

if ($search_query != "" && $filter_column != "" && $filter_value != "") {
    $stmt->bind_param('ss', $search_query, $filter_value);
} elseif ($search_query != "") {
    $stmt->bind_param('s', $search_query);
} elseif ($filter_column != "" && $filter_value != "") {
    $stmt->bind_param('s', $filter_value);
}

$stmt->execute();
$subscribers = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_styles.css"> <!-- Adjust path to your CSS file -->
    <title>Admin Dashboard - Subscribers</title>
    <!-- Add your CSS here -->
</head>
<style>
    /* news letter subcribers page */
body {
    font-family: Arial, sans-serif;
    background-color:#000;
    margin: 0;
    padding: 0;
    color: white;
}

/* Heading */
h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: white;
    text-align: center;
}

/* Form Styles */
form {
    margin-bottom: 20px;
}

input[type="text"], input[type="date"], select {
    width: 200px;
    padding: 10px;
    margin-right: 10px;
    border: 1px solid #333;
    border-radius: 50px;
    background-color: #797979;
    transition: border-color 0.3s ease;
    color: white;
}

input[type="text"]:focus, input[type="date"]:focus, select:focus {
    border-color: #007bff;
    outline: none;
    color: white;
}

input[type="text"]::placeholder {
    color: white;
}


button[type="submit"] {
    padding: 10px 20px;
    margin-top: 20px;
    background-color: #2b2b2b; /* Make the background transparent */
    color: white;
    /*border: 2px solid white;  Border color and thickness */
    border: none;
    border-radius: 50px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

button[type="submit"]:hover {
    background-color: #838383;
    border: none;
}

/* Table Styles */
/* Table Styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #333; /* Dark background for table */
    color: #fff; /* White text */
    border-radius: 8px; /* Rounded corners */
    overflow: hidden; /* Ensures rounded corners apply to table */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Subtle shadow for a modern touch */
}

table th, table td {
    padding: 10px; /* Slightly smaller padding for consistency with buttons */
    border-bottom: 1px solid #444; /* Border color for cells */
    text-align: left;
    font-size: 16px;
}

table th {
    background-color: #555; /* Darker background for headers */
    color: #fff; /* White text color for headers */
    text-transform: uppercase; /* Modern text transformation */
    letter-spacing: 1px; /* Slightly increased letter spacing */
}

table td {
    background-color: #333; /* Dark background for table rows */
    color: #fff; /* White text color for table cells */
}

table tr:nth-child(even) td {
    background-color: #444; /* Alternating row color */
}

table tr:hover td {
    background-color: #555; /* Subtle hover effect for rows */
    cursor: pointer; /* Pointer cursor on hover */
}


table tr:nth-child(even) td {
    background-color: #5c5c5c; /* Alternating row color */
}

table tr:hover td {
    background-color: #525252; /* Subtle hover effect for modern interactivity */
    cursor: pointer; /* Pointer cursor on hover */
    color: white;
}

/* Batch Actions */
.batch-actions {
    margin-top: 20px;
    text-align: right;
}

.batch-actions button {
    padding: 10px 20px; /* Larger padding for modern buttons */
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 50px; /* Fully rounded buttons for a modern look */
    cursor: pointer;
    margin-left: 10px;
    transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transitions for hover */
}

.batch-actions button:hover {
    background-color: #218838;
    transform: scale(1.05); /* Slight scaling effect on hover for modern interactivity */
}

/* Checkbox Styles */
input[type="checkbox"] {
    transform: scale(1.5);
    margin-right: 10px;
    accent-color: #28a745; /* Modern accent color for checkboxes */
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        width: 95%;
    }

    input[type="text"], input[type="date"], select {
        width: 100%;
        margin-bottom: 10px;
    }

    table th, table td {
        font-size: 14px;
        padding: 12px;
    }

    .batch-actions {
        text-align: center;
        margin-top: 10px;
    }

    table {
        font-size: 14px; /* Slightly smaller text for smaller screens */
    }
}

</style>
<body>

<?php include 'side_menu.php'; ?>


    <div class="container">
        <h2>Newsletter Subscribers</h2>

        <!-- Search Form -->
        <form method="GET" action="">
            <input class="search" type="text" name="search" placeholder="Search by email" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Filter Form -->
        <form method="GET" action="">
            <select name="filter_column">
                <option value="subscribed_at" <?php echo $filter_column == "subscribed_at" ? "selected" : ""; ?>>Subscribed At</option>
            </select>
            <input type="date" name="filter_value" value="<?php echo $filter_value; ?>">
            <button type="submit">Filter</button>
        </form>

        <!-- Subscribers Table -->
        <table border="1">
        <thead>
            <tr>
                <!-- Removed the checkbox from the header -->
                <th>Select</th>
                <th>Email</th>
                <th>Subscribed At</th>
                <th>Actions</th>
            </tr>
        </thead>
            <tbody>
                <?php while($row = $subscribers->fetch_assoc()) { ?>
                    <tr>
                        <td><input type="checkbox" name="subscriber_ids[]" value="<?php echo $row['id']; ?>"></td>
                        <td><?php echo $row['email']; ?></td>
                        <td><?php echo $row['subscribed_at']; ?></td>
                        <td>
                            <!-- Unsubscribe Form -->
                            <form action="unsubscribe.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit">Unsubscribe</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Batch Actions -->
        <form method="POST" action="batch_actions.php">
            <input type="hidden" name="selected_ids" id="selected_ids">
            <button type="submit" name="action" value="unsubscribe">Unsubscribe Selected</button>
            <button type="submit" name="action" value="export">Export Selected</button>
        </form>

    </div>

    <script>
        // Select/Deselect All Checkboxes
        document.getElementById('select-all').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('input[name="subscriber_ids[]"]');
            checkboxes.forEach((checkbox) => {
                checkbox.checked = e.target.checked;
            });
        });

        // Gather Selected IDs for Batch Actions
        document.querySelector('form[action="batch_actions.php"]').addEventListener('submit', function(e) {
            const selectedIds = Array.from(document.querySelectorAll('input[name="subscriber_ids[]"]:checked')).map(cb => cb.value);
            document.getElementById('selected_ids').value = selectedIds.join(',');
        });
    </script>
</body>
</html>
