<?php
$title = "Корзина";
require __DIR__ . '/includes/header.php';

$cart_items = [];
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    foreach ($products as $product) {
        $quantity = $_SESSION['cart'][$product['id']];
        $subtotal = $product['price'] * $quantity;
        $cart_items[] = ['product' => $product, 'quantity' => $quantity, 'subtotal' => $subtotal];
        $total_price += $subtotal;
    }
}
?>

<h1>🛒 Корзина</h1>
<a href="/" class="btn btn-secondary mb-3">← Продолжить покупки</a>

<?php if (empty($cart_items)): ?>
    <div class="alert alert-info">Корзина пуста.</div>
<?php else: ?>
    <table class="table table-bordered">
        <thead><tr><th>Товар</th><th>Цена</th><th>Количество</th><th>Сумма</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($cart_items as $item): $p = $item['product']; ?>
            <tr>
                <td><?= h($p['title']) ?></td>
                <td><?= number_format($p['price'], 2) ?> ₽</td>
                <td>
                    <form method="POST" action="/updatecart.php" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" class="form-control d-inline w-50">
                        <button type="submit" class="btn btn-sm btn-primary">Обновить</button>
                    </form>
                 </td>
                <td><?= number_format($item['subtotal'], 2) ?> ₽</td>
                <td>
                    <form method="POST" action="/removefromcart.php" class="d-inline">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Удалить товар?')">Удалить</button>
                    </form>
                 </td>
             </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><th colspan="3">Итого</th><th><?= number_format($total_price, 2) ?> ₽</th><th></th></tr>
        </tfoot>
    </table>
    <form method="POST" action="/checkout.php">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
        <button type="submit" class="btn btn-success">Оформить заказ</button>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>