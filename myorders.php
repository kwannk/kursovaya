<?php
$title = "Мои заказы";
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/checkauth.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT o.id as order_id, o.created_at, p.title, p.price, p.image_url
                       FROM orders o JOIN products p ON o.product_id = p.id
                       WHERE o.user_id = ? ORDER BY o.created_at DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>
<h1>📦 Мои заказы</h1>
<a href="/" class="btn btn-secondary mb-3">← На главную</a>
<?php if (empty($orders)): ?>
    <div class="alert alert-info">У вас пока нет заказов.</div>
<?php else: ?>
    <div class="row">
        <?php foreach ($orders as $order): ?>
            <div class="col-md-4 mb-3">
                <div class="card">
                    <img src="<?= h($order['image_url'] ?: 'https://via.placeholder.com/150') ?>" class="card-img-top" style="height:150px;object-fit:cover">
                    <div class="card-body">
                        <h5><?= h($order['title']) ?></h5>
                        <p><?= number_format($order['price'], 2) ?> ₽</p>
                        <small>Заказ №<?= $order['order_id'] ?> от <?= $order['created_at'] ?></small>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>