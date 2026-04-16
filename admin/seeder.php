<?php
$title = "Генератор данных";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$message = '';
$tables = [];
$stmt = $pdo->query("SHOW TABLES");
while ($row = $stmt->fetch(PDO::FETCH_NUM)) $tables[] = $row[0];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $table = $_POST['table_name'];
    $count = (int)$_POST['count'];
    if (!in_array($table, $tables)) die("Таблица не найдена");
    $exportDir = __DIR__ . '/../exports/';
    if (!is_dir($exportDir)) mkdir($exportDir, 0777, true);
    $filename = $exportDir . $table . '_' . date('Y-m-d_H-i-s') . '.csv';
    $data = $pdo->query("SELECT * FROM `$table`")->fetchAll();
    if (empty($data)) {
        $message = "Таблица пуста";
    } else {
        $fp = fopen($filename, 'w');
        fputcsv($fp, array_keys($data[0]));
        foreach ($data as $row) fputcsv($fp, $row);
        fclose($fp);
        $message = "Бэкап: $filename<br>";
        $template = $data[array_rand($data)];
        $inserted = 0;
        for ($i = 0; $i < $count; $i++) {
            $cols = []; $vals = [];
            foreach ($template as $key => $value) {
                if ($key === 'id') continue;
                if (is_numeric($value)) {
                    if (strpos($key, 'id') !== false) $new = $value;
                    else $new = round($value * (1 + mt_rand(-15,15)/100), 2);
                } else {
                    $new = $value . '_' . mt_rand(1000,9999);
                }
                $cols[] = "`$key`";
                $vals[] = $pdo->quote($new);
            }
            $sql = "INSERT INTO `$table` (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
            try { $pdo->exec($sql); $inserted++; } catch (Exception $e) {}
        }
        $message .= "Сгенерировано: $inserted из $count";
    }
}
?>
<div class="card shadow"><div class="card-header bg-primary text-white"><h3>⚙️ Генератор контента</h3></div><div class="card-body">
<?php if ($message): ?><div class="alert alert-info"><?= $message ?></div><?php endif; ?>
<form method="POST"><div class="mb-3"><label>Таблица</label><select name="table_name" class="form-select"><?php foreach ($tables as $t): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?></select></div>
<div class="mb-3"><label>Количество записей</label><input type="number" name="count" class="form-control" value="10" min="1" max="100"></div>
<div class="alert alert-warning"><small>⚠️ Создаст CSV-бэкап в папке /exports, затем скопирует случайную запись с изменениями.</small></div>
<button type="submit" class="btn btn-success w-100">🚀 Наполнить и бэкапить</button></form>
<a href="index.php" class="btn btn-secondary mt-3">← Вернуться</a>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>