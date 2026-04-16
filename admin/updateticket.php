<?php
require __DIR__ . '/../includes/checkadmin.php';
require __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['ticket_id'];
    $action = $_POST['action'];
    if ($action === 'status') {
        $status = $_POST['status'];
        $pdo->prepare("UPDATE tickets SET status = ? WHERE id = ?")->execute([$status, $id]);
    } elseif ($action === 'priority') {
        $priority = $_POST['priority'];
        $pdo->prepare("UPDATE tickets SET priority = ? WHERE id = ?")->execute([$priority, $id]);
    }
}
header("Location: tickets.php");
exit;