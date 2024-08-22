<?php
include('../server/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $selected_ids = explode(',', $_POST['selected_ids']);

    if ($action === 'unsubscribe') {
        $stmt = $conn->prepare("DELETE FROM newsletter_subscribers WHERE id IN (" . implode(',', array_fill(0, count($selected_ids), '?')) . ")");
        $stmt->bind_param(str_repeat('i', count($selected_ids)), ...$selected_ids);
        $stmt->execute();
        header("Location: newsletter_sub.php?success=unsubscribed");
    } elseif ($action === 'export') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="subscribers.csv";');

        $output = fopen('php://output', 'w');
        fputcsv($output, array('ID', 'Email', 'Subscribed At'));

        $stmt = $conn->prepare("SELECT * FROM newsletter_subscribers WHERE id IN (" . implode(',', array_fill(0, count($selected_ids), '?')) . ")");
        $stmt->bind_param(str_repeat('i', count($selected_ids)), ...$selected_ids);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit();
    }
}
?>