<?php
$title = "Управление заявками";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';
$dept = $_GET['department'] ?? '';
$params = [];
$where = [];
if ($status) { $where[] = "t.status = ?"; $params[] = $status; }
if ($priority) { $where[] = "t.priority = ?"; $params[] = $priority; }
if ($dept) { $where[] = "t.department_id = ?"; $params[] = $dept; }
$sql = "SELECT t.*, u.email as user_email, d.name as dept_name FROM tickets t JOIN users u ON t.user_id = u.id LEFT JOIN departments d ON t.department_id = d.id";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);
$sql .= " ORDER BY FIELD(t.status,'new','in_progress','closed'), FIELD(t.priority,'high','medium','low'), t.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$tickets = $stmt->fetchAll();
$departments = $pdo->query("SELECT id, name FROM departments")->fetchAll();
?>
<h1>Все заявки</h1>
<a href="index.php" class="btn btn-secondary mb-3">← Админка</a>
<form method="GET" class="row g-3 mb-4">
    <div class="col-md-3"><label>Статус</label><select name="status" class="form-select"><option value="">Все</option>
        <option value="new" <?= $status=='new'?'selected':'' ?>>Новая</option>
        <option value="in_progress" <?= $status=='in_progress'?'selected':'' ?>>В работе</option>
        <option value="closed" <?= $status=='closed'?'selected':'' ?>>Закрыта</option></select></div>
    <div class="col-md-3"><label>Приоритет</label><select name="priority" class="form-select"><option value="">Все</option>
        <option value="low" <?= $priority=='low'?'selected':'' ?>>Низкий</option>
        <option value="medium" <?= $priority=='medium'?'selected':'' ?>>Средний</option>
        <option value="high" <?= $priority=='high'?'selected':'' ?>>Высокий</option></select></div>
    <div class="col-md-3"><label>Отдел</label><select name="department" class="form-select"><option value="">Любой</option>
        <?php foreach ($departments as $d): ?><option value="<?= $d['id'] ?>" <?= $dept==$d['id']?'selected':'' ?>><?= h($d['name']) ?></option><?php endforeach; ?></select></div>
    <div class="col-md-3 align-self-end"><button type="submit" class="btn btn-primary">Фильтр</button> <a href="tickets.php" class="btn btn-secondary">Сброс</a></div>
</form>
<table class="table table-bordered">
    <thead><tr><th>ID</th><th>Клиент</th><th>Тема</th><th>Отдел</th><th>Приоритет</th><th>Статус</th><th>Дата</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($tickets as $t): ?>
    <tr>
        <td><?= $t['id'] ?></td>
        <td><?= h($t['user_email']) ?></td>
        <td><?= h($t['subject']) ?></td>
        <td><?= h($t['dept_name'] ?? '—') ?></td>
        <td>
            <form method="POST" action="updateticket.php" class="d-inline">
                <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                <input type="hidden" name="action" value="priority">
                <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="low" <?= $t['priority']=='low'?'selected':'' ?>>🟢 Низкий</option>
                    <option value="medium" <?= $t['priority']=='medium'?'selected':'' ?>>🟡 Средний</option>
                    <option value="high" <?= $t['priority']=='high'?'selected':'' ?>>🔴 Высокий</option>
                </select>
            </form>
        </td>
        <td>
            <form method="POST" action="updateticket.php" class="d-inline">
                <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">
                <input type="hidden" name="action" value="status">
                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="new" <?= $t['status']=='new'?'selected':'' ?>>🆕 Новая</option>
                    <option value="in_progress" <?= $t['status']=='in_progress'?'selected':'' ?>>⚙️ В работе</option>
                    <option value="closed" <?= $t['status']=='closed'?'selected':'' ?>>✅ Закрыта</option>
                </select>
            </form>
        </td>
        <td><?= $t['created_at'] ?></td>
        <td><a href="/ticketview.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-info">Просмотр</a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php require __DIR__ . '/../includes/footer.php'; ?>