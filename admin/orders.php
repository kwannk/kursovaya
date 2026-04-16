<?php
$title = "Все заказы";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$sql = "SELECT orders.id as order_id, orders.created_at, users.email, products.title, products.price
        FROM orders JOIN users ON orders.user_id = users.id JOIN products ON orders.product_id = products.id
        ORDER BY orders.id DESC";
$orders = $pdo->query($sql)->fetchAll();
?>
<h1>Все заказы</h1>
<a href="index.php" class="btn btn-secondary mb-3">← Админка</a>
<table class="table table-bordered">
    <thead><tr><th>ID заказа</th><th>Дата</th><th>Клиент</th><th>Товар</th><th>Цена</th></tr></thead>
    <tbody>
    <?php foreach ($orders as $o): ?>
    <tr><td><?= $o['order_id'] ?></td><td><?= $o['created_at'] ?></td><td><?= h($o['email']) ?></td><td><?= h($o['title']) ?></td><td><?= $o['price'] ?> ₽</td></tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>