<?php
// includes/checkauth.php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Доступ запрещён. <a href='/login.php'>Войдите</a>");
}