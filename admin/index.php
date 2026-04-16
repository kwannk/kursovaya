<?php
$title = "Админ-панель";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';
?>
<div class="alert alert-success">
    <h1>Панель Администратора</h1>
    <p>Добро пожаловать в систему управления.</p>
    <a href="additem.php" class="btn btn-primary">➕ Добавить товар</a>
    <a href="orders.php" class="btn btn-info">📦 Заказы</a>
    <a href="tickets.php" class="btn btn-warning">🎫 Заявки поддержки</a>
    <a href="seeder.php" class="btn btn-secondary">⚙️ Генератор данных</a>
    <a href="/logout.php" class="btn btn-danger">🚪 Выйти</a>
</div>
<?php require __DIR__ . '/../includes/footer.php'; ?>