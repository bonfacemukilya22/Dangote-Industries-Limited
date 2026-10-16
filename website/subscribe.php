<?php
require __DIR__ . '/inc.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') go('index.php');
check_csrf();
$em = strtolower(trim($_POST['email'] ?? ''));
if (!filter_var($em, FILTER_VALIDATE_EMAIL)) go('index.php?sub=bad#updates');
db()->prepare('INSERT OR IGNORE INTO subscribers(email,joined) VALUES(?,?)')->execute([$em, date('Y-m-d')]);
go('index.php?sub=ok#updates');