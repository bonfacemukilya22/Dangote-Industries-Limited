<?php
require __DIR__ . '/inc.php';
$l = $_GET['l'] ?? 'en';
if (in_array($l, ['en', 'fr', 'sw'], true)) $_SESSION['lang'] = $l;
$back = $_GET['back'] ?? 'index.php';
if (!preg_match('/^[a-z]+\.php(\?[A-Za-z0-9=&_%-]*)?$/', $back)) $back = 'index.php';
go($back);
