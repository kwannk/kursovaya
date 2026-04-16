<?php
$title = "Каталог косметики";
require __DIR__ . '/includes/header.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;

$total_stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_rows = $total_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$stmt = $pdo->prepare("SELECT * FROM products ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div class="row">
    <?php if (empty($products)): ?>
        <div class="col-12"><div class="alert alert-warning">Товаров пока нет.</div></div>
    <?php else: ?>
        <?php foreach ($products as $p): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= h($p['image_url'] ?: 'https://via.placeholder.com/300x200?text=Нет+фото') ?>" class="card-img-top">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= h($p['title']) ?></h5>
                        <p class="card-text text-muted"><?= h(mb_substr($p['description'], 0, 100)) ?>...</p>
                        <p class="product-price mt-auto"><?= number_format($p['price'], 2, '.', ' ') ?> ₽</p>
                        <form method="POST" action="/addtocart.php">
                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                            <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-primary w-100">🛍️ В корзину</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if ($total_pages > 1): ?>
    <nav><ul class="pagination justify-content-center">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul></nav>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>