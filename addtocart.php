<?php
session_start();
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('/');
if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF атака!");

$product_id = (int)$_POST['product_id'];
$stmt = $pdo->prepare("SELECT id FROM products WHERE id = ?");
$stmt->execute([$product_id]);
if (!$stmt->fetch()) die("Товар не найден");

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
$_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;

redirect($_SERVER['HTTP_REFERER'] ?? '/');