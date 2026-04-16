<?php
require __DIR__ . '/includes/checkauth.php';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

$ticket_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$role = $_SESSION['user_role'];

$stmt = $pdo->prepare("SELECT t.*, u.email as user_email, d.name as dept_name FROM tickets t JOIN users u ON t.user_id = u.id LEFT JOIN departments d ON t.department_id = d.id WHERE t.id = ?");
$stmt->execute([$ticket_id]);
$ticket = $stmt->fetch();
if (!$ticket) die("Заявка не найдена");
if ($role !== 'admin' && $role !== 'support' && $ticket['user_id'] != $user_id) die("Нет доступа");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $comment = trim($_POST['comment']);
    if ($comment !== '') {
        $ins = $pdo->prepare("INSERT INTO comments (ticket_id, user_id, comment) VALUES (?, ?, ?)");
        $ins->execute([$ticket_id, $user_id, $comment]);
        if ($ticket['status'] == 'new') {
            $upd = $pdo->prepare("UPDATE tickets SET status = 'in_progress' WHERE id = ?");
            $upd->execute([$ticket_id]);
            $ticket['status'] = 'in_progress';
        }
        header("Location: ticketview.php?id=$ticket_id");
        exit;
    }
}

$comments = $pdo->prepare("SELECT c.*, u.email, u.role FROM comments c JOIN users u ON c.user_id = u.id WHERE c.ticket_id = ? ORDER BY c.created_at ASC");
$comments->execute([$ticket_id]);
$comments = $comments->fetchAll();
$title = "Заявка #$ticket_id";
require __DIR__ . '/includes/header.php';
?>
<h1>Заявка #<?= $ticket_id ?>: <?= h($ticket['subject']) ?></h1>
<a href="<?= ($role=='admin'||$role=='support') ? '/admin/tickets.php' : '/mytickets.php' ?>" class="btn btn-secondary mb-3">← Назад</a>
<div class="card mb-4"><div class="card-body">
    <p><strong>Клиент:</strong> <?= h($ticket['user_email']) ?></p>
    <p><strong>Отдел:</strong> <?= h($ticket['dept_name'] ?? '—') ?></p>
    <p><strong>Приоритет:</strong> <?= $ticket['priority']=='low'?'🟢 Низкий':($ticket['priority']=='medium'?'🟡 Средний':'🔴 Высокий') ?></p>
    <p><strong>Статус:</strong> <?= $ticket['status']=='new'?'🆕 Новая':($ticket['status']=='in_progress'?'⚙️ В работе':'✅ Закрыта') ?></p>
    <p><strong>Сообщение:</strong><br><?= nl2br(h($ticket['message'])) ?></p>
</div></div>
<h3>Комментарии</h3>
<?php foreach ($comments as $c): ?>
    <div class="border p-2 mb-2 rounded"><strong><?= h($c['email']) ?> (<?= $c['role'] ?>)</strong> <small><?= $c['created_at'] ?></small><p><?= nl2br(h($c['comment'])) ?></p></div>
<?php endforeach; ?>
<?php if ($ticket['status'] !== 'closed'): ?>
<form method="POST"><div class="mb-3"><label>Ваш комментарий</label><textarea name="comment" class="form-control" rows="3" required></textarea></div>
<button type="submit" class="btn btn-primary">Отправить</button></form>
<?php else: ?>
    <div class="alert alert-secondary">Заявка закрыта. Комментарии невозможны.</div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>