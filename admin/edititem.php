<?php
require __DIR__ . '/../includes/checkadmin.php';
require __DIR__ . '/../config/db.php';
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();
if (!$product) die("Товар не найден");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    $title = trim($_POST['title']);
    $price = (float)$_POST['price'];
    $desc = trim($_POST['description']);
    $img = trim($_POST['image_url']);
    $upd = $pdo->prepare("UPDATE products SET title=?, description=?, price=?, image_url=? WHERE id=?");
    $upd->execute([$title, $desc, $price, $img, $id]);
    header("Location: index.php");
    exit;
}
$title = "Редактировать товар";
require __DIR__ . '/../includes/header.php';
?>
<h1>Редактирование товара</h1>
<form method="POST" class="card p-4">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <input type="text" name="title" class="form-control mb-2" value="<?= h($product['title']) ?>" required>
    <input type="number" name="price" class="form-control mb-2" value="<?= $product['price'] ?>" step="0.01" required>
    <input type="text" name="image_url" class="form-control mb-2" value="<?= h($product['image_url']) ?>">
    <textarea name="description" class="form-control mb-2"><?= h($product['description']) ?></textarea>
    <button type="submit" class="btn btn-primary">Обновить</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>