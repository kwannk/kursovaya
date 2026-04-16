<?php
$title = "Мои заявки";
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/checkauth.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT t.*, d.name as dept_name FROM tickets t LEFT JOIN departments d ON t.department_id = d.id WHERE t.user_id = ? ORDER BY t.created_at DESC");
$stmt->execute([$user_id]);
$tickets = $stmt->fetchAll();
?>
<h1>Мои заявки</h1>
<a href="/" class="btn btn-secondary mb-3">← На главную</a>
<a href="/createticket.php" class="btn btn-success mb-3">+ Новая заявка</a>
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>Тема</th><th>Отдел</th><th>Приоритет</th><th>Статус</th><th>Дата</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($tickets as $t): ?>
    <tr>
        <td><?= $t['id'] ?></td>
        <td><?= h($t['subject']) ?></td>
        <td><?= h($t['dept_name'] ?? '—') ?></td>
        <td><?= $t['priority']=='low'?'🟢 Низкий':($t['priority']=='medium'?'🟡 Средний':'🔴 Высокий') ?></td>
        <td><?= $t['status']=='new'?'🆕 Новая':($t['status']=='in_progress'?'⚙️ В работе':'✅ Закрыта') ?></td>
        <td><?= $t['created_at'] ?></td>
        <td><a href="/ticketview.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-info">Просмотр</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/includes/footer.php'; ?>