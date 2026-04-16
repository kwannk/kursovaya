<?php
// config/db.php
$host = 'localhost'; // На Beget хост ВСЕГДА localhost
$db   = 'w913892g_baza'; // Имя базы данных (из панели MySQL)
$user = 'w913892g_baza'; // Имя пользователя (часто совпадает с именем БД)
$pass = 'Ponchik294617.'; // Пароль, который вы задали при создании БД
$charset = 'utf8mb4'; // Кодировка (поддерживает эмодзи и все языки)

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Ошибка БД: " . $e->getMessage());
}

// CSRF-токен
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}