<?php
require __DIR__ . '/../includes/checkadmin.php';
require __DIR__ . '/../config/db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
}
header("Location: /admin/");
exit;