<?php
$title = "Добавить товар";
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/checkadmin.php';

$uploadDir = __DIR__ . '/../uploads/products/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$message = '';
$imagePath = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['csrf_token'] ?? '')) {
        die("CSRF атака!");
    }

    $title = trim($_POST['title']);
    $price = (float)$_POST['price'];
    $desc = trim($_POST['description']);
    $imgUrl = trim($_POST['image_url']); 

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $_FILES['image_file']['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, $allowedMime)) {
            $message = '<div class="alert alert-danger">Можно загружать только JPG, PNG, WEBP</div>';
        } else {
            $ext = pathinfo($_FILES['image_file']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $uploadPath = $uploadDir . $filename;
            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadPath)) {
                $imagePath = '/uploads/products/' . $filename;
            } else {
                $message = '<div class="alert alert-danger">Ошибка сохранения файла</div>';
            }
        }
    } elseif (!empty($imgUrl)) {
        $imagePath = $imgUrl;
    }

    if (empty($title)) {
        $message = '<div class="alert alert-danger">Заполните название!</div>';
    } elseif ($price <= 0) {
        $message = '<div class="alert alert-danger">Цена должна быть больше 0</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO products (title, description, price, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $price, $imagePath]);
        $message = '<div class="alert alert-success">✅ Товар добавлен! <a href="/">Посмотреть</a></div>';
    }
}
?>

<h1>➕ Новый товар</h1>
<a href="index.php" class="btn btn-secondary mb-3">← Назад в админку</a>
<?= $message ?>

<form method="POST" enctype="multipart/form-data" class="card p-4">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="mb-3">
        <label>Название товара</label>
        <input type="text" name="title" class="form-control" required>
    </div>

    <div class="mb-3">
        <label>Цена (₽)</label>
        <input type="number" name="price" class="form-control" step="0.01" min="0.01" required>
    </div>

    <div class="mb-3">
        <label>Изображение товара</label>
        <input type="file" name="image_file" class="form-control" accept="image/jpeg,image/png,image/webp">
        <small class="text-muted">Или укажите URL картинки ниже</small>
    </div>

    <div class="mb-3">
        <label>URL картинки (если нет файла)</label>
        <input type="text" name="image_url" class="form-control" placeholder="https://example.com/photo.jpg">
    </div>

    <div class="mb-3">
        <label>Описание</label>
        <textarea name="description" class="form-control" rows="4"></textarea>
    </div>

    <button type="submit" class="btn btn-primary">💾 Сохранить товар</button>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>