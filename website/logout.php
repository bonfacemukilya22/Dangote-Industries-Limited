<?php
require __DIR__ . '/inc.php';
unset($_SESSION['uid']);
session_regenerate_id(true);
go('index.php');
