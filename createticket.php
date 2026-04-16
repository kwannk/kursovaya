<?php
$title = "Новая заявка";
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/checkauth.php';

$departments = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) die("CSRF");
    $subject = trim($_POST['subject']);
    $msg = trim($_POST['message']);
    $dept = $_POST['department_id'] ?: null;
    $priority = $_POST['priority'];
    if (empty($subject) || empty($msg)) {
        $message = '<div class="alert alert-danger">Заполните тему и сообщение</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO tickets (user_id, department_id, subject, message, priority) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $dept, $subject, $msg, $priority]);
        $message = '<div class="alert alert-success">Заявка создана! <a href="/mytickets.php">Мои заявки</a></div>';
    }
}
?>
<h1>Создание заявки</h1>
<a href="/" class="btn btn-secondary mb-3">← На главную</a>
<?= $message ?>
<form method="POST" class="card p-4">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    <div class="mb-3"><label>Тема</label><input type="text" name="subject" class="form-control" required></div>
    <div class="mb-3"><label>Отдел</label><select name="department_id" class="form-select"><option value="">-- Любой --</option>
        <?php foreach ($departments as $d): ?><option value="<?= $d['id'] ?>"><?= h($d['name']) ?></option><?php endforeach; ?>
    </select></div>
    <div class="mb-3"><label>Приоритет</label><select name="priority" class="form-select">
        <option value="low">Низкий</option><option value="medium" selected>Средний</option><option value="high">Высокий</option>
    </select></div>
    <div class="mb-3"><label>Сообщение</label><textarea name="message" rows="5" class="form-control" required></textarea></div>
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>
<?php require __DIR__ . '/includes/footer.php'; ?>