<?php
// includes/checkadmin.php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'support')) {
    die("Доступ запрещён. <a href='/login.php'>Войдите как администратор</a>");
}